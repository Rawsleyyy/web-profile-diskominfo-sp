import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import { api } from "../services/api";
const fallbackNavigation = [
  { id: "fallback-home", label: "Home", type: "route", url: "/", target: "_self", is_external: false, children: [] },
];

const DEFAULT_CONFIG = {
  settings: {
    site_name: "Diskominfo SP Kota Surakarta",
    site_short_name: "Diskominfo SP",
    site_description: "",
    logo_url: null,
    favicon_url: null,
    phone: "(0271) 806060",
    email: "diskominfosp@surakarta.go.id",
    address: "",
    socials: {},
    footer_text: "Diskominfo SP Kota Surakarta",
    announcement: null,
    header: {
      style: "theme_gradient",
      width_mode: "adaptive",
      show_site_name: true,
      logo_height: 44,
      topbar_enabled: true,
      topbar_color: "#1C2030",
      search_enabled: true,
      dark_toggle_enabled: true,
      glass_enabled: true,
      shadow_enabled: true,
      custom_color_start: "#0B8A3B",
      custom_color_end: "#46535B",
      gradient_angle: 90,
    },
  },
  modules: [],
  navigation: fallbackNavigation,
  homepage_sections: [{ key: "hero" }],
};

const SiteConfigContext = createContext({
  ...DEFAULT_CONFIG,
  loaded: false,
  refreshConfig: async () => {},
  isModuleEnabled: () => true,
});

export function SiteConfigProvider({ children }) {
  const [config, setConfig] = useState(DEFAULT_CONFIG);
  const [loaded, setLoaded] = useState(false);

  const refreshConfig = useCallback(async () => {
    try {
      const response = await api.get("/site-config");
      const data = response.data?.data || {};
      setConfig((current) => ({
        settings: { ...current.settings, ...(data.settings || {}) },
        modules: Array.isArray(data.modules) ? data.modules : current.modules,
        navigation: Array.isArray(data.navigation) ? data.navigation : current.navigation,
        homepage_sections: Array.isArray(data.homepage_sections) ? data.homepage_sections : current.homepage_sections,
      }));
      setLoaded(true);
    } catch (error) {
      console.error("Gagal memuat konfigurasi website:", error);
      // Gunakan konfigurasi fallback agar website publik tetap dapat digunakan.
      setLoaded(true);
    }
  }, []);

  useEffect(() => {
    refreshConfig();
    const onFocus = () => refreshConfig();
    window.addEventListener("focus", onFocus);
    return () => window.removeEventListener("focus", onFocus);
  }, [refreshConfig]);

  useEffect(() => {
    if (!config.settings?.favicon_url) return;
    let link = document.querySelector("link[rel='icon']");
    if (!link) {
      link = document.createElement("link");
      link.rel = "icon";
      document.head.appendChild(link);
    }
    link.href = config.settings.favicon_url;
  }, [config.settings?.favicon_url]);

  useEffect(() => {
    if (config.settings?.site_name) {
      document.title = config.settings.site_name;
    }
  }, [config.settings?.site_name]);

  const moduleMap = useMemo(
    () => Object.fromEntries((config.modules || []).map((module) => [module.slug, Boolean(module.is_enabled)])),
    [config.modules],
  );

  const isModuleEnabled = useCallback(
    (slug) => (loaded && slug in moduleMap ? moduleMap[slug] : true),
    [loaded, moduleMap],
  );

  return (
    <SiteConfigContext.Provider value={{ ...config, loaded, refreshConfig, isModuleEnabled }}>
      {children}
    </SiteConfigContext.Provider>
  );
}

export function useSiteConfig() {
  return useContext(SiteConfigContext);
}
