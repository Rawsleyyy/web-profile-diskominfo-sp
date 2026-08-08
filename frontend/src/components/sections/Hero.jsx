import { useEffect, useMemo, useState } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { api, storageUrl } from "../../services/api";
import heroImage1 from "../../assets/hero/cover-diskominfo.jpeg";
import heroImage2 from "../../assets/hero/prestasi-solo1.jpg";

const fallbackBanners = [
  {
    id: "fallback-1",
    title: "DISKOMINFO SP KOTA SURAKARTA",
    subtitle: "Gedung Bale Upakari Lantai 3, Jl. Jenderal Sudirman No.2 Kampung Baru, Kec. Pasar Kliwon, Kota Surakarta, Jawa Tengah 57133",
    image_url: heroImage1,
    alt_text: "Gedung Diskominfo SP Kota Surakarta",
  },
  {
    id: "fallback-2",
    title: "Prestasi Kota Surakarta",
    subtitle: "Mewujudkan Solo Smart City yang inklusif dan transparan untuk seluruh warga.",
    image_url: heroImage2,
    alt_text: "Prestasi Kota Surakarta",
  },
];

function resolveImage(slide) {
  return slide.image_url || storageUrl(slide.image_path) || heroImage1;
}

export default function Hero() {
  const [remoteBanners, setRemoteBanners] = useState([]);
  const [current, setCurrent] = useState(0);

  useEffect(() => {
    let active = true;

    api.get("/hero-slides")
      .then(({ data }) => {
        if (!active) return;
        const items = Array.isArray(data) ? data : data?.data || [];
        setRemoteBanners(items);
      })
      .catch((error) => {
        console.error("Gagal memuat header dari dashboard:", error);
      });

    return () => {
      active = false;
    };
  }, []);

  const banners = useMemo(
    () => (remoteBanners.length > 0 ? remoteBanners : fallbackBanners),
    [remoteBanners],
  );

  useEffect(() => {
    setCurrent((index) => Math.min(index, Math.max(0, banners.length - 1)));
  }, [banners.length]);

  useEffect(() => {
    if (banners.length <= 1) return undefined;

    const timer = window.setInterval(() => {
      setCurrent((previous) => (previous + 1) % banners.length);
    }, 5000);

    return () => window.clearInterval(timer);
  }, [banners.length]);

  const activeBanner = banners[current] || fallbackBanners[0];
  const previous = () => setCurrent((index) => (index - 1 + banners.length) % banners.length);
  const next = () => setCurrent((index) => (index + 1) % banners.length);

  return (
    <section className="theme-hero relative h-[500px] md:h-[600px] w-full overflow-hidden">
      <AnimatePresence mode="wait">
        <motion.div
          key={activeBanner.id}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.8 }}
          className="absolute inset-0"
        >
          <img
            src={resolveImage(activeBanner)}
            className="h-full w-full object-cover"
            alt={activeBanner.alt_text || activeBanner.title || "Header Diskominfo SP"}
          />
          <div className="theme-hero-overlay absolute inset-0 flex items-center px-6 md:px-20">
            <div className="max-w-3xl text-white">
              <h1 className="mb-6 text-4xl font-bold leading-none tracking-tight md:text-6xl">
                {activeBanner.title}
              </h1>
              {activeBanner.subtitle && (
                <p className="max-w-xl text-sm font-bold leading-relaxed opacity-80 md:text-lg">
                  {activeBanner.subtitle}
                </p>
              )}
              {activeBanner.button_label && activeBanner.button_url && (
                <a
                  href={activeBanner.button_url}
                  target={/^https?:\/\//i.test(activeBanner.button_url) ? "_blank" : undefined}
                  rel={/^https?:\/\//i.test(activeBanner.button_url) ? "noopener noreferrer" : undefined}
                  className="keep-light-surface mt-7 inline-flex rounded-xl px-5 py-3 text-sm font-black shadow-lg transition hover:-translate-y-0.5"
                >
                  {activeBanner.button_label}
                </a>
              )}
            </div>
          </div>
        </motion.div>
      </AnimatePresence>

      <div className="absolute left-4 top-1/2 z-20 flex -translate-y-1/2 flex-col gap-4 text-white">
        <a href="https://www.instagram.com/diskominfosp_surakarta" target="_blank" rel="noopener noreferrer" className="flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-primary-700 transition-colors hover:bg-white hover:text-primary" aria-label="Instagram Diskominfo SP">
          <i className="bi bi-instagram" />
        </a>
        <a href="https://www.facebook.com/diskominfospsurakarta/" target="_blank" rel="noopener noreferrer" className="flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-primary-700 transition-colors hover:bg-white hover:text-primary" aria-label="Facebook Diskominfo SP">
          <i className="bi bi-facebook" />
        </a>
        <a href="https://www.youtube.com/@diskominfospsurakarta8388" target="_blank" rel="noopener noreferrer" className="flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-primary-700 transition-colors hover:bg-white hover:text-primary" aria-label="YouTube Diskominfo SP">
          <i className="bi bi-youtube" />
        </a>
      </div>

      {banners.length > 1 && (
        <>
          <button type="button" onClick={previous} className="absolute left-20 top-1/2 z-20 flex h-12 w-12 items-center justify-center rounded-full bg-black/20 text-white hover:bg-black/40" aria-label="Header sebelumnya">
            <i className="bi bi-chevron-left" />
          </button>
          <button type="button" onClick={next} className="absolute right-10 top-1/2 z-20 flex h-12 w-12 items-center justify-center rounded-full bg-black/20 text-white hover:bg-black/40" aria-label="Header berikutnya">
            <i className="bi bi-chevron-right" />
          </button>

          <div className="absolute bottom-10 left-1/2 z-20 flex -translate-x-1/2 gap-3">
            {banners.map((banner, index) => (
              <button
                type="button"
                key={banner.id}
                onClick={() => setCurrent(index)}
                className={`h-1.5 rounded-full transition-all ${index === current ? "w-10 bg-white" : "w-3 bg-white/30"}`}
                aria-label={`Tampilkan header ${index + 1}`}
              />
            ))}
          </div>
        </>
      )}
    </section>
  );
}
