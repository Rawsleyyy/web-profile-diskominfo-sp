import { useEffect, useState } from "react";
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import Navbar from "./components/layout/Navbar";
import Footer from "./components/layout/footer";

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

const COLOR_MODE_KEY = "diskominfo-color-mode";

function getInitialDarkMode() {
  const savedMode = localStorage.getItem(COLOR_MODE_KEY);
  if (savedMode === "dark") return true;
  if (savedMode === "light") return false;
  return window.matchMedia("(prefers-color-scheme: dark)").matches;
}

const AppContent = ({ dark, toggleDark }) => (
  <div className="app-background min-h-screen flex flex-col font-sans antialiased transition-colors duration-300">
    <Navbar dark={dark} toggleDark={toggleDark} />

    <main className="flex-grow">
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/visi-misi" element={<VisiMisi />} />
        <Route path="/tupoksi" element={<Tupoksi />} />
        <Route path="/artikel" element={<ArtikelList />} />
        <Route path="/artikel/:id" element={<ArtikelDetail />} />
        <Route path="/publikasi" element={<PublikasiList />} />
        <Route path="/publikasi/:id" element={<PublikasiDetail />} />
        <Route path="/skm" element={<SKMForm />} />
        <Route path="/maklumat" element={<MaklumatPelayanan />} />
        <Route path="/ppid" element={<PPIDPage />} />
        <Route path="/struktur" element={<StrukturOrganisasi />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </main>

    <Footer />
  </div>
);

function App() {
  const [dark, setDark] = useState(getInitialDarkMode);

  useEffect(() => {
    const root = document.documentElement;
    root.classList.toggle("dark", dark);
    root.dataset.colorMode = dark ? "dark" : "light";
    root.style.colorScheme = dark ? "dark" : "light";
    localStorage.setItem(COLOR_MODE_KEY, dark ? "dark" : "light");
  }, [dark]);

  const toggleDark = () => setDark((current) => !current);

  return (
    <Router>
      <AppContent dark={dark} toggleDark={toggleDark} />
    </Router>
  );
}

export default App;
