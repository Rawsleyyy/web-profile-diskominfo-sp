import React, { useState, useEffect, useMemo } from 'react';
import { Search, User, Eye, Calendar, ChevronRight, FileText, Newspaper } from 'lucide-react';
import { Link } from 'react-router-dom';
import { api, BASE_URL } from '../services/api';

export default function ArtikelList() {
  // Tab aktif: 'berita' (Informasi Terkini) atau 'artikel' (Artikel/Publikasi)
  const [activeTab, setActiveTab] = useState('berita');

  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [searchTerm, setSearchTerm] = useState('');
  const [filterYear, setFilterYear] = useState('Semua Tahun');
  const [itemsPerPage, setItemsPerPage] = useState(5);
  const [currentPage, setCurrentPage] = useState(1);

  // Fetch ulang setiap kali tab berpindah
  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        setError(null);
        const endpoint = activeTab === 'berita' ? '/berita' : '/articles';
        const response = await api.get(endpoint);
        const data = response.data.data || response.data || [];
        setItems(data);
      } catch (err) {
        console.error('Gagal memuat data:', err);
        setError(activeTab === 'berita' ? 'Gagal mengambil data berita dari server.' : 'Gagal mengambil data artikel dari server.');
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, [activeTab]);

  // Reset filter tiap ganti tab/pencarian
  useEffect(() => {
    setCurrentPage(1);
  }, [searchTerm, filterYear, itemsPerPage, activeTab]);

  const filteredItems = useMemo(() => {
    return items.filter((item) => {
      const title = item.judul || item.title || '';
      const matchesSearch = title.toLowerCase().includes(searchTerm.toLowerCase());

      const rawDate = item.created_at || item.published_at || '';
      const itemYear = rawDate ? new Date(rawDate).getFullYear().toString() : '';
      const matchesYear = filterYear === 'Semua Tahun' || itemYear === filterYear;

      return matchesSearch && matchesYear;
    });
  }, [items, searchTerm, filterYear]);

  const indexOfLastItem = currentPage * itemsPerPage;
  const indexOfFirstItem = indexOfLastItem - itemsPerPage;
  const currentItems = filteredItems.slice(indexOfFirstItem, indexOfLastItem);
  const totalResults = filteredItems.length;
  const totalPages = Math.ceil(totalResults / itemsPerPage);

  const getImageUrl = (item) => {
    const imgPath = item.gambar || item.thumbnail || item.image;
    if (!imgPath) return null;
    if (imgPath.startsWith('http')) return imgPath;
    return `${BASE_URL}/storage/${imgPath}`;
  };

  const formatDate = (dateString) => {
    if (!dateString) return 'Terbaru';
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? dateString : date.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
  };

  const detailLink = (item) => (activeTab === 'berita' ? `/artikel/${item.id}` : `/publikasi/${item.id}`);

  return (
    <div className="pt-44 pb-20 px-6 max-w-7xl mx-auto font-sans min-h-screen">
      {/* HEADER SECTION */}
      <div className="mb-8 text-center md:text-left">
        <h2 className="text-4xl font-black text-slate-800 tracking-tighter uppercase leading-none">Informasi & Artikel</h2>
        <p className="text-slate-400 mt-3 font-medium">Pusat edukasi dan kabar terkini seputar Kota Surakarta</p>
      </div>

      {/* TAB SWITCHER */}
      <div className="flex gap-2 mb-8 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm w-fit">
        <button
          onClick={() => setActiveTab('berita')}
          className={`flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-black uppercase tracking-wide transition-all ${
            activeTab === 'berita' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-400 hover:text-primary-700'
          }`}
        >
          <Newspaper size={16} /> Informasi Terkini
        </button>
        <button
          onClick={() => setActiveTab('artikel')}
          className={`flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-black uppercase tracking-wide transition-all ${
            activeTab === 'artikel' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-400 hover:text-primary-700'
          }`}
        >
          <FileText size={16} /> Artikel
        </button>
      </div>

      {/* FILTER BAR */}
      <div className="bg-white p-5 rounded-[2.5rem] shadow-2xl shadow-slate-200 border border-slate-100 flex flex-wrap gap-4 items-center mb-12">
        <div className="flex-1 min-w-[280px] relative group">
          <Search size={18} className="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-700 transition-colors" />
          <input
            type="text"
            placeholder={activeTab === 'berita' ? 'Cari judul berita...' : 'Cari judul artikel...'}
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 transition-all outline-none"
          />
        </div>

        <select
          value={filterYear}
          onChange={(e) => setFilterYear(e.target.value)}
          className="bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-black uppercase text-slate-600 focus:ring-2 focus:ring-primary/20 cursor-pointer outline-none"
        >
          <option>Semua Tahun</option>
          <option>2026</option>
          <option>2025</option>
          <option>2024</option>
        </select>

        <select
          value={itemsPerPage}
          onChange={(e) => setItemsPerPage(Number(e.target.value))}
          className="bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-black uppercase text-slate-600 focus:ring-2 focus:ring-primary/20 cursor-pointer outline-none"
        >
          <option value={5}>Tampilkan 5</option>
          <option value={10}>Tampilkan 10</option>
          <option value={20}>Tampilkan 20</option>
        </select>
      </div>

      {/* LOADING / ERROR */}
      {loading ? (
        <div className="py-20 text-center bg-white rounded-[3rem] border border-slate-100 shadow-sm">
          <div className="inline-block animate-spin rounded-full h-10 w-10 border-4 border-primary border-t-transparent mb-4"></div>
          <p className="text-slate-500 font-bold">Memuat data dari server...</p>
        </div>
      ) : error ? (
        <div className="py-20 text-center bg-red-50 rounded-[3rem] border border-red-100 text-red-600 font-bold">
          {error}
        </div>
      ) : (
        <div className="grid grid-cols-1 gap-8">
          {currentItems.length > 0 ? (
            currentItems.map((item) => {
              const imageUrl = getImageUrl(item);
              return (
                <div key={item.id} className="bg-white p-3 rounded-[3rem] border border-slate-100 flex flex-col md:flex-row hover:shadow-2xl transition-all duration-500 group">
                  {/* Thumbnail: kalau tidak ada gambar (Artikel), tampilkan ikon dokumen */}
                  <div className="md:w-80 h-60 bg-slate-100 rounded-[2.5rem] overflow-hidden shrink-0 relative flex items-center justify-center">
                    {imageUrl ? (
                      <img
                        src={imageUrl}
                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                        alt={item.judul || item.title}
                      />
                    ) : (
                      <div className="w-20 h-20 rounded-3xl bg-primary-50 flex items-center justify-center text-primary-700">
                        <FileText size={32} />
                      </div>
                    )}
                  </div>

                  <div className="flex-1 p-6 md:p-8 flex flex-col justify-between">
                    <div>
                      <div className="flex flex-wrap gap-5 items-center text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">
                        {activeTab === 'artikel' && item.category && (
                          <span className="bg-primary/10 text-primary px-3 py-1 rounded-full">{item.category}</span>
                        )}
                        <span className="flex items-center gap-2">
                          <User size={13} className="text-primary-700" />
                          {item.penulis || item.author?.name || item.author || 'Admin Diskominfo'}
                        </span>
                        {activeTab === 'berita' && (
                          <span className="flex items-center gap-2">
                            <Eye size={13} className="text-primary-700" />
                            {item.views || 0} Dilihat
                          </span>
                        )}
                      </div>
                      <h3 className="text-xl md:text-2xl font-black text-slate-800 group-hover:text-primary-700 transition-colors leading-tight mb-4">
                        {item.judul || item.title}
                      </h3>
                    </div>

                    <div className="flex justify-between items-center pt-6 border-t border-slate-50">
                      <span className="flex items-center gap-2 text-[11px] font-bold text-slate-400">
                        <Calendar size={13} /> {formatDate(item.created_at || item.published_at)}
                      </span>
                      <Link
                        to={detailLink(item)}
                        className="flex items-center gap-2 bg-slate-50 text-primary-700 px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-tighter hover:bg-primary hover:text-white transition-all shadow-sm"
                      >
                        Baca Selengkapnya <ChevronRight size={12} />
                      </Link>
                    </div>
                  </div>
                </div>
              );
            })
          ) : (
            <div className="py-20 text-center bg-white rounded-[3rem] border border-dashed border-slate-200">
              <p className="text-slate-400 font-bold italic">
                {activeTab === 'berita' ? 'Belum ada berita yang dipublikasikan.' : 'Belum ada artikel yang dipublikasikan.'}
              </p>
            </div>
          )}
        </div>
      )}

      {/* PAGINATION */}
      {!loading && !error && totalResults > 0 && (
        <div className="mt-16 flex flex-col md:flex-row justify-between items-center gap-6 border-t border-slate-100 pt-10">
          <p className="text-[11px] font-black text-slate-400 uppercase tracking-widest">
            Showing {indexOfFirstItem + 1} to {Math.min(indexOfLastItem, totalResults)} of {totalResults} results
          </p>

          <div className="flex gap-2">
            {Array.from({ length: totalPages }, (_, index) => index + 1).map((pageNum) => (
              <button
                key={pageNum}
                onClick={() => setCurrentPage(pageNum)}
                className={`w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs transition-all ${
                  currentPage === pageNum
                    ? 'bg-primary text-white shadow-lg shadow-primary/20'
                    : 'bg-white border border-slate-100 text-slate-400 hover:bg-primary hover:text-white'
                }`}
              >
                {pageNum}
              </button>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
