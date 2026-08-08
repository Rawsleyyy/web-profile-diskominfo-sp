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
    <section className="theme-section-alt py-20">
      <div className="mx-auto max-w-7xl px-6 text-center">
        <div className="theme-chip mb-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[10px] font-bold uppercase shadow-sm">
          <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-current" />
          Prestasi
        </div>

        <h2 className="theme-text-main mb-14 text-4xl font-black tracking-tighter">
          Prestasi & Penghargaan
        </h2>

        <div className="grid grid-cols-2 gap-6 lg:grid-cols-4" aria-busy={loading}>
          {loading && Array.from({ length: 4 }).map((_, index) => (
            <div
              key={index}
              className="theme-card h-64 animate-pulse rounded-[2.5rem]"
            />
          ))}

          {!loading && prestasi.map((item) => (
            <article
              key={item.id}
              className="theme-card group flex flex-col items-center rounded-[2.5rem] p-8 transition-all hover:-translate-y-2"
            >
              <div className="keep-light-surface mb-6 flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl border shadow-lg" style={{ borderColor: "var(--line-default)" }}>
                {item.image ? (
                  <img
                    src={item.image}
                    alt={item.title || item.name || "Penghargaan"}
                    className="h-full w-full object-cover transition-transform group-hover:scale-110"
                    loading="lazy"
                  />
                ) : (
                  <div className="theme-action-gradient flex h-full w-full items-center justify-center text-2xl text-white">
                    <i className="bi bi-trophy" />
                  </div>
                )}
              </div>

              <h3 className="theme-text-main mb-1 text-sm font-bold leading-tight md:text-base">
                {item.title || item.name}
              </h3>
              <p className="theme-text-secondary mb-4 line-clamp-3 text-[10px] font-bold uppercase tracking-tighter">
                {item.description || "Penghargaan Diskominfo SP Kota Surakarta"}
              </p>
              <span className="theme-chip rounded-full px-4 py-1 text-[10px] font-black">
                {item.year}
              </span>
            </article>
          ))}
        </div>

        {!loading && prestasi.length === 0 && (
          <p className="theme-text-muted text-sm font-medium">
            Belum ada data penghargaan yang ditampilkan.
          </p>
        )}
      </div>
    </section>
  );
}
