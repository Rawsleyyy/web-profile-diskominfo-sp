import React from 'react';

const SKMStatsCard = ({ stats }) => {
  if (!stats) {
    return (
      <div className="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 mb-8 max-w-4xl mx-auto text-center text-slate-400">
        Memuat data statistik...
      </div>
    );
  }

  return (
    <div className="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100 mb-8 max-w-4xl mx-auto">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        
        {/* BAGIAN KIRI: NILAI IKM & DEMOGRAFI GENDER */}
        <div className="space-y-4">
          <div>
            <span className="text-xs font-bold tracking-wider text-slate-400 uppercase">
              Indeks Kepuasan Masyarakat
            </span>
            <h1 className="text-6xl font-black text-slate-900 mt-1">
              {stats.ikm ?? 0}
            </h1>
            <div className="mt-2">
              <span className="inline-block bg-primary-50 text-primary-700 text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                Mutu: {stats.mutu || 'BELUM ADA DATA'}
              </span>
            </div>
          </div>

          <div className="pt-2 border-t border-slate-100 text-xs text-slate-500 space-y-1.5">
            <p className="flex justify-between max-w-[200px]">
              <span>Total Responden</span>
              <strong className="text-slate-800">{stats.total_responden ?? 0} Orang</strong>
            </p>
            <p className="flex justify-between max-w-[200px]">
              <span>Laki-laki</span>
              <strong className="text-slate-800">{stats.laki_laki ?? 0} Orang</strong>
            </p>
            <p className="flex justify-between max-w-[200px]">
              <span>Perempuan</span>
              <strong className="text-slate-800">{stats.perempuan ?? 0} Orang</strong>
            </p>
          </div>
        </div>

        {/* BAGIAN KANAN: RINGKASAN PENDIDIKAN */}
        <div className="bg-slate-50 p-5 rounded-2xl border border-slate-100">
          <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
            Demografi Pendidikan
          </h4>
          <div className="space-y-2 text-xs">
            {stats.pendidikan && Object.keys(stats.pendidikan).length > 0 ? (
              Object.entries(stats.pendidikan).map(([key, val]) => (
                <div key={key} className="flex justify-between items-center bg-white p-2.5 rounded-xl border border-slate-100 shadow-sm">
                  <span className="text-slate-600 font-semibold">{key}</span>
                  <span className="font-bold text-primary-700 bg-primary-50 px-2.5 py-0.5 rounded-lg">
                    {val} Orang
                  </span>
                </div>
              ))
            ) : (
              <p className="text-slate-400 italic text-center py-4">Belum ada data pendidikan</p>
            )}
          </div>
        </div>

      </div>
    </div>
  );
};

export default SKMStatsCard;