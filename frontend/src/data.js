// src/data.js
export const navMenus = [
  { label: "Home", href: "/", icon: "Home" },
  { 
    label: "PPID", 
    href: "/ppid", 
    icon: "FileText",
    sub: [
      { label: "Daftar Informasi Publik", href: "/ppid?tab=daftar" },
      { label: "Informasi Berkala", href: "/ppid?tab=berkala" },
      { label: "Informasi Setiap Saat", href: "/ppid?tab=setiap-saat" },
      { label: "Informasi Serta Merta", href: "/ppid?tab=serta-merta" },
      { label: "Informasi Dikecualikan", href: "/ppid?tab=dikecualikan" }
    ]
  },
  { 
    label: "Profil", 
    href: "#", 
    icon: "Info",
    sub: [
      { label: "Visi & Misi", href: "/visi-misi" },
      { label: "Struktur Organisasi", href: "/struktur" },
      { label: "Tupoksi", href: "/tupoksi" }
    ]
  },
  { label: "SKM", href: "/skm", icon: "Star" },
  { label: "Informasi", href: "/artikel", icon: "Newspaper" },
  { 
    label: "Layanan", 
    href: "#", 
    icon: "ClipboardList",
    sub: [
      // Jika ini ke halaman internal:
      { label: "Maklumat Layanan", href: "/maklumat" },
      { label: "Standar Layanan", href: "/maklumat" },
      // Jika ingin ke web eksternal (Contoh):
      { label: "Portal Solo", href: "https://surakarta.go.id", isExternal: true }
    ]
  },
];

export const visitorStats = [
  { label: "Hari Ini", value: "243" },
  { label: "Kemarin", value: "831" },
  { label: "Bulan Ini", value: "19.083" },
  { label: "Total", value: "28.040" },
];

export const ppidDocuments = [
  { id: 1, judul: "Laporan Keuangan Dinas 2025", kategori: "Informasi Berkala", format: "PDF", ukuran: "2.4 MB" },
  { id: 2, judul: "Rencana Strategis (RENSTRA) 2021-2026", kategori: "Informasi Berkala", format: "PDF", ukuran: "5.1 MB" },
  { id: 3, judul: "Daftar Aset Teknologi Informasi", kategori: "Informasi Setiap Saat", format: "XLSX", ukuran: "1.2 MB" },
  { id: 4, judul: "Prosedur Penanganan Siber", kategori: "Informasi Serta Merta", format: "PDF", ukuran: "850 KB" },
];

export const infografisMedia = [
  { id: 1, judul: "Podcast Ep. 25 — Masa Depan AI di Pemerintahan Solo", tipe: "Podcast", durasi: "24:10", icon: "Mic", color: "bg-purple-100 text-purple-600" },
  { id: 2, judul: "Infografis: Alur Permohonan Informasi PPID", tipe: "Infografis", durasi: "1 halaman", icon: "BarChart3", color: "bg-accent-100 text-accent-700" },
  { id: 3, judul: "Video: Tur Virtual Command Center Diskominfo", tipe: "Video", durasi: "05:42", icon: "Video", color: "bg-rose-100 text-rose-600" },
  { id: 4, judul: "Infografis: Statistik Layanan Publik Triwulan III", tipe: "Infografis", durasi: "1 halaman", icon: "PieChart", color: "bg-emerald-100 text-emerald-600" },
];

export const galeriFoto = [
  { id: 1, judul: "Konferensi Pers Peluncuran Aplikasi Solo Smart City", tanggal: "15 Juli 2026", image: "https://images.unsplash.com/photo-1540575861501-7ad05823c95b?q=80&w=400" },
  { id: 2, judul: "Rapat Koordinasi Internal Bidang Statistik Sektoral", tanggal: "10 Juli 2026", image: "https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=400" },
  { id: 3, judul: "Kunjungan Kerja Wali Kota ke Command Center", tanggal: "02 Juli 2026", image: "https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=400" },
];