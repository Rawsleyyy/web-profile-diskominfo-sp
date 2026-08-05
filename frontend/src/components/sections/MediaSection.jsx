import { useEffect, useState } from "react";
import { api, BASE_URL } from "../../services/api";

const TABS = ["Rilis Data", "LKJIP", "Statistik"];

function getFileUrl(item) {
  if (!item.file_path) return "#";
  if (item.file_path.startsWith("http")) return item.file_path;
  return `${BASE_URL}/storage/${item.file_path}`;
}

function getThumbUrl(item) {
  if (!item.thumbnail) return null;
  if (item.thumbnail.startsWith("http")) return item.thumbnail;
  return `${BASE_URL}/storage/${item.thumbnail}`;
}

// --- Helper baru khusus podcast ---

// Sama seperti getFileUrl, tapi untuk url_audio: kalau relatif, tambahkan BASE_URL.
// Ini yang tadinya hilang -> penyebab blank page waktu play file upload.
function resolveAudioUrl(urlAudio) {
  if (!urlAudio) return null;
  if (urlAudio.startsWith("http")) return urlAudio;
  return `${BASE_URL}/storage/${urlAudio}`;
}

function getYoutubeEmbedUrl(url) {
  // Menangani format: youtu.be/ID, youtube.com/watch?v=ID, youtube.com/embed/ID
  const patterns = [
    /youtu\.be\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/,
    /youtube\.com\/embed\/([a-zA-Z0-9_-]+)/,
  ];
  for (const pattern of patterns) {
    const match = url.match(pattern);
    if (match) return `https://www.youtube.com/embed/${match[1]}?autoplay=1`;
  }
  return null;
}

function getSpotifyEmbedUrl(url) {
  // open.spotify.com/episode/XXX -> open.spotify.com/embed/episode/XXX
  if (url.includes("/embed/")) return url;
  return url.replace("open.spotify.com/", "open.spotify.com/embed/");
}

// Mendeteksi tipe sumber murni dari isi URL-nya (tidak bergantung field tipe_sumber dari API)
function detectSourceType(urlAudio) {
  if (!urlAudio) return null;
  if (urlAudio.includes("youtube.com") || urlAudio.includes("youtu.be")) return "youtube";
  if (urlAudio.includes("spotify.com")) return "spotify";
  return "file";
}

// --- Modal Player ---

function PodcastPlayerModal({ podcast, onClose }) {
  if (!podcast) return null;

  const sourceType = detectSourceType(podcast.url_audio);

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
      onClick={onClose}
    >
      <div
        className="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 relative"
        onClick={(e) => e.stopPropagation()}
      >
        <button
          type="button"
          onClick={onClose}
          className="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 text-sm"
        >
          ✕
        </button>

        <h4 className="text-[10px] font-black uppercase tracking-widest text-primary mb-1">
          {podcast.episode ? `Episode ${podcast.episode}` : "Podcast"}
        </h4>
        <p className="font-bold text-lg text-slate-800 mb-6 pr-8">{podcast.judul}</p>

        {sourceType === "youtube" && getYoutubeEmbedUrl(podcast.url_audio) && (
          <div className="aspect-video rounded-2xl overflow-hidden bg-black">
            <iframe
              src={getYoutubeEmbedUrl(podcast.url_audio)}
              title={podcast.judul}
              className="w-full h-full"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowFullScreen
            />
          </div>
        )}

        {sourceType === "spotify" && (
          <iframe
            src={getSpotifyEmbedUrl(podcast.url_audio)}
            title={podcast.judul}
            className="w-full rounded-2xl"
            height="152"
            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
          />
        )}

        {sourceType === "file" && (
          <audio controls autoPlay className="w-full">
            <source src={resolveAudioUrl(podcast.url_audio)} />
            Browser Anda tidak mendukung pemutar audio.
          </audio>
        )}

        {podcast.deskripsi && (
          <p className="text-xs text-slate-500 mt-4 leading-relaxed">{podcast.deskripsi}</p>
        )}
      </div>
    </div>
  );
}

export default function MediaSection() {
  const [activeTab, setActiveTab] = useState("Rilis Data");
  const [dokumen, setDokumen] = useState([]);
  const [loadingDokumen, setLoadingDokumen] = useState(true);

  const [podcasts, setPodcasts] = useState([]);
  const [loadingPodcast, setLoadingPodcast] = useState(true);

  // Podcast yang lagi diputar di modal (null = modal tertutup)
  const [activePlayer, setActivePlayer] = useState(null);

  useEffect(() => {
    let isMounted = true;
    api.get("/dokumen-publik")
      .then((res) => {
        const data = res.data.data || res.data || [];
        if (isMounted) setDokumen(data);
      })
      .catch((err) => console.error("Gagal memuat dokumen publik:", err))
      .finally(() => { if (isMounted) setLoadingDokumen(false); });
    return () => { isMounted = false; };
  }, []);

  useEffect(() => {
    let isMounted = true;
    api.get("/podcast")
      .then((res) => {
        const data = res.data.data || res.data || [];
        if (isMounted) setPodcasts(data);
      })
      .catch((err) => console.error("Gagal memuat podcast:", err))
      .finally(() => { if (isMounted) setLoadingPodcast(false); });
    return () => { isMounted = false; };
  }, []);

  const filteredDokumen = dokumen.filter((d) => d.kategori === activeTab);
  const [featured, ...restPodcasts] = podcasts;

  return (
    <section className="py-20 bg-slate-50">
      <div className="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-3 gap-12">

        {/* Kolom Dokumen (2/3) */}
        <div className="lg:col-span-2">
          <h2 className="text-3xl font-black text-slate-800 mb-8 tracking-tighter">Dokumen & Data Publik</h2>
          <div className="bg-white rounded-[2.5rem] p-4 border border-slate-200 shadow-sm">
            <div className="flex gap-4 p-2 mb-4 border-b border-slate-100">
              {TABS.map((tab) => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab)}
                  className={`text-xs font-bold pb-2 px-2 transition ${
                    activeTab === tab ? "text-primary border-b-2 border-primary" : "text-slate-400 hover:text-primary-700"
                  }`}
                >
                  {tab}
                </button>
              ))}
            </div>

            <div className="space-y-2 min-h-[120px]">
              {loadingDokumen ? (
                <p className="text-center text-slate-400 text-xs font-medium py-8">Memuat dokumen...</p>
              ) : filteredDokumen.length === 0 ? (
                <p className="text-center text-slate-300 text-xs font-medium italic py-8">Belum ada dokumen untuk kategori ini.</p>
              ) : (
                filteredDokumen.map((item) => (
                  <div key={item.id} className="flex items-center justify-between p-4 hover:bg-slate-50 rounded-2xl transition group">
                    <div className="flex gap-4 items-center min-w-0">
                      <div className="w-10 h-10 bg-accent-100 text-primary-700 rounded-xl flex items-center justify-center flex-shrink-0"><i className="bi bi-file-earmark-pdf"></i></div>
                      <div className="min-w-0">
                        <h4 className="text-[11px] font-bold text-slate-700 truncate">{item.judul}</h4>
                        <p className="text-[9px] text-slate-400 uppercase font-medium">{item.ukuran_formatted} — {item.format}</p>
                      </div>
                    </div>
                    <a
                      href={getFileUrl(item)}
                      target="_blank"
                      rel="noopener noreferrer"
                      download
                      className="w-8 h-8 rounded-full flex items-center justify-center text-primary-700 bg-accent-50 opacity-0 group-hover:opacity-100 transition flex-shrink-0"
                    >
                      <i className="bi bi-download"></i>
                    </a>
                  </div>
                ))
              )}
            </div>
          </div>
        </div>

        {/* Kolom Media/Podcast (1/3) */}
        <div>
          <h2 className="text-3xl font-black text-slate-800 mb-8 tracking-tighter">KOMINPOD</h2>
          <div className="bg-primary rounded-[2.5rem] p-8 text-white shadow-xl shadow-primary/20 relative overflow-hidden min-h-[280px]">
            <i className="bi bi-mic-fill absolute -right-4 -bottom-4 text-9xl opacity-10 rotate-12"></i>

            {loadingPodcast ? (
              <p className="text-xs font-medium opacity-70">Memuat podcast...</p>
            ) : !featured ? (
              <p className="text-xs font-medium opacity-70 italic">Belum ada episode podcast.</p>
            ) : (
              <>
                <h4 className="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2">Podcast Terbaru</h4>
                <p className="font-bold text-lg leading-tight mb-8">
                  {featured.episode ? `Ep. ${featured.episode} — ` : ""}{featured.judul}
                </p>

                {restPodcasts.length > 0 && (
                  <div className="space-y-4">
                    {restPodcasts.slice(0, 2).map((p) => (
                      <button
                        key={p.id}
                        type="button"
                        onClick={() => setActivePlayer(p)}
                        className="w-full flex items-center gap-4 bg-white/10 p-3 rounded-2xl border border-white/10 backdrop-blur-sm hover:bg-white/20 transition text-left"
                      >
                        <div className="w-8 h-8 rounded-full bg-white text-primary flex items-center justify-center text-xs flex-shrink-0"><i className="bi bi-play-fill"></i></div>
                        <span className="text-[10px] font-bold opacity-80 truncate">
                          {p.episode ? `Edisi ${p.episode} — ` : ""}{p.judul}
                        </span>
                      </button>
                    ))}
                  </div>
                )}

                <button
                  type="button"
                  onClick={() => setActivePlayer(featured)}
                  className="block text-center w-full mt-8 py-3 bg-white text-primary rounded-xl font-bold text-xs hover:bg-slate-50 transition"
                >
                  Dengarkan Semua
                </button>
              </>
            )}
          </div>
        </div>
      </div>

      <PodcastPlayerModal podcast={activePlayer} onClose={() => setActivePlayer(null)} />
    </section>
  );
}