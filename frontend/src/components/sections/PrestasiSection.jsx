import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api } from "../../services/api";

export default function PrestasiSection() {
  const [prestasi, setPrestasi] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    let active = true;

    api.get("/awards")
      .then(({ data }) => {
        if (!active) return;

        const items = Array.isArray(data) ? data : data?.data || [];

        // Homepage hanya menampilkan 4 penghargaan terbaru.
        // Data ke-5 dan seterusnya tetap tersedia di halaman /penghargaan.
        setPrestasi(items.slice(0, 4));
      })
      .catch((err) => {
        console.error("Gagal mengambil data penghargaan:", err);
        if (active) setError("Gagal mengambil data penghargaan dari server.");
      })
      .finally(() => active && setLoading(false));

    return () => {
      active = false;
    };
  }, []);

  return (
    <section className="theme-section-alt py-20">
      <div className="mx-auto max-w-7xl px-6">
        <div className="mb-10 flex flex-wrap items-end justify-between gap-4">
          <div>
            <span className="inline-flex items-center gap-1.5 rounded-full border border-accent/20 bg-accent/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-primary dark:border-white/[0.13] dark:bg-white/[0.07] dark:text-accent-300">
              <span className="h-1.5 w-1.5 rounded-full bg-accent" />
              Prestasi Terkini
            </span>

            <h2 className="mt-4 text-2xl font-extrabold tracking-tight text-primary-900 dark:text-white md:text-3xl">
              Prestasi & Penghargaan
            </h2>
          </div>

          <Link
            to="/penghargaan"
            className="hidden items-center gap-1 text-xs font-bold uppercase tracking-widest text-primary hover:underline dark:text-accent-300 md:inline-flex"
          >
            Lihat Semua <i className="bi bi-arrow-right" />
          </Link>
        </div>

        {loading ? (
          <div className="py-16 text-center font-medium text-slate-400 dark:text-white/40">
            Memuat penghargaan...
          </div>
        ) : error ? (
          <div className="py-16 text-center font-medium text-red-500 dark:text-red-400">
            {error}
          </div>
        ) : prestasi.length === 0 ? (
          <div className="py-16 text-center font-medium italic text-slate-400 dark:text-white/40">
            Belum ada penghargaan yang dipublikasikan.
          </div>
        ) : (
          <>
            <div className="flex snap-x gap-6 overflow-x-auto pb-10 scrollbar-hide">
              {prestasi.map((item) => (
                <article
                  key={item.id}
                  className="group flex min-w-[280px] snap-start flex-col overflow-hidden rounded-2xl border border-primary/15 bg-white/80 shadow-[0_4px_20px_rgba(30,79,146,0.10)] backdrop-blur-xl transition-all duration-300 hover:border-accent/40 hover:bg-white/95 hover:shadow-[0_8px_28px_rgba(41,168,224,0.18)] dark:border-white/[0.13] dark:bg-white/[0.07] dark:shadow-[0_4px_20px_rgba(0,0,0,0.30)] dark:hover:border-white/[0.24] dark:hover:bg-white/[0.12] md:min-w-[300px] lg:min-w-0 lg:flex-1"
                >
                  <div className="relative h-44 overflow-hidden bg-slate-100 dark:bg-white/[0.05]">
                    {item.image ? (
                      <img
                        src={item.image}
                        alt={item.title || item.name || "Penghargaan"}
                        className="h-full w-full object-cover transition duration-700 group-hover:scale-110"
                        loading="lazy"
                      />
                    ) : (
                      <div className="theme-action-gradient flex h-full w-full items-center justify-center text-5xl text-white">
                        <i className="bi bi-trophy-fill" />
                      </div>
                    )}

                    <div className="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1 text-[10px] font-black text-primary-900 shadow-sm">
                      {item.year || "—"}
                    </div>
                  </div>

                  <div className="flex flex-1 flex-col p-5">
                    <h3 className="line-clamp-2 font-bold leading-snug text-primary-900 transition group-hover:text-accent dark:text-white dark:group-hover:text-accent-300">
                      {item.title || item.name || "Penghargaan"}
                    </h3>

                    <p className="mt-2 line-clamp-2 text-xs leading-5 text-slate-500 dark:text-white/50">
                      {item.description || "Penghargaan dan capaian resmi instansi."}
                    </p>

                    <div className="mt-auto flex items-center gap-4 border-t border-primary/10 pt-4 text-xs font-medium text-slate-500 dark:border-white/10 dark:text-white/50">
                      <span className="inline-flex items-center gap-1.5">
                        <i className="bi bi-trophy" />
                        Tahun {item.year || "—"}
                      </span>

                      <span className="ml-auto inline-flex items-center gap-1 font-bold text-primary dark:text-accent-300">
                        Prestasi <i className="bi bi-award" />
                      </span>
                    </div>
                  </div>
                </article>
              ))}
            </div>

            <div className="text-center md:hidden">
              <Link
                to="/penghargaan"
                className="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-xs font-black uppercase tracking-wider text-white shadow-sm"
              >
                Lihat Semua Penghargaan
                <i className="bi bi-arrow-right" />
              </Link>
            </div>
          </>
        )}
      </div>
    </section>
  );
}
