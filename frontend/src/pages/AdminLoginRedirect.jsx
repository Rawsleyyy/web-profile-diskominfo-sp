import { useEffect } from "react";

const ADMIN_LOGIN_URL = import.meta.env.VITE_ADMIN_LOGIN_URL || "http://localhost:8000/admin/login";

export default function AdminLoginRedirect() {
  useEffect(() => {
    window.location.replace(ADMIN_LOGIN_URL);
  }, []);

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-50">
      <p className="text-sm font-semibold text-slate-600">Mengarahkan ke login administrator...</p>
    </div>
  );
}
