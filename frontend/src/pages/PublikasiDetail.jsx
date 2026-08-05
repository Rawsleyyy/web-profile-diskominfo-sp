import { useEffect, useRef, useState } from "react";
import { useParams, Link } from "react-router-dom";
import { User, Calendar, ChevronLeft } from "lucide-react";
import { api } from "../services/api";

function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;
  return date.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

export default function PublikasiDetail() {
  const { id } = useParams();
  const [article, setArticle] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchedIdRef = useRef(null);

  useEffect(() => {
    if (fetchedIdRef.current === id) return;
    fetchedIdRef.current = id;

    const fetchArticle = async () => {
      try {
        setLoading(true);
        setError(null);
        const response = await api.get(`/articles/${id}`);
        const data = response.data.data || response.data;
        setArticle(data);
      } catch (err) {
        console.error("Gagal memuat detail publikasi:", err);
        setError("Artikel tidak ditemukan atau belum dipublikasikan.");
      } finally {
        setLoading(false);
      }
    };

    fetchArticle();
  }, [id]);

  if (loading) {
    return (
      <div className="pt-44 pb-20 px-6 max-w-3xl mx-auto min-h-screen text-center">
        <div className="inline-block animate-spin rounded-full h-10 w-10 border-4 border-primary border-t-transparent mb-4"></div>
        <p className="text-slate-500 font-bold">Memuat artikel...</p>
      </div>
    );
  }

  if (error || !article) {
    return (
      <div className="pt-44 pb-20 px-6 max-w-3xl mx-auto min-h-screen text-center">
        <p className="text-red-500 font-bold mb-6">{error || "Artikel tidak ditemukan."}</p>
        <Link to="/publikasi" className="text-primary font-bold hover:underline">Kembali ke daftar publikasi</Link>
      </div>
    );
  }

  return (
    <div className="pt-44 pb-20 px-6 max-w-3xl mx-auto min-h-screen">
      <Link to="/publikasi" className="inline-flex items-center gap-2 text-slate-400 hover:text-primary font-bold text-xs uppercase tracking-widest mb-8 transition">
        <ChevronLeft size={16} /> Kembali ke Daftar Publikasi
      </Link>

      <span className="bg-primary text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">
        {article.category}
      </span>

      <h1 className="text-3xl md:text-4xl font-black text-slate-800 mt-6 mb-6 leading-tight tracking-tight">
        {article.title}
      </h1>

      <div className="flex flex-wrap gap-6 items-center text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-10 pb-8 border-b border-slate-100">
        <span className="flex items-center gap-2">
          <User size={14} className="text-primary" /> {article.author}
        </span>
        <span className="flex items-center gap-2">
          <Calendar size={14} className="text-primary" /> {formatDate(article.published_at)}
        </span>
      </div>

      <div className="prose prose-slate max-w-none text-slate-600 leading-relaxed whitespace-pre-line">
        {article.content}
      </div>
    </div>
  );
}
