<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LayananController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Layanan::query()
            ->where('is_active', true)
            ->whereNotNull('url_eksternal')
            ->orderBy('urutan')
            ->orderBy('nama_layanan')
            ->get();

        return response()->json($services);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('icon')) {
            $validated['icon_path'] = $request->file('icon')->store('layanan', 'public');
        }

        $layanan = Layanan::create($validated);

        ActivityLogger::log('Layanan', 'CREATE', 'success', $request->user()->id, $layanan->nama_layanan);

        return response()->json([
            'message' => 'Layanan berhasil dibuat.',
            'data' => $layanan,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $layanan = Layanan::findOrFail($id);
        $validated = $this->validatePayload($request, partial: true);

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('icon')) {
            if ($layanan->icon_path) {
                Storage::disk('public')->delete($layanan->icon_path);
            }
            $validated['icon_path'] = $request->file('icon')->store('layanan', 'public');
        }

        $layanan->update($validated);

        ActivityLogger::log('Layanan', 'UPDATE', 'success', $request->user()->id, $layanan->nama_layanan);

        return response()->json([
            'message' => 'Layanan berhasil diperbarui.',
            'data' => $layanan->fresh(),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $layanan = Layanan::findOrFail($id);
        $name = $layanan->nama_layanan;

        if ($layanan->icon_path) {
            Storage::disk('public')->delete($layanan->icon_path);
        }

        $layanan->delete();
        ActivityLogger::log('Layanan', 'DELETE', 'success', $request->user()->id, $name);

        return response()->json(['message' => 'Layanan berhasil dihapus.']);
    }

    private function validatePayload(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'nama_layanan' => [$required, 'string', 'min:3', 'max:150'],
            'kategori' => ['sometimes', 'nullable', 'string', 'max:100'],
            'deskripsi' => ['sometimes', 'nullable', 'string', 'max:500'],
            'url_eksternal' => [$required, 'url:http,https', 'max:2048'],
            'icon_path' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'icon' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'urutan' => ['sometimes', 'integer', 'min:0', 'max:999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
