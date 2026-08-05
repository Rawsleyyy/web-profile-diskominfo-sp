import React from 'react';
import { MessageCircleHeart, Clock3, ShieldCheck, Megaphone } from 'lucide-react';

const prinsipLayanan = [
  {
    title: 'Melayani dengan Sopan & Santun',
    desc: 'Setiap petugas wajib memberikan pelayanan yang ramah, sopan, dan santun kepada setiap pemohon layanan.',
    icon: MessageCircleHeart,
    color: 'bg-accent-100 text-accent-700',
  },
  {
    title: 'Tepat Waktu & Tepat Sasaran',
    desc: 'Kami berkomitmen menyelesaikan setiap permohonan layanan sesuai dengan standar waktu yang telah ditentukan.',
    icon: Clock3,
    color: 'bg-emerald-100 text-emerald-600',
  },
  {
    title: 'Transparan & Akuntabel',
    desc: 'Seluruh proses layanan dilaksanakan secara transparan, dapat dipertanggungjawabkan, dan sesuai prosedur.',
    icon: ShieldCheck,
    color: 'bg-purple-100 text-purple-600',
  },
  {
    title: 'Siap Menerima Pengaduan',
    desc: 'Kami membuka kanal pengaduan bagi masyarakat yang belum puas terhadap pelayanan yang diberikan.',
    icon: Megaphone,
    color: 'bg-orange-100 text-orange-600',
  },
];

const standarPelayanan = [
  { label: 'Persyaratan', value: 'Sesuai ketentuan yang berlaku per jenis layanan' },
  { label: 'Prosedur', value: '1–3 tahap, tergantung jenis layanan' },
  { label: 'Waktu Penyelesaian', value: '1–14 hari kerja' },
  { label: 'Biaya / Tarif', value: 'Tidak dipungut biaya (gratis)' },
  { label: 'Produk Layanan', value: 'Informasi, dokumen, rekomendasi, dan sertifikat' },
  { label: 'Sarana & Prasarana', value: 'Ruang tunggu, komputer, wifi gratis, toilet' },
];

export default function MaklumatPelayanan() {
  return (
    <div className="pt-40 pb-20 px-6 max-w-5xl mx-auto">
      {/* Breadcrumb */}
      <div className="mb-10 flex gap-2 items-center text-[10px] font-bold uppercase tracking-widest text-slate-400">
        <span>Home</span><span>/</span><span>Layanan</span><span>/</span>
        <span className="text-primary">Maklumat Pelayanan</span>
      </div>

      {/* Header Halaman */}
      <div className="text-center mb-16">
        <span className="text-primary font-bold uppercase tracking-[0.3em] text-[10px]">Layanan Publik</span>
        <h2 className="text-4xl font-bold text-slate-800 tracking-tight mt-4">Maklumat Pelayanan</h2>
        <p className="text-slate-400 mt-4 font-medium italic">
          Keputusan Kepala Diskominfo SP Kota Surakarta Nomor 439 Tahun 2026
        </p>
      </div>

      <div className="grid grid-cols-1 gap-10">

        {/* SECTION 1: MAKLUMAT RESMI */}
        <div className="bg-white rounded-2xl p-10 md:p-16 border-t-4 border-primary shadow-sm">
          <div>
            <h3 className="text-2xl font-bold text-slate-800 mb-8 uppercase tracking-tight text-center md:text-left">
              Maklumat Pelayanan
            </h3>
            <div className="bg-slate-50 p-8 md:p-12 rounded-2xl border border-slate-100 italic">
              <p className="text-lg md:text-xl font-bold text-slate-700 leading-relaxed text-center">
                "DENGAN INI, KAMI MENYATAKAN SANGGUP MENYELENGGARAKAN PELAYANAN SESUAI STANDAR PELAYANAN YANG TELAH DITETAPKAN DAN APABILA TIDAK MENEPATI JANJI INI, KAMI SIAP MENERIMA SANKSI SESUAI PERATURAN PERUNDANG-UNDANGAN YANG BERLAKU."
              </p>
            </div>
            <p className="mt-8 text-center text-sm font-bold text-slate-400">
              Kepala Dinas Komunikasi, Informatika, Statistik dan Persandian Kota Surakarta
            </p>
          </div>
        </div>

        {/* SECTION 2 (BARU): GRID PRINSIP LAYANAN — sesuai Figma */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {prinsipLayanan.map((item, i) => {
            const Icon = item.icon;
            return (
              <div
                key={i}
                className="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm flex gap-5 items-start"
              >
                <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 ${item.color}`}>
                  <Icon size={22} />
                </div>
                <div>
                  <h4 className="font-bold text-slate-800 text-sm mb-2">{item.title}</h4>
                  <p className="text-xs text-slate-500 leading-relaxed font-medium">{item.desc}</p>
                </div>
              </div>
            );
          })}
        </div>

        {/* SECTION 3 (BARU): TABEL STANDAR PELAYANAN — sesuai Figma */}
        <div className="bg-white rounded-2xl p-10 md:p-14 border border-slate-100 shadow-sm">
          <h3 className="text-xl font-bold text-slate-800 mb-8 uppercase tracking-tight">Standar Pelayanan</h3>
          <div className="divide-y divide-slate-100">
            {standarPelayanan.map((row, i) => (
              <div key={i} className="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-6 py-4">
                <span className="text-xs font-bold text-slate-400 uppercase tracking-widest">{row.label}</span>
                <span className="md:col-span-2 text-sm font-bold text-slate-700">{row.value}</span>
              </div>
            ))}
          </div>
        </div>

        {/* SECTION 4: VISI, MISI, SLOGAN */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          {/* Visi & Misi Card */}
          <div className="bg-primary text-white p-10 rounded-2xl shadow-xl">
            <h3 className="text-xl font-bold mb-6 uppercase tracking-widest border-b border-white/20 pb-4">Visi & Misi</h3>
            <div className="space-y-6">
              <div>
                <p className="text-[10px] font-bold opacity-60 uppercase mb-2">Visi Dinas:</p>
                <p className="text-sm font-bold italic leading-relaxed">
                  "Gotong Royong mewujudkan Surakarta Berbudaya, Maju, Sejahtera, dan Berkelanjutan"
                </p>
              </div>
              <div>
                <p className="text-[10px] font-bold opacity-60 uppercase mb-2">Misi Utama (Digitalisasi):</p>
                <p className="text-sm font-medium leading-relaxed">
                  "Inovasi Birokrasi untuk Pelayanan Publik yang Adaptif dan Inklusif (Digitalisasi)."
                </p>
              </div>
            </div>
          </div>

          {/* Slogan Card */}
          <div className="bg-emerald-500 text-white p-10 rounded-2xl shadow-xl flex flex-col justify-center items-center text-center">
            <i className="bi bi-megaphone text-5xl mb-6 opacity-40"></i>
            <h3 className="text-xl font-bold mb-2 uppercase tracking-widest">Slogan Pelayanan</h3>
            <p className="text-2xl font-bold tracking-tight">
              "SOLO SMART CITY: <br /> MELAYANI DENGAN HATI"
            </p>
          </div>
        </div>

        {/* SECTION 5: PDF VIEWER (Keputusan No. 439) */}
        <div className="bg-white rounded-2xl p-4 border border-slate-100 shadow-xl overflow-hidden">
          <div className="p-6 border-b border-slate-50 flex justify-between items-center">
            <h4 className="font-bold text-slate-700 text-sm">Dokumen Keputusan No. 439 Tahun 2026</h4>
            <button className="bg-primary text-white px-4 py-2 rounded-xl text-[10px] font-bold">
              <i className="bi bi-download mr-2"></i> Unduh PDF
            </button>
          </div>
          {/* Ganti 'src' dengan path PDF asli kamu di folder public */}
          <iframe
            src="/dokumen/maklumat_439_2026.pdf"
            className="w-full h-[600px] rounded-b-[2rem]"
            title="Dokumen Maklumat"
          ></iframe>
        </div>

      </div>
    </div>
  );
}