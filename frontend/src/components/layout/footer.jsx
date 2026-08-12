import { useEffect, useState } from "react";
import { ExternalLink, Mail, MapPin, Phone, Users } from "lucide-react";
import { Link } from "react-router-dom";
import { api } from "../../services/api";
import { useSiteConfig } from "../../context/siteconfigcontext";

export default function Footer() {
  const { settings, navigation } = useSiteConfig();
  const [visitorStats, setVisitorStats] = useState([
    { label: "Hari Ini", value: "..." }, { label: "Kemarin", value: "..." }, { label: "Bulan Ini", value: "..." }, { label: "Total", value: "..." },
  ]);

  useEffect(() => {
    api.get("/visitor-stats").then(({ data }) => setVisitorStats([
      { label: "Hari Ini", value: data.hari_ini }, { label: "Kemarin", value: data.kemarin }, { label: "Bulan Ini", value: data.bulan_ini }, { label: "Total", value: data.total },
    ])).catch(() => {});
  }, []);

  const rootLinks = (navigation || []).filter((item) => item.url && item.url !== "#").slice(0, 6);

  return (
    <footer className="relative mt-10 overflow-hidden border-t border-white/10 text-white" style={{ background: "var(--color-primary-900)" }}>
      <div className="site-container grid gap-10 py-12 md:grid-cols-4">
        <div className="md:col-span-2">
          {settings?.logo_footer_url && <img src={settings.logo_footer_url} alt={settings?.site_name || "Logo"} className="mb-4 h-12 max-w-60 object-contain" />}
          <h3 className="text-xl font-black">{settings?.site_name}</h3>
          {settings?.site_description && <p className="mt-3 max-w-xl text-sm leading-7 text-white/65">{settings.site_description}</p>}
          <div className="mt-5 space-y-2 text-sm text-white/65">
            {settings?.address && <p className="flex gap-2"><MapPin size={16} className="mt-0.5 shrink-0" />{settings.address}</p>}
            {settings?.phone && <a className="flex gap-2 hover:text-white" href={`tel:${settings.phone}`}><Phone size={16} />{settings.phone}</a>}
            {settings?.email && <a className="flex gap-2 hover:text-white" href={`mailto:${settings.email}`}><Mail size={16} />{settings.email}</a>}
          </div>
        </div>

        <div>
          <h4 className="mb-4 text-xs font-black uppercase tracking-widest text-accent-300">Navigasi</h4>
          <div className="space-y-2">
            {rootLinks.map((item) => /^https?:\/\//i.test(item.url)
              ? <a key={item.id ?? item.label} href={item.url} target="_blank" rel="noreferrer" className="block text-sm text-white/65 hover:text-white">{item.label}</a>
              : <Link key={item.id ?? item.label} to={item.url} className="block text-sm text-white/65 hover:text-white">{item.label}</Link>)}
          </div>
        </div>

        <div>
          <h4 className="mb-4 flex items-center gap-2 text-xs font-black uppercase tracking-widest text-accent-300"><Users size={14} /> Pengunjung</h4>
          <div className="rounded-2xl border border-white/10 bg-white/5 p-4">
            {visitorStats.map((stat) => <div key={stat.label} className="flex justify-between border-b border-white/10 py-2 text-xs last:border-0"><span className="text-white/60">{stat.label}</span><strong>{stat.value}</strong></div>)}
          </div>
          <div className="mt-5 flex flex-wrap gap-3">
            {Object.entries(settings?.socials || {}).filter(([, url]) => url).map(([name, url]) => <a key={name} href={url} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 text-xs font-semibold uppercase text-white/60 hover:text-white">{name}<ExternalLink size={11}/></a>)}
          </div>
        </div>
      </div>
      <div className="border-t border-white/10 px-6 py-4 text-center text-[11px] font-semibold text-white/45">© {new Date().getFullYear()} — {settings?.footer_text || settings?.site_name}</div>
    </footer>
  );
}
