import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api, BASE_URL } from "../../services/api";

// Fallback gambar jika berita tidak punya thumbnail
const FALLBACK_IMG = "https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=400";

function getImageUrl(item) {
  if (!item.thumbnail) return FALLBACK_IMG;
  if (item.thumbnail.startsWith("http")) return item.thumbnail;
  return `${BASE_URL}/storage/${item.thumbnail}`;
}

function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;
  return date.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

export default function NewsSection() {
  const [news, setNews] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    let isMounted = true;

    const fetchNews = async () => {
      try {
        setLoading(true);
        // Ambil berita dari backend Laravel (hanya yang berstatus terbit)
        const response = await api.get("/berita");
        const data = response.data.data || response.data || [];
        if (isMounted) setNews(data.slice(0, 8)); // tampilkan maksimal 8 berita terbaru
      } catch (err) {
        console.error("Gagal memuat berita:", err);
        if (isMounted) setError("Gagal mengambil data berita dari server.");
      } finally {
        if (isMounted) setLoading(false);
      }
    };

    fetchNews();
    return () => { isMounted = false; };
  }, []);

  return (
    <section className="py-20">
      <div className="max-w-7xl mx-auto px-6">
        <div className="flex flex-wrap justify-between items-end gap-4 mb-10">
          <div>
            <span className="inline-flex items-center gap-1.5 text-primary font-bold text-xs uppercase tracking-widest bg-accent/10 dark:bg-white/[0.07] px-3 py-1 rounded-full border border-accent/20 dark:border-white/[0.13] dark:text-accent-300">
              <span className="w-1.5 h-1.5 rounded-full bg-accent" />
              Berita Terkini
            </span>
            <h2 className="text-2xl md:text-3xl font-extrabold text-primary-900 dark:text-white mt-4 tracking-tight">
              Berita & Informasi
            </h2>
          </div>
          <Link
            to="/artikel"
            className="text-primary dark:text-accent-300 text-xs font-bold uppercase tracking-widest hover:underline hidden md:inline-flex items-center gap-1"
          >
            Lihat Semua <i className="bi bi-arrow-right" />
          </Link>
        </div>

        {loading ? (
          <div className="py-16 text-center text-slate-400 dark:text-white/40 font-medium">
            Memuat berita...
          </div>
        ) : error ? (
          <div className="py-16 text-center text-red-500 dark:text-red-400 font-medium">
            {error}
          </div>
        ) : news.length === 0 ? (
          <div className="py-16 text-center text-slate-400 dark:text-white/40 font-medium italic">
            Belum ada berita yang dipublikasikan.
          </div>
        ) : (
          <div className="flex gap-6 overflow-x-auto pb-10 snap-x scrollbar-hide">
            {news.map((item) => (
              <Link
                to={`/artikel/${item.id}`}
                key={item.id}
                className="min-w-[300px] md:min-w-[320px] snap-start group rounded-2xl border border-primary/15 bg-white/80 backdrop-blur-xl shadow-[0_4px_20px_rgba(30,79,146,0.10)] hover:bg-white/95 hover:border-accent/40 hover:shadow-[0_8px_28px_rgba(41,168,224,0.18)] dark:border-white/[0.13] dark:bg-white/[0.07] dark:shadow-[0_4px_20px_rgba(0,0,0,0.30)] dark:hover:bg-white/[0.12] dark:hover:border-white/[0.24] transition-all duration-300 overflow-hidden flex flex-col"
              >
                <div className="relative h-44 overflow-hidden">
                  <img
                    src={getImageUrl(item)}
                    className="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                    alt={item.judul}
                  />
                </div>
                <div className="p-5 flex flex-col flex-1">
                  <h3 className="font-bold text-primary-900 dark:text-white leading-snug group-hover:text-accent dark:group-hover:text-accent-300 transition line-clamp-2">
                    {item.judul}
                  </h3>
                  <div className="flex items-center gap-4 mt-4 pt-4 border-t border-primary/10 dark:border-white/10 text-xs text-slate-500 dark:text-white/50 font-medium">
                    <span className="inline-flex items-center gap-1.5">
                      <i className="bi bi-calendar3" />
                      {formatDate(item.created_at)}
                    </span>
                    <span className="text-primary dark:text-accent-300 font-bold ml-auto inline-flex items-center gap-1">
                      Baca <i className="bi bi-chevron-right" />
                    </span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </section>
  );
}