import { useEffect, useState } from "react";
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import Navbar from "./components/layout/Navbar";
import Footer from "./components/layout/footer";
import ModuleGuard from "./components/routing/ModuleGuard";
import SeoHead from "./components/seo/SeoHead";
import { useSiteConfig } from "./context/siteconfigcontext";
import { useTheme } from "./context/themecontext";
import Home from "./pages/Home";
import VisiMisi from "./pages/VisiMisi"; import Tupoksi from "./pages/Tupoksi"; import ArtikelList from "./pages/ArtikelList"; import ArtikelDetail from "./pages/ArtikelDetail"; import PublikasiDetail from "./pages/PublikasiDetail"; import PublikasiList from "./pages/PublikasiList"; import NotFound from "./pages/NotFound"; import SKMForm from "./pages/SKMForm"; import MaklumatPelayanan from "./pages/MaklumatPelayanan"; import PPIDPage from "./pages/PPIDPage"; import StrukturOrganisasi from "./pages/StrukturOrganisasi"; import AdminLoginRedirect from "./pages/AdminLoginRedirect"; import CustomPage from "./pages/CustomPage";

function App(){
  const { settings }=useSiteConfig(); const { colorMode }=useTheme();
  const [dark,setDark]=useState(()=>localStorage.getItem("theme")?localStorage.getItem("theme")==="dark":window.matchMedia("(prefers-color-scheme: dark)").matches);
  useEffect(()=>{if(colorMode==="dark")setDark(true);else if(colorMode==="light")setDark(false);},[colorMode]);
  useEffect(()=>{document.documentElement.classList.toggle("dark",dark);if(colorMode==="auto")localStorage.setItem("theme",dark?"dark":"light");},[dark,colorMode]);
  const preview=new URLSearchParams(window.location.search).has("preview_token");
  return <Router><SeoHead seo={settings?.seo||{}} siteName={settings?.site_name}/><div className="app-background min-h-screen flex flex-col antialiased transition-colors">
    {preview&&<div className="fixed bottom-4 left-1/2 z-[100] -translate-x-1/2 rounded-full bg-amber-400 px-5 py-2 text-xs font-black text-slate-900 shadow-xl">MODE PREVIEW DRAFT · tidak terlihat publik</div>}
    <Navbar dark={dark} toggleDark={()=>setDark(v=>!v)} />
    <main className="flex-grow"><Routes>
      <Route path="/" element={<Home/>}/><Route path="/visi-misi" element={<ModuleGuard module="profil"><VisiMisi/></ModuleGuard>}/><Route path="/tupoksi" element={<ModuleGuard module="profil"><Tupoksi/></ModuleGuard>}/><Route path="/struktur" element={<ModuleGuard module="struktur"><StrukturOrganisasi/></ModuleGuard>}/><Route path="/artikel" element={<ModuleGuard module="articles"><ArtikelList/></ModuleGuard>}/><Route path="/artikel/:id" element={<ModuleGuard module="articles"><ArtikelDetail/></ModuleGuard>}/><Route path="/publikasi" element={<ModuleGuard module="berita"><PublikasiList/></ModuleGuard>}/><Route path="/publikasi/:id" element={<ModuleGuard module="berita"><PublikasiDetail/></ModuleGuard>}/><Route path="/skm" element={<ModuleGuard module="skm"><SKMForm/></ModuleGuard>}/><Route path="/maklumat" element={<ModuleGuard module="layanan"><MaklumatPelayanan/></ModuleGuard>}/><Route path="/ppid" element={<ModuleGuard module="ppid"><PPIDPage/></ModuleGuard>}/><Route path="/page/:slug" element={<CustomPage/>}/><Route path="/login" element={<AdminLoginRedirect/>}/><Route path="*" element={<NotFound/>}/>
    </Routes></main><Footer/></div></Router>;
}
export default App;
