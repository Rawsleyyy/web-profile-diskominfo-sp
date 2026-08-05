import { Mic, BarChart3, Video, PieChart, ChevronRight } from 'lucide-react';
import { infografisMedia, galeriFoto } from '../../data';

const iconMap = { Mic, BarChart3, Video, PieChart };

export default function InfografisSection() {
  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12">

        {/* Kolom Kiri — List Media & Infografis */}
        <div>
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-3xl font-black text-slate-800 tracking-tighter">Media & Infografis</h2>
            <a href="#" className="text-primary text-xs font-bold flex items-center gap-1 hover:gap-2 transition-all">
              Lihat Semua <ChevronRight size={14} />
            </a>
          </div>
          <div className="space-y-3">
            {infografisMedia.map((item) => {
              const Icon = iconMap[item.icon];
              return (
                <div
                  key={item.id}
                  className="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 hover:border-primary/20 hover:bg-accent-50/50 transition-all cursor-pointer group"
                >
                  <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 ${item.color}`}>
                    <Icon size={20} />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">{item.tipe}</p>
                    <h4 className="text-sm font-bold text-slate-700 leading-snug truncate group-hover:text-primary transition">
                      {item.judul}
                    </h4>
                  </div>
                  <span className="text-[10px] font-bold text-slate-400 shrink-0">{item.durasi}</span>
                </div>
              );
            })}
          </div>
        </div>

        {/* Kolom Kanan — Galeri Foto Kegiatan */}
        <div>
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-3xl font-black text-slate-800 tracking-tighter">Galeri Kegiatan</h2>
            <a href="#" className="text-primary text-xs font-bold flex items-center gap-1 hover:gap-2 transition-all">
              Lihat Semua <ChevronRight size={14} />
            </a>
          </div>
          <div className="space-y-4">
            {galeriFoto.map((item) => (
              <div
                key={item.id}
                className="flex items-center gap-4 p-3 rounded-2xl hover:bg-slate-50 transition-all cursor-pointer group"
              >
                <div className="w-20 h-16 rounded-xl overflow-hidden shrink-0">
                  <img
                    src={item.image}
                    alt={item.judul}
                    className="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                  />
                </div>
                <div className="min-w-0">
                  <h4 className="text-sm font-bold text-slate-700 leading-snug line-clamp-2 group-hover:text-primary transition">
                    {item.judul}
                  </h4>
                  <p className="text-[10px] font-bold text-slate-400 mt-1">{item.tanggal}</p>
                </div>
              </div>
            ))}
          </div>
        </div>

      </div>
    </section>
  );
}