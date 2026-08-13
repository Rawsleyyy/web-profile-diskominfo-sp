<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Layanan;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MoniksController extends Controller
{
    private array $stopWords = [
        'apa', 'apakah', 'bagaimana', 'cara', 'yang', 'dan', 'atau', 'di', 'ke', 'dari',
        'untuk', 'saya', 'kami', 'anda', 'ini', 'itu', 'ada', 'bisa', 'dapat', 'mau', 'ingin',
        'dong', 'ya', 'kah', 'nya', 'dengan', 'tentang', 'tolong', 'mohon', 'dimana', 'mana',
    ];

    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:500'],
        ]);

        $message = trim($validated['message']);
        $normalized = $this->normalize($message);
        $faqs = Faq::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $suggestions = $faqs->take(3)->pluck('question')->values()->all();

        if ($this->looksLikeGreeting($normalized)) {
            return $this->reply(
                'Halo! Saya MONIKS. Saya membantu menjawab berdasarkan FAQ, identitas instansi, dan layanan aktif di website ini. Silakan tanyakan layanan, PPID, alamat, kontak, atau informasi yang tersedia.',
                'system',
                $suggestions,
            );
        }

        $matchedFaq = $this->findBestFaq($normalized, $faqs);
        if ($matchedFaq) {
            return $this->reply(
                $matchedFaq->answer,
                'faq',
                $suggestions,
                null,
                null,
                ['faq_id' => $matchedFaq->id, 'category' => $matchedFaq->category],
            );
        }

        $serviceReply = $this->serviceReply($normalized);
        if ($serviceReply) {
            return $this->reply(
                $serviceReply['answer'],
                'service',
                $suggestions,
                $serviceReply['label'] ?? null,
                $serviceReply['url'] ?? null,
            );
        }

        $settingsReply = $this->settingsReply($normalized);
        if ($settingsReply) {
            return $this->reply(
                $settingsReply['answer'],
                'site_settings',
                $suggestions,
                $settingsReply['label'] ?? null,
                $settingsReply['url'] ?? null,
            );
        }

        $settings = SiteSetting::query()->first();
        $contact = collect([
            $settings?->phone ? 'telepon '.$settings->phone : null,
            $settings?->email ? 'email '.$settings->email : null,
        ])->filter()->implode(' atau ');

        $fallback = 'Maaf, saya belum menemukan jawaban yang cukup cocok pada basis informasi resmi website.';
        if ($contact !== '') {
            $fallback .= ' Anda dapat menghubungi '.$contact.' untuk informasi lebih lanjut.';
        } else {
            $fallback .= ' Silakan pilih salah satu FAQ yang tersedia atau gunakan menu layanan terkait.';
        }

        return $this->reply($fallback, 'fallback', $suggestions);
    }

    private function findBestFaq(string $message, $faqs): ?Faq
    {
        $messageTokens = $this->tokens($message);
        $best = null;
        $bestScore = 0;

        foreach ($faqs as $faq) {
            $question = $this->normalize($faq->question);
            $keywords = collect(preg_split('/[,;\n]+/', (string) $faq->keywords))
                ->map(fn ($keyword) => $this->normalize((string) $keyword))
                ->filter()
                ->values();

            $score = 0;

            if ($message === $question) {
                $score += 100;
            }

            if (mb_strlen($message) >= 8 && (Str::contains($question, $message) || Str::contains($message, $question))) {
                $score += 35;
            }

            foreach ($keywords as $keyword) {
                if (mb_strlen($keyword) >= 3 && Str::contains($message, $keyword)) {
                    $score += 24;
                }
            }

            $haystackTokens = $this->tokens($question.' '.$keywords->implode(' '));
            $overlap = count(array_intersect($messageTokens, $haystackTokens));
            $score += $overlap * 6;

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $faq;
            }
        }

        return $bestScore >= 12 ? $best : null;
    }

    private function serviceReply(string $message): ?array
    {
        $services = Layanan::query()
            ->where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('nama_layanan')
            ->get();

        if ($services->isEmpty()) {
            return null;
        }

        foreach ($services as $service) {
            $name = $this->normalize($service->nama_layanan);
            if (mb_strlen($name) >= 3 && Str::contains($message, $name)) {
                $answer = $service->nama_layanan;
                if ($service->deskripsi) {
                    $answer .= ': '.$service->deskripsi;
                }

                return [
                    'answer' => $answer,
                    'label' => 'Buka '.$service->nama_layanan,
                    'url' => $service->url_eksternal,
                ];
            }
        }

        if ($this->containsAny($message, ['layanan', 'pelayanan', 'service'])) {
            $names = $services->take(6)->pluck('nama_layanan')->implode(', ');

            return [
                'answer' => 'Beberapa layanan aktif yang tersedia saat ini: '.$names.'. Silakan buka menu Layanan untuk melihat seluruh layanan.',
                'label' => 'Lihat semua layanan',
                'url' => '/layanan',
            ];
        }

        return null;
    }

    private function settingsReply(string $message): ?array
    {
        $settings = SiteSetting::query()->first();
        if (! $settings) {
            return null;
        }

        if ($this->containsAny($message, ['alamat', 'lokasi', 'kantor', 'tempat'])) {
            if (! $settings->address) {
                return null;
            }

            return [
                'answer' => 'Alamat instansi: '.$settings->address,
                'label' => 'Buka Google Maps',
                'url' => 'https://www.google.com/maps/search/?api=1&query='.urlencode($settings->address),
            ];
        }

        if ($this->containsAny($message, ['telepon', 'telpon', 'nomor', 'kontak', 'hubungi'])) {
            $parts = collect([
                $settings->phone ? 'Telepon: '.$settings->phone : null,
                $settings->email ? 'Email: '.$settings->email : null,
            ])->filter()->values();

            if ($parts->isEmpty()) {
                return null;
            }

            return ['answer' => $parts->implode(' · ')];
        }

        if ($this->containsAny($message, ['email', 'surel'])) {
            return $settings->email ? ['answer' => 'Email instansi: '.$settings->email] : null;
        }

        return null;
    }

    private function reply(
        string $answer,
        string $source,
        array $suggestions,
        ?string $actionLabel = null,
        ?string $actionUrl = null,
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'data' => array_merge([
                'answer' => $answer,
                'source' => $source,
                'suggestions' => $suggestions,
                'action' => $actionLabel && $actionUrl ? [
                    'label' => $actionLabel,
                    'url' => $actionUrl,
                ] : null,
            ], $meta),
        ]);
    }

    private function normalize(string $value): string
    {
        $value = Str::lower(Str::ascii($value));
        $value = preg_replace('/[^a-z0-9\s-]+/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function tokens(string $value): array
    {
        return array_values(array_unique(array_filter(
            preg_split('/\s+/', $this->normalize($value)) ?: [],
            fn ($token) => mb_strlen($token) >= 3 && ! in_array($token, $this->stopWords, true),
        )));
    }

    private function containsAny(string $message, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (Str::contains($message, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function looksLikeGreeting(string $message): bool
    {
        return (bool) preg_match('/^(halo|hai|hi|hello|assalamualaikum|selamat pagi|selamat siang|selamat sore|selamat malam)(\b|$)/', $message);
    }
}
