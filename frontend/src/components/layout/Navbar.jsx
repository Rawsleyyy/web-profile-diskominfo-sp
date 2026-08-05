import { useState, useRef, useEffect } from "react";
import { Search, Menu, X, ChevronDown, Moon, Sun, Home, FileText, Info, Star, Newspaper, ClipboardList, Clock } from "lucide-react";
import { Link } from "react-router-dom";
import { useDateTime } from "@/hooks/UseDateTime";
import { navMenus } from "../../data";
import logoNavbar from "../../assets/diskomifo-pemkot.png";

// Mapping icon string ke komponen Lucide
const iconMap = {
  Home, FileText, Info, Star, Newspaper, ClipboardList,
};

export default function Navbar({ dark, toggleDark }) {
  const [menuOpen, setMenuOpen] = useState(false);
  const [drop, setDrop] = useState(null);
  const [scrolled, setScrolled] = useState(false);
  const closeTimer = useRef(null);

  const currentDateTime = useDateTime();

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 20);
    handleScroll(); // cek posisi awal (misal reload di tengah scroll)
    window.addEventListener("scroll", handleScroll, { passive: true });
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const openDropdown = (label) => {
    if (closeTimer.current) clearTimeout(closeTimer.current);
    setDrop(label);
  };
  const closeDropdownDelayed = () => {
    closeTimer.current = setTimeout(() => setDrop(null), 200);
  };

  return (
    <header className="fixed top-0 left-0 right-0 z-50 font-sans">
      {/* 1. TOP UTILITY BAR*/}
      <div
        className={`hidden md:block border-b border-white/10 overflow-hidden transition-all duration-300 ease-in-out ${
          scrolled ? "max-h-0 opacity-0" : "max-h-12 opacity-100"
        }`}
        style={{ background: "#1c2030" }}
      >
        <div className="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between">
          <span className="flex items-center gap-2 text-slate-300 text-[11px] font-semibold uppercase tracking-wider">
            <Clock size={12} className="text-slate-400" />
            {currentDateTime}
          </span>

          <div className="flex items-center gap-1">
            <div className="flex items-center gap-3 pr-4 border-r border-white/15">
              <i className="bi bi-instagram text-slate-400 hover:text-white cursor-pointer transition-colors text-[14px]"></i>
              <i className="bi bi-facebook text-slate-400 hover:text-white cursor-pointer transition-colors text-[14px]"></i>
              <i className="bi bi-youtube text-slate-400 hover:text-white cursor-pointer transition-colors text-[14px]"></i>
            </div>
            <a href="tel:02718060" className="pl-4 text-slate-300 text-[11px] font-semibold hover:text-white transition-colors">
              (0271) 806060
            </a>
            <span className="mx-2 text-white/15">|</span>
            <a href="mailto:diskominfosp@surakarta.go.id" className="text-slate-300 text-[11px] font-semibold hover:text-white transition-colors">
              diskominfosp@surakarta.go.id
            </a>
          </div>
        </div>
      </div>

      {/* 2. MAIN NAV*/}
      <div
        className={`transition-all duration-300 ease-in-out ${
          scrolled ? "px-4 pt-2 pb-1.5" : "px-0 pt-0 pb-0"
        }`}
      >
        <div
          className={`mx-auto flex items-center justify-between backdrop-blur-xl border-white/20 transition-all duration-300 ease-in-out relative ${
            scrolled
              ? "max-w-7xl rounded-2xl border px-5 py-2.5"
              : "max-w-full rounded-none border-x-0 border-t-0 border-b px-6 py-3.5"
          }`}
          style={{
            background: "linear-gradient(135deg, rgba(21,35,75,0.95) 0%, rgba(30,79,146,0.95) 50%, rgba(41,168,224,0.95) 100%)",
            boxShadow: scrolled
              ? "0 4px 24px rgba(0,0,0,0.20), inset 0 1px 0 rgba(255,255,255,0.2)"
              : "none",
          }}
        >
          {/* ========================================================
              ELEMEN BATIK RANDOM (Dibungkus div terpisah dengan overflow-hidden)
              ======================================================== */}
          <div className="absolute inset-0 pointer-events-none z-0 overflow-hidden rounded-inherit">
            {/* Ornamen Batik 1 */}
            <svg className="absolute -top-6 left-10 w-24 h-24 text-white opacity-[0.04] rotate-12" viewBox="0 0 100 100" fill="currentColor">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
              <circle cx="50" cy="50" r="10" fill="transparent" stroke="currentColor" strokeWidth="4"/>
            </svg>
            {/* Ornamen Batik 2 */}
            <svg className="absolute top-4 left-1/4 w-12 h-12 text-white opacity-[0.06] -rotate-45" viewBox="0 0 100 100" fill="currentColor">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
            </svg>
            {/* Ornamen Batik 3 */}
            <svg className="absolute -bottom-10 left-1/2 w-32 h-32 text-white opacity-[0.03] rotate-45" viewBox="0 0 100 100" fill="currentColor">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
              <circle cx="50" cy="50" r="12" fill="transparent" stroke="currentColor" strokeWidth="3"/>
            </svg>
            {/* Ornamen Batik 4 */}
            <svg className="absolute top-1 right-1/3 w-16 h-16 text-white opacity-[0.05] rotate-90" viewBox="0 0 100 100" fill="currentColor">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
            </svg>
            {/* Ornamen Batik 5 */}
            <svg className="absolute -top-4 right-12 w-20 h-20 text-white opacity-[0.04] -rotate-12" viewBox="0 0 100 100" fill="currentColor">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
              <circle cx="50" cy="50" r="8" fill="transparent" stroke="currentColor" strokeWidth="5"/>
            </svg>
          </div>

          {/* Logo Section */}
          <Link to="/" className="flex items-center gap-4 group relative z-10">
            <div>
              <img 
                src={logoNavbar} 
                alt="Logo Diskominfo" 
                className="h-10 md:h-11 w-auto object-contain group-hover:scale-105 transition-transform duration-300" 
              />
            </div>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex items-center gap-1 relative z-10">
            {navMenus.map((item) => {
              const Icon = iconMap[item.icon];
              return (
                <div
                  key={item.label}
                  className="relative"
                  onMouseEnter={() => item.sub && openDropdown(item.label)}
                  onMouseLeave={closeDropdownDelayed}
                >
                  <Link
                    to={item.href.startsWith('#') ? '#' : item.href}
                    className="flex items-center gap-2 px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-tight text-white/85 hover:text-white hover:bg-white/15 transition-colors duration-200"
                  >
                    {Icon && <Icon size={14} className="text-accent-300" />}
                    {item.label}
                    {item.sub && (
                      <ChevronDown
                        size={10}
                        className={`text-accent-300/80 transition-transform duration-200 ${drop === item.label ? "rotate-180" : ""}`}
                      />
                    )}
                  </Link>

                  {/* Dropdown Menu */}
                  {item.sub && drop === item.label && (
                    <div
                      onMouseEnter={() => clearTimeout(closeTimer.current)}
                      onMouseLeave={closeDropdownDelayed}
                      className="absolute top-full left-0 mt-2 min-w-64 rounded-2xl border border-primary/15 shadow-[0_8px_32px_rgba(0,0,0,0.18)] py-2 z-50 backdrop-blur-xl"
                      style={{ background: "rgba(255,255,255,0.97)" }}
                    >
                      {item.sub.map((s) => {
                        const routeMap = {
                          "Visi & Misi": "/visi-misi",
                          "Tupoksi": "/tupoksi",
                          "Struktur Organisasi": "/struktur",
                          "Maklumat Layanan": "/maklumat",
                          "Standar Layanan": "/maklumat",
                          "Daftar Informasi Publik": "/ppid?tab=Daftar+Informasi+Publik",
                          "Informasi Berkala": "/ppid?tab=Informasi+Berkala",
                          "Informasi Setiap Saat": "/ppid?tab=Informasi+Setiap+Saat",
                          "Informasi Serta Merta": "/ppid?tab=Informasi+Serta+Merta",
                          "Informasi Dikecualikan": "/ppid?tab=Informasi+Dikecualikan",
                        };
                        const label = typeof s === "string" ? s : s.label;
                        const to = typeof s === "string" ? (routeMap[s] || "/") : (s.href || "/");
                        return (
                          <Link
                            key={label}
                            to={to}
                            className="block px-5 py-3 text-[12px] text-primary font-semibold hover:bg-accent-50 transition-colors border-b border-slate-50 last:border-0"
                          >
                            {label}
                          </Link>
                        );
                      })}
                    </div>
                  )}
                </div>
              );
            })}
          </nav>

          {/* Action Buttons */}
          <div className="flex items-center gap-2 relative z-10">
            <button className="w-8 h-8 rounded-xl bg-white/12 border border-white/20 flex items-center justify-center text-white hover:bg-white/22 transition-all">
              <Search size={15} />
            </button>
            <button
              onClick={toggleDark}
              className="w-8 h-8 rounded-xl bg-white/12 border border-white/20 flex items-center justify-center text-white hover:bg-white/22 transition-all"
              title={dark ? "Mode Terang" : "Mode Gelap"}
            >
              {dark ? <Sun size={15} /> : <Moon size={15} />}
            </button>
            <button
              onClick={() => setMenuOpen(!menuOpen)}
              className="lg:hidden w-8 h-8 rounded-xl bg-white/12 border border-white/20 flex items-center justify-center text-white"
            >
              {menuOpen ? <X size={16} /> : <Menu size={16} />}
            </button>
          </div>
        </div>

        {/* Mobile Menu */}
        {menuOpen && (
          <div
            className="lg:hidden max-w-7xl mx-auto mt-2 rounded-2xl border border-primary/15 shadow-[0_8px_32px_rgba(0,0,0,0.15)] p-3 backdrop-blur-xl"
            style={{ background: "rgba(255,255,255,0.97)" }}
          >
            {navMenus.map((item) => {
              const Icon = iconMap[item.icon];
              return (
                <Link
                  key={item.label}
                  to={item.href.startsWith('#') ? '#' : item.href}
                  onClick={() => setMenuOpen(false)}
                  className="flex items-center gap-4 px-5 py-3.5 rounded-xl text-xs font-bold uppercase text-primary hover:bg-accent-50 transition-colors"
                >
                  {Icon && <Icon size={16} className="text-accent-600" />}
                  {item.label}
                </Link>
              );
            })}
          </div>
        )}
      </div>
    </header>
  );
}