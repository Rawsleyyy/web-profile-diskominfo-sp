import React, { useState } from 'react';
import { FileText, Download, Search, FileType, ExternalLink } from 'lucide-react';
import { ppidDocuments } from '../../data';

export default function UnduhanSection() {
  const [filter, setFilter] = useState("Semua");
  
  // Ambil kategori unik dari data
  const categories = ["Semua", ...new Set(ppidDocuments.map(doc => doc.kategori))];

  const filteredDocs = filter === "Semua" 
    ? ppidDocuments 
    : ppidDocuments.filter(doc => doc.kategori === filter);

  return (
    <section className="py-24 bg-[#f4f9ff] font-sans">
      <div className="max-w-7xl mx-auto px-6">
        
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
          <div>
            <div className="inline-flex items-center gap-2 bg-white text-primary px-4 py-1.5 rounded-full mb-4 font-bold text-[10px] uppercase border border-slate-100 shadow-sm">
              <FileText size={12} /> Open Data & Transparansi
            </div>
            <h2 className="text-4xl font-black text-slate-800 tracking-tighter uppercase">Dokumen Publik</h2>
            <p className="text-slate-500 font-medium text-sm mt-2">Akses dokumen perencanaan, laporan keuangan, dan informasi publik lainnya.</p>
          </div>

          {/* Filter Kategori */}
          <div className="flex flex-wrap gap-2">
            {categories.map(cat => (
              <button
                key={cat}
                onClick={() => setFilter(cat)}
                className={`px-5 py-2 rounded-xl text-[11px] font-black uppercase transition-all ${
                  filter === cat 
                  ? "bg-primary text-white shadow-lg shadow-primary/30" 
                  : "bg-white text-slate-400 hover:bg-slate-50 border border-slate-100"
                }`}
              >
                {cat}
              </button>
            ))}
          </div>
        </div>

        {/* Grid Dokumen */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {filteredDocs.map((doc) => (
            <div 
              key={doc.id} 
              className="group bg-white p-6 rounded-[2rem] border border-slate-100 flex items-center justify-between hover:shadow-xl hover:shadow-primary/5 transition-all duration-300"
            >
              <div className="flex items-center gap-5">
                <div className="w-14 h-14 rounded-2xl bg-slate-50 flex flex-col items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-500">
                  <FileType size={20} />
                  <span className="text-[8px] font-black mt-1 uppercase">{doc.format}</span>
                </div>
                <div>
                  <h4 className="font-bold text-slate-800 text-sm leading-tight mb-1 group-hover:text-primary transition-colors">
                    {doc.judul}
                  </h4>
                  <div className="flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    <span>{doc.kategori}</span>
                    <span className="w-1 h-1 bg-slate-200 rounded-full"></span>
                    <span>{doc.ukuran}</span>
                  </div>
                </div>
              </div>

              <button className="p-4 bg-slate-50 text-slate-400 rounded-2xl hover:bg-primary hover:text-white transition-all">
                <Download size={18} />
              </button>
            </div>
          ))}
        </div>

        {/* Kominpod (Podcast/Media) Promo Card */}
        <div className="mt-16 bg-[#1e4f92] rounded-[3rem] p-8 md:p-12 text-white relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
          <div className="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
          
          <div className="relative z-10 text-center md:text-left">
            <h3 className="text-2xl font-black uppercase tracking-tighter mb-2">Kominpod Surakarta</h3>
            <p className="text-blue-100/70 text-sm font-medium max-w-md leading-relaxed">
              Dengarkan diskusi menarik seputar teknologi dan informasi bersama narasumber ahli hanya di Podcast Diskominfo SP.
            </p>
          </div>

          <a 
            href="https://youtube.com/@diskominfo_solo" 
            target="_blank"
            className="relative z-10 bg-white text-primary px-8 py-4 rounded-full font-black text-xs uppercase tracking-widest flex items-center gap-3 hover:scale-105 transition-transform shadow-xl"
          >
            Tonton Sekarang <ExternalLink size={16} />
          </a>
        </div>
      </div>
    </section>
  );
}