export default function NewsPage() {
  return (
    <div className="pt-40 pb-20 px-6 max-w-7xl mx-auto">
      {/* Search Bar & Filter */}
      <div className="bg-white p-6 rounded-[2rem] shadow-xl border border-accent-50 flex flex-wrap gap-4 items-center mb-12">
        <div className="flex-1 min-w-[300px] relative">
          <i className="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input type="text" placeholder="Cari judul artikel atau kategori..." className="w-full pl-12 pr-6 py-4 bg-slate-50 border-none rounded-2xl" />
        </div>
        <select className="bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold">
          <option>Tahun Artikel</option>
          <option>2026</option>
          <option>2025</option>
        </select>
        <select className="bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold">
          <option>Tampilkan: 5</option>
          <option>10</option>
          <option>20</option>
        </select>
      </div>

      <div className="grid grid-cols-1 gap-8">
        {[1, 2, 3].map(i => (
          <div key={i} className="bg-white group rounded-[2.5rem] overflow-hidden border border-slate-100 flex flex-col md:flex-row shadow-sm hover:shadow-2xl transition-all">
            <div className="md:w-72 h-64 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=400" className="w-full h-full object-cover group-hover:scale-110 transition duration-700" />
            </div>
            <div className="flex-1 p-8">
              <div className="flex items-center gap-4 mb-4">
                <span className="bg-accent-50 text-primary-700 px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest">Berita Daerah</span>
                <span className="text-slate-400 text-[10px]"><i className="bi bi-person mr-1"></i> Admin Diskominfo</span>
                <span className="text-slate-400 text-[10px]"><i className="bi bi-eye mr-1"></i> 1,402 views</span>
              </div>
              <h3 className="text-xl font-bold text-slate-800 mb-4 group-hover:text-primary transition leading-tight">Penandatanganan Kerja Sama Digitalisasi Pelayanan Publik Surakarta</h3>
              <p className="text-slate-500 text-xs leading-relaxed mb-6 line-clamp-2">Dinas Komunikasi, Informatika, Statistik dan Persandian Kota Surakarta melakukan langkah strategis dengan...</p>
              <span className="text-slate-300 text-[10px] font-medium">14 Juli 2026</span>
            </div>
          </div>
        ))}
      </div>

      <div className="mt-12 flex justify-between items-center px-6">
        <p className="text-xs text-slate-400 font-medium tracking-tight uppercase">Showing 1 to 5 of 108 results</p>
        <div className="flex gap-2">
           <button className="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/30"><i className="bi bi-1-square-fill"></i></button>
           <button className="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-accent-50 transition font-bold text-xs">2</button>
        </div>
      </div>
    </div>
  );
}