import { useEffect, useMemo, useState } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { useSiteConfig } from "../../context/siteconfigcontext";
import heroImage1 from "../../assets/hero/cover-diskominfo.jpeg";
import heroImage2 from "../../assets/hero/prestasi-solo1.jpg";

export default function Hero() {
  const { settings } = useSiteConfig();
  const [current, setCurrent] = useState(0);

  const banners = useMemo(() => [
    {
      id: 1,
      title: (settings?.site_name || "Website Instansi").toUpperCase(),
      address: settings?.address || settings?.site_description || "Portal informasi resmi instansi.",
      bg: heroImage1,
    },
    {
      id: 2,
      title: settings?.site_short_name ? `Informasi ${settings.site_short_name}` : "Informasi & Layanan Publik",
      address: settings?.site_description || "Menyediakan informasi dan layanan publik yang mudah diakses masyarakat.",
      bg: heroImage2,
    },
  ], [settings]);

  useEffect(() => {
    const timer = window.setInterval(() => {
      setCurrent((prev) => (prev === banners.length - 1 ? 0 : prev + 1));
    }, 5000);
    return () => window.clearInterval(timer);
  }, [banners.length]);

  const socials = settings?.socials || {};
  const socialItems = [
    ["instagram", "bi bi-instagram"],
    ["facebook", "bi bi-facebook"],
    ["youtube", "bi bi-youtube"],
    ["tiktok", "bi bi-tiktok"],
  ].filter(([name]) => socials[name]);

  return (
    <section className="relative h-[500px] md:h-[600px] w-full overflow-hidden">
      <AnimatePresence mode="wait">
        <motion.div
          key={current}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.8 }}
          className="absolute inset-0"
        >
          <img src={banners[current].bg} className="h-full w-full object-cover" alt={banners[current].title} />
          <div className="absolute inset-0 flex items-center bg-gradient-to-r from-primary/90 via-primary/40 to-transparent px-6 md:px-20">
            <div className="max-w-3xl text-white">
              <h1 className="mb-6 text-4xl font-bold leading-none tracking-tight md:text-6xl">{banners[current].title}</h1>
              <p className="max-w-xl text-sm font-bold leading-relaxed opacity-80 md:text-lg">{banners[current].address}</p>
            </div>
          </div>
        </motion.div>
      </AnimatePresence>

      {socialItems.length > 0 && (
        <div className="absolute left-4 top-1/2 z-20 flex -translate-y-1/2 flex-col gap-4 text-white">
          {socialItems.map(([name, icon]) => (
            <a key={name} href={socials[name]} target="_blank" rel="noopener noreferrer" className="flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-primary-700 hover:bg-white hover:text-primary">
              <i className={icon}></i>
            </a>
          ))}
        </div>
      )}

      <button onClick={() => setCurrent(current === 0 ? banners.length - 1 : current - 1)} className="absolute left-20 top-1/2 z-20 flex h-12 w-12 items-center justify-center rounded-full bg-black/20 text-white hover:bg-black/40"><i className="bi bi-chevron-left"></i></button>
      <button onClick={() => setCurrent(current === banners.length - 1 ? 0 : current + 1)} className="absolute right-10 top-1/2 z-20 flex h-12 w-12 items-center justify-center rounded-full bg-black/20 text-white hover:bg-black/40"><i className="bi bi-chevron-right"></i></button>

      <div className="absolute bottom-10 left-1/2 z-20 flex -translate-x-1/2 gap-3">
        {banners.map((banner, index) => <button aria-label={`Banner ${index + 1}`} onClick={() => setCurrent(index)} key={banner.id} className={`h-1.5 rounded-full transition-all ${index === current ? "w-10 bg-white" : "w-3 bg-white/30"}`} />)}
      </div>
    </section>
  );
}
