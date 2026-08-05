import { useEffect, useState } from "react";
import { ExternalLink } from "lucide-react";
import { api, storageUrl } from "../../services/api";

import iconLaporSP4N from "../../assets/layanan/lapor-sp4n.png";
import iconLaporGub from "../../assets/layanan/lapor-gub.png";
import iconUlas from "../../assets/layanan/ulas.png";
import iconLaporMasWali from "../../assets/layanan/lapor-mas-wali.png";
import iconKonata from "../../assets/layanan/konata.png";
import iconSoloData from "../../assets/layanan/solo-data.png";
import iconPpidPelaksana from "../../assets/layanan/ppid-pelaksana.png";
import iconFasilitasPublik from "../../assets/layanan/fasilitas-publik.png";

const fallbackServices = [
  { id: "fallback-1", nama_layanan: "LAPOR SP4N", deskripsi: "Aduan Nasional", icon_path: iconLaporSP4N, url_eksternal: "https://www.lapor.go.id/" },
  { id: "fallback-2", nama_layanan: "Lapor Gub", deskripsi: "Aduan Provinsi", icon_path: iconLaporGub, url_eksternal: "https://laporgub.jatengprov.go.id/" },
  { id: "fallback-3", nama_layanan: "ULAS", deskripsi: "Aduan Kota Solo", icon_path: iconUlas, url_eksternal: "https://ulas.surakarta.go.id/" },
  { id: "fallback-4", nama_layanan: "Lapor Mas Wali", deskripsi: "Aduan WhatsApp", icon_path: iconLaporMasWali, url_eksternal: "https://wa.me/6281225067171" },
  { id: "fallback-5", nama_layanan: "KONATA", deskripsi: "Layanan Disabilitas", icon_path: iconKonata, url_eksternal: "https://konata.surakarta.go.id/" },
  { id: "fallback-6", nama_layanan: "Solo Data", deskripsi: "Portal Data Terbuka", icon_path: iconSoloData, url_eksternal: "https://data.surakarta.go.id/" },
  { id: "fallback-7", nama_layanan: "PPID Pelaksana", deskripsi: "Informasi Publik", icon_path: iconPpidPelaksana, url_eksternal: "https://ppid.surakarta.go.id/" },
  { id: "fallback-8", nama_layanan: "Fasilitas Publik", deskripsi: "Akses Sarpras", icon_path: iconFasilitasPublik, url_eksternal: "https://surakarta.go.id" },
];

function resolveIcon(path) {
  if (!path) return iconFasilitasPublik;
  if (path.startsWith("data:") || path.startsWith("blob:")) return path;
  // Imported Vite assets resolve to /src/... in development or /assets/... in production.
  if (path.startsWith("/src/") || path.startsWith("/assets/") || path.includes("/assets/")) return path;
  if (/^https?:\/\//i.test(path)) return path;
  return storageUrl(path);
}

export default function LayananCepat() {
  const [services, setServices] = useState([]);
  const [loading, setLoading] = useState(true);
  const [usingFallback, setUsingFallback] = useState(false);

  useEffect(() => {
    let active = true;
    api.get("/layanan")
      .then(({ data }) => {
        if (!active) return;
        const items = Array.isArray(data) ? data : data?.data || [];
        if (items.length) {
          setServices(items.slice(0, 8));
        } else {
          setServices(fallbackServices);
          setUsingFallback(true);
        }
      })
      .catch((error) => {
        console.error("Gagal mengambil layanan:", error);
        if (active) {
          setServices(fallbackServices);
          setUsingFallback(true);
        }
      })
      .finally(() => active && setLoading(false));
    return () => { active = false; };
  }, []);

  return (
    <section id="layanan-cepat-section" className="py-24 font-sans scroll-mt-20">
      <div className="max-w-7xl mx-auto px-6">
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 bg-accent/10 text-primary dark:text-accent-300 px-4 py-1.5 rounded-full mb-4 font-bold text-xs uppercase border border-accent/20">
            <span className="w-1.5 h-1.5 bg-accent rounded-full animate-pulse" /> One Stop Digital Services
          </div>
          <h2 className="text-2xl md:text-3xl font-extrabold text-primary-900 dark:text-white tracking-tight uppercase">Layanan Cepat</h2>
          <p className="text-slate-500 dark:text-white/50 font-medium text-sm mt-3 max-w-lg mx-auto leading-relaxed">
            Akses kanal layanan publik Pemerintah Kota Surakarta secara terintegrasi.
          </p>
          {usingFallback && <p className="sr-only">Data cadangan ditampilkan karena layanan API belum tersedia.</p>}
        </div>

        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8" aria-busy={loading}>
          {loading
            ? Array.from({ length: 8 }).map((_, index) => <div key={index} className="h-56 rounded-2xl bg-white/70 dark:bg-white/10 animate-pulse" />)
            : services.map((item) => (
              <a key={item.id ?? item.nama_layanan} href={item.url_eksternal} target="_blank" rel="noopener noreferrer" className="group p-8 rounded-2xl border border-primary/15 bg-white/80 backdrop-blur-xl shadow-lg hover:border-accent/40 hover:-translate-y-1 dark:border-white/10 dark:bg-white/[0.07] transition-all duration-300 flex flex-col items-center text-center">
                <div className="w-16 h-16 rounded-2xl overflow-hidden flex items-center justify-center bg-white ring-2 ring-primary/10 mb-6 shadow-md group-hover:scale-105 transition-transform">
                  <img src={resolveIcon(item.icon_path)} alt={`Ikon ${item.nama_layanan}`} className="w-full h-full object-contain" loading="lazy" />
                </div>
                <h3 className="font-black text-primary-900 dark:text-white text-xs md:text-sm uppercase tracking-tight flex items-center gap-2">
                  {item.nama_layanan}<ExternalLink size={13} className="opacity-0 group-hover:opacity-100" />
                </h3>
                <p className="text-xs font-bold text-slate-400 dark:text-white/40 mt-2 uppercase tracking-wider line-clamp-2">
                  {item.deskripsi || item.kategori || "Layanan publik"}
                </p>
              </a>
            ))}
        </div>
      </div>
    </section>
  );
}
