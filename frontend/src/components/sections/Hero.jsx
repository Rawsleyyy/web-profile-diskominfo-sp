import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import heroImage1 from "../../assets/hero/cover-diskominfo.jpeg";
import heroImage2 from "../../assets/hero/prestasi-solo1.jpg";

const banners = [
  {
    id: 1,
    title: "DISKOMINFO SP KOTA SURAKARTA",
    address: "Gedung Bale Upakari Lantai 3, Jl. Jenderal Sudirman No.2 Kampung Baru, Kec. Pasar Kliwon, Kota Surakarta, Jawa Tengah 57133",
    bg: heroImage1,
  },
  {
    id: 2,
    title: "Prestasi Kota Surakarta",
    address: "Mewujudkan Solo Smart City yang inklusif dan transparan untuk seluruh warga.",
    bg: heroImage2,
  }
];

export default function Hero() {
  const [current, setCurrent] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrent((prev) => (prev === banners.length - 1 ? 0 : prev + 1));
    }, 5000); // Geser tiap 5 detik
    return () => clearInterval(timer);
  }, []);

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
          <img src={banners[current].bg} className="w-full h-full object-cover" alt="Banner" />
          <div className="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/40 to-transparent flex items-center px-6 md:px-20">
            <div className="max-w-3xl text-white">
              <h1 className="text-4xl md:text-6xl font-bold mb-6 tracking-tight leading-none">
                {banners[current].title}
              </h1>
              <p className="text-sm md:text-lg font-bold opacity-80 max-w-xl leading-relaxed">
                {banners[current].address}
              </p>
            </div>
          </div>
        </motion.div>
      </AnimatePresence>

      {/* Social Media Sidebar (Left) */}
      <div className="absolute left-4 top-1/2 -translate-y-1/2 flex flex-col gap-4 z-20 text-white">
        <a href="https://www.instagram.com/diskominfosp_surakarta" target="_blank" rel="noopener noreferrer" className="w-10 h-10 rounded-full border border-white/40 bg-primary-700 flex items-center justify-center hover:bg-white hover:text-primary transition-colors">
          <i className="bi bi-instagram"></i>
        </a>
        <a href="https://www.facebook.com/diskominfospsurakarta/" target="_blank" rel="noopener noreferrer" className="w-10 h-10 rounded-full border border-white/40 bg-primary-700 flex items-center justify-center hover:bg-white hover:text-primary transition-colors">
          <i className="bi bi-facebook"></i>
        </a>
        <a href="https://www.youtube.com/@diskominfospsurakarta8388" target="_blank" rel="noopener noreferrer" className="w-10 h-10 rounded-full border border-white/40 bg-primary-700 flex items-center justify-center hover:bg-white hover:text-primary transition-colors">
          <i className="bi bi-youtube"></i>
        </a>
      </div>

      {/* Navigation Arrows */}
      <button onClick={() => setCurrent(current === 0 ? banners.length-1 : current-1)} className="absolute left-20 top-1/2 w-12 h-12 rounded-full bg-black/20 text-white flex items-center justify-center hover:bg-black/40 z-20">
        <i className="bi bi-chevron-left"></i>
      </button>
      <button onClick={() => setCurrent(current === banners.length-1 ? 0 : current+1)} className="absolute right-10 top-1/2 w-12 h-12 rounded-full bg-black/20 text-white flex items-center justify-center hover:bg-black/40 z-20">
        <i className="bi bi-chevron-right"></i>
      </button>

      {/* Indicators */}
      <div className="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-3 z-20">
        {banners.map((_, i) => (
          <div key={i} className={`h-1.5 rounded-full transition-all ${i === current ? 'w-10 bg-white' : 'w-3 bg-white/30'}`} />
        ))}
      </div>
    </section>
  );
}