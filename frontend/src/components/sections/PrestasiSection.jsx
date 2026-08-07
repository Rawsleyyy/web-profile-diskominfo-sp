import { useEffect, useState } from "react";
import { api } from "../../services/api";

export default function PrestasiSection() {
  const [prestasi, setPrestasi] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let active = true;

    api.get("/awards")
      .then(({ data }) => {
        if (!active) return;
        setPrestasi(Array.isArray(data) ? data : data?.data || []);
      })
      .catch((error) => console.error("Gagal mengambil data penghargaan:", error))
      .finally(() => active && setLoading(false));

    return () => {
      active = false;
    };
  }, []);

  return (
    <section className="py-20 bg-accent-50/50">
      <div className="max-w-7xl mx-auto px-6 text-center">
        <div className="inline-flex items-center gap-2 bg-white text-accent-700 px-4 py-1.5 rounded-full mb-4 font-bold text-[10px] uppercase border border-accent-100 shadow-sm">
          <span className="w-1.5 h-1.5 bg-accent-600 rounded-full animate-ping" /> Prestasi
        </div>
        <h2 className="text-4xl font-black text-slate-800 mb-16 tracking-tighter">Prestasi & Penghargaan</h2>

        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6" aria-busy={loading}>
          {loading && Array.from({ length: 4 }).map((_, index) => (
            <div key={index} className="h-64 rounded-[2.5rem] bg-white/70 animate-pulse" />
          ))}

          {!loading && prestasi.map((item) => (
            <div key={item.id} className="bg-white p-8 rounded-[2.5rem] border border-accent-100 shadow-sm hover:-translate-y-2 transition-all flex flex-col items-center group">
              <div className="w-16 h-16 rounded-2xl overflow-hidden flex items-center justify-center mb-6 shadow-lg border border-gray-100">
                {item.image ? (
                  <img
                    src={item.image}
                    alt={item.title || item.name || "Penghargaan"}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform"
                    loading="lazy"
                  />
                ) : (
                  <div className="w-full h-full bg-blue-600 text-white flex items-center justify-center text-2xl">
                    <i className="bi bi-trophy" />
                  </div>
                )}
              </div>

              <h3 className="font-bold text-slate-800 mb-1 leading-tight text-sm md:text-base">
                {item.title || item.name}
              </h3>
              <p className="text-[10px] font-bold text-slate-500 uppercase mb-4 tracking-tighter line-clamp-3">
                {item.description || "Penghargaan Diskominfo SP Kota Surakarta"}
              </p>
              <span className="bg-accent-50 text-primary text-[10px] font-black px-4 py-1 rounded-full border border-accent-100">
                {item.year}
              </span>
            </div>
          ))}
        </div>

        {!loading && prestasi.length === 0 && (
          <p className="text-sm font-medium text-slate-500">Belum ada data penghargaan yang ditampilkan.</p>
        )}
      </div>
    </section>
  );
}
