import { createContext, useCallback, useContext, useEffect, useState } from "react";
import { api } from "../services/api";
import { applyColorShades } from "../utils/ColorUtils";

const DEFAULT_THEME = {
  primary: "#1E3A8A", secondary: "#0F172A", accent: "#DC2626", background: "#F8FAFC", surface: "#FFFFFF",
  textPrimary: "#0F172A", textSecondary: "#64748B", fontHeading: "Inter", fontBody: "Inter", radiusStyle: "rounded",
  buttonStyle: "solid", cardStyle: "soft", containerWidth: "1280", navbarStyle: "solid", colorMode: "auto", loaded: false,
};
const ThemeContext = createContext({ ...DEFAULT_THEME, refreshTheme: async () => {} });

const radiusMap = { square: "0px", small: "6px", rounded: "14px", large: "24px", pill: "999px" };

export function ThemeProvider({ children }) {
  const [theme, setTheme] = useState(DEFAULT_THEME);
  const applyTheme = useCallback((data = {}, loaded = true) => {
    const next = {
      primary: data.primary_color_hex || DEFAULT_THEME.primary,
      secondary: data.secondary_color_hex || DEFAULT_THEME.secondary,
      accent: data.accent_color_hex || DEFAULT_THEME.accent,
      background: data.background_color_hex || DEFAULT_THEME.background,
      surface: data.surface_color_hex || DEFAULT_THEME.surface,
      textPrimary: data.text_primary_hex || DEFAULT_THEME.textPrimary,
      textSecondary: data.text_secondary_hex || DEFAULT_THEME.textSecondary,
      fontHeading: data.font_heading || DEFAULT_THEME.fontHeading,
      fontBody: data.font_body || DEFAULT_THEME.fontBody,
      radiusStyle: data.radius_style || DEFAULT_THEME.radiusStyle,
      buttonStyle: data.button_style || DEFAULT_THEME.buttonStyle,
      cardStyle: data.card_style || DEFAULT_THEME.cardStyle,
      containerWidth: data.container_width || DEFAULT_THEME.containerWidth,
      navbarStyle: data.navbar_style || DEFAULT_THEME.navbarStyle,
      colorMode: data.color_mode || DEFAULT_THEME.colorMode,
      loaded,
    };
    applyColorShades("primary", next.primary); applyColorShades("accent", next.accent); applyColorShades("secondary", next.secondary);
    const root = document.documentElement;
    root.style.setProperty("--site-background", next.background); root.style.setProperty("--site-surface", next.surface);
    root.style.setProperty("--site-text-primary", next.textPrimary); root.style.setProperty("--site-text-secondary", next.textSecondary);
    root.style.setProperty("--site-font-heading", next.fontHeading); root.style.setProperty("--site-font-body", next.fontBody);
    root.style.setProperty("--site-radius", radiusMap[next.radiusStyle] || "14px"); root.style.setProperty("--site-container", `${next.containerWidth}px`);
    root.dataset.buttonStyle = next.buttonStyle; root.dataset.cardStyle = next.cardStyle; root.dataset.navbarStyle = next.navbarStyle;
    setTheme(next);
  }, []);

  const refreshTheme = useCallback(async () => {
    try {
      const token = new URLSearchParams(window.location.search).get("preview_token");
      const response = await api.get("/theme", { params: token ? { preview_token: token } : {} });
      applyTheme(response.data?.data || response.data || {}, true);
    } catch (error) { applyTheme({}, false); console.error("Gagal memuat theme setting:", error); }
  }, [applyTheme]);

  useEffect(() => { applyTheme({}, false); refreshTheme(); const onFocus=()=>refreshTheme(); window.addEventListener("focus",onFocus); return()=>window.removeEventListener("focus",onFocus); }, [applyTheme, refreshTheme]);
  return <ThemeContext.Provider value={{ ...theme, refreshTheme }}>{children}</ThemeContext.Provider>;
}
export function useTheme(){ return useContext(ThemeContext); }
