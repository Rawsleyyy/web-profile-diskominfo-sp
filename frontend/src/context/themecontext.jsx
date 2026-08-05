import { createContext, useCallback, useContext, useEffect, useState } from "react";
import { api } from "../services/api";
import { applyColorShades } from "../utils/ColorUtils";

const DEFAULT_THEME = {
  primary: "#1E3A8A",
  accent: "#DC2626",
  loaded: false,
};

const ThemeContext = createContext({ ...DEFAULT_THEME, refreshTheme: async () => {} });

export function ThemeProvider({ children }) {
  const [theme, setTheme] = useState(DEFAULT_THEME);

  const applyTheme = useCallback((primary, accent, loaded = true) => {
    applyColorShades("primary", primary);
    applyColorShades("accent", accent);
    setTheme((current) => ({ ...current, primary, accent, loaded }));
  }, []);

  const refreshTheme = useCallback(async () => {
    try {
      const response = await api.get("/theme");
      const data = response.data?.data || response.data || {};
      applyTheme(
        data.primary_color_hex || DEFAULT_THEME.primary,
        data.accent_color_hex || DEFAULT_THEME.accent,
        true,
      );
    } catch (error) {
      applyTheme(DEFAULT_THEME.primary, DEFAULT_THEME.accent, false);
      console.error("Gagal memuat theme setting:", error);
    }
  }, [applyTheme]);

  useEffect(() => {
    applyTheme(DEFAULT_THEME.primary, DEFAULT_THEME.accent, false);
    refreshTheme();

    const onFocus = () => refreshTheme();
    window.addEventListener("focus", onFocus);
    const timer = window.setInterval(refreshTheme, 60_000);

    return () => {
      window.removeEventListener("focus", onFocus);
      window.clearInterval(timer);
    };
  }, [applyTheme, refreshTheme]);

  return (
    <ThemeContext.Provider value={{ ...theme, refreshTheme }}>
      {children}
    </ThemeContext.Provider>
  );
}

export function useTheme() {
  return useContext(ThemeContext);
}
