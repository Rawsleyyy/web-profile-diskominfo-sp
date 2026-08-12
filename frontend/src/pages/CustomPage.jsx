import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { CalendarDays } from "lucide-react";
import { api } from "../services/api";
import SeoHead from "../components/seo/SeoHead";
import { useSiteConfig } from "../context/siteconfigcontext";

export default function CustomPage(){
  const {slug}=useParams(); const {settings}=useSiteConfig(); const [page,setPage]=useState(null); const [loading,setLoading]=useState(true); const [notFound,setNotFound]=useState(false);
  useEffect(()=>{setLoading(true);setNotFound(false);api.get(`/pages/${encodeURIComponent(slug)}`).then(r=>setPage(r.data?.data||null)).catch(e=>{if(e.response?.status===404)setNotFound(true);else console.error("Gagal mengambil halaman:",e);}).finally(()=>setLoading(false));},[slug]);
  if(loading)return <div className="min-h-[60vh] flex items-center justify-center font-semibold text-slate-500">Memuat halaman...</div>;
  if(notFound||!page)return <div className="min-h-[60vh] flex flex-col items-center justify-center px-6 text-center"><h1 className="text-3xl font-black">Halaman tidak ditemukan</h1><p className="mt-2 text-slate-500">Halaman belum dipublikasikan atau sudah dihapus.</p></div>;
  const seo={...(settings?.seo||{}),...(page.seo||{}),canonical_url:`${window.location.origin}/page/${page.slug}`};
  return <><SeoHead seo={seo} siteName={settings?.site_name}/><article className="px-5 pb-16 pt-36 md:pt-40"><div className="theme-card mx-auto max-w-5xl overflow-hidden border border-slate-200/80 dark:border-slate-700">{page.banner_url&&<img src={page.banner_url} alt={page.title} className="h-64 w-full object-cover md:h-96"/>}<div className="p-7 md:p-12"><div className="mb-5 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-accent-600"><CalendarDays size={14}/> Halaman Instansi</div><h1 className="text-3xl font-black leading-tight md:text-5xl">{page.title}</h1>{page.excerpt&&<p className="mt-5 text-lg leading-8" style={{color:"var(--site-text-secondary)"}}>{page.excerpt}</p>}<div className="mt-8 whitespace-pre-wrap border-t border-slate-100 pt-8 text-[16px] leading-8 dark:border-slate-800">{page.content}</div></div></div></article></>;
}
