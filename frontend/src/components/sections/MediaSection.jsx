import { useEffect, useState } from "react";
import { api, BASE_URL } from "../../services/api";

const TABS = ["Rilis Data", "LKJIP", "Statistik"];

function getFileUrl(item) {
  if (!item.file_path) return "#";
  if (item.file_path.startsWith("http")) return item.file_path;
  return `${BASE_URL}/storage/${item.file_path}`;
}

function resolveAudioUrl(urlAudio) {
  if (!urlAudio) return null;
  if (urlAudio.startsWith("http")) return urlAudio;
  return `${BASE_URL}/storage/${urlAudio}`;
}

function getYoutubeEmbedUrl(url) {
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
  if (url.includes("/embed/")) return url;
  return url.replace("open.spotify.com/", "open.spotify.com/embed/");
}

function detectSourceType(urlAudio) {
  if (!urlAudio) return null;
  if (urlAudio.includes("youtube.com") || urlAudio.includes("youtu.be")) return "youtube";
  if (urlAudio.includes("spotify.com")) return "spotify";
  return "file";
}

function PodcastPlayerModal({ podcast, onClose }) {
  if (!podcast) return null;

  const sourceType = detectSourceType(podcast.url_audio);

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4 backdrop-blur-sm"
      onClick={onClose}
      role="presentation"
    >
      <div
        className="theme-card relative w-full max-w-lg rounded-3xl p-6"
        onClick={(event) => event.stopPropagation()}
        role="dialog"
        aria-modal="true"
        aria-label={`Pemutar podcast ${podcast.judul}`}
      >
        <button
          type="button"
          onClick={onClose}
          className="theme-muted-surface theme-text-secondary absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full text-sm transition hover:scale-105"
          aria-label="Tutup pemutar podcast"
        >
          ✕
        </button>

        <h4 className="theme-brand-text mb-1 text-[10px] font-black uppercase tracking-widest">
          {podcast.episode ? `Episode ${podcast.episode}` : "Podcast"}
        </h4>
        <p className="theme-text-main mb-6 pr-8 text-lg font-bold">{podcast.judul}</p>

        {sourceType === "youtube" && getYoutubeEmbedUrl(podcast.url_audio) && (
          <div className="aspect-video overflow-hidden rounded-2xl bg-black">
            <iframe
              src={getYoutubeEmbedUrl(podcast.url_audio)}
              title={podcast.judul}
              className="h-full w-full"
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
          <p className="theme-text-secondary mt-4 text-xs leading-relaxed">
            {podcast.deskripsi}
          </p>
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
  const [activePlayer, setActivePlayer] = useState(null);

  useEffect(() => {
    let mounted = true;

    api.get("/dokumen-publik")
      .then((response) => {
        const data = response.data.data || response.data || [];
        if (mounted) setDokumen(data);
      })
      .catch((error) => console.error("Gagal memuat dokumen publik:", error))
      .finally(() => mounted && setLoadingDokumen(false));

    return () => {
      mounted = false;
    };
  }, []);

  useEffect(() => {
    let mounted = true;

    api.get("/podcast")
      .then((response) => {
        const data = response.data.data || response.data || [];
        if (mounted) setPodcasts(data);
      })
      .catch((error) => console.error("Gagal memuat podcast:", error))
      .finally(() => mounted && setLoadingPodcast(false));

    return () => {
      mounted = false;
    };
  }, []);

  const filteredDokumen = dokumen.filter((item) => item.kategori === activeTab);
  const [featured, ...restPodcasts] = podcasts;

  return (
    <section className="theme-section-alt py-20">
      <div className="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-6 lg:grid-cols-3">
        <div className="lg:col-span-2">
          <h2 className="theme-text-main mb-8 text-3xl font-black tracking-tighter">
            Dokumen & Data Publik
          </h2>

          <div className="theme-card rounded-[2.5rem] p-4">
            <div className="theme-divider mb-4 flex gap-4 border-b p-2">
              {TABS.map((tab) => (
                <button
                  key={tab}
                  type="button"
                  onClick={() => setActiveTab(tab)}
                  className={`border-b-2 px-2 pb-2 text-xs font-bold transition ${
                    activeTab === tab
                      ? "theme-brand-text border-current"
                      : "theme-text-muted border-transparent hover:text-primary"
                  }`}
                >
                  {tab}
                </button>
              ))}
            </div>

            <div className="min-h-[120px] space-y-2">
              {loadingDokumen ? (
                <p className="theme-text-muted py-8 text-center text-xs font-medium">
                  Memuat dokumen...
                </p>
              ) : filteredDokumen.length === 0 ? (
                <p className="theme-text-muted py-8 text-center text-xs font-medium italic">
                  Belum ada dokumen untuk kategori ini.
                </p>
              ) : (
                filteredDokumen.map((item) => (
                  <div
                    key={item.id}
                    className="group flex items-center justify-between rounded-2xl p-4 transition hover:-translate-y-0.5"
                    style={{ background: "color-mix(in srgb, var(--surface-muted) 55%, transparent)" }}
                  >
                    <div className="flex min-w-0 items-center gap-4">
                      <div className="theme-chip flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                        <i className="bi bi-file-earmark-pdf" />
                      </div>
                      <div className="min-w-0">
                        <h4 className="theme-text-main truncate text-[11px] font-bold">
                          {item.judul}
                        </h4>
                        <p className="theme-text-muted text-[9px] font-medium uppercase">
                          {item.ukuran_formatted} — {item.format}
                        </p>
                      </div>
                    </div>
                    <a
                      href={getFileUrl(item)}
                      target="_blank"
                      rel="noopener noreferrer"
                      download
                      className="theme-chip flex h-8 w-8 shrink-0 items-center justify-center rounded-full opacity-70 transition group-hover:opacity-100"
                      aria-label={`Unduh ${item.judul}`}
                    >
                      <i className="bi bi-download" />
                    </a>
                  </div>
                ))
              )}
            </div>
          </div>
        </div>

        <div>
          <h2 className="theme-text-main mb-8 text-3xl font-black tracking-tighter">
            KOMINPOD
          </h2>

          <div className="theme-panel-gradient relative min-h-[280px] overflow-hidden rounded-[2.5rem] p-8 text-white shadow-xl">
            <i className="bi bi-mic-fill absolute -bottom-4 -right-4 rotate-12 text-9xl opacity-10" />

            {loadingPodcast ? (
              <p className="text-xs font-medium text-white/70">Memuat podcast...</p>
            ) : !featured ? (
              <p className="text-xs font-medium italic text-white/70">
                Belum ada episode podcast.
              </p>
            ) : (
              <>
                <h4 className="mb-2 text-[10px] font-black uppercase tracking-widest text-white/60">
                  Podcast Terbaru
                </h4>
                <p className="mb-8 text-lg font-bold leading-tight">
                  {featured.episode ? `Ep. ${featured.episode} — ` : ""}
                  {featured.judul}
                </p>

                {restPodcasts.length > 0 && (
                  <div className="space-y-4">
                    {restPodcasts.slice(0, 2).map((podcast) => (
                      <button
                        key={podcast.id}
                        type="button"
                        onClick={() => setActivePlayer(podcast)}
                        className="flex w-full items-center gap-4 rounded-2xl border border-white/10 bg-white/10 p-3 text-left backdrop-blur-sm transition hover:bg-white/20"
                      >
                        <div className="keep-light-surface flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs">
                          <i className="bi bi-play-fill" />
                        </div>
                        <span className="truncate text-[10px] font-bold text-white/80">
                          {podcast.episode ? `Edisi ${podcast.episode} — ` : ""}
                          {podcast.judul}
                        </span>
                      </button>
                    ))}
                  </div>
                )}

                <button
                  type="button"
                  onClick={() => setActivePlayer(featured)}
                  className="keep-light-surface mt-8 block w-full rounded-xl py-3 text-center text-xs font-bold transition hover:brightness-95"
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
