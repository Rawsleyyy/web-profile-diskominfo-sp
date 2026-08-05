import { useEffect, useRef, useState } from "react";
import { useParams, Link } from "react-router-dom";
import { User, Calendar, ChevronLeft, Eye } from "lucide-react";
import { api, BASE_URL } from "../services/api";

const FALLBACK_IMG = "https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=800";

function getImageUrl(item) {
  if (!item?.thumbnail) return FALLBACK_IMG;
  if (item.thumbnail.startsWith("http")) return item.thumbnail;
  return `${BASE_URL}/storage/${item.thumbnail}`;
}

function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;
  return date.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

export default function ArtikelDetail() {
  const { id } = useParams();
  const [berita, setBerita] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Mencegah request terkirim dobel (mis. React StrictMode di mode development)
  // sehingga hitungan "views" di backend tidak ikut nambah 2x untuk 1x kunjungan.
  const fetchedIdRef = useRef(null);

  useEffect(() => {
    if (fetchedIdRef.current === id) {
      return; // sudah pernah fetch untuk id yang sama, jangan diulang
    }
    fetchedIdRef.current = id;

    const fetchBerita = async () => {
      try {
        setLoading(true);
        setError(null);
        const response = await api.get(`/berita/${id}`);
        const data = response.data.data || response.data;
        setBerita(data);
      } catch (err) {
        console.error("Gagal memuat detail berita:", err);
        setError("Berita tidak ditemukan atau sudah tidak dipublikasikan.");
      } finally {
        setLoading(false);
      }
    };

    fetchBerita();
  }, [id]);

  if (loading) {
    return (
      <div className="pt-44 pb-20 px-6 max-w-4xl mx-auto min-h-screen text-center">
        <div className="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-600 border-t-transparent mb-4"></div>
        <p className="text-slate-500 font-bold">Memuat berita...</p>
      </div>
    );
  }

  if (error || !berita) {
    return (
      <div className="pt-44 pb-20 px-6 max-w-4xl mx-auto min-h-screen text-center">
        <p className="text-red-500 font-bold mb-6">{error || "Berita tidak ditemukan."}</p>
        <Link to="/artikel" className="text-blue-600 font-bold hover:underline">Kembali ke daftar berita</Link>
      </div>
    );
  }

  return (
    <div className="pt-44 pb-20 px-6 max-w-4xl mx-auto min-h-screen">
      <Link to="/artikel" className="inline-flex items-center gap-2 text-slate-400 hover:text-blue-600 font-bold text-xs uppercase tracking-widest mb-8 transition">
        <ChevronLeft size={16} /> Kembali ke Daftar Berita
      </Link>

      <h1 className="text-3xl md:text-4xl font-black text-slate-800 mt-6 mb-6 leading-tight tracking-tight">
        {berita.judul}
      </h1>

      <div className="flex flex-wrap gap-6 items-center text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-8 pb-8 border-b border-slate-100">
        <span className="flex items-center gap-2">
          <User size={14} className="text-blue-600" /> {berita.author?.name || "Admin Diskominfo"}
        </span>
        <span className="flex items-center gap-2">
          <Calendar size={14} className="text-blue-600" /> {formatDate(berita.created_at)}
        </span>
        <span className="flex items-center gap-2">
          <Eye size={14} className="text-blue-600" /> {(berita.views ?? 0).toLocaleString("id-ID")} Dilihat
        </span>
      </div>

      <div className="rounded-[2.5rem] overflow-hidden mb-10 shadow-xl">
        <img src={getImageUrl(berita)} alt={berita.judul} className="w-full max-h-[420px] object-cover" />
      </div>

      <div className="prose prose-slate max-w-none text-slate-600 leading-relaxed whitespace-pre-line">
        {berita.konten}
      </div>
    </div>
  );
}
