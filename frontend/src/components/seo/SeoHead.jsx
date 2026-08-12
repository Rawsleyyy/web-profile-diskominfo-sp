import { useEffect } from "react";

function ensureMeta(selector, attrs) {
  let el = document.head.querySelector(selector);
  if (!el) { el = document.createElement("meta"); document.head.appendChild(el); }
  Object.entries(attrs).forEach(([key,value]) => { if (value !== undefined && value !== null) el.setAttribute(key, String(value)); });
  return el;
}

export default function SeoHead({ seo = {}, siteName = "Website Instansi" }) {
  useEffect(() => {
    const title = seo.title || siteName; if (title) document.title = title;
    ensureMeta('meta[name="description"]', { name:"description", content:seo.description || "" });
    ensureMeta('meta[name="robots"]', { name:"robots", content:seo.robots_index === false ? "noindex,nofollow" : "index,follow" });
    ensureMeta('meta[property="og:title"]', { property:"og:title", content:title });
    ensureMeta('meta[property="og:description"]', { property:"og:description", content:seo.description || "" });
    if (seo.og_image_url) ensureMeta('meta[property="og:image"]', { property:"og:image", content:seo.og_image_url });
    let canonical = document.head.querySelector('link[rel="canonical"]');
    if (!canonical) { canonical=document.createElement("link"); canonical.rel="canonical"; document.head.appendChild(canonical); }
    canonical.href = seo.canonical_url || window.location.href.split("?")[0];
  }, [seo, siteName]);
  return null;
}
