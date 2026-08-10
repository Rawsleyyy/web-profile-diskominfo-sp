import { useEffect, useRef, useState } from "react";
import { ChevronDown, Clock, Menu, Moon, Search, Sun, X } from "lucide-react";
import { Link } from "react-router-dom";
import { useDateTime } from "@/hooks/UseDateTime";
import { useSiteConfig } from "../../context/siteconfigcontext";
import logoNavbar from "../../assets/diskomifo-pemkot.png";

function MenuLink({ item, className = "", onClick }) {
  const external = item.is_external || item.target === "_blank" || /^https?:\/\//i.test(item.url || "");
  if (external) {
    return <a href={item.url || "#"} target={item.target || "_blank"} rel="noreferrer" onClick={onClick} className={className}>{item.label}</a>;
  }
  return <Link to={item.url || "#"} onClick={onClick} className={className}>{item.label}</Link>;
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

  const openDropdown = (id) => {
    if (closeTimer.current) clearTimeout(closeTimer.current);
    setDrop(id);
  };
  const closeDropdownDelayed = () => {
    closeTimer.current = setTimeout(() => setDrop(null), 180);
  };

  const announcement = settings?.announcement;
  const logo = settings?.logo_url || logoNavbar;

  return (
    <header className="fixed top-0 left-0 right-0 z-50 font-sans">
      {announcement && (
        <div className="px-4 py-2 text-center text-xs font-bold text-white" style={{ backgroundColor: announcement.color || "#DC2626" }}>
          {announcement.url ? (
            /^https?:\/\//i.test(announcement.url)
              ? <a href={announcement.url} target="_blank" rel="noreferrer" className="hover:underline">{announcement.text}</a>
              : <Link to={announcement.url} className="hover:underline">{announcement.text}</Link>
          ) : announcement.text}
        </div>
      )}

      <div className={`hidden md:block border-b border-white/10 overflow-hidden transition-all duration-300 ${scrolled ? "max-h-0 opacity-0" : "max-h-12 opacity-100"}`} style={{ background: "#1c2030" }}>
        <div className="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between gap-6">
          <span className="flex items-center gap-2 text-slate-300 text-[11px] font-semibold uppercase tracking-wider"><Clock size={12} />{currentDateTime}</span>
          <div className="flex items-center gap-3 text-[11px] font-semibold text-slate-300">
            {settings?.phone && <a href={`tel:${settings.phone}`} className="hover:text-white">{settings.phone}</a>}
            {settings?.phone && settings?.email && <span className="text-white/20">|</span>}
            {settings?.email && <a href={`mailto:${settings.email}`} className="hover:text-white">{settings.email}</a>}
          </div>
        </div>
      </div>

      <div className={`transition-all duration-300 ${scrolled ? "px-4 pt-2 pb-1.5" : "px-0"}`}>
        <div className={`mx-auto flex items-center justify-between border-white/20 backdrop-blur-xl transition-all duration-300 relative ${scrolled ? "max-w-7xl rounded-2xl border px-5 py-2.5" : "max-w-full border-b px-6 py-3.5"}`} style={{ background: "linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 96%, #0f172a) 0%, var(--color-primary) 58%, color-mix(in srgb, var(--color-primary) 65%, var(--color-accent)) 100%)", boxShadow: scrolled ? "0 4px 24px rgba(0,0,0,.20)" : "none" }}>
          <Link to="/" className="relative z-10 flex min-w-0 items-center gap-3">
            <img src={logo} alt={settings?.site_short_name || "Logo Instansi"} className="h-10 md:h-11 max-w-52 object-contain" />
            {settings?.logo_url && <span className="hidden xl:block max-w-52 truncate text-sm font-extrabold text-white">{settings.site_short_name}</span>}
          </Link>

          <nav className="hidden lg:flex items-center gap-1 relative z-10">
            {(navigation || []).map((item) => {
              const hasChildren = Array.isArray(item.children) && item.children.length > 0;
              return (
                <div key={item.id ?? item.label} className="relative" onMouseEnter={() => hasChildren && openDropdown(item.id ?? item.label)} onMouseLeave={closeDropdownDelayed}>
                  <div className="flex items-center rounded-xl text-[11px] font-bold uppercase tracking-tight text-white/90 hover:bg-white/15">
                    <MenuLink item={item} className="px-3 py-2 text-white/90 hover:text-white" />
                    {hasChildren && <ChevronDown size={11} className={`mr-3 transition-transform ${drop === (item.id ?? item.label) ? "rotate-180" : ""}`} />}
                  </div>
                  {hasChildren && drop === (item.id ?? item.label) && (
                    <div onMouseEnter={() => clearTimeout(closeTimer.current)} onMouseLeave={closeDropdownDelayed} className="absolute top-full left-0 mt-2 min-w-64 rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-2xl backdrop-blur-xl">
                      {item.children.map((child) => <MenuLink key={child.id ?? child.label} item={child} className="block rounded-xl px-4 py-3 text-[12px] font-semibold text-slate-700 hover:bg-slate-100" />)}
                    </div>
                  )}
                </div>
              );
            })}
          </nav>

          <div className="relative z-10 flex items-center gap-2">
            <button className="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white"><Search size={15} /></button>
            <button onClick={toggleDark} className="flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white" title={dark ? "Mode Terang" : "Mode Gelap"}>{dark ? <Sun size={15} /> : <Moon size={15} />}</button>
            <button onClick={() => setMenuOpen(!menuOpen)} className="lg:hidden flex h-8 w-8 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white">{menuOpen ? <X size={16} /> : <Menu size={16} />}</button>
          </div>
        </div>

        {menuOpen && (
          <div className="lg:hidden mx-4 mt-2 max-h-[70vh] overflow-y-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl">
            {(navigation || []).map((item) => (
              <div key={item.id ?? item.label} className="border-b border-slate-100 last:border-0">
                <MenuLink item={item} onClick={() => !item.children?.length && setMenuOpen(false)} className="block px-4 py-3 text-xs font-bold uppercase text-slate-800" />
                {item.children?.map((child) => <MenuLink key={child.id ?? child.label} item={child} onClick={() => setMenuOpen(false)} className="block px-7 py-2.5 text-xs font-semibold text-slate-500 hover:text-primary" />)}
              </div>
            ))}
          </div>
        )}
      </div>
    </header>
  );
}
