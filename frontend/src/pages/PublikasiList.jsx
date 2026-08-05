import { useEffect, useState, useMemo } from "react";
import { Link } from "react-router-dom";
import { Search, User, Calendar, ChevronRight, FileText } from "lucide-react";
import { api } from "../services/api";

function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;
  return date.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

export default function PublikasiList() {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [searchTerm, setSearchTerm] = useState("");

  useEffect(() => {
    const fetchArticles = async () => {
      try {
        setLoading(true);
        const response = await api.get("/articles");
        const data = response.data.data || response.data || [];
        setArticles(data);
      } catch (err) {
        console.error("Gagal memuat publikasi:", err);
        setError("Gagal mengambil data publikasi dari server.");
      } finally {
        setLoading(false);
      }
    };
    fetchArticles();
  }, []);

  const filtered = useMemo(() => {
    return articles.filter((a) =>
      (a.title || "").toLowerCase().includes(searchTerm.toLowerCase())
    );
  }, [articles, searchTerm]);

  return (
    <div className="pt-44 pb-20 px-6 max-w-5xl mx-auto min-h-screen">
      <div className="mb-12 text-center">
        <span className="text-accent-700 font-bold text-[10px] uppercase tracking-widest bg-accent-50 px-3 py-1 rounded-full border border-accent-100 italic">
          ● Publikasi Resmi
        </span>
        <h1 className="text-4xl font-black text-slate-800 mt-4 tracking-tighter">Artikel & Publikasi</h1>
        <p className="text-slate-500 mt-3 max-w-xl mx-auto">
          Kumpulan artikel dan publikasi resmi dari Dinas Komunikasi, Informatika, Statistik, dan Persandian Surakarta.
        </p>
      </div>

      <div className="relative max-w-md mx-auto mb-12">
        <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
        <input
          type="text"
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          placeholder="Cari judul artikel..."
          className="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-200 bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition"
        />
      </div>

      {loading ? (
        <div className="py-16 text-center text-slate-400 font-medium">Memuat publikasi...</div>
      ) : error ? (
        <div className="py-16 text-center text-red-500 font-medium">{error}</div>
      ) : filtered.length === 0 ? (
        <div className="py-16 text-center text-slate-400 font-medium italic">Belum ada artikel yang dipublikasikan.</div>
      ) : (
        <div className="space-y-4">
          {filtered.map((item) => (
            <Link
              to={`/publikasi/${item.id}`}
              key={item.id}
              className="flex items-start gap-5 bg-white rounded-3xl p-6 border border-slate-100 hover:border-primary/30 hover:shadow-lg transition group"
            >
              <div className="w-12 h-12 rounded-2xl bg-primary-50 flex items-center justify-center flex-shrink-0 text-primary-700">
                <FileText size={20} />
              </div>
              <div className="flex-1 min-w-0">
                <span className="inline-block bg-primary/10 text-primary text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest mb-2">
                  {item.category}
                </span>
                <h3 className="font-bold text-slate-800 text-lg leading-snug group-hover:text-primary transition">
                  {item.title}
                </h3>
                <div className="flex flex-wrap items-center gap-4 mt-3 text-[11px] text-slate-400 font-medium">
                  <span className="flex items-center gap-1.5"><User size={13} /> {item.author}</span>
                  <span className="flex items-center gap-1.5"><Calendar size={13} /> {formatDate(item.published_at)}</span>
                </div>
              </div>
              <ChevronRight className="text-slate-300 group-hover:text-primary group-hover:translate-x-1 transition flex-shrink-0 mt-4" size={20} />
            </Link>
          ))}
        </div>
      )}
    </div>
  );
}