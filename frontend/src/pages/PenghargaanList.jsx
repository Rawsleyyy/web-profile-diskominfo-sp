import { useEffect, useMemo, useState } from "react";
import { Search, Trophy, CalendarDays, ChevronLeft, ChevronRight } from "lucide-react";
import { api } from "../services/api";

export default function PenghargaanList() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [searchTerm, setSearchTerm] = useState("");
  const [filterYear, setFilterYear] = useState("Semua Tahun");
  const [itemsPerPage, setItemsPerPage] = useState(8);
  const [currentPage, setCurrentPage] = useState(1);

  useEffect(() => {
    let active = true;

    api.get("/awards")
      .then(({ data }) => {
        if (!active) return;
        setItems(Array.isArray(data) ? data : data?.data || []);
      })
      .catch((err) => {
        console.error("Gagal memuat penghargaan:", err);
        if (active) setError("Gagal mengambil data penghargaan dari server.");
      })
      .finally(() => active && setLoading(false));

    return () => {
      active = false;
    };
  }, []);

  useEffect(() => {
    setCurrentPage(1);
  }, [searchTerm, filterYear, itemsPerPage]);

  const availableYears = useMemo(() => {
    return [...new Set(items.map((item) => String(item.year || "")).filter(Boolean))]
      .sort((a, b) => Number(b) - Number(a));
  }, [items]);

  const filteredItems = useMemo(() => {
    const keyword = searchTerm.toLowerCase().trim();

    return items.filter((item) => {
      const title = String(item.title || item.name || "").toLowerCase();
      const description = String(item.description || "").toLowerCase();
      const year = String(item.year || "");

      const matchesSearch =
        !keyword ||
        title.includes(keyword) ||
        description.includes(keyword);

      const matchesYear =
        filterYear === "Semua Tahun" || year === filterYear;

      return matchesSearch && matchesYear;
    });
  }, [items, searchTerm, filterYear]);

  const totalPages = Math.max(1, Math.ceil(filteredItems.length / itemsPerPage));
  const startIndex = (currentPage - 1) * itemsPerPage;
  const currentItems = filteredItems.slice(startIndex, startIndex + itemsPerPage);

  const goToPage = (page) => {
    const safePage = Math.min(Math.max(page, 1), totalPages);
    setCurrentPage(safePage);
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  return (
    <div className="mx-auto min-h-screen max-w-7xl px-6 pb-20 pt-40 font-sans">
      <div className="mb-9">
        <span className="inline-flex items-center gap-2 rounded-full border border-accent/20 bg-accent/10 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-primary dark:border-white/[0.13] dark:bg-white/[0.07] dark:text-accent-300">
          <Trophy size={12} />
          Capaian Instansi
        </span>

        <h1 className="mt-4 text-3xl font-black tracking-tight text-primary-900 dark:text-white md:text-4xl">
          Prestasi & Penghargaan
        </h1>

        <p className="mt-3 max-w-2xl text-sm leading-7 text-slate-500 dark:text-white/50">
          Daftar penghargaan dan capaian yang telah diraih oleh instansi.
        </p>
      </div>

      <div className="mb-10 flex flex-wrap items-center gap-4 rounded-[2rem] border border-slate-100 bg-white p-5 shadow-lg shadow-slate-200/50 dark:border-white/10 dark:bg-white/[0.06] dark:shadow-none">
        <div className="group relative min-w-[260px] flex-1">
          <Search
            size={18}
            className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 transition group-focus-within:text-primary"
          />
          <input
            type="text"
            value={searchTerm}
            onChange={(event) => setSearchTerm(event.target.value)}
            placeholder="Cari penghargaan..."
            className="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-primary/40 focus:ring-2 focus:ring-primary/10 dark:border-white/10 dark:bg-white/[0.05] dark:text-white"
          />
        </div>

        <select
          value={filterYear}
          onChange={(event) => setFilterYear(event.target.value)}
          className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-black uppercase text-slate-600 outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 dark:border-white/10 dark:bg-white/[0.05] dark:text-white"
        >
          <option>Semua Tahun</option>
          {availableYears.map((year) => (
            <option key={year}>{year}</option>
          ))}
        </select>

        <select
          value={itemsPerPage}
          onChange={(event) => setItemsPerPage(Number(event.target.value))}
          className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-xs font-black uppercase text-slate-600 outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 dark:border-white/10 dark:bg-white/[0.05] dark:text-white"
        >
          <option value={8}>8 / Halaman</option>
          <option value={12}>12 / Halaman</option>
          <option value={20}>20 / Halaman</option>
        </select>
      </div>

      {loading ? (
        <div className="py-20 text-center font-bold text-slate-400">
          Memuat data penghargaan...
        </div>
      ) : error ? (
        <div className="rounded-3xl border border-red-100 bg-red-50 py-20 text-center font-bold text-red-500">
          {error}
        </div>
      ) : currentItems.length === 0 ? (
        <div className="rounded-3xl border border-dashed border-slate-200 bg-white py-20 text-center dark:border-white/10 dark:bg-white/[0.04]">
          <Trophy className="mx-auto mb-4 text-slate-300" size={38} />
          <p className="font-bold text-slate-400">
            Tidak ada penghargaan yang sesuai.
          </p>
        </div>
      ) : (
        <>
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {currentItems.map((item) => (
              <article
                key={item.id}
                className="group flex min-h-[330px] flex-col overflow-hidden rounded-2xl border border-primary/15 bg-white/80 shadow-[0_4px_20px_rgba(30,79,146,0.10)] backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:border-accent/40 hover:shadow-[0_8px_28px_rgba(41,168,224,0.18)] dark:border-white/[0.13] dark:bg-white/[0.07]"
              >
                <div className="relative h-48 overflow-hidden bg-slate-100 dark:bg-white/[0.05]">
                  {item.image ? (
                    <img
                      src={item.image}
                      alt={item.title || item.name || "Penghargaan"}
                      className="h-full w-full object-cover transition duration-700 group-hover:scale-110"
                      loading="lazy"
                    />
                  ) : (
                    <div className="theme-action-gradient flex h-full w-full items-center justify-center text-5xl text-white">
                      <Trophy size={44} />
                    </div>
                  )}

                  <span className="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1 text-[10px] font-black text-primary-900 shadow-sm">
                    {item.year || "—"}
                  </span>
                </div>

                <div className="flex flex-1 flex-col p-5">
                  <h2 className="line-clamp-2 text-base font-black leading-snug text-primary-900 dark:text-white">
                    {item.title || item.name || "Penghargaan"}
                  </h2>

                  <p className="mt-2 line-clamp-3 text-xs leading-6 text-slate-500 dark:text-white/50">
                    {item.description || "Penghargaan dan capaian resmi instansi."}
                  </p>

                  <div className="mt-auto flex items-center justify-between border-t border-primary/10 pt-4 text-xs dark:border-white/10">
                    <span className="inline-flex items-center gap-1.5 font-semibold text-slate-500 dark:text-white/50">
                      <CalendarDays size={13} />
                      Tahun {item.year || "—"}
                    </span>

                    <span className="font-black uppercase tracking-wider text-primary dark:text-accent-300">
                      Award
                    </span>
                  </div>
                </div>
              </article>
            ))}
          </div>

          <div className="mt-12 flex flex-col items-center justify-between gap-5 border-t border-slate-100 pt-8 dark:border-white/10 md:flex-row">
            <p className="text-[11px] font-black uppercase tracking-widest text-slate-400">
              Menampilkan {startIndex + 1}–{Math.min(startIndex + itemsPerPage, filteredItems.length)} dari {filteredItems.length} penghargaan
            </p>

            {totalPages > 1 && (
              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={() => goToPage(currentPage - 1)}
                  disabled={currentPage === 1}
                  className="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-primary hover:text-white disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/10 dark:bg-white/[0.05]"
                  aria-label="Halaman sebelumnya"
                >
                  <ChevronLeft size={16} />
                </button>

                {Array.from({ length: totalPages }, (_, index) => index + 1).map((page) => (
                  <button
                    type="button"
                    key={page}
                    onClick={() => goToPage(page)}
                    className={`flex h-10 min-w-10 items-center justify-center rounded-xl px-3 text-xs font-black transition ${
                      currentPage === page
                        ? "bg-primary text-white shadow-lg shadow-primary/20"
                        : "border border-slate-200 bg-white text-slate-500 hover:bg-primary hover:text-white dark:border-white/10 dark:bg-white/[0.05]"
                    }`}
                  >
                    {page}
                  </button>
                ))}

                <button
                  type="button"
                  onClick={() => goToPage(currentPage + 1)}
                  disabled={currentPage === totalPages}
                  className="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-primary hover:text-white disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/10 dark:bg-white/[0.05]"
                  aria-label="Halaman berikutnya"
                >
                  <ChevronRight size={16} />
                </button>
              </div>
            )}
          </div>
        </>
      )}
    </div>
  );
}
