import React, { useState } from 'react';
import { Lock, Mail, Eye, EyeOff, ShieldCheck, Loader2 } from 'lucide-react';
import { authApi, BASE_URL } from '../services/api';

const Login = () => {
  const [showPassword, setShowPassword] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(() => {
    const expired = new URLSearchParams(window.location.search).get('expired') === '1';
    return expired ? 'Session Anda telah berakhir. Silakan login kembali.' : null;
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    try {
      // 1. Ambil cookie CSRF dari Laravel dulu (pola resmi Sanctum SPA auth)
      await authApi.get('/sanctum/csrf-cookie');

      // 2. Kirim email & password ke route login berbasis session
      await authApi.post('/auth/login', { email, password });

      // 3. Berhasil -> session Laravel sudah aktif, arahkan ke dashboard admin.
      //    Alamat akan pindah ke domain Laravel (localhost:8000) karena
      //    dashboard admin memang di-render di sana, bukan di React.
      window.location.href = `${BASE_URL}/admin/dashboard`;
    } catch (err) {
      const message = err.response?.data?.message || 'Gagal login. Periksa kembali email dan password Anda.';
      setError(message);
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-[#f8fafc] px-4 relative">
      {/* Background Subtle Gradient */}
      <div className="absolute inset-0 bg-gradient-to-br from-primary-50 via-white to-red-50/30 -z-10"></div>

      <div className="max-w-md w-full bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(30,58,138,0.1)] border border-slate-100 p-10">
        {/* HEADER */}
        <div className="text-center mb-10">
          <div className="w-16 h-16 bg-primary mx-auto rounded-2xl flex items-center justify-center shadow-lg mb-4">
             <ShieldCheck size={32} className="text-white" />
          </div>
          <h1 className="text-xl font-black text-primary uppercase tracking-wider">Admin Portal</h1>
          <p className="text-slate-400 text-sm font-medium mt-1">Dinas Kominfo SP Surakarta</p>
        </div>

        {error && (
          <div className="mb-6 p-3 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-xl text-center">
            {error}
          </div>
        )}

        {/* FORM */}
        <form className="space-y-5" onSubmit={handleSubmit}>
          <div>
            <div className="relative">
              <Mail className="absolute left-4 top-4 text-slate-400" size={18} />
              <input
                type="email"
                placeholder="Email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-primary focus:bg-white transition-all text-sm font-medium"
              />
            </div>
          </div>

          <div>
            <div className="relative">
              <Lock className="absolute left-4 top-4 text-slate-400" size={18} />
              <input
                type={showPassword ? "text" : "password"}
                placeholder="Password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="w-full pl-12 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-primary focus:bg-white transition-all text-sm font-medium"
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-4 top-4 text-slate-400 hover:text-primary-700"
              >
                {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
              </button>
            </div>
          </div>

          <div className="flex items-center justify-end text-xs font-bold">
            <a href="#" className="text-[#dc2626] hover:underline uppercase tracking-tighter">Lupa Password?</a>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-4 bg-primary text-white rounded-xl font-black text-sm tracking-widest hover:bg-primary-800 shadow-lg shadow-primary/20 transition-all active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            {loading ? (
              <>
                <Loader2 size={18} className="animate-spin" /> MEMPROSES...
              </>
            ) : (
              "MASUK KE SISTEM"
            )}
          </button>
        </form>

        {/* COPYRIGHT SEDERHANA (SESUAI PERMINTAAN) */}
        <div className="mt-10 pt-6 border-t border-slate-50 text-center">
          <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
            © 2026 PEMKOT SURAKARTA
          </p>
        </div>
      </div>
    </div>
  );
};

export default Login;