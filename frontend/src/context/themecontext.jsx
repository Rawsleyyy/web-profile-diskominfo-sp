import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useLayoutEffect,
  useRef,
  useState,
} from "react";
import { api } from "../services/api";
import { applyThemeVariables } from "../utils/ColorUtils";

const THEME_CACHE_KEY = "diskominfo-brand-theme";

const DEFAULT_THEME = {
  primary: "#1E3A8A",
  accent: "#DC2626",
  loaded: false,
};

function readCachedTheme() {
  try {
    const cached = JSON.parse(localStorage.getItem(THEME_CACHE_KEY) || "null");
    if (cached?.primary && cached?.accent) {
      return { ...DEFAULT_THEME, ...cached, loaded: false };
    }
  } catch {
    // Cache yang rusak cukup diabaikan; API akan mengisi ulang.
  }
  return DEFAULT_THEME;
}

const ThemeContext = createContext({
  ...DEFAULT_THEME,
  refreshTheme: async () => {},
});

export function ThemeProvider({ children }) {
  const [theme, setTheme] = useState(readCachedTheme);
  const themeRef = useRef(theme);

  const applyTheme = useCallback((primary, accent, loaded = true) => {
    applyThemeVariables(primary, accent);

    const nextTheme = { primary, accent, loaded };
    themeRef.current = nextTheme;
    setTheme(nextTheme);

    try {
      localStorage.setItem(
        THEME_CACHE_KEY,
        JSON.stringify({ primary, accent }),
      );
    } catch {
      // Local storage tidak wajib untuk menjalankan aplikasi.
    }
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
      // Gunakan cache/default agar UI tetap stabil ketika API sementara gagal.
      applyTheme(themeRef.current.primary, themeRef.current.accent, false);
      console.error("Gagal memuat theme setting:", error);
    }
  }, [applyTheme]);

  // useLayoutEffect mengurangi flash warna bawaan sebelum theme diterapkan.
  useLayoutEffect(() => {
    applyThemeVariables(theme.primary, theme.accent);
  }, [theme.primary, theme.accent]);

  useEffect(() => {
    refreshTheme();

    const onFocus = () => refreshTheme();
    window.addEventListener("focus", onFocus);

    // Tidak perlu polling terlalu agresif. Perubahan dashboard tetap diperbarui
    // ketika tab kembali aktif dan setiap lima menit sebagai cadangan.
    const timer = window.setInterval(refreshTheme, 300_000);

    return () => {
      window.removeEventListener("focus", onFocus);
      window.clearInterval(timer);
    };
  }, [refreshTheme]);

  return (
    <ThemeContext.Provider value={{ ...theme, refreshTheme }}>
      {children}
    </ThemeContext.Provider>
  );
}

export function useTheme() {
  return useContext(ThemeContext);
}
