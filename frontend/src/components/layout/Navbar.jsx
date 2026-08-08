import { useEffect, useRef, useState } from "react";
import {
  ChevronDown,
  ClipboardList,
  Clock,
  FileText,
  Home,
  Info,
  Menu,
  Moon,
  Newspaper,
  Search,
  Star,
  Sun,
  X,
} from "lucide-react";
import { Link } from "react-router-dom";
import { useDateTime } from "@/hooks/UseDateTime";
import { navMenus } from "../../data";
import logoNavbar from "../../assets/diskomifo-pemkot.png";

const iconMap = {
  Home,
  FileText,
  Info,
  Star,
  Newspaper,
  ClipboardList,
};

export default function Navbar({ dark, toggleDark }) {
  const [menuOpen, setMenuOpen] = useState(false);
  const [drop, setDrop] = useState(null);
  const [mobileDrop, setMobileDrop] = useState(null);
  const [scrolled, setScrolled] = useState(false);
  const closeTimer = useRef(null);
  const currentDateTime = useDateTime();

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 20);
    handleScroll();
    window.addEventListener("scroll", handleScroll, { passive: true });
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  useEffect(() => () => {
    if (closeTimer.current) window.clearTimeout(closeTimer.current);
  }, []);

  const openDropdown = (label) => {
    if (closeTimer.current) window.clearTimeout(closeTimer.current);
    setDrop(label);
  };

  const closeDropdownDelayed = () => {
    closeTimer.current = window.setTimeout(() => setDrop(null), 180);
  };

  const closeMobileMenu = () => {
    setMenuOpen(false);
    setMobileDrop(null);
  };

  const renderSubLink = (subItem, mobile = false) => {
    const label = typeof subItem === "string" ? subItem : subItem.label;
    const href = typeof subItem === "string" ? "/" : (subItem.href || "/");
    const isExternal = typeof subItem !== "string" && subItem.isExternal;
    const classes = mobile
      ? "theme-popover-link block rounded-xl px-4 py-3 text-xs font-semibold transition-colors"
      : "theme-popover-link block border-b px-5 py-3 text-[12px] font-semibold transition-colors last:border-0";

    const style = mobile ? undefined : { borderColor: "var(--line-default)" };

    if (isExternal) {
      return (
        <a
          key={label}
          href={href}
          target="_blank"
          rel="noopener noreferrer"
          onClick={mobile ? closeMobileMenu : undefined}
          className={classes}
          style={style}
        >
          {label}
        </a>
      );
    }

    return (
      <Link
        key={label}
        to={href}
        onClick={mobile ? closeMobileMenu : undefined}
        className={classes}
        style={style}
      >
        {label}
      </Link>
    );
  };

  return (
    <header className="fixed inset-x-0 top-0 z-50 font-sans">
      <div
        className={`theme-navbar-top hidden overflow-hidden border-b border-white/10 transition-all duration-300 ease-in-out md:block ${
          scrolled ? "max-h-0 opacity-0" : "max-h-12 opacity-100"
        }`}
      >
        <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-2">
          <span className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-white/75">
            <Clock size={12} className="text-white/55" />
            {currentDateTime}
          </span>

          <div className="flex items-center gap-1">
            <div className="flex items-center gap-3 border-r border-white/15 pr-4">
              <i className="bi bi-instagram cursor-pointer text-[14px] text-white/60 transition-colors hover:text-white" />
              <i className="bi bi-facebook cursor-pointer text-[14px] text-white/60 transition-colors hover:text-white" />
              <i className="bi bi-youtube cursor-pointer text-[14px] text-white/60 transition-colors hover:text-white" />
            </div>
            <a
              href="tel:0271806060"
              className="pl-4 text-[11px] font-semibold text-white/75 transition-colors hover:text-white"
            >
              (0271) 806060
            </a>
            <span className="mx-2 text-white/15">|</span>
            <a
              href="mailto:diskominfosp@surakarta.go.id"
              className="text-[11px] font-semibold text-white/75 transition-colors hover:text-white"
            >
              diskominfosp@surakarta.go.id
            </a>
          </div>
        </div>
      </div>

      <div
        className={`transition-all duration-300 ease-in-out ${
          scrolled ? "px-4 pb-1.5 pt-2" : "px-0 pb-0 pt-0"
        }`}
      >
        <div
          className={`theme-navbar-surface relative mx-auto flex items-center justify-between border-white/20 backdrop-blur-xl transition-all duration-300 ease-in-out ${
            scrolled
              ? "max-w-7xl rounded-2xl border px-5 py-2.5"
              : "max-w-full rounded-none border-x-0 border-b border-t-0 px-6 py-3.5"
          }`}
          style={{
            boxShadow: scrolled
              ? "0 5px 26px rgba(0,0,0,0.22), inset 0 1px 0 rgba(255,255,255,0.18)"
              : "none",
          }}
        >
          <div className="pointer-events-none absolute inset-0 z-0 overflow-hidden rounded-[inherit]">
            <svg className="absolute -top-6 left-10 h-24 w-24 rotate-12 text-white opacity-[0.04]" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
              <circle cx="50" cy="50" r="10" fill="transparent" stroke="currentColor" strokeWidth="4" />
            </svg>
            <svg className="absolute left-1/4 top-4 h-12 w-12 -rotate-45 text-white opacity-[0.06]" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
            </svg>
            <svg className="absolute -bottom-10 left-1/2 h-32 w-32 rotate-45 text-white opacity-[0.03]" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
            </svg>
            <svg className="absolute right-12 -top-4 h-20 w-20 -rotate-12 text-white opacity-[0.04]" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true">
              <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
            </svg>
          </div>

          <Link to="/" className="group relative z-10 flex items-center gap-4">
            <img
              src={logoNavbar}
              alt="Logo Diskominfo"
              className="h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105 md:h-11"
            />
          </Link>

          <nav className="relative z-10 hidden items-center gap-1 lg:flex">
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
                    to={item.href.startsWith("#") ? "#" : item.href}
                    className="flex items-center gap-2 rounded-xl px-4 py-2 text-[11px] font-bold uppercase tracking-tight text-white/85 transition-colors duration-200 hover:bg-white/15 hover:text-white"
                  >
                    {Icon && <Icon size={14} className="theme-navbar-accent" />}
                    {item.label}
                    {item.sub && (
                      <ChevronDown
                        size={10}
                        className={`theme-navbar-accent transition-transform duration-200 ${
                          drop === item.label ? "rotate-180" : ""
                        }`}
                      />
                    )}
                  </Link>

                  {item.sub && drop === item.label && (
                    <div
                      onMouseEnter={() => {
                        if (closeTimer.current) window.clearTimeout(closeTimer.current);
                      }}
                      onMouseLeave={closeDropdownDelayed}
                      className="theme-popover absolute left-0 top-full z-50 mt-2 min-w-64 overflow-hidden rounded-2xl border py-2"
                    >
                      {item.sub.map((subItem) => renderSubLink(subItem))}
                    </div>
                  )}
                </div>
              );
            })}
          </nav>

          <div className="relative z-10 flex items-center gap-2">
            <button
              type="button"
              className="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white transition-all hover:bg-white/20"
              aria-label="Cari"
            >
              <Search size={15} />
            </button>
            <button
              type="button"
              onClick={toggleDark}
              className="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white transition-all hover:bg-white/20"
              title={dark ? "Mode Terang" : "Mode Gelap"}
              aria-label={dark ? "Aktifkan mode terang" : "Aktifkan mode gelap"}
            >
              {dark ? <Sun size={15} /> : <Moon size={15} />}
            </button>
            <button
              type="button"
              onClick={() => setMenuOpen((current) => !current)}
              className="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white lg:hidden"
              aria-label="Buka menu navigasi"
              aria-expanded={menuOpen}
            >
              {menuOpen ? <X size={16} /> : <Menu size={16} />}
            </button>
          </div>
        </div>

        {menuOpen && (
          <div className="theme-popover mx-auto mt-2 max-h-[70vh] max-w-7xl overflow-y-auto rounded-2xl border p-3 lg:hidden">
            {navMenus.map((item) => {
              const Icon = iconMap[item.icon];

              if (!item.sub) {
                return (
                  <Link
                    key={item.label}
                    to={item.href}
                    onClick={closeMobileMenu}
                    className="theme-popover-link flex items-center gap-4 rounded-xl px-5 py-3.5 text-xs font-bold uppercase transition-colors"
                  >
                    {Icon && <Icon size={16} className="theme-brand-text" />}
                    {item.label}
                  </Link>
                );
              }

              const isOpen = mobileDrop === item.label;

              return (
                <div key={item.label} className="border-b last:border-0" style={{ borderColor: "var(--line-default)" }}>
                  <button
                    type="button"
                    onClick={() => setMobileDrop(isOpen ? null : item.label)}
                    className="theme-popover-link flex w-full items-center justify-between rounded-xl px-5 py-3.5 text-xs font-bold uppercase transition-colors"
                    aria-expanded={isOpen}
                  >
                    <span className="flex items-center gap-4">
                      {Icon && <Icon size={16} className="theme-brand-text" />}
                      {item.label}
                    </span>
                    <ChevronDown size={14} className={`transition-transform ${isOpen ? "rotate-180" : ""}`} />
                  </button>

                  {isOpen && (
                    <div className="mb-2 ml-9 border-l-2 pl-3" style={{ borderColor: "var(--theme-border-light)" }}>
                      {item.sub.map((subItem) => renderSubLink(subItem, true))}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        )}
      </div>
    </header>
  );
}
