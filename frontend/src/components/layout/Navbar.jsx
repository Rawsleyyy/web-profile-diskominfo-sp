import { useEffect, useRef, useState } from "react";
import { ChevronDown, Clock, Menu, Moon, Search, Sun, X } from "lucide-react";
import { Link } from "react-router-dom";
import { useDateTime } from "@/hooks/UseDateTime";
import { useSiteConfig } from "../../context/siteconfigcontext";
import logoNavbar from "../../assets/diskomifo-pemkot.png";

function MenuLink({ item, className = "", onClick }) {
  const external = item.is_external || item.target === "_blank" || /^https?:\/\//i.test(item.url || "");

  if (external) {
    return (
      <a
        href={item.url || "#"}
        target={item.target || "_blank"}
        rel="noreferrer"
        onClick={onClick}
        className={className}
      >
        {item.label}
      </a>
    );
  }

  return (
    <Link to={item.url || "/"} onClick={onClick} className={className}>
      {item.label}
    </Link>
  );
}

function DesktopMenuItem({ item, opened, onOpen, onClose }) {
  const hasChildren = Array.isArray(item.children) && item.children.length > 0;
  const menuKey = item.id ?? item.label;
  const isPureDropdown = item.type === "dropdown" || item.url === "#";

  return (
    <div
      className="relative"
      onMouseEnter={() => hasChildren && onOpen(menuKey)}
      onMouseLeave={onClose}
    >
      <div className="flex items-center rounded-xl text-[11px] font-bold uppercase tracking-tight text-white/90 transition hover:bg-white/15">
        {isPureDropdown ? (
          <button
            type="button"
            onClick={() => hasChildren && onOpen(opened === menuKey ? null : menuKey)}
            className="px-3 py-2 text-white/90 hover:text-white"
          >
            {item.label}
          </button>
        ) : (
          <MenuLink item={item} className="px-3 py-2 text-white/90 hover:text-white" />
        )}

        {hasChildren && (
          <button
            type="button"
            onClick={() => onOpen(opened === menuKey ? null : menuKey)}
            aria-label={`Buka submenu ${item.label}`}
            className="mr-2 flex h-7 w-6 items-center justify-center"
          >
            <ChevronDown size={11} className={`transition-transform ${opened === menuKey ? "rotate-180" : ""}`} />
          </button>
        )}
      </div>

      {hasChildren && opened === menuKey && (
        <div
          onMouseEnter={() => onOpen(menuKey)}
          onMouseLeave={onClose}
          className="absolute left-0 top-full mt-2 min-w-64 rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-2xl backdrop-blur-xl"
        >
          {item.children.map((child) => (
            <MenuLink
              key={child.id ?? child.label}
              item={child}
              className="block rounded-xl px-4 py-3 text-[12px] font-semibold normal-case tracking-normal text-slate-700 hover:bg-slate-100"
            />
          ))}
        </div>
      )}
    </div>
  );
}

export default function Navbar({ dark, toggleDark }) {
  const { navigation, settings } = useSiteConfig();
  const [menuOpen, setMenuOpen] = useState(false);
  const [drop, setDrop] = useState(null);
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
    if (closeTimer.current) clearTimeout(closeTimer.current);
  }, []);

  const openDropdown = (id) => {
    if (closeTimer.current) clearTimeout(closeTimer.current);
    setDrop(id);
  };

  const closeDropdownDelayed = () => {
    if (closeTimer.current) clearTimeout(closeTimer.current);
    closeTimer.current = setTimeout(() => setDrop(null), 180);
  };

  const logo = settings?.logo_url || logoNavbar;

  return (
    <header className="fixed left-0 right-0 top-0 z-50 font-sans">
      <div
        className={`hidden overflow-hidden border-b border-white/10 transition-all duration-300 md:block ${scrolled ? "max-h-0 opacity-0" : "max-h-12 opacity-100"}`}
        style={{ background: "#1c2030" }}
      >
        <div className="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-2">
          <span className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-300">
            <Clock size={12} />
            {currentDateTime}
          </span>
          <div className="flex items-center gap-3 text-[11px] font-semibold text-slate-300">
            {settings?.phone && <a href={`tel:${settings.phone}`} className="hover:text-white">{settings.phone}</a>}
            {settings?.phone && settings?.email && <span className="text-white/20">|</span>}
            {settings?.email && <a href={`mailto:${settings.email}`} className="hover:text-white">{settings.email}</a>}
          </div>
        </div>
      </div>

      <div className={`transition-all duration-300 ${scrolled ? "px-4 pb-1.5 pt-2" : "px-0"}`}>
        <div
          className={`relative mx-auto flex items-center justify-between border-white/20 backdrop-blur-xl transition-all duration-300 ${scrolled ? "max-w-7xl rounded-2xl border px-5 py-2.5" : "max-w-full border-b px-6 py-3.5"}`}
          style={{
            background: "linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 96%, #0f172a) 0%, var(--color-primary) 58%, color-mix(in srgb, var(--color-primary) 65%, var(--color-accent)) 100%)",
            boxShadow: scrolled ? "0 4px 24px rgba(0,0,0,.20)" : "none",
          }}
        >
          <Link to="/" className="relative z-10 flex min-w-0 items-center gap-3">
            <img src={logo} alt={settings?.site_short_name || "Logo Instansi"} className="h-10 max-w-52 object-contain md:h-11" />
            {settings?.logo_url && (
              <span className="hidden max-w-52 truncate text-sm font-extrabold text-white xl:block">
                {settings.site_short_name}
              </span>
            )}
          </Link>

          <nav className="relative z-10 hidden items-center gap-1 lg:flex">
            {(navigation || []).map((item) => (
              <DesktopMenuItem
                key={item.id ?? item.label}
                item={item}
                opened={drop}
                onOpen={openDropdown}
                onClose={closeDropdownDelayed}
              />
            ))}
          </nav>

          <div className="relative z-10 flex items-center gap-2">
            <button type="button" className="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white" aria-label="Cari">
              <Search size={15} />
            </button>
            <button type="button" onClick={toggleDark} className="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white" title={dark ? "Mode Terang" : "Mode Gelap"}>
              {dark ? <Sun size={15} /> : <Moon size={15} />}
            </button>
            <button type="button" onClick={() => setMenuOpen(!menuOpen)} className="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white lg:hidden" aria-label="Buka menu">
              {menuOpen ? <X size={16} /> : <Menu size={16} />}
            </button>
          </div>
        </div>

        {menuOpen && (
          <div className="mx-4 mt-2 max-h-[70vh] overflow-y-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl lg:hidden">
            {(navigation || []).map((item) => {
              const hasChildren = Array.isArray(item.children) && item.children.length > 0;
              const isPureDropdown = item.type === "dropdown" || item.url === "#";
              return (
                <div key={item.id ?? item.label} className="border-b border-slate-100 last:border-0">
                  {isPureDropdown ? (
                    <div className="px-4 py-3 text-xs font-bold uppercase text-slate-800">{item.label}</div>
                  ) : (
                    <MenuLink item={item} onClick={() => !hasChildren && setMenuOpen(false)} className="block px-4 py-3 text-xs font-bold uppercase text-slate-800" />
                  )}
                  {item.children?.map((child) => (
                    <MenuLink
                      key={child.id ?? child.label}
                      item={child}
                      onClick={() => setMenuOpen(false)}
                      className="block px-7 py-2.5 text-xs font-semibold text-slate-500 hover:text-primary"
                    />
                  ))}
                </div>
              );
            })}
          </div>
        )}
      </div>
    </header>
  );
}
