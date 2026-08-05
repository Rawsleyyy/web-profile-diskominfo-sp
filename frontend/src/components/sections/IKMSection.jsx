import { Link } from 'react-router-dom';
import React, { useState, useEffect } from 'react';
import { PieChart, Pie, Cell, ResponsiveContainer, Legend } from 'recharts';
import { api } from '../../services/api'; // Sesuaikan path api kamu jika berbeda

// Warna bawaan untuk chart pendidikan
const COLORS = ['#7c3aed', '#6366f1', '#60a5fa', '#38bdf8', '#a855f7', '#ec4899'];

export default function IKMSection() {
  const [stats, setStats] = useState(null);

  // Ambil data statistik IKM dari Backend Laravel
  useEffect(() => {
    api.get('/skm/stats')
      .then((res) => setStats(res.data))
      .catch((err) => console.error("Gagal memuat statistik IKM:", err));
  }, []);

  // Olah data pendidikan dari backend menjadi format Recharts
  const dataPie = stats?.pendidikan && Object.keys(stats.pendidikan).length > 0
    ? Object.entries(stats.pendidikan).map(([key, value], index) => ({
        name: key,
        value: Number(value),
        color: COLORS[index % COLORS.length]
      }))
    : [{ name: 'Belum Ada Data', value: 1, color: '#cbd5e1' }];

  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-6 border-2 border-slate-100 rounded-[2rem] p-8 md:p-12 shadow-sm relative">
        <div className="flex justify-between items-start mb-12">
          <div className="flex items-center gap-4">
             <h2 className="text-2xl font-black text-primary uppercase tracking-tighter">Indeks Kepuasan Masyarakat</h2>
             <div className="flex gap-2 text-primary">
                <i className="bi bi-pencil-square"></i>
                <i className="bi bi-people-fill"></i>
             </div>
          </div>
          
          {/* TOMBOL YANG SUDAH BISA DI-KLIK */}
          <Link 
            to="/skm" 
            className="bg-primary text-white px-8 py-3 rounded-lg font-bold text-sm hover:bg-opacity-90 transition-all inline-block"
          >
            Form Kepuasan Masyarakat
          </Link>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12 items-center">
          {/* Skor Section */}
          <div className="text-center lg:border-r border-slate-200 lg:pr-12">
            <img src="/logo-solo-colored.png" className="w-16 mx-auto mb-6" alt="Solo" />
            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">IKM</p>
            
            {/* Skor IKM Dinamis */}
            <h3 className="text-7xl font-black text-slate-800 tracking-tighter">
              {stats ? stats.ikm : '...'}
            </h3>
            
            <p className="font-bold text-slate-600 mt-2">Mutu Pelayanan</p>
            
            {/* Mutu Dinamis */}
            <p className="text-primary font-black uppercase tracking-widest mt-1">
              {stats ? stats.mutu : 'MEMUAT...'}
            </p>
            
            {/* Jumlah Responden Dinamis */}
            <div className="mt-8 text-left space-y-2 text-xs font-bold text-slate-500">
               <p>Responden : {stats ? stats.total_responden : 0} Orang</p>
               <p>Laki-laki : {stats ? stats.laki_laki : 0} Orang</p>
               <p>Perempuan : {stats ? stats.perempuan : 0} Orang</p>
            </div>
          </div>

          {/* Chart & Filter */}
          <div className="lg:col-span-2">
            <div className="flex gap-4 mb-8">
               <select className="flex-1 bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm font-bold">
                  <option>Triwulan III</option>
               </select>
               <select className="flex-1 bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm font-bold">
                  <option>2026</option>
               </select>
            </div>

            {/* Donut Chart Recharts */}
            <div className="h-[300px] w-full flex items-center">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie data={dataPie} innerRadius={60} outerRadius={100} paddingAngle={5} dataKey="value">
                    {dataPie.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <Legend verticalAlign="middle" align="right" layout="vertical" />
                </PieChart>
              </ResponsiveContainer>
            </div>
            
            <div className="mt-6 flex justify-center">
               <button className="bg-emerald-500 text-white px-6 py-2 rounded font-bold text-xs uppercase flex items-center gap-2 tracking-widest">
                  Download as Image
               </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}