import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { CalendarDays } from "lucide-react";
import { api } from "../services/api";

export default function CustomPage() {
  const { slug } = useParams();
  const [page, setPage] = useState(null);
  const [loading, setLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);

  useEffect(() => {
    setLoading(true);
    setNotFound(false);
    api.get(`/pages/${encodeURIComponent(slug)}`)
      .then((response) => setPage(response.data?.data || null))
      .catch((error) => {
        if (error.response?.status === 404) setNotFound(true);
        else console.error("Gagal mengambil halaman:", error);
      })
      .finally(() => setLoading(false));
  }, [slug]);

  if (loading) {
    return <div className="min-h-[60vh] flex items-center justify-center text-slate-500 font-semibold">Memuat halaman...</div>;
  }

  if (notFound || !page) {
    return <div className="min-h-[60vh] flex flex-col items-center justify-center px-6 text-center"><h1 className="text-3xl font-black text-slate-900 dark:text-white">Halaman tidak ditemukan</h1><p className="mt-2 text-slate-500">Halaman belum dipublikasikan atau sudah dihapus.</p></div>;
  }

  return (
    <article className="pt-36 md:pt-40 pb-16 px-5">
      <div className="mx-auto max-w-5xl overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
        {page.banner_url && <img src={page.banner_url} alt={page.title} className="h-64 md:h-96 w-full object-cover" />}
        <div className="p-7 md:p-12">
          <div className="mb-5 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-accent-600">
            <CalendarDays size={14} /> Halaman Instansi
          </div>
          <h1 className="text-3xl md:text-5xl font-black leading-tight text-slate-900 dark:text-white">{page.title}</h1>
          {page.excerpt && <p className="mt-5 text-lg leading-8 text-slate-500 dark:text-slate-300">{page.excerpt}</p>}
          <div className="mt-8 border-t border-slate-100 pt-8 text-[16px] leading-8 text-slate-700 dark:border-slate-800 dark:text-slate-200 whitespace-pre-wrap">{page.content}</div>
        </div>
      </div>
    </article>
  );
}
