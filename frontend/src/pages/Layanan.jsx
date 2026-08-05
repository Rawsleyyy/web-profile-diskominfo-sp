import React from 'react';

export default function Layanan() {
  const daftarLayanan = [
    { title: "Informasi Publik", desc: "Permohonan informasi sesuai UU KIP.", icon: "📄" },
    { title: "Pengaduan Masyarakat", desc: "Sampaikan keluhan Anda melalui kanal resmi.", icon: "📢" },
    { title: "Sertifikasi Elektronik", desc: "Layanan tanda tangan digital bagi ASN.", icon: "🔑" },
  ];

  return (
    <div className="pt-32 pb-20 bg-[#f4f9ff] min-h-screen">
      <div className="max-w-7xl mx-auto px-6">
        <div className="text-center mb-16">
          <h1 className="text-4xl font-black text-slate-800 uppercase tracking-tight">Layanan Kami</h1>
          <p className="text-slate-500 mt-4 max-w-2xl mx-auto">
            Diskominfo SP menyediakan berbagai layanan digital untuk mendukung transformasi Solo Smart City.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {daftarLayanan.map((l, i) => (
            <div key={i} className="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all">
              <div className="text-4xl mb-6">{l.icon}</div>
              <h3 className="text-xl font-bold text-slate-800 mb-2">{l.title}</h3>
              <p className="text-slate-500 text-sm leading-relaxed">{l.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}