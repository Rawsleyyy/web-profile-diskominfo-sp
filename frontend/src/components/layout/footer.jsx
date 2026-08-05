import React, { useState, useEffect } from "react";
import { MapPin, Info, ExternalLink, Users, ChevronRight } from "lucide-react";
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
      if (response.data) {
        setVisitorStats([
          { label: "Hari Ini", value: response.data.hari_ini },
          { label: "Kemarin", value: response.data.kemarin },
          { label: "Bulan Ini", value: response.data.bulan_ini },
          { label: "Total", value: response.data.total },
        ]);
      }
    })
    .catch((error) => {
      console.error("Gagal mengambil data statistik:", error);
    });
}, []);

  const orgsData = [
    { name: "PPID Kota Surakarta", sub: "Pejabat Pengelola Informasi & Dokumentasi", img: logoPPID },
    { name: "SoloData", sub: "Portal Data Terbuka", img: logoSoloData },
    { name: "Pemerintah Kota Surakarta", sub: "Kota Bengawan", img: logoPemkot },
  ];

  const duplicatedOrgs = [...orgsData, ...orgsData, ...orgsData, ...orgsData, ...orgsData];

  return (
    <footer
      className="relative border-t border-white/[0.1] mt-10 font-sans overflow-hidden"
      style={{ background: "var(--color-primary-900)" }}
    >
      <style>
        {`
          @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
          }
          .animate-marquee {
            animation: marquee 25s linear infinite;
          }
        `}
      </style>

      {/* ========================================================
          ELEMEN BATIK RANDOM TERSEBAR (Hanya Estetika Background) 
          ======================================================== */}
      <div className="absolute inset-0 pointer-events-none z-0">
        <div className="absolute -top-24 left-1/4 w-96 h-96 rounded-full bg-accent/10 blur-3xl" />
        <div className="absolute -bottom-24 right-1/4 w-96 h-96 rounded-full bg-primary-400/10 blur-3xl" />
        
        {/* Ornamen Batik 1 */}
        <svg className="absolute top-10 left-10 w-40 h-40 text-white opacity-[0.03] rotate-12" viewBox="0 0 100 100" fill="currentColor">
          <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
          <circle cx="50" cy="50" r="10" fill="transparent" stroke="currentColor" strokeWidth="4"/>
        </svg>
        {/* Ornamen Batik 2 */}
        <svg className="absolute top-1/3 right-10 w-24 h-24 text-white opacity-[0.02] -rotate-45" viewBox="0 0 100 100" fill="currentColor">
          <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
        </svg>
        {/* Ornamen Batik 3 */}
        <svg className="absolute bottom-10 left-1/3 w-32 h-32 text-white opacity-[0.04] rotate-45" viewBox="0 0 100 100" fill="currentColor">
          <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
          <circle cx="50" cy="50" r="12" fill="transparent" stroke="currentColor" strokeWidth="3"/>
        </svg>
        {/* Ornamen Batik 4 */}
        <svg className="absolute bottom-20 right-1/3 w-48 h-48 text-white opacity-[0.02] rotate-90" viewBox="0 0 100 100" fill="currentColor">
          <path d="M50,0 C60,30 80,40 100,50 C80,60 60,70 50,100 C40,70 20,60 0,50 C20,40 40,30 50,0 Z" />
        </svg>
      </div>

      <div className="relative border-b border-white/[0.08] py-4 backdrop-blur-sm flex overflow-hidden z-10">
        <div className="flex w-max animate-marquee hover:[animation-play-state:paused] items-center">
          {duplicatedOrgs.map((org, idx) => (
            <div key={idx} className="flex items-center gap-4 group cursor-pointer px-8 md:px-12 shrink-0">
              <div className="flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                <img 
                  src={org.img} 
                  alt={org.name} 
                  className="h-10 md:h-12 w-auto object-contain" 
                />
              </div>
              <div>
                <p className="text-white font-extrabold text-sm leading-tight group-hover:text-accent-300 transition-colors whitespace-nowrap">
                  {org.name}
                </p>
                <p className="text-accent-300/60 text-[10px] font-medium uppercase tracking-tighter mt-0.5 whitespace-nowrap">
                  {org.sub}
                </p>
              </div>
            </div>
          ))}
        </div>
      </div>

      <div className="relative px-6 py-8 max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 z-10">
        <div>
          <h4 className="text-accent-300 font-black text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
            <MapPin size={14} strokeWidth={3} /> Lokasi
          </h4>
          <div className="rounded-2xl overflow-hidden border border-white/[0.12] backdrop-blur-md mb-3 shadow-2xl aspect-video relative group">
            <iframe
              title="Lokasi Diskominfo SP"
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.123!2d110.8265!3d-7.5558!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a168636a0d0d1%3A0x6b1f2382e2136e05!2sBalaikota%20Surakarta!5e0!3m2!1sen!2sid!4v1700000000000"
              width="100%"
              height="100%"
              style={{ border: 0 }}
              loading="lazy"
              className="grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700"
            />
          </div>
          <p className="text-accent-300/65 text-[11px] leading-relaxed font-medium">
            Gedung Bale Upakari Lantai 3, Jl. Jenderal Sudirman No. 2,
            Kompleks Balaikota Surakarta 57133
          </p>
        </div>

        <div>
          <h4 className="text-accent-300 font-black text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
            <Info size={14} strokeWidth={3} /> Informasi Publik
          </h4>
          <div className="flex flex-col gap-1">
            {["Informasi Berkala", "Informasi Setiap Saat", "Informasi Serta Merta", "Informasi Dikecualikan"].map((item) => (
              <a key={item} href="#" className="flex items-center gap-2 text-accent-300/65 text-xs py-1.5 hover:text-white transition-all group border-b border-white/5 last:border-0">
                <ChevronRight size={10} className="text-accent-300/50 group-hover:translate-x-1 transition-transform flex-shrink-0" />
                <span className="font-bold tracking-tight">{item}</span>
              </a>
            ))}
          </div>
        </div>

        <div>
          <h4 className="text-accent-300 font-black text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
            <ExternalLink size={14} strokeWidth={3} /> Link Terkait
          </h4>
          <div className="flex flex-col gap-1">
            {["Pemerintah Kota Surakarta", "PPID Kota Surakarta", "Solo Data", "Kominfo RI"].map((item) => (
              <a key={item} href="#" className="flex items-center gap-2 text-accent-300/65 text-xs py-1.5 hover:text-white transition-all group border-b border-white/5 last:border-0">
                <ChevronRight size={10} className="text-accent-300/50 group-hover:translate-x-1 transition-transform flex-shrink-0" />
                <span className="font-bold tracking-tight">{item}</span>
              </a>
            ))}
          </div>
        </div>

        <div>
          <h4 className="text-accent-300 font-black text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
            <Users size={14} strokeWidth={3} /> Pengunjung
          </h4>
          <div className="bg-white/[0.06] backdrop-blur-md rounded-2xl p-3 border border-white/10">
            {visitorStats.map((stat) => (
              <div
                key={stat.label}
                className="flex items-center justify-between py-1.5 border-b border-white/[0.07] last:border-0"
              >
                <span className="text-accent-300/65 text-[11px] font-bold uppercase tracking-tight">{stat.label}</span>
                <span className="text-white font-black text-sm tabular-nums tracking-tighter">
                  {stat.value}
                </span>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="relative border-t border-white/[0.07] px-6 py-4 text-center sm:text-left z-10">
        <div className="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-accent-300/40 text-[10px] font-bold uppercase tracking-[0.15em]">
          <p>© 2026 — Pemerintah Kota Surakarta. Hak cipta dilindungi undang-undang.</p>
          <p className="text-accent-300/60 uppercase">Dinas Komunikasi Informatika dan Persandian Kota Surakarta</p>
        </div>
      </div>
    </footer>
  );
}