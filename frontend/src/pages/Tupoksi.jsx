import React, { useState } from 'react';
import { Briefcase, ChevronDown, User } from 'lucide-react';

const strukturOrganisasi = [
  {
    id: 'kepala-dinas',
    nama: 'Kepala Dinas',
    deskripsi: 'Memimpin, mengoordinasikan, mengendalikan, dan mempertanggungjawabkan pelaksanaan urusan pemerintahan bidang komunikasi, informatika, statistik, dan persandian.',
    tugas: [
      'Perumusan kebijakan teknis bidang komunikasi dan informatika',
      'Penyelenggaraan urusan pemerintahan dan pelayanan umum',
      'Pembinaan dan pelaksanaan tugas bidang komunikasi dan informatika',
      'Pelaksanaan tugas lain yang diberikan oleh Wali Kota',
    ],
  },
  {
    id: 'sekretariat',
    nama: 'Sekretariat',
    deskripsi: 'Melaksanakan kesekretariatan dinas terkait perencanaan, penganggaran, administrasi umum, serta organisasi dan kepegawaian.',
    tugas: [
      'Koordinasi penyusunan rencana kerja dan anggaran dinas',
      'Pengelolaan administrasi umum, kepegawaian, dan aset',
      'Pelaksanaan monitoring dan evaluasi kinerja dinas',
    ],
  },
  {
    id: 'bidang-ikp',
    nama: 'Bidang Informasi & Komunikasi Publik',
    deskripsi: 'Menyelenggarakan urusan pemerintahan bidang informasi dan komunikasi publik, termasuk pengelolaan opini dan aspirasi publik.',
    tugas: [
      'Pengelolaan informasi dan komunikasi publik pemerintah daerah',
      'Pengelolaan hubungan media dan kemitraan komunikasi publik',
      'Layanan PPID dan keterbukaan informasi publik',
    ],
  },
  {
    id: 'bidang-tik',
    nama: 'Bidang TIK & E-Government',
    deskripsi: 'Menyelenggarakan urusan aplikasi informatika dan infrastruktur teknologi informasi untuk mendukung operasional pemerintah kota.',
    tugas: [
      'Pengembangan dan pemeliharaan aplikasi layanan pemerintah',
      'Pengelolaan infrastruktur jaringan dan pusat data',
      'Integrasi sistem informasi antar perangkat daerah',
    ],
  },
  {
    id: 'bidang-statistik',
    nama: 'Bidang Statistik',
    deskripsi: 'Menyelenggarakan urusan pemerintahan bidang statistik sektoral sebagai dasar perencanaan pembangunan daerah.',
    tugas: [
      'Pengumpulan dan pengolahan data statistik sektoral',
      'Penyajian dan diseminasi data melalui portal SoloData',
      'Pembinaan statistik sektoral perangkat daerah',
    ],
  },
  {
    id: 'bidang-persandian',
    nama: 'Bidang Persandian & Keamanan Informasi',
    deskripsi: 'Menyelenggarakan urusan persandian untuk pengamanan informasi pemerintah daerah.',
    tugas: [
      'Pengelolaan keamanan informasi dan sistem persandian',
      'Deteksi dan penanganan insiden keamanan siber',
      'Pembinaan tata kelola keamanan informasi perangkat daerah',
    ],
  },
];

export default function Tupoksi() {
  const [openId, setOpenId] = useState('kepala-dinas');

  const toggle = (id) => setOpenId((prev) => (prev === id ? null : id));

  return (
    <div className="pt-40 pb-20 px-6 max-w-5xl mx-auto">
      <div className="mb-10 flex gap-2 items-center text-[10px] font-bold uppercase tracking-widest text-slate-400">
        <span>Home</span><span>/</span><span>Profil</span><span>/</span>
        <span className="text-primary">Tupoksi</span>
      </div>

      <div className="text-center mb-16">
        <span className="text-primary font-black uppercase tracking-[0.3em] text-[10px]">Profil Instansi</span>
        <h2 className="text-4xl font-black text-slate-800 tracking-tighter mt-4">Tugas Pokok & Fungsi</h2>
        <p className="text-slate-400 mt-3 font-medium">Berdasarkan Peraturan Wali Kota tentang Susunan Organisasi dan Tata Kerja</p>
      </div>

      {/* Tugas Pokok Dinas — dipertahankan dari versi lama */}
      <div className="bg-slate-50 p-8 md:p-10 rounded-[2rem] border-l-8 border-primary mb-10">
        <p className="text-slate-600 leading-loose text-sm font-medium italic">
          "Melaksanakan urusan pemerintahan bidang komunikasi dan informatika, urusan pemerintahan bidang
          statistik, serta urusan pemerintahan bidang persandian yang menjadi kewenangan Pemerintahan
          Daerah berdasarkan asas otonomi dan tugas pembantuan sesuai Peraturan Wali kota Surakarta
          Nomor 37 Tahun 2025."
        </p>
      </div>

      {/* Accordion Struktur Organisasi — sesuai Figma */}
      <div className="bg-white rounded-[3rem] border border-slate-100 shadow-2xl p-4 md:p-6 space-y-3">
        {strukturOrganisasi.map((unit) => {
          const isOpen = openId === unit.id;
          return (
            <div key={unit.id} className="border border-slate-100 rounded-[2rem] overflow-hidden">
              <button
                onClick={() => toggle(unit.id)}
                className="w-full flex items-center justify-between gap-4 p-5 md:p-6 hover:bg-accent-50/50 transition text-left"
              >
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center shrink-0">
                    {unit.id === 'kepala-dinas' ? <User size={18} /> : <Briefcase size={16} />}
                  </div>
                  <h3 className="font-black text-slate-800 text-sm md:text-base">{unit.nama}</h3>
                </div>
                <ChevronDown
                  size={18}
                  className={`text-slate-400 shrink-0 transition-transform duration-300 ${isOpen ? 'rotate-180 text-primary' : ''}`}
                />
              </button>

              {isOpen && (
                <div className="px-6 pb-6 md:px-8 md:pb-8 pt-0">
                  <p className="text-sm text-slate-600 leading-relaxed mb-4">{unit.deskripsi}</p>
                  <ul className="space-y-2">
                    {unit.tugas.map((t, i) => (
                      <li key={i} className="flex gap-3 text-xs font-bold text-slate-500">
                        <span className="text-primary">•</span> {t}
                      </li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
          );
        })}
      </div>
    </div>
  );
}