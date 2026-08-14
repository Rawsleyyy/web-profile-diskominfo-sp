import { useEffect, useMemo, useState } from "react";
import { api, BASE_URL } from "../services/api";

function getFotoUrl(person) {
  if (!person?.foto) {
    return `https://ui-avatar.co/300?name=${encodeURIComponent(person?.nama || "Pejabat")}&background=e8f5ee&color=0f7a3f`;
  }

  if (person.foto.startsWith("http")) return person.foto;

  return `${BASE_URL}/storage/${person.foto}`;
}

/*
 * Data dari API tetap flat:
 * id, nama, jabatan, parent_id, urutan, foto
 *
 * Fungsi ini mengubah data flat menjadi tree berdasarkan parent_id.
 * Mekanisme database/API tidak berubah.
 */
function buildTree(list) {
  const byParent = {};

  list.forEach((person) => {
    const key = person.parent_id ?? "root";

    if (!byParent[key]) {
      byParent[key] = [];
    }

    byParent[key].push(person);
  });

  Object.values(byParent).forEach((items) => {
    items.sort(
      (a, b) =>
        Number(a.urutan || 0) - Number(b.urutan || 0) ||
        Number(a.id || 0) - Number(b.id || 0),
    );
  });

  function attachChildren(person, visited = new Set()) {
    // Proteksi tambahan jika data lama sempat memiliki circular hierarchy.
    if (visited.has(person.id)) {
      return { ...person, children: [] };
    }

    const nextVisited = new Set(visited);
    nextVisited.add(person.id);

    return {
      ...person,
      children: (byParent[person.id] || []).map((child) =>
        attachChildren(child, nextVisited),
      ),
    };
  }

  return (byParent.root || []).map((person) => attachChildren(person));
}

function countDescendants(node) {
  return (node.children || []).reduce(
    (total, child) => total + 1 + countDescendants(child),
    0,
  );
}

function PersonIdentity({ person, compact = false }) {
  return (
    <div className="flex min-w-0 items-center gap-4">
      <div
        className={`shrink-0 overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm dark:border-white/10 dark:bg-white/10 ${
          compact ? "h-14 w-14" : "h-20 w-20"
        }`}
      >
        <img
          src={getFotoUrl(person)}
          alt={person.nama || "Pejabat"}
          className="h-full w-full object-cover"
          loading="lazy"
        />
      </div>

      <div className="min-w-0">
        <p
          className={`theme-text-main font-black leading-tight ${
            compact ? "text-sm" : "text-base md:text-lg"
          }`}
        >
          {person.nama}
        </p>

        <p className="mt-1 text-[10px] font-black uppercase leading-5 tracking-wider text-primary">
          {person.jabatan}
        </p>
      </div>
    </div>
  );
}

/*
 * BranchNode tidak menggambar satu garis horizontal global.
 *
 * Setiap node mempunyai "kotak cabang" sendiri.
 * Seluruh anak node hanya berada di dalam panel parent tersebut,
 * sehingga relasi parent-child lebih mudah dibaca.
 */
function BranchNode({ node, depth = 1 }) {
  const hasChildren = Array.isArray(node.children) && node.children.length > 0;
  const [open, setOpen] = useState(depth <= 2);

  return (
    <article
      className={`relative rounded-[1.6rem] border bg-white/80 shadow-[0_10px_35px_rgba(15,23,42,0.06)] backdrop-blur-sm transition dark:bg-white/[0.06] ${
        depth === 1
          ? "border-primary/20"
          : "border-slate-200/80 dark:border-white/10"
      }`}
    >
      <div className="p-4 md:p-5">
        <div className="flex items-start justify-between gap-3">
          <PersonIdentity person={node} compact={depth > 1} />

          {hasChildren && (
            <button
              type="button"
              onClick={() => setOpen((value) => !value)}
              className="theme-chip flex h-9 shrink-0 items-center gap-2 rounded-xl px-3 text-[10px] font-black uppercase tracking-wide"
              aria-expanded={open}
            >
              <span>{node.children.length} Anggota</span>
              <i
                className={`bi bi-chevron-down transition-transform duration-300 ${
                  open ? "rotate-180" : ""
                }`}
              />
            </button>
          )}
        </div>

        {!hasChildren && (
          <div className="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:border-white/10 dark:text-white/35">
            <span className="h-2 w-2 rounded-full bg-slate-300 dark:bg-white/30" />
            Tidak memiliki Anggota langsung
          </div>
        )}
      </div>

      {hasChildren && open && (
        <div className="border-t border-primary/10 bg-slate-50/70 p-4 dark:border-white/10 dark:bg-white/[0.025] md:p-5">
          <div className="mb-4 flex items-center gap-3">
            <div className="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10 text-primary">
              <i className="bi bi-diagram-3-fill text-xs" />
            </div>

            <div>
              <p className="theme-text-main text-[11px] font-black uppercase tracking-wider">
                Di bawah {node.nama}
              </p>
              <p className="theme-text-muted mt-0.5 text-[10px]">
                {node.children.length} Anggota langsung
              </p>
            </div>
          </div>

          <div className="relative pl-5">
            {/* Garis hanya berlaku untuk cabang parent ini. */}
            <div className="absolute bottom-3 left-[7px] top-2 w-px bg-primary/25" />

            <div
              className={`grid gap-4 ${
                depth === 1
                  ? "xl:grid-cols-2"
                  : "grid-cols-1"
              }`}
            >
              {node.children.map((child) => (
                <div key={child.id} className="relative">
                  <div className="absolute -left-[13px] top-8 h-px w-[13px] bg-primary/25" />
                  <div className="absolute -left-[16px] top-[29px] h-2 w-2 rounded-full border-2 border-primary/40 bg-white dark:bg-slate-900" />

                  <BranchNode node={child} depth={depth + 1} />
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </article>
  );
}

function RootOrganization({ root, index }) {
  const directChildren = root.children || [];
  const totalDescendants = countDescendants(root);

  return (
    <section className={index > 0 ? "mt-12 border-t border-slate-200 pt-12 dark:border-white/10" : ""}>
      <div className="theme-card relative overflow-hidden rounded-[2rem] border border-primary/20 p-5 shadow-[0_18px_55px_rgba(15,23,42,0.08)] md:p-7">
        <div className="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-primary/10 blur-3xl" />
        <div className="pointer-events-none absolute -bottom-16 left-16 h-40 w-40 rounded-full bg-[color:var(--color-accent)]/10 blur-3xl" />

        <div className="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
          <div className="flex items-center gap-5">
            <div className="relative shrink-0">
              <div className="absolute -inset-2 rounded-[1.75rem] bg-primary/10 blur-md" />
              <div className="relative h-24 w-24 overflow-hidden rounded-[1.5rem] border-4 border-white bg-white shadow-lg dark:border-white/10 dark:bg-white/10 md:h-28 md:w-28">
                <img
                  src={getFotoUrl(root)}
                  alt={root.nama || "Pimpinan"}
                  className="h-full w-full object-cover"
                />
              </div>
            </div>

            <div>
              <div className="theme-chip mb-2 inline-flex items-center gap-2 rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-widest">
                <i className="bi bi-person-badge-fill" />
                Pimpinan Utama
              </div>

              <h2 className="theme-text-main text-xl font-black leading-tight md:text-2xl">
                {root.nama}
              </h2>

              <p className="mt-1 text-xs font-black uppercase tracking-wider text-primary">
                {root.jabatan}
              </p>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-3 md:min-w-[250px]">
            <div className="rounded-2xl border border-primary/10 bg-primary/5 p-4 text-center dark:border-white/10 dark:bg-white/[0.04]">
              <p className="text-2xl font-black text-primary">
                {directChildren.length}
              </p>
              <p className="theme-text-muted mt-1 text-[9px] font-black uppercase tracking-wider">
                Unit Langsung
              </p>
            </div>

            <div className="rounded-2xl border border-primary/10 bg-primary/5 p-4 text-center dark:border-white/10 dark:bg-white/[0.04]">
              <p className="text-2xl font-black text-primary">
                {totalDescendants}
              </p>
              <p className="theme-text-muted mt-1 text-[9px] font-black uppercase tracking-wider">
                Total Anggota
              </p>
            </div>
          </div>
        </div>
      </div>

      {directChildren.length > 0 && (
        <div className="mt-6">
          <div className="mb-5 flex items-center gap-3">
            <div className="h-px flex-1 bg-gradient-to-r from-transparent via-primary/25 to-primary/25" />

            <div className="theme-chip inline-flex shrink-0 items-center gap-2 rounded-full px-4 py-2 text-[10px] font-black uppercase tracking-wider">
              <i className="bi bi-diagram-3" />
              Unit di bawah {root.nama}
            </div>

            <div className="h-px flex-1 bg-gradient-to-l from-transparent via-primary/25 to-primary/25" />
          </div>

          <div className="grid gap-5 lg:grid-cols-2">
            {directChildren.map((child) => (
              <BranchNode key={child.id} node={child} depth={1} />
            ))}
          </div>
        </div>
      )}
    </section>
  );
}

export default function StrukturOrganisasi() {
  const [pejabatList, setPejabatList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    let isMounted = true;

    api
      .get("/pejabat")
      .then((response) => {
        const data = response.data?.data || response.data || [];

        if (isMounted) {
          setPejabatList(Array.isArray(data) ? data : []);
        }
      })
      .catch((err) => {
        console.error("Gagal memuat struktur organisasi:", err);

        if (isMounted) {
          setError("Gagal mengambil data struktur organisasi dari server.");
        }
      })
      .finally(() => {
        if (isMounted) {
          setLoading(false);
        }
      });

    return () => {
      isMounted = false;
    };
  }, []);

  const tree = useMemo(() => buildTree(pejabatList), [pejabatList]);

  return (
    <main className="min-h-screen px-5 pb-20 pt-36 md:px-6 md:pt-40">
      <div className="mx-auto max-w-7xl">
        <header className="mb-10">
          <div className="theme-chip inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[10px] font-black uppercase tracking-widest">
            <span className="h-1.5 w-1.5 rounded-full bg-current" />
            Kelembagaan
          </div>

          <div className="mt-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
              <h1 className="theme-text-main text-3xl font-black tracking-tight md:text-4xl">
                Struktur Organisasi
              </h1>

              <p className="theme-text-muted mt-3 max-w-3xl text-sm leading-7">
                Struktur ditampilkan berdasarkan hubungan atasan dan Anggota.
              </p>
            </div>

            {!loading && !error && pejabatList.length > 0 && (
              <div className="flex flex-wrap gap-2">
                <span className="theme-card-flat rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-wider">
                  <i className="bi bi-people-fill mr-2 text-primary" />
                  {pejabatList.length} Pejabat
                </span>

                <span className="theme-card-flat rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-wider">
                  <i className="bi bi-diagram-3-fill mr-2 text-primary" />
                  Data Tree
                </span>
              </div>
            )}
          </div>
        </header>

        {loading ? (
          <div className="space-y-5">
            <div className="theme-card h-40 animate-pulse rounded-[2rem]" />
            <div className="grid gap-5 lg:grid-cols-2">
              <div className="theme-card h-48 animate-pulse rounded-[1.6rem]" />
              <div className="theme-card h-48 animate-pulse rounded-[1.6rem]" />
            </div>
          </div>
        ) : error ? (
          <div className="rounded-[2rem] border border-red-200 bg-red-50 px-6 py-14 text-center text-sm font-bold text-red-600 dark:border-red-400/20 dark:bg-red-500/10 dark:text-red-300">
            {error}
          </div>
        ) : pejabatList.length === 0 ? (
          <div className="theme-card rounded-[2rem] px-6 py-14 text-center">
            <div className="theme-action-gradient mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl text-2xl text-white">
              <i className="bi bi-diagram-3" />
            </div>

            <h2 className="theme-text-main text-lg font-black">
              Data struktur belum tersedia
            </h2>

            <p className="theme-text-muted mt-2 text-sm">
              Tambahkan pejabat dan relasi atasan melalui dashboard admin.
            </p>
          </div>
        ) : tree.length === 0 ? (
          <div className="rounded-[2rem] border border-amber-200 bg-amber-50 px-6 py-14 text-center text-sm font-bold text-amber-700 dark:border-amber-400/20 dark:bg-amber-500/10 dark:text-amber-200">
            Data pejabat tersedia, tetapi belum ditemukan pejabat level teratas.
            Periksa kembali pilihan <strong>Atasan</strong> di dashboard admin.
          </div>
        ) : (
          <>
            {tree.map((root, index) => (
              <RootOrganization key={root.id} root={root} index={index} />
            ))}

            <div className="mt-10 rounded-2xl border border-primary/10 bg-primary/5 px-5 py-4 dark:border-white/10 dark:bg-white/[0.035]">
              <div className="flex gap-3">
                <div className="theme-chip flex h-9 w-9 shrink-0 items-center justify-center rounded-xl">
                  <i className="bi bi-info-circle-fill" />
                </div>

                <div>
                  <p className="theme-text-main text-xs font-black uppercase tracking-wider">
                    Cara membaca struktur
                  </p>

                  <p className="theme-text-muted mt-1 text-xs leading-6">
                    Setiap panel hanya menampilkan Anggota dari pejabat yang berada
                    tepat di atas panel tersebut. Klik tombol jumlah Anggota untuk
                    membuka atau menutup cabang organisasi.
                  </p>
                </div>
              </div>
            </div>
          </>
        )}
      </div>
    </main>
  );
}
