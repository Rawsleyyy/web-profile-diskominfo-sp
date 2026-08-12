import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import { api } from "../services/api";
const fallbackNavigation=[
  {id:"fallback-home",
    label:"Home",
    type:"route",
    url:"/",
    target:"_self",
    is_external:false,
    children:[]}
  ];
const DEFAULT_CONFIG={
  settings:{
    site_name:"Diskominfo SP Kota Surakarta",
    site_short_name:"Diskominfo SP",
    site_description:"",
    tagline:"",
    logo_url:null,
    logo_footer_url:null,
    logo_dark_url:null,
    favicon_url:null,
    phone:"",
    email:"",
    address:"",
    socials:{},
    footer_text:"",
    seo:{}},
    modules:[],
    navigation:fallbackNavigation,
    homepage_sections:[{key:"hero"}],
    publication:null};

const SiteConfigContext=createContext({...DEFAULT_CONFIG,loaded:false,refreshConfig:async()=>{},isModuleEnabled:()=>true});
export function SiteConfigProvider({children}){
  const [config,setConfig]=useState(DEFAULT_CONFIG);
  const [loaded,setLoaded]=useState(false);
  const refreshConfig=useCallback(async()=>{try{const token=new URLSearchParams(window.location.search).get("preview_token"); 
  const response=await api.get("/site-config",{params:token?{preview_token:token}:{}});
  const data=response.data?.data||{}; 
  setConfig(current=>({settings:{...current.settings,...(data.settings||{})},modules:Array.isArray(data.modules)?data.modules:current.modules,navigation:Array.isArray(data.navigation)?data.navigation:current.navigation,homepage_sections:Array.isArray(data.homepage_sections)?data.homepage_sections:current.homepage_sections,publication:data.publication||null}));setLoaded(true);}catch(error){console.error("Gagal memuat konfigurasi website:",error);setLoaded(true);}},[]);
  useEffect(()=>{refreshConfig();const onFocus=()=>refreshConfig();window.addEventListener("focus",onFocus);return()=>window.removeEventListener("focus",onFocus);},[refreshConfig]);
  useEffect(()=>{if(!config.settings?.favicon_url)return;let link=document.querySelector("link[rel='icon']");if(!link){link=document.createElement("link");link.rel="icon";document.head.appendChild(link);}link.href=config.settings.favicon_url;},[config.settings?.favicon_url]);
  const moduleMap=useMemo(()=>Object.fromEntries((config.modules||[]).map(module=>[module.slug,Boolean(module.is_enabled)])),[config.modules]);
  const isModuleEnabled=useCallback(slug=>(loaded&&slug in moduleMap?moduleMap[slug]:true),[loaded,moduleMap]);
  return <SiteConfigContext.Provider value={{...config,loaded,refreshConfig,isModuleEnabled}}>{children}</SiteConfigContext.Provider>;
}
export function useSiteConfig(){return useContext(SiteConfigContext);}
