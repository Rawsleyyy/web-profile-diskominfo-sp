const DEFAULT_COLOR = "#1e3a8a";
const HEX_PATTERN = /^#([0-9a-f]{3}|[0-9a-f]{6})$/i;

function normalizeHex(value, fallback = DEFAULT_COLOR) {
  let hex = typeof value === "string" ? value.trim() : "";
  if (!HEX_PATTERN.test(hex)) hex = fallback;
  if (hex.length === 4) {
    hex = `#${hex.slice(1).split("").map((char) => char + char).join("")}`;
  }
  return hex.toLowerCase();
}

function hexToRgb(hex) {
  const normalized = normalizeHex(hex).slice(1);
  return {
    r: Number.parseInt(normalized.slice(0, 2), 16),
    g: Number.parseInt(normalized.slice(2, 4), 16),
    b: Number.parseInt(normalized.slice(4, 6), 16),
  };
}

function rgbToHex({ r, g, b }) {
  const toHex = (channel) => Math.round(Math.max(0, Math.min(255, channel)))
    .toString(16)
    .padStart(2, "0");

  return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
}

export function mixHex(baseHex, targetHex, targetWeight = 0.5) {
  const base = hexToRgb(baseHex);
  const target = hexToRgb(targetHex);
  const weight = Math.max(0, Math.min(1, targetWeight));

  return rgbToHex({
    r: base.r * (1 - weight) + target.r * weight,
    g: base.g * (1 - weight) + target.g * weight,
    b: base.b * (1 - weight) + target.b * weight,
  });
}

function channelLuminance(channel) {
  const value = channel / 255;
  return value <= 0.03928
    ? value / 12.92
    : ((value + 0.055) / 1.055) ** 2.4;
}

export function relativeLuminance(hex) {
  const { r, g, b } = hexToRgb(hex);
  return (
    0.2126 * channelLuminance(r)
    + 0.7152 * channelLuminance(g)
    + 0.0722 * channelLuminance(b)
  );
}

export function contrastRatio(firstHex, secondHex) {
  const first = relativeLuminance(firstHex);
  const second = relativeLuminance(secondHex);
  const lighter = Math.max(first, second);
  const darker = Math.min(first, second);
  return (lighter + 0.05) / (darker + 0.05);
}

/**
 * Menggeser warna menuju hitam/putih sampai memenuhi rasio kontras minimum.
 * Fungsi ini mencegah warna tema yang terlalu neon dipakai langsung sebagai
 * warna teks atau latar navigasi.
 */
export function ensureContrast(
  foregroundHex,
  backgroundHex,
  minimumRatio = 4.5,
  towardHex = "#020617",
) {
  let result = normalizeHex(foregroundHex);
  const background = normalizeHex(backgroundHex, "#ffffff");

  if (contrastRatio(result, background) >= minimumRatio) return result;

  for (let step = 1; step <= 24; step += 1) {
    result = mixHex(foregroundHex, towardHex, step / 24);
    if (contrastRatio(result, background) >= minimumRatio) return result;
  }

  return normalizeHex(towardHex);
}

/**
 * Palet 50–900 tetap disediakan supaya class Tailwind lama masih bekerja,
 * tetapi alias `primary` dan `accent` akan diarahkan ke warna yang aman dibaca.
 */
export function generateShades(baseHex) {
  const base = normalizeHex(baseHex);

  return {
    50: mixHex(base, "#ffffff", 0.94),
    100: mixHex(base, "#ffffff", 0.84),
    200: mixHex(base, "#ffffff", 0.68),
    300: mixHex(base, "#ffffff", 0.48),
    400: mixHex(base, "#ffffff", 0.24),
    500: base,
    600: mixHex(base, "#020617", 0.16),
    700: mixHex(base, "#020617", 0.31),
    800: mixHex(base, "#020617", 0.48),
    900: mixHex(base, "#020617", 0.66),
  };
}

function toRgbChannels(hex) {
  const { r, g, b } = hexToRgb(hex);
  return `${r} ${g} ${b}`;
}

export function buildThemeTokens(primaryHex, accentHex) {
  const primary = normalizeHex(primaryHex, "#1e3a8a");
  const accent = normalizeHex(accentHex, "#dc2626");

  // Warna aksi pada halaman terang selalu cukup gelap untuk teks putih.
  const primaryOnLight = ensureContrast(primary, "#ffffff", 4.8, "#020617");
  const accentOnLight = ensureContrast(accent, "#ffffff", 4.8, "#020617");

  // Warna dekoratif/teks pada permukaan gelap selalu cukup terang.
  const primaryOnDark = ensureContrast(primary, "#07101f", 4.5, "#ffffff");
  const accentOnDark = ensureContrast(accent, "#07101f", 4.5, "#ffffff");

  // Navbar/footer tetap mengikuti warna admin, tetapi warna selalu dicampur
  // dengan navy agar teks putih tidak pernah tenggelam.
  const navbarStart = ensureContrast(
    mixHex(primary, "#081426", 0.52),
    "#ffffff",
    5.2,
    "#020617",
  );
  const navbarMiddle = ensureContrast(
    mixHex(primary, "#123f75", 0.38),
    "#ffffff",
    5.0,
    "#020617",
  );
  const navbarEnd = ensureContrast(
    mixHex(accent, "#087ea9", 0.52),
    "#ffffff",
    4.8,
    "#020617",
  );

  const footerStart = ensureContrast(
    mixHex(primary, "#020617", 0.76),
    "#ffffff",
    7,
    "#000000",
  );
  const footerEnd = ensureContrast(
    mixHex(accent, "#020617", 0.84),
    "#ffffff",
    7,
    "#000000",
  );

  const heroStart = ensureContrast(
    mixHex(primary, "#020617", 0.42),
    "#ffffff",
    5.5,
    "#000000",
  );
  const heroMiddle = ensureContrast(
    mixHex(primary, "#020617", 0.56),
    "#ffffff",
    5.5,
    "#000000",
  );

  return {
    primary,
    accent,
    primaryOnLight,
    accentOnLight,
    primaryOnDark,
    accentOnDark,
    navbarStart,
    navbarMiddle,
    navbarEnd,
    footerStart,
    footerEnd,
    heroStart,
    heroMiddle,
    softLight: mixHex(primary, "#ffffff", 0.91),
    softDark: mixHex(primary, "#0f172a", 0.82),
    borderLight: mixHex(primary, "#ffffff", 0.74),
    borderDark: mixHex(primaryOnDark, "#0f172a", 0.72),
  };
}

function setPalette(root, prefix, baseHex, readableAlias) {
  const shades = generateShades(baseHex);
  Object.entries(shades).forEach(([stop, hex]) => {
    root.style.setProperty(`--color-${prefix}-${stop}`, hex);
  });
  root.style.setProperty(`--color-${prefix}`, readableAlias);
}

/**
 * Mekanisme tema versi baru:
 * - Warna admin menjadi identitas/brand.
 * - Light/dark mode mengatur surface, teks, dan border secara terpisah.
 * - Navbar/footer memakai gradien brand yang sudah diamankan kontrasnya.
 */
export function applyThemeVariables(primaryHex, accentHex) {
  const root = document.documentElement;
  const tokens = buildThemeTokens(primaryHex, accentHex);

  setPalette(root, "primary", tokens.primary, tokens.primaryOnLight);
  setPalette(root, "accent", tokens.accent, tokens.accentOnLight);

  root.style.setProperty("--theme-primary-raw", tokens.primary);
  root.style.setProperty("--theme-accent-raw", tokens.accent);
  root.style.setProperty("--theme-on-light", tokens.primaryOnLight);
  root.style.setProperty("--theme-accent-on-light", tokens.accentOnLight);
  root.style.setProperty("--theme-on-dark", tokens.primaryOnDark);
  root.style.setProperty("--theme-accent-on-dark", tokens.accentOnDark);
  root.style.setProperty("--theme-soft-light", tokens.softLight);
  root.style.setProperty("--theme-soft-dark", tokens.softDark);
  root.style.setProperty("--theme-border-light", tokens.borderLight);
  root.style.setProperty("--theme-border-dark", tokens.borderDark);

  root.style.setProperty(
    "--theme-navbar-gradient",
    `linear-gradient(105deg, ${tokens.navbarStart} 0%, ${tokens.navbarMiddle} 52%, ${tokens.navbarEnd} 100%)`,
  );
  root.style.setProperty(
    "--theme-footer-gradient",
    `linear-gradient(118deg, ${tokens.footerStart} 0%, ${tokens.footerEnd} 100%)`,
  );
  root.style.setProperty(
    "--theme-panel-gradient",
    `linear-gradient(135deg, ${tokens.navbarMiddle} 0%, ${tokens.navbarEnd} 100%)`,
  );
  root.style.setProperty(
    "--theme-hero-gradient",
    `linear-gradient(90deg, rgba(${toRgbChannels(tokens.heroStart)} / 0.94) 0%, rgba(${toRgbChannels(tokens.heroMiddle)} / 0.72) 48%, rgba(${toRgbChannels(tokens.heroMiddle)} / 0.08) 100%)`,
  );
  root.style.setProperty("--theme-navbar-top", footerStartOrFallback(tokens.footerStart));
  root.style.setProperty("--theme-navbar-accent", tokens.accentOnDark);
  root.style.setProperty("--theme-footer-accent", tokens.accentOnDark);
  root.style.setProperty("--theme-focus", tokens.primaryOnLight);
}

function footerStartOrFallback(value) {
  return value || "#111827";
}

// Dipertahankan untuk kompatibilitas dengan kode lama.
export function applyColorShades(prefix, baseHex) {
  const root = document.documentElement;
  const normalized = normalizeHex(baseHex);
  const readable = ensureContrast(normalized, "#ffffff", 4.8, "#020617");
  setPalette(root, prefix, normalized, readable);
}
