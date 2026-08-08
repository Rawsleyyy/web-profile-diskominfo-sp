import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { Cell, Legend, Pie, PieChart, ResponsiveContainer } from "recharts";
import { api } from "../../services/api";

const CHART_COLORS = ["#7c3aed", "#6366f1", "#3b82f6", "#06b6d4", "#a855f7", "#ec4899"];

export default function IKMSection() {
  const [stats, setStats] = useState(null);

  useEffect(() => {
    api.get("/skm/stats")
      .then((response) => setStats(response.data))
      .catch((error) => console.error("Gagal memuat statistik IKM:", error));
  }, []);

  const dataPie = useMemo(() => {
    if (stats?.pendidikan && Object.keys(stats.pendidikan).length > 0) {
      return Object.entries(stats.pendidikan).map(([key, value], index) => ({
        name: key,
        value: Number(value),
        color: CHART_COLORS[index % CHART_COLORS.length],
      }));
    }

    return [{ name: "Belum Ada Data", value: 1, color: "var(--chart-empty)" }];
  }, [stats]);

  return (
    <section className="theme-section py-20">
      <div className="theme-card relative mx-auto max-w-7xl rounded-[2rem] p-8 md:p-12">
        <div className="mb-12 flex flex-col items-start justify-between gap-5 md:flex-row">
          <div className="flex items-center gap-4">
            <h2 className="theme-brand-text text-2xl font-black uppercase tracking-tighter">
              Indeks Kepuasan Masyarakat
            </h2>
            <div className="theme-brand-text flex gap-2">
              <i className="bi bi-pencil-square" />
              <i className="bi bi-people-fill" />
            </div>
          </div>

          <Link
            to="/skm"
            className="theme-action inline-block rounded-lg px-8 py-3 text-sm font-bold text-white transition-all"
          >
            Form Kepuasan Masyarakat
          </Link>
        </div>

        <div className="grid grid-cols-1 items-center gap-12 lg:grid-cols-3">
          <div className="text-center lg:border-r lg:pr-12" style={{ borderColor: "var(--line-default)" }}>
            <img src="/logo-solo-colored.png" className="keep-light-surface mx-auto mb-6 w-16 rounded-xl p-1" alt="Solo" />
            <p className="theme-text-muted text-[10px] font-bold uppercase tracking-widest">IKM</p>

            <h3 className="theme-text-main text-7xl font-black tracking-tighter">
              {stats ? stats.ikm : "..."}
            </h3>

            <p className="theme-text-secondary mt-2 font-bold">Mutu Pelayanan</p>
            <p className="theme-brand-text mt-1 font-black uppercase tracking-widest">
              {stats ? stats.mutu : "MEMUAT..."}
            </p>

            <div className="theme-text-secondary mt-8 space-y-2 text-left text-xs font-bold">
              <p>Responden: {stats ? stats.total_responden : 0} Orang</p>
              <p>Laki-laki: {stats ? stats.laki_laki : 0} Orang</p>
              <p>Perempuan: {stats ? stats.perempuan : 0} Orang</p>
            </div>
          </div>

          <div className="lg:col-span-2">
            <div className="mb-8 flex flex-col gap-4 sm:flex-row">
              <select className="theme-input flex-1 rounded-lg p-3 text-sm font-bold">
                <option>Triwulan III</option>
              </select>
              <select className="theme-input flex-1 rounded-lg p-3 text-sm font-bold">
                <option>2026</option>
              </select>
            </div>

            <div className="flex h-[300px] w-full items-center">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={dataPie}
                    innerRadius={60}
                    outerRadius={100}
                    paddingAngle={5}
                    dataKey="value"
                  >
                    {dataPie.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <Legend
                    verticalAlign="middle"
                    align="right"
                    layout="vertical"
                    formatter={(value) => (
                      <span style={{ color: "var(--text-secondary)", fontSize: 12 }}>
                        {value}
                      </span>
                    )}
                  />
                </PieChart>
              </ResponsiveContainer>
            </div>

            <div className="mt-6 flex justify-center">
              <button
                type="button"
                className="theme-action flex items-center gap-2 rounded px-6 py-2 text-xs font-bold uppercase tracking-widest text-white"
              >
                Download as Image
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
