import { Link } from "react-router-dom";

export default function NotFound() {
  return (
    <section className="min-h-[70vh] flex items-center justify-center px-6 pt-28">
      <div className="text-center max-w-lg">
        <p className="text-accent font-black text-6xl">404</p>
        <h1 className="mt-4 text-3xl font-black text-primary-900 dark:text-white">Halaman tidak ditemukan</h1>
        <p className="mt-3 text-slate-500 dark:text-white/60">Alamat yang dibuka tidak tersedia atau telah dipindahkan.</p>
        <Link to="/" className="inline-flex mt-7 rounded-xl bg-primary px-5 py-3 text-white font-bold hover:bg-primary-700">Kembali ke Beranda</Link>
      </div>
    </section>
  );
}
