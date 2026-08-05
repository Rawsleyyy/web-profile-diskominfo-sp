import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api, BASE_URL } from "../../services/api";

function getFotoUrl(p) {
  if (!p.foto) return "https://ui-avatar.co/300?u=" + p.id;
  if (p.foto.startsWith("http")) return p.foto;
  return `${BASE_URL}/storage/${p.foto}`;
}

// Sama seperti di StrukturOrganisasi.jsx (halaman penuh) -> susun daftar flat jadi pohon.
function buildTree(list) {
  const byParent = {};
  list.forEach((p) => {
    const key = p.parent_id ?? "root";
    if (!byParent[key]) byParent[key] = [];
    byParent[key].push(p);
  });

  Object.values(byParent).forEach((arr) => arr.sort((a, b) => a.urutan - b.urutan));

  function attachChildren(p) {
    return { ...p, children: (byParent[p.id] || []).map(attachChildren) };
  }

  return (byParent["root"] || []).map(attachChildren);
}

export default function StructureSection() {
  const [pejabatList, setPejabatList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    let isMounted = true;
    api.get("/pejabat")
      .then((res) => {
        const data = res.data.data || res.data || [];
        if (isMounted) setPejabatList(data);
      })
      .catch((err) => {
        console.error("Gagal memuat struktur organisasi:", err);
        if (isMounted) setError("Gagal mengambil data struktur organisasi dari server.");
      })
      .finally(() => { if (isMounted) setLoading(false); });
    return () => { isMounted = false; };
  }, []);

  const tree = buildTree(pejabatList);
  // Teaser homepage: cuma root (biasanya Kepala Dinas) + anak langsungnya.
  // Cucu ke bawah TIDAK ditampilkan di sini -> arahkan ke /struktur.
  const root = tree[0];
  const directChildren = root?.children || [];

  return (
    <div className="py-20 px-6 max-w-7xl mx-auto">
      <div className="text-center mb-16">
        <span className="text-blue-600 font-black uppercase tracking-[0.3em] text-[10px]">Kelembagaan</span>
        <h1 className="text-4xl font-black text-slate-800 tracking-tighter mt-4">Struktur Organisasi</h1>
        <p className="text-slate-500 mt-3 max-w-xl mx-auto">
          Susunan pimpinan dan pejabat di lingkungan Dinas Komunikasi, Informatika, Statistik, dan Persandian Kota Surakarta.
        </p>
      </div>

      {loading ? (
        <div className="py-20 text-center text-slate-400 font-medium">Memuat data...</div>
      ) : error ? (
        <div className="py-20 text-center text-red-500 font-medium">{error}</div>
      ) : !root ? (
        <div className="py-20 text-center text-slate-400 font-medium italic">Data struktur organisasi belum tersedia.</div>
      ) : (
        <>
          <div className="w-full overflow-x-auto overflow-y-hidden pb-12 pt-4">
            <div className="min-w-max flex flex-col items-center mx-auto px-10">

              {/* LEVEL 1: Root (Kepala Dinas) */}
              <div className="flex flex-col items-center">
                <div className="flex flex-row gap-12 justify-center">
                  <div className="flex flex-col items-center">
                    <div className="relative z-10 bg-white rounded-3xl border-t-4 border-t-blue-500 shadow-xl flex flex-col items-center w-64 p-6 hover:-translate-y-1 transition duration-300">
                      <img src={getFotoUrl(root)} className="w-28 h-28 rounded-full object-cover border-4 border-slate-50 shadow-md mb-4" alt={root.nama} />
                      <h3 className="font-bold text-slate-800 text-sm text-center leading-tight">{root.nama}</h3>
                      <div className="mt-4 bg-blue-500 text-white text-[10px] font-bold px-4 py-2 rounded-lg uppercase tracking-widest text-center w-full shadow-sm">
                        {root.jabatan}
                      </div>
                    </div>
                  </div>
                </div>

                {directChildren.length > 0 && (
                  <div className="w-[2px] h-12 bg-blue-500"></div>
                )}
              </div>

              {/* LEVEL 2: Anak langsung saja (bukan seluruh cabang ke bawah) */}
              {directChildren.length > 0 && (
                <div className="flex flex-row flex-wrap justify-center items-start gap-y-10">
                  {directChildren.map((p, index) => {
                    const isFirst = index === 0;
                    const isLast = index === directChildren.length - 1;
                    const isOnly = directChildren.length === 1;
                    const hasMoreBelow = p.children && p.children.length > 0;

                    return (
                      <div key={p.id} className="relative flex flex-col items-center px-4">

                        {!isOnly && (
                          <div
                            className={`absolute top-0 h-[2px] bg-blue-500 -z-10
                            ${isFirst ? 'left-1/2 w-1/2' : isLast ? 'right-1/2 w-1/2' : 'left-0 w-full'}`}
                          ></div>
                        )}

                        <div className="w-[2px] h-10 bg-blue-500 -z-10"></div>

                        <div className="relative z-10 bg-white rounded-3xl border-t-4 border-t-green-400 shadow-lg flex flex-col items-center w-56 p-5 hover:-translate-y-1 transition duration-300">
                          <img src={getFotoUrl(p)} className="w-20 h-20 rounded-full object-cover border-2 border-slate-50 shadow-sm mb-3" alt={p.nama} />
                          <h3 className="font-bold text-slate-700 text-xs text-center leading-snug">{p.nama}</h3>
                          <div className="mt-3 bg-green-50 text-green-700 border border-green-200 text-[9px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider text-center w-full">
                            {p.jabatan}
                          </div>
                          {hasMoreBelow && (
                            <span className="mt-2 text-[9px] text-blue-500 font-semibold">
                              +{p.children.length} Anggota
                            </span>
                          )}
                        </div>
                      </div>
                    );
                  })}
                </div>
              )}
            </div>
          </div>

          <div className="text-center mt-4">
            <Link
              to="/struktur"
              className="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs uppercase tracking-widest transition shadow-sm"
            >
              Lihat Struktur Organisasi Lengkap →
            </Link>
          </div>
        </>
      )}
    </div>
  );
}