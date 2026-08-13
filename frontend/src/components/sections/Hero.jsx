import { useEffect, useMemo, useState } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { Link } from "react-router-dom";
import { useSiteConfig } from "../../context/siteconfigcontext";
import { api, storageUrl } from "../../services/api";
import heroImage1 from "../../assets/hero/cover-diskominfo.jpeg";
import heroImage2 from "../../assets/hero/prestasi-solo1.jpg";

function BannerButton({ banner }) {
  if (!banner?.button_label || !banner?.button_url) return null;

  const external = /^https?:\/\//i.test(banner.button_url);
  const classes = "mt-7 inline-flex items-center rounded-xl border border-white/25 bg-white/15 px-5 py-3 text-sm font-bold text-white backdrop-blur-md transition hover:bg-white hover:text-slate-900";

  if (external) {
    return (
      <a href={banner.button_url} target="_blank" rel="noreferrer" className={classes}>
        {banner.button_label}
      </a>
    );
  }

  return <Link to={banner.button_url} className={classes}>{banner.button_label}</Link>;
}

export default function Hero() {
  const { settings } = useSiteConfig();
  const [managedBanners, setManagedBanners] = useState([]);
  const [current, setCurrent] = useState(0);

  const fallbackBanners = useMemo(() => [
    {
      id: "fallback-1",
      title: (settings?.site_name || "Website Instansi").toUpperCase(),
      subtitle: settings?.address || settings?.site_description || "Portal informasi resmi instansi.",
      image_url: heroImage1,
      alt_text: settings?.site_name || "Website Instansi",
    },
    {
      id: "fallback-2",
      title: settings?.site_short_name ? `Informasi ${settings.site_short_name}` : "Informasi & Layanan Publik",
      subtitle: settings?.site_description || "Menyediakan informasi dan layanan publik yang mudah diakses masyarakat.",
      image_url: heroImage2,
      alt_text: "Informasi dan layanan publik",
    },
  ], [settings]);

  useEffect(() => {
    let active = true;

    api.get("/hero-slides")
      .then(({ data }) => {
        if (!active || !Array.isArray(data)) return;

        const normalized = data
          .map((slide) => ({
            ...slide,
            image_url: storageUrl(slide.image_path) || slide.image_url,
          }))
          .filter((slide) => slide.image_url);

        setManagedBanners(normalized);
        setCurrent(0);
      })
      .catch(() => {
        // Banner cadangan tetap dipakai jika API belum tersedia / belum ada data.
      });

    return () => {
      active = false;
    };
  }, []);

  const banners = managedBanners.length > 0 ? managedBanners : fallbackBanners;

  useEffect(() => {
    if (banners.length <= 1) return undefined;

    const timer = window.setInterval(() => {
      setCurrent((prev) => (prev >= banners.length - 1 ? 0 : prev + 1));
    }, 5000);

    return () => window.clearInterval(timer);
  }, [banners.length]);

  useEffect(() => {
    if (current >= banners.length) setCurrent(0);
  }, [banners.length, current]);

  const socials = settings?.socials || {};
  const socialItems = [
    ["instagram", "bi bi-instagram"],
    ["facebook", "bi bi-facebook"],
    ["youtube", "bi bi-youtube"],
    ["tiktok", "bi bi-tiktok"],
  ].filter(([name]) => socials[name]);

  const banner = banners[current] || banners[0];

  if (!banner) return null;

  const previous = () => setCurrent((prev) => (prev <= 0 ? banners.length - 1 : prev - 1));
  const next = () => setCurrent((prev) => (prev >= banners.length - 1 ? 0 : prev + 1));

  return (
    <section className="relative h-[500px] w-full overflow-hidden md:h-[600px]">
      <AnimatePresence mode="wait">
        <motion.div
          key={banner.id ?? current}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.8 }}
          className="absolute inset-0"
        >
          <img
            src={banner.image_url}
            className="h-full w-full object-cover"
            alt={banner.alt_text || banner.title || `Banner ${current + 1}`}
          />
          <div className="absolute inset-0 flex items-center bg-gradient-to-r from-primary/90 via-primary/45 to-transparent px-6 md:px-20">
            <div className="max-w-3xl text-white">
              {banner.title && (
                <h1 className="mb-6 text-4xl font-bold leading-none tracking-tight md:text-6xl">
                  {banner.title}
                </h1>
              )}
              {banner.subtitle && (
                <p className="max-w-xl text-sm font-bold leading-relaxed text-white/80 md:text-lg">
                  {banner.subtitle}
                </p>
              )}
              <BannerButton banner={banner} />
            </div>
          </div>
        </motion.div>
      </AnimatePresence>

      {socialItems.length > 0 && (
        <div className="absolute left-4 top-1/2 z-20 flex -translate-y-1/2 flex-col gap-4 text-white">
          {socialItems.map(([name, icon]) => (
            <a
              key={name}
              href={socials[name]}
              target="_blank"
              rel="noopener noreferrer"
              aria-label={name}
              className="flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-primary-700 hover:bg-white hover:text-primary"
            >
              <i className={icon}></i>
            </a>
          ))}
        </div>
      )}

      {banners.length > 1 && (
        <>
          <button
            type="button"
            onClick={previous}
            aria-label="Banner sebelumnya"
            className="absolute left-20 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-black/20 text-white transition hover:bg-black/40"
          >
            <i className="bi bi-chevron-left"></i>
          </button>
          <button
            type="button"
            onClick={next}
            aria-label="Banner berikutnya"
            className="absolute right-10 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-black/20 text-white transition hover:bg-black/40"
          >
            <i className="bi bi-chevron-right"></i>
          </button>
        </>
      )}

      {banners.length > 1 && (
        <div className="absolute bottom-10 left-1/2 z-20 flex -translate-x-1/2 gap-3">
          {banners.map((item, index) => (
            <button
              type="button"
              aria-label={`Banner ${index + 1}`}
              onClick={() => setCurrent(index)}
              key={item.id ?? index}
              className={`h-1.5 rounded-full transition-all ${index === current ? "w-10 bg-white" : "w-3 bg-white/30"}`}
            />
          ))}
        </div>
      )}
    </section>
  );
}
