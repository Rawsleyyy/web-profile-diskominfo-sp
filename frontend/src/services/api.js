import axios from "axios";
const configuredBase=(import.meta.env.VITE_API_BASE_URL||"http://localhost:8000").replace(/\/+$/,"" ).replace(/\/api$/,"");
export const BASE_URL=configuredBase;
export const api=axios.create(
  {
    baseURL:`${BASE_URL}/api`,
    timeout:15000,
    headers:{Accept:"application/json"}
  }
);
api.interceptors.request.use((config)=>{const token=new URLSearchParams(window.location.search).get("preview_token");
  if(token){config.params={...(config.params||{}),preview_token:token};}return config;});
  
export const authApi=axios.create({
  baseURL:"",
  timeout:15000,
  withCredentials:true,
  xsrfCookieName:"XSRF-TOKEN",
  xsrfHeaderName:"X-XSRF-TOKEN",
  headers:{Accept:"application/json","Content-Type":"application/json"}});

export function storageUrl(path){if(!path)return null;
  if(/^https?:\/\//i.test(path))return path;
  const cleaned=String(path).replace(/^\/+/,"").replace(/^storage\//,"");
  return `${BASE_URL}/storage/${cleaned}`;}
