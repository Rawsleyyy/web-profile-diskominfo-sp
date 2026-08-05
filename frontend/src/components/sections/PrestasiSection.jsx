import { useEffect, useState } from "react";

export default function PrestasiSection() {
  const [prestasi, setPrestasi] = useState([]);

  useEffect(() => {
    // Ambil data dari API Laravel backend
    fetch("http://127.0.0.1:8000/api/awards") // Sesuaikan URL API Laravel kamu
      .then((res) => res.json())
      .then((data) => setPrestasi(data))
      .catch((err) => console.error("Gagal mengambil data:", err));
  }, []);

  return (
    <section className="py-20 bg-accent-50/50">
      <div className="max-w-7xl mx-auto px-6 text-center">
        <div className="inline-flex items-center gap-2 bg-white text-accent-700 px-4 py-1.5 rounded-full mb-4 font-bold text-[10px] uppercase border border-accent-100 shadow-sm">
          <span className="w-1.5 h-1.5 bg-accent-600 rounded-full animate-ping"></span> Prestasi
        </div>
        <h2 className="text-4xl font-black text-slate-800 mb-16 tracking-tighter">Prestasi & Penghargaan</h2>
        
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
          {prestasi.map((p, i) => (
            <div key={i} className="bg-white p-8 rounded-[2.5rem] border border-accent-100 shadow-sm hover:-translate-y-2 transition-all flex flex-col items-center group">
              
              {/* Tempat Gambar / Foto Penghargaan */}
              <div className="w-16 h-16 rounded-2xl overflow-hidden flex items-center justify-center mb-6 shadow-lg border border-gray-100">
                {p.image ? (
                  <img 
                    src={p.image} 
                    alt={p.title || p.name} 
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform"
                  />
                ) : (
                  <div className="w-full h-full bg-blue-600 text-white flex items-center justify-center text-2xl">
                    <i className="bi bi-trophy"></i>
                  </div>
                )}
              </div>

              {/* Detail Data dari Database */}
              <h3 className="font-bold text-slate-800 mb-1 leading-tight text-sm md:text-base">
                {p.title || p.name}
              </h3>
              <p className="text-[10px] font-bold text-slate-500 uppercase mb-4 tracking-tighter">
                {p.description || p.agency}
              </p>
              <span className="bg-accent-50 text-primary text-[10px] font-black px-4 py-1 rounded-full border border-accent-100">
                {p.year}
              </span>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}