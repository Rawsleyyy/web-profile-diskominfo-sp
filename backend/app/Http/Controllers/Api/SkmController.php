<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skm;
use Illuminate\Support\Facades\DB;

class SkmController extends Controller
{
    /**
     * Mengambil daftar data SKM (Opsional jika ingin menampilkan tabel di frontend)
     */
    public function index()
    {
        return response()->json(Skm::latest()->get());
    }

    /**
     * Mengambil Statistik & Nilai IKM (PermenPAN-RB)
     */
    public function getStats(Request $request)
    {
        $tahun = $request->query('tahun');
        $triwulan = $request->query('triwulan'); // Opsional: Triwulan I, II, III, IV

        $query = Skm::query();

        // 1. Filter berdasarkan Tahun
        if ($tahun && $tahun !== 'all') {
            $query->whereYear('created_at', $tahun);
        }

        // 2. Filter berdasarkan Triwulan (Opsional)
        if ($triwulan && $triwulan !== 'all') {
            switch ($triwulan) {
                case '1':
                case 'Triwulan I':
                    $query->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 3);
                    break;
                case '2':
                case 'Triwulan II':
                    $query->whereMonth('created_at', '>=', 4)->whereMonth('created_at', '<=', 6);
                    break;
                case '3':
                case 'Triwulan III':
                    $query->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 9);
                    break;
                case '4':
                case 'Triwulan IV':
                    $query->whereMonth('created_at', '>=', 10)->whereMonth('created_at', '<=', 12);
                    break;
            }
        }

        $surveys = $query->get();
        $totalResponden = $surveys->count();

        // Jika belum ada data
        if ($totalResponden === 0) {
            return response()->json([
                'ikm' => 0,
                'mutu' => 'BELUM ADA DATA',
                'total_responden' => 0,
                'laki_laki' => 0,
                'perempuan' => 0,
                'pendidikan' => []
            ]);
        }

        // 1. Hitung Demografi Gender
        $lakiLaki = $surveys->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
        $perempuan = $surveys->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();

        // 2. Hitung Demografi Pendidikan
        $pendidikanData = $surveys->groupBy('pendidikan')->map(function ($item) {
            return $item->count();
        });

        // 3. Rumus Perhitungan Nilai IKM (PermenPAN-RB)
        $bobotMap = [
            'Sangat Sesuai' => 4, 'Sangat Mudah' => 4, 'Sangat Cepat' => 4,
            'Gratis / Sesuai Ketentuan' => 4, 'Sangat Kompeten' => 4,
            'Sangat Sopan dan Ramah' => 4, 'Dikelola dengan baik' => 4, 'Sangat Baik' => 4,

            'Sesuai' => 3, 'Mudah' => 3, 'Cepat' => 3, 'Murah' => 3,
            'Kompeten' => 3, 'Sopan dan Ramah' => 3, 'Kurang Maksimal' => 3, 'Baik' => 3,

            'Kurang Sesuai' => 2, 'Kurang Mudah' => 2, 'Kurang Cepat' => 2,
            'Cukup Mahal' => 2, 'Kurang Kompeten' => 2, 'Kurang Sopan dan Ramah' => 2,
            'Tidak Berfungsi' => 2, 'Cukup' => 2,

            'Tidak Sesuai' => 1, 'Tidak Mudah' => 1, 'Tidak Cepat' => 1,
            'Sangat Mahal' => 1, 'Tidak Kompeten' => 1, 'Tidak Sopan dan Ramah' => 1,
            'Tidak Ada Sarana' => 1, 'Buruk' => 1
        ];

        $totalSkorPerPertanyaan = [0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach ($surveys as $s) {
            for ($i = 1; $i <= 9; $i++) {
                $val = $s->{"jawaban_$i"};
                $score = $bobotMap[$val] ?? 3; // Default score 3 jika tidak cocok string
                $totalSkorPerPertanyaan[$i - 1] += $score;
            }
        }

        // Rata-rata per unsur (NRR)
        $totalNRR = 0;
        foreach ($totalSkorPerPertanyaan as $skor) {
            $nrr = $skor / $totalResponden;
            $totalNRR += $nrr;
        }

        // Nilai IKM Konversi = (Total NRR / 9) * 25
        $ikm = round(($totalNRR / 9) * 25, 2);

        // Kategori Mutu Pelayanan
        $mutu = 'SANGAT BAIK';
        if ($ikm < 65) $mutu = 'BURUK';
        elseif ($ikm < 76.61) $mutu = 'KURANG BAIK';
        elseif ($ikm < 88.31) $mutu = 'BAIK';

        return response()->json([
            'ikm' => $ikm,
            'mutu' => $mutu,
            'total_responden' => $totalResponden,
            'laki_laki' => $lakiLaki,
            'perempuan' => $perempuan,
            'pendidikan' => $pendidikanData
        ]);
    }

    /**
     * Menyimpan Data Form Survei Kepuasan Masyarakat
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Data
        $validated = $request->validate([
            'jenis_layanan_id' => 'required|integer',
            'nama'             => 'required|string|max:255',
            'no_whatsapp'      => 'required|string|max:20',
            'usia'             => 'required|integer',
            'jenis_kelamin'    => 'required|string',
            'pendidikan'       => 'required|string',
            'pekerjaan'        => 'required|string',
            'kecamatan'        => 'required|string',
            'kelurahan'        => 'required|string',
            'jawaban_1'        => 'required|string',
            'jawaban_2'        => 'required|string',
            'jawaban_3'        => 'required|string',
            'jawaban_4'        => 'required|string',
            'jawaban_5'        => 'required|string',
            'jawaban_6'        => 'required|string',
            'jawaban_7'        => 'required|string',
            'jawaban_8'        => 'required|string',
            'jawaban_9'        => 'required|string',
            'saran'            => 'nullable|string',
        ]);

        try {
            // 2. Simpan ke Database
            $skm = Skm::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data survei berhasil disimpan!',
                'data'    => $skm
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan ke database: ' . $e->getMessage()
            ], 500);
        }
    }
}
