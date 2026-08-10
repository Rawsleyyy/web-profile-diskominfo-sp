import { Navigate } from "react-router-dom";
import { useSiteConfig } from "../../context/siteconfigcontext";

export default function ModuleGuard({ module, children }) {
  const { loaded, isModuleEnabled } = useSiteConfig();

  if (!loaded) {
    return <div className="min-h-[45vh] flex items-center justify-center text-sm font-semibold text-slate-500">Memuat konfigurasi...</div>;
  }

  if (!isModuleEnabled(module)) {
    return <Navigate to="/" replace />;
  }

  return children;
}
