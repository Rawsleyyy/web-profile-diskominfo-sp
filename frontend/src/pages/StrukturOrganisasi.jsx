import { useEffect, useState } from "react";
import { api, BASE_URL } from "../services/api";

function getFotoUrl(p) {
  if (!p.foto) return "https://ui-avatar.co/300?u=" + p.id;
  if (p.foto.startsWith("http")) return p.foto;
  return `${BASE_URL}/storage/${p.foto}`;
}

// Ubah daftar flat (dengan parent_id) jadi struktur pohon.
// Pejabat dengan parent_id kosong/null = akar (level teratas).
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

// Satu kartu pejabat + garis penghubung ke anak-anaknya (rekursif, tidak terbatas kedalaman).
function OrgNode({ node, depth = 0 }) {
  const hasChildren = node.children && node.children.length > 0;
  const isRoot = depth === 0;

  return (
    <div className="flex flex-col items-center">
      <div className="text-center group">
        <div className="relative inline-block">
          {isRoot && (
            <div className="absolute -inset-2 bg-gradient-to-tr from-primary to-accent-300 rounded-full blur opacity-20 group-hover:opacity-40 transition"></div>
          )}
          <img
            src={getFotoUrl(node)}
            alt={node.nama}
            className={
              isRoot
                ? "relative w-40 h-40 md:w-48 md:h-48 rounded-full object-cover border-4 border-white shadow-2xl"
                : "relative w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg"
            }
          />
        </div>
        <h3 className={isRoot ? "mt-8 font-black text-xl text-slate-800 leading-tight" : "mt-5 font-bold text-slate-800 text-sm leading-tight"}>
          {node.nama}
        </h3>
        <p className={isRoot ? "text-primary font-bold text-sm uppercase tracking-widest mt-2" : "text-primary-700 font-semibold text-[11px] uppercase tracking-wide mt-1"}>
          {node.jabatan}
        </p>
      </div>

      {hasChildren && (
        <div className="flex flex-col items-center mt-2">
          {/* garis vertikal dari kartu ini turun ke garis horizontal anak-anaknya */}
          <div className="w-px h-8 bg-slate-300"></div>

          <div className="flex flex-wrap justify-center gap-x-12 gap-y-10 border-t border-slate-300 pt-8">
            {node.children.map((child) => (
              <div key={child.id} className="relative flex flex-col items-center">
                {/* stub kecil dari garis horizontal turun ke kartu anak */}
                <div className="absolute -top-8 w-px h-8 bg-slate-300"></div>
                <OrgNode node={child} depth={depth + 1} />
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

export default function StrukturOrganisasi() {
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

  return (
    <div className="pt-44 pb-20 px-6 max-w-6xl mx-auto min-h-screen overflow-x-auto">
      <div className="text-center mb-16">
        <span className="text-primary font-black uppercase tracking-[0.3em] text-[10px]">Kelembagaan</span>
        <h1 className="text-4xl font-black text-slate-800 tracking-tighter mt-4">Struktur Organisasi</h1>
        <p className="text-slate-500 mt-3 max-w-xl mx-auto">
          Susunan pimpinan dan pejabat di lingkungan Dinas Komunikasi, Informatika, Statistik, dan Persandian Kota Surakarta.
        </p>
      </div>

      {loading ? (
        <div className="py-20 text-center text-slate-400 font-medium">Memuat data...</div>
      ) : error ? (
        <div className="py-20 text-center text-red-500 font-medium">{error}</div>
      ) : pejabatList.length === 0 ? (
        <div className="py-20 text-center text-slate-400 font-medium italic">Data struktur organisasi belum tersedia.</div>
      ) : tree.length === 0 ? (
        // Ada data, tapi semuanya punya parent_id yang tidak valid / tidak ada yang level teratas
        <div className="py-20 text-center text-amber-600 font-medium italic">
          Belum ada pejabat level teratas. Atur "Atasan" di dashboard admin untuk membentuk struktur.
        </div>
      ) : (
        <div className="flex flex-wrap justify-center gap-x-20 gap-y-16">
          {tree.map((root) => (
            <OrgNode key={root.id} node={root} depth={0} />
          ))}
        </div>
      )}
    </div>
  );
}
