import { useEffect, useState } from "react";
import { ExternalLink, Mail, MapPin, Phone, Users } from "lucide-react";
import { Link } from "react-router-dom";
import { api } from "../../services/api";
import { useSiteConfig } from "../../context/siteconfigcontext";
import logoPemkot from "../../assets/footer/logo-pemkot.png";
import logoPpid from "../../assets/footer/logo-ppid.png";
import logoSoloData from "../../assets/footer/logo-solodata.png";

export default function Footer() {
  const { settings, navigation } = useSiteConfig();
  const [visitorStats, setVisitorStats] = useState([
    { label: "Hari Ini", value: "..." },
    { label: "Kemarin", value: "..." },
    { label: "Bulan Ini", value: "..." },
    { label: "Total", value: "..." },
  ]);

  useEffect(() => {
    api
      .get("/visitor-stats")
      .then(({ data }) =>
        setVisitorStats([
          { label: "Hari Ini", value: data.hari_ini },
          { label: "Kemarin", value: data.kemarin },
          { label: "Bulan Ini", value: data.bulan_ini },
          { label: "Total", value: data.total },
        ]),
      )
      .catch(() => {});
  }, []);

  const rootLinks = (navigation || []).filter((item) => item.url && item.url !== "#").slice(0, 6);
  const mapQuery = settings?.address || settings?.site_name || "DISKOMINFO SP Surakarta";
  const encodedMapQuery = encodeURIComponent(mapQuery);
  const mapEmbedUrl = `https://www.google.com/maps?q=${encodedMapQuery}&output=embed`;
  const mapOpenUrl = `https://www.google.com/maps/search/?api=1&query=${encodedMapQuery}`;

  const footerLogos = [
    { src: logoPemkot, alt: "Pemerintah Kota Surakarta" },
    { src: logoPpid, alt: "PPID Kota Surakarta" },
    { src: logoSoloData, alt: "Solo Data" },
  ];

  return (
    <footer
      className="relative isolate mt-10 overflow-hidden border-t border-white/10 text-white"
      style={{
        background:
          "linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 78%, #122400) 0%, color-mix(in srgb, var(--color-primary) 58%, #0f172a) 60%, #0f172a 100%)",
      }}
    >
      <div className="pointer-events-none absolute inset-0 z-0 overflow-hidden">
        <div className="absolute -top-16 left-0 h-56 w-56 rounded-full bg-white/10 blur-3xl" />
        <div className="absolute bottom-0 right-0 h-72 w-72 rounded-full bg-lime-300/10 blur-3xl" />
        <div className="absolute inset-x-0 top-0 h-px bg-white/20" />
      </div>

      <style>{`
        @keyframes footer-logo-marquee {
          from { transform: translate3d(0, 0, 0); }
          to { transform: translate3d(-50%, 0, 0); }
        }
        .footer-logo-marquee {
          width: 100%;
          overflow: hidden;
          mask-image: linear-gradient(to right, transparent 0, black 3%, black 97%, transparent 100%);
          -webkit-mask-image: linear-gradient(to right, transparent 0, black 3%, black 97%, transparent 100%);
        }
        .footer-logo-marquee-track {
          display: flex;
          width: max-content;
          min-width: 200%;
          animation: footer-logo-marquee 22s linear infinite;
          will-change: transform;
        }
        .footer-logo-marquee:hover .footer-logo-marquee-track {
          animation-play-state: paused;
        }
        @media (prefers-reduced-motion: reduce) {
          .footer-logo-marquee-track { animation: none; }
        }
      `}</style>

      <div className="relative z-10 border-b border-white/10 bg-white/[0.03] py-4">
        <div className="footer-logo-marquee" aria-label="Tautan dan identitas layanan terkait">
          <div className="footer-logo-marquee-track">
            {[0, 1].map((groupIndex) => (
              <div key={groupIndex} className="flex shrink-0 items-center gap-12 px-8 md:gap-16 md:px-12" aria-hidden={groupIndex === 1}>
                {footerLogos.map((logo, index) => (
                  <img
                    key={`${groupIndex}-${logo.alt}-${index}`}
                    src={logo.src}
                    alt={groupIndex === 0 ? logo.alt : ""}
                    className="h-12 w-auto shrink-0 object-contain opacity-95 drop-shadow-[0_2px_10px_rgba(0,0,0,0.18)] md:h-14"
                  />
                ))}
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="relative z-10 mx-auto grid max-w-7xl gap-8 px-6 py-12 md:grid-cols-2 lg:grid-cols-12 lg:gap-7">
        <div className="md:col-span-2 lg:col-span-3">
          <div className="mb-4 flex items-center justify-between gap-3">
            <h4 className="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-accent-300">
              <MapPin size={14} /> Lokasi Instansi
            </h4>
            <a
              href={mapOpenUrl}
              target="_blank"
              rel="noreferrer"
              className="inline-flex items-center gap-1 text-[11px] font-semibold text-white/60 transition hover:text-white"
            >
              Buka Maps <ExternalLink size={11} />
            </a>
          </div>

          <div className="overflow-hidden rounded-2xl border border-white/15 bg-white/10 p-1.5 shadow-lg shadow-black/10 backdrop-blur-sm">
            <iframe
              src={mapEmbedUrl}
              title={`Lokasi ${settings?.site_name || "instansi"}`}
              className="h-52 w-full rounded-xl border-0 md:h-56 lg:h-52"
              loading="lazy"
              allowFullScreen
              referrerPolicy="no-referrer-when-downgrade"
            />
          </div>
          {settings?.address && <p className="mt-3 line-clamp-2 text-xs leading-5 text-white/55">{settings.address}</p>}
        </div>

        <div className="md:col-span-2 lg:col-span-5">
          <h3 className="text-xl font-black">{settings?.site_name}</h3>
          {settings?.site_description && <p className="mt-3 max-w-xl text-sm leading-7 text-white/65">{settings.site_description}</p>}
          <div className="mt-5 space-y-2 text-sm text-white/65">
            {settings?.address && <p className="flex gap-2"><MapPin size={16} className="mt-0.5 shrink-0" />{settings.address}</p>}
            {settings?.phone && <a className="flex gap-2 hover:text-white" href={`tel:${settings.phone}`}><Phone size={16} />{settings.phone}</a>}
            {settings?.email && <a className="flex gap-2 hover:text-white" href={`mailto:${settings.email}`}><Mail size={16} />{settings.email}</a>}
          </div>
        </div>

        <div className="lg:col-span-2">
          <h4 className="mb-4 text-xs font-black uppercase tracking-widest text-accent-300">Navigasi</h4>
          <div className="space-y-2">
            {rootLinks.map((item) => /^https?:\/\//i.test(item.url)
              ? <a key={item.id ?? item.label} href={item.url} target="_blank" rel="noreferrer" className="block text-sm text-white/70 transition hover:translate-x-1 hover:text-white">{item.label}</a>
              : <Link key={item.id ?? item.label} to={item.url} className="block text-sm text-white/70 transition hover:translate-x-1 hover:text-white">{item.label}</Link>)}
          </div>
        </div>

        <div className="lg:col-span-2">
          <h4 className="mb-4 flex items-center gap-2 text-xs font-black uppercase tracking-widest text-accent-300"><Users size={14} /> Pengunjung</h4>
          <div className="rounded-2xl border border-white/10 bg-white/10 p-4 shadow-lg shadow-black/10 backdrop-blur-sm">
            {visitorStats.map((stat) => <div key={stat.label} className="flex justify-between border-b border-white/10 py-2 text-xs last:border-0"><span className="text-white/60">{stat.label}</span><strong>{stat.value}</strong></div>)}
          </div>
          <div className="mt-5 flex flex-wrap gap-3">
            {Object.entries(settings?.socials || {}).filter(([, url]) => url).map(([name, url]) => <a key={name} href={url} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 text-xs font-semibold uppercase text-white/60 hover:text-white">{name}<ExternalLink size={11}/></a>)}
          </div>
        </div>
      </div>

      <div className="relative z-10 border-t border-white/10 px-6 py-4 text-center text-[11px] font-semibold text-white/45">© {new Date().getFullYear()} — {settings?.footer_text || settings?.site_name}</div>
    </footer>
  );
}
