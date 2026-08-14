import { useEffect, useState } from "react";
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import Navbar from "./components/layout/Navbar";
import Footer from "./components/layout/footer";
import ModuleGuard from "./components/routing/ModuleGuard";
import Home from "./pages/Home";
import VisiMisi from "./pages/VisiMisi";
import Tupoksi from "./pages/Tupoksi";
import ArtikelList from "./pages/ArtikelList";
import ArtikelDetail from "./pages/ArtikelDetail";
import PublikasiDetail from "./pages/PublikasiDetail";
import PublikasiList from "./pages/PublikasiList";
import NotFound from "./pages/NotFound";
import SKMForm from "./pages/SKMForm";
import MaklumatPelayanan from "./pages/MaklumatPelayanan";
import PPIDPage from "./pages/PPIDPage";
import StrukturOrganisasi from "./pages/StrukturOrganisasi";
import PenghargaanList from "./pages/PenghargaanList";
import AdminLoginRedirect from "./pages/AdminLoginRedirect";
import CustomPage from "./pages/CustomPage";

function App() {
  const [dark, setDark] = useState(() => {
    const saved = localStorage.getItem("theme");
    return saved ? saved === "dark" : window.matchMedia("(prefers-color-scheme: dark)").matches;
  });

  useEffect(() => {
    document.documentElement.classList.toggle("dark", dark);
    localStorage.setItem("theme", dark ? "dark" : "light");
  }, [dark]);

  return (
    <Router>
      <div className="app-background antialiased min-h-screen flex flex-col font-sans transition-colors">
        <Navbar dark={dark} toggleDark={() => setDark((value) => !value)} />
        <main className="flex-grow">
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/visi-misi" element={<ModuleGuard module="profil"><VisiMisi /></ModuleGuard>} />
            <Route path="/tupoksi" element={<ModuleGuard module="profil"><Tupoksi /></ModuleGuard>} />
            <Route path="/struktur" element={<ModuleGuard module="struktur"><StrukturOrganisasi /></ModuleGuard>} />
            <Route path="/artikel" element={<ModuleGuard module="articles"><ArtikelList /></ModuleGuard>} />
            <Route path="/artikel/:id" element={<ModuleGuard module="articles"><ArtikelDetail /></ModuleGuard>} />
            <Route path="/publikasi" element={<ModuleGuard module="berita"><PublikasiList /></ModuleGuard>} />
            <Route path="/publikasi/:id" element={<ModuleGuard module="berita"><PublikasiDetail /></ModuleGuard>} />
            <Route path="/penghargaan" element={<ModuleGuard module="awards"><PenghargaanList /></ModuleGuard>} />
            <Route path="/skm" element={<ModuleGuard module="skm"><SKMForm /></ModuleGuard>} />
            <Route path="/maklumat" element={<ModuleGuard module="layanan"><MaklumatPelayanan /></ModuleGuard>} />
            <Route path="/ppid" element={<ModuleGuard module="ppid"><PPIDPage /></ModuleGuard>} />
            <Route path="/page/:slug" element={<CustomPage />} />
            <Route path="/login" element={<AdminLoginRedirect />} />
            <Route path="*" element={<NotFound />} />
          </Routes>
        </main>
        <Footer />
      </div>
    </Router>
  );
}

export default App;
