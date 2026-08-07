import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import {
  MapPin,
  Info,
  ExternalLink,
  Users,
  ChevronRight,
} from "lucide-react";
import { api } from "../../services/api";

import logoPPID from "../../assets/footer/logo-ppid.png";
import logoSoloData from "../../assets/footer/logo-solodata.png";
import logoPemkot from "../../assets/footer/logo-pemkot.png";

export default function Footer() {
  const [visitorStats, setVisitorStats] = useState([
    { label: "Hari Ini", value: "..." },
    { label: "Kemarin", value: "..." },
    { label: "Bulan Ini", value: "..." },
    { label: "Total", value: "..." },
  ]);

  useEffect(() => {
    api
      .get("/visitor-stats")
      .then((response) => {
        if (!response.data) return;

        setVisitorStats([
          { label: "Hari Ini", value: response.data.hari_ini ?? 0 },
          { label: "Kemarin", value: response.data.kemarin ?? 0 },
          { label: "Bulan Ini", value: response.data.bulan_ini ?? 0 },
          { label: "Total", value: response.data.total ?? 0 },
        ]);
      })
      .catch((error) => {
        console.error("Gagal mengambil data statistik:", error);
      });
  }, []);

  const organizations = [
    {
      name: "PPID Kota Surakarta",
      sub: "Pejabat Pengelola Informasi dan Dokumentasi",
      img: logoPPID,
    },
    {
      name: "SoloData",
      sub: "Portal Data Terbuka",
      img: logoSoloData,
    },
    {
      name: "Pemerintah Kota Surakarta",
      sub: "Kota Bengawan",
      img: logoPemkot,
    },
  ];

  const marqueeItems = [...organizations, ...organizations];

  const publicInformation = [
    { label: "Informasi Berkala", to: "/ppid" },
    { label: "Informasi Setiap Saat", to: "/ppid" },
    { label: "Informasi Serta Merta", to: "/ppid" },
    { label: "Informasi Dikecualikan", to: "/ppid" },
  ];

  const relatedLinks = [
    { label: "Pemerintah Kota Surakarta", href: "https://surakarta.go.id" },
    { label: "PPID Kota Surakarta", href: "https://ppid.surakarta.go.id" },
    { label: "Solo Data", href: "https://data.surakarta.go.id" },
    { label: "Kominfo RI", href: "https://kominfo.go.id" },
  ];

  const headingClass =
    "mb-5 flex items-center gap-2.5 text-xs font-black uppercase tracking-[0.18em] text-white";

  const linkClass =
    "group flex items-center gap-2 py-2 text-sm font-medium text-slate-300 transition-colors hover:text-white";

  return (
    <footer
      className="relative mt-16 overflow-hidden border-t text-white"
      style={{
        background:
          "linear-gradient(90deg, #1b2f5b 0%, #1f4e8c 45%, #2479b8 78%, #2ca8df 100%)",
        borderTop: "1px solid rgba(255,255,255,0.10)",
      }}
      >   
    
      <style>
        {`
          @keyframes footerMarquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
          }

          .footer-marquee {
            animation: footerMarquee 26s linear infinite;
          }

          @media (prefers-reduced-motion: reduce) {
            .footer-marquee { animation: none; }
          }
        `}
      </style>

      {/* Ornamen latar dibuat sangat tipis agar tidak mengganggu konten. */}
      <div className="pointer-events-none absolute inset-0 overflow-hidden">
        <div className="absolute -left-28 top-20 h-72 w-72 rounded-full bg-white/[0.035] blur-3xl" />
        <div className="absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-cyan-300/[0.045] blur-3xl" />
        <svg
          className="absolute bottom-10 left-[38%] h-44 w-44 text-white opacity-[0.025]"
          viewBox="0 0 100 100"
          fill="currentColor"
          aria-hidden="true"
        >
          <path d="M50 0C60 30 80 40 100 50C80 60 60 70 50 100C40 70 20 60 0 50C20 40 40 30 50 0Z" />
        </svg>
      </div>

      {/* Bar identitas mitra: tetap bergaya lama, tetapi tanpa kartu. */}
      <div className="relative border-b border-white/10 bg-black/10">
        <div className="overflow-hidden py-5">
          <div className="footer-marquee flex w-max items-center hover:[animation-play-state:paused]">
            {marqueeItems.map((organization, index) => (
              <div
                key={`${organization.name}-${index}`}
                className="flex min-w-[330px] shrink-0 items-center gap-4 px-8 md:min-w-[390px] md:px-12"
              >
                <img
                  src={organization.img}
                  alt={organization.name}
                  className="h-11 w-auto max-w-[120px] object-contain md:h-13"
                />

                <div className="h-9 w-px bg-white/15" />

                <div className="min-w-0">
                  <p className="truncate text-sm font-extrabold text-white md:text-base">
                    {organization.name}
                  </p>
                  <p className="mt-0.5 truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400 md:text-[11px]">
                    {organization.sub}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Hanya satu pembungkus utama. Kolom dipisahkan garis, bukan card. */}
      <div className="relative mx-auto max-w-7xl px-6 py-11 lg:px-8">
        <div className="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-0">
          <section className="lg:pr-8">
            <h4 className={headingClass}>
              <MapPin size={16} className="text-cyan-300" />
              Lokasi
            </h4>

            <div className="overflow-hidden rounded-2xl border border-white/10 bg-black/20">
              <div className="aspect-[16/10]">
                <iframe
                  title="Lokasi Diskominfo SP"
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.123!2d110.8265!3d-7.5558!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a168636a0d0d1%3A0x6b1f2382e2136e05!2sBalaikota%20Surakarta!5e0!3m2!1sen!2sid!4v1700000000000"
                  width="100%"
                  height="100%"
                  style={{ border: 0 }}
                  loading="lazy"
                  className="h-full w-full opacity-85 grayscale-[20%] transition duration-500 hover:opacity-100 hover:grayscale-0"
                />
              </div>
            </div>

            <p className="mt-4 text-sm leading-6 text-slate-300">
              Gedung Bale Upakari Lantai 3, Jl. Jenderal Sudirman No. 2,
              Kompleks Balaikota Surakarta 57133.
            </p>
          </section>

          <section className="lg:border-l lg:border-white/10 lg:px-8">
            <h4 className={headingClass}>
              <Info size={16} className="text-cyan-300" />
              Informasi Publik
            </h4>

            <nav className="divide-y divide-white/[0.07]">
              {publicInformation.map((item) => (
                <Link key={item.label} to={item.to} className={linkClass}>
                  <ChevronRight
                    size={14}
                    className="shrink-0 text-cyan-300 transition-transform group-hover:translate-x-1"
                  />
                  {item.label}
                </Link>
              ))}
            </nav>
          </section>

          <section className="lg:border-l lg:border-white/10 lg:px-8">
            <h4 className={headingClass}>
              <ExternalLink size={16} className="text-cyan-300" />
              Link Terkait
            </h4>

            <nav className="divide-y divide-white/[0.07]">
              {relatedLinks.map((item) => (
                <a
                  key={item.label}
                  href={item.href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className={linkClass}
                >
                  <ChevronRight
                    size={14}
                    className="shrink-0 text-cyan-300 transition-transform group-hover:translate-x-1"
                  />
                  {item.label}
                </a>
              ))}
            </nav>
          </section>

          <section className="lg:border-l lg:border-white/10 lg:pl-8">
            <h4 className={headingClass}>
              <Users size={16} className="text-cyan-300" />
              Pengunjung
            </h4>

            <div className="divide-y divide-white/[0.08]">
              {visitorStats.map((stat) => (
                <div
                  key={stat.label}
                  className="flex items-center justify-between py-3 first:pt-0"
                >
                  <span className="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">
                    {stat.label}
                  </span>
                  <span className="text-lg font-black tabular-nums text-white">
                    {stat.value}
                  </span>
                </div>
              ))}
            </div>
          </section>
        </div>
      </div>

      <div className="relative border-t border-white/10 bg-black/15">
        <div className="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-5 text-center sm:flex-row sm:text-left lg:px-8">
          <p className="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">
            © 2026 Pemerintah Kota Surakarta. Hak cipta dilindungi undang-undang.
          </p>
          <p className="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">
            Dinas Komunikasi Informatika dan Persandian Kota Surakarta
          </p>
        </div>
      </div>
    </footer>
  );
}
