import React from "react";
import { 
  ClipboardList, 
  Newspaper, 
  Download, 
  Settings, 
  ArrowRight, 
  LayoutDashboard,
  LogOut,
  UserCircle
} from "lucide-react";
import { Link, useNavigate } from "react-router-dom";

export default function KelolaLayanan() {
  const navigate = useNavigate();

  // Data Menu Dashboard Admin
  const adminMenus = [
    {
      title: "Survei Kepuasan (SKM)",
      desc: "Ubah pertanyaan, lihat hasil skor, dan kelola form survei.",
      icon: ClipboardList,
      link: "/admin/skm",
      color: "bg-blue-500",
    },
    {
      title: "Berita & Artikel",
      desc: "Tambah berita terbaru atau edit artikel yang sudah tayang.",
      icon: Newspaper,
      link: "/artikel", // Sementara balik ke list publik atau buat route admin nantinya
      color: "bg-emerald-500",
    },
    {
      title: "Pusat Unduhan",
      desc: "Kelola file PDF, dokumen perencanaan, dan data publik.",
      icon: Download,
      link: "#",
      color: "bg-amber-500",
    },
    {
      title: "Pengaturan Profil",
      desc: "Update informasi dinas, visi misi, dan struktur organisasi.",
      icon: Settings,
      link: "/visi-misi",
      color: "bg-purple-500",
    },
  ];

  return (
    <div className="min-h-screen bg-[#f4f9ff] flex font-sans">
      {/* Sidebar Sederhana */}
      <aside className="w-72 bg-white border-r border-slate-200 hidden lg:flex flex-col">
        <div className="p-8">
          <div className="flex items-center gap-3 mb-10">
            <img src="/logo-solo.png" alt="Logo" className="h-10" />
            <div className="leading-none">
              <h2 className="font-black text-primary text-sm uppercase">Admin Panel</h2>
              <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Diskominfo SP</p>
            </div>
          </div>

          <nav className="space-y-2">
            <Link to="/admin/dashboard" className="flex items-center gap-3 p-3 bg-primary/5 text-primary rounded-xl font-bold text-sm">
              <LayoutDashboard size={18} /> Dashboard
            </Link>
            <Link to="/" className="flex items-center gap-3 p-3 text-slate-500 hover:bg-slate-50 rounded-xl font-bold text-sm transition-all">
              <UserCircle size={18} /> Lihat Web Utama
            </Link>
          </nav>
        </div>
        
        <div className="mt-auto p-8">
          <button 
            onClick={() => navigate('/login')}
            className="flex items-center gap-3 p-3 w-full text-rose-500 hover:bg-rose-50 rounded-xl font-bold text-sm transition-all"
          >
            <LogOut size={18} /> Keluar
          </button>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 p-8 lg:p-12 overflow-y-auto">
        <header className="flex justify-between items-center mb-12">
          <div>
            <h1 className="text-3xl font-black text-slate-800 tracking-tight">Selamat Datang, Admin</h1>
            <p className="text-slate-500 mt-1 font-medium">Pilih modul untuk mengelola konten website.</p>
          </div>
          <div className="flex items-center gap-4">
            <div className="text-right hidden sm:block">
              <p className="text-sm font-bold text-slate-800">Administrator</p>
              <p className="text-xs text-slate-400">admin@surakarta.go.id</p>
            </div>
            <div className="w-12 h-12 rounded-2xl bg-primary flex items-center justify-center text-white font-bold shadow-lg shadow-primary/20">
              AD
            </div>
          </div>
        </header>

        {/* Grid Menu */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {adminMenus.map((menu, idx) => (
            <Link 
              key={idx} 
              to={menu.link}
              className="group bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 flex items-start gap-6"
            >
              <div className={`${menu.color} w-16 h-16 rounded-[1.5rem] flex items-center justify-center text-white shadow-inner shrink-0 group-hover:scale-110 transition-transform`}>
                <menu.icon size={30} />
              </div>
              
              <div className="flex-1">
                <h3 className="text-xl font-black text-slate-800 mb-2">{menu.title}</h3>
                <p className="text-sm text-slate-500 leading-relaxed mb-4">
                  {menu.desc}
                </p>
                <div className="flex items-center gap-2 text-primary font-bold text-sm uppercase tracking-wider">
                  Mulai Kelola <ArrowRight size={14} className="group-hover:translate-x-2 transition-transform" />
                </div>
              </div>
            </Link>
          ))}
        </div>

        {/* Info Box */}
        <div className="mt-12 bg-white/50 border border-dashed border-slate-300 rounded-[2.5rem] p-8 text-center">
          <p className="text-slate-400 text-sm font-medium">
            Butuh bantuan teknis? Hubungi <span className="text-primary font-bold">Tim IT Diskominfo SP Kota Surakarta</span>
          </p>
        </div>
      </main>
    </div>
  );
}