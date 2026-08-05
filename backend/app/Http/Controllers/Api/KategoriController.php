<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Kategori::where('is_publish', true)->orderBy('nama_kategori')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:150'],
            'is_publish' => ['sometimes', 'boolean'],
        ]);
        $validated['is_publish'] = $request->boolean('is_publish', true);

        $kategori = Kategori::create($validated);
        ActivityLogger::log('Kategori', 'CREATE', 'success', $request->user()->id, $kategori->nama_kategori);

        return response()->json($kategori, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $kategori = Kategori::findOrFail($id);
        $validated = $request->validate([
            'nama_kategori' => ['sometimes', 'required', 'string', 'max:150'],
            'is_publish' => ['sometimes', 'boolean'],
        ]);
        if ($request->has('is_publish')) {
            $validated['is_publish'] = $request->boolean('is_publish');
        }

        $kategori->update($validated);
        ActivityLogger::log('Kategori', 'UPDATE', 'success', $request->user()->id, $kategori->nama_kategori);

        return response()->json($kategori->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $kategori = Kategori::findOrFail($id);
        $name = $kategori->nama_kategori;
        $kategori->delete();
        ActivityLogger::log('Kategori', 'DELETE', 'success', $request->user()->id, $name);

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}
