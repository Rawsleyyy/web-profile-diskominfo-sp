<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SkmController extends Controller
{
    private const ANSWER_OPTIONS = [
        1 => ['Sangat Sesuai', 'Sesuai', 'Kurang Sesuai', 'Tidak Sesuai'],
        2 => ['Sangat Mudah', 'Mudah', 'Kurang Mudah', 'Tidak Mudah'],
        3 => ['Sangat Cepat', 'Cepat', 'Kurang Cepat', 'Tidak Cepat'],
        4 => ['Gratis / Sesuai Ketentuan', 'Murah', 'Cukup Mahal', 'Sangat Mahal'],
        5 => ['Sangat Sesuai', 'Sesuai', 'Kurang Sesuai', 'Tidak Sesuai'],
        6 => ['Sangat Kompeten', 'Kompeten', 'Kurang Kompeten', 'Tidak Kompeten'],
        7 => ['Sangat Sopan dan Ramah', 'Sopan dan Ramah', 'Kurang Sopan dan Ramah', 'Tidak Sopan dan Ramah'],
        8 => ['Dikelola dengan baik', 'Kurang Maksimal', 'Tidak Berfungsi', 'Tidak Ada Sarana'],
        9 => ['Sangat Baik', 'Baik', 'Cukup', 'Buruk'],
    ];

    private const SCORE_MAP = [
        'Sangat Sesuai' => 4,
        'Sangat Mudah' => 4,
        'Sangat Cepat' => 4,
        'Gratis / Sesuai Ketentuan' => 4,
        'Sangat Kompeten' => 4,
        'Sangat Sopan dan Ramah' => 4,
        'Dikelola dengan baik' => 4,
        'Sangat Baik' => 4,
        'Sesuai' => 3,
        'Mudah' => 3,
        'Cepat' => 3,
        'Murah' => 3,
        'Kompeten' => 3,
        'Sopan dan Ramah' => 3,
        'Kurang Maksimal' => 3,
        'Baik' => 3,
        'Kurang Sesuai' => 2,
        'Kurang Mudah' => 2,
        'Kurang Cepat' => 2,
        'Cukup Mahal' => 2,
        'Kurang Kompeten' => 2,
        'Kurang Sopan dan Ramah' => 2,
        'Tidak Berfungsi' => 2,
        'Cukup' => 2,
        'Tidak Sesuai' => 1,
        'Tidak Mudah' => 1,
        'Tidak Cepat' => 1,
        'Sangat Mahal' => 1,
        'Tidak Kompeten' => 1,
        'Tidak Sopan dan Ramah' => 1,
        'Tidak Ada Sarana' => 1,
        'Buruk' => 1,
    ];

    public function index(): JsonResponse
    {
        return response()->json(Skm::latest()->get());
    }

    public function getStats(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'tahun' => ['nullable', 'regex:/^(all|\d{4})$/'],
            'triwulan' => ['nullable', Rule::in(['all', '1', '2', '3', '4', 'Triwulan I', 'Triwulan II', 'Triwulan III', 'Triwulan IV'])],
        ]);

        $query = Skm::query();
        $tahun = $filters['tahun'] ?? null;
        $triwulan = $filters['triwulan'] ?? null;

        if ($tahun && $tahun !== 'all') {
            $query->whereYear('created_at', (int) $tahun);
        }

        $quarterMap = [
            '1' => [1, 3], 'Triwulan I' => [1, 3],
            '2' => [4, 6], 'Triwulan II' => [4, 6],
            '3' => [7, 9], 'Triwulan III' => [7, 9],
            '4' => [10, 12], 'Triwulan IV' => [10, 12],
        ];

        if ($triwulan && isset($quarterMap[$triwulan])) {
            [$startMonth, $endMonth] = $quarterMap[$triwulan];
            $query->whereMonth('created_at', '>=', $startMonth)
                ->whereMonth('created_at', '<=', $endMonth);
        }

        $surveys = $query->get();
        $totalResponden = $surveys->count();

        if ($totalResponden === 0) {
            return response()->json([
                'ikm' => 0,
                'mutu' => 'BELUM ADA DATA',
                'total_responden' => 0,
                'laki_laki' => 0,
                'perempuan' => 0,
                'pendidikan' => [],
            ]);
        }

        $lakiLaki = $surveys->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
        $perempuan = $surveys->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();
        $pendidikanData = $surveys->groupBy('pendidikan')->map->count();
        $totalSkorPerPertanyaan = array_fill(0, 9, 0);

        foreach ($surveys as $survey) {
            for ($index = 1; $index <= 9; $index++) {
                $answer = $survey->{"jawaban_{$index}"};
                $totalSkorPerPertanyaan[$index - 1] += self::SCORE_MAP[$answer] ?? 0;
            }
        }

        $totalNrr = array_sum(array_map(
            fn (int $score) => $score / $totalResponden,
            $totalSkorPerPertanyaan,
        ));

        $ikm = round(($totalNrr / 9) * 25, 2);
        $mutu = match (true) {
            $ikm < 65 => 'BURUK',
            $ikm < 76.61 => 'KURANG BAIK',
            $ikm < 88.31 => 'BAIK',
            default => 'SANGAT BAIK',
        };

        return response()->json([
            'ikm' => $ikm,
            'mutu' => $mutu,
            'total_responden' => $totalResponden,
            'laki_laki' => $lakiLaki,
            'perempuan' => $perempuan,
            'pendidikan' => $pendidikanData,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'jenis_layanan_id' => ['required', 'integer', 'exists:layanans,id'],
            'nama' => ['required', 'string', 'min:2', 'max:150'],
            'no_whatsapp' => ['required', 'string', 'max:20', 'regex:/^(?:\\+62|62|0)8[1-9][0-9]{6,11}$/'],
            'usia' => ['required', 'integer', 'min:15', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'pendidikan' => ['required', 'string', 'max:100'],
            'pekerjaan' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kelurahan' => ['required', 'string', 'max:100'],
            'saran' => ['nullable', 'string', 'max:2000'],
        ];

        foreach (self::ANSWER_OPTIONS as $number => $options) {
            $rules["jawaban_{$number}"] = ['required', Rule::in($options)];
        }

        $validated = $request->validate($rules, [
            'no_whatsapp.regex' => 'Nomor WhatsApp harus menggunakan format Indonesia yang valid, misalnya 081234567890.',
            'jenis_layanan_id.exists' => 'Jenis layanan yang dipilih tidak tersedia.',
        ]);

        try {
            $skm = Skm::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data survei berhasil disimpan.',
                'data' => $skm,
            ], 201);
        } catch (\Throwable $exception) {
            Log::error('Gagal menyimpan SKM', [
                'exception' => $exception,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data survei gagal disimpan. Silakan coba kembali.',
            ], 500);
        }
    }
}
