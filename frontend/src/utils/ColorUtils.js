// Utilitas untuk mengubah 1 warna hex (dipilih admin lewat Theme Setting)
// menjadi rangkaian gradasi (shade 50-900) seperti palet bawaan Tailwind.
// Tujuannya supaya warna di frontend tidak terlihat "kaku" / flat karena
// dipakai mentah-mentah di semua tempat, tapi punya variasi terang-gelap
// yang natural untuk background lembut, hover state, border, dsb.

function hexToHsl(hex) {
  hex = hex.replace("#", "");
  if (hex.length === 3) {
    hex = hex.split("").map((c) => c + c).join("");
  }
  const r = parseInt(hex.substring(0, 2), 16) / 255;
  const g = parseInt(hex.substring(2, 4), 16) / 255;
  const b = parseInt(hex.substring(4, 6), 16) / 255;

  const max = Math.max(r, g, b);
  const min = Math.min(r, g, b);
  let h, s;
  const l = (max + min) / 2;

  if (max === min) {
    h = s = 0;
  } else {
    const d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    switch (max) {
      case r: h = (g - b) / d + (g < b ? 6 : 0); break;
      case g: h = (b - r) / d + 2; break;
      case b: h = (r - g) / d + 4; break;
      default: h = 0;
    }
    h /= 6;
  }
  return { h, s, l };
}

function hslToHex(h, s, l) {
  let r, g, b;
  if (s === 0) {
    r = g = b = l;
  } else {
    const hue2rgb = (p, q, t) => {
      if (t < 0) t += 1;
      if (t > 1) t -= 1;
      if (t < 1 / 6) return p + (q - p) * 6 * t;
      if (t < 1 / 2) return q;
      if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
      return p;
    };
    const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
    const p = 2 * l - q;
    r = hue2rgb(p, q, h + 1 / 3);
    g = hue2rgb(p, q, h);
    b = hue2rgb(p, q, h - 1 / 3);
  }
  const toHex = (v) => {
    const hex = Math.round(v * 255).toString(16).padStart(2, "0");
    return hex;
  };
  return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
}

// Fraksi seberapa jauh tiap stop bergerak dari warna dasar menuju putih/hitam.
// Angka ini yang bikin hasilnya kelihatan "wajar", bukan sekadar terang/gelap kasar.
const LIGHT_TARGETS = { 50: 0.95, 100: 0.85, 200: 0.65, 300: 0.45, 400: 0.2 };
const DARK_TARGETS = { 600: 0.15, 700: 0.3, 800: 0.45, 900: 0.6 };

/**
 * Ubah 1 warna hex jadi objek shade { 50: '#..', 100: '#..', ..., 500: baseHex, ..., 900: '#..' }
 */
export function generateShades(baseHex) {
  if (!baseHex || !/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(baseHex)) {
    baseHex = "#1E3A8A"; // fallback aman kalau format warna tidak valid
  }
  const { h, s, l } = hexToHsl(baseHex);
  const shades = { 500: baseHex.toLowerCase() };

  for (const [stop, frac] of Object.entries(LIGHT_TARGETS)) {
    const newL = l + (0.97 - l) * frac;
    shades[stop] = hslToHex(h, s, Math.min(newL, 0.98));
  }
  for (const [stop, frac] of Object.entries(DARK_TARGETS)) {
    const newL = l - (l - 0.12) * frac;
    shades[stop] = hslToHex(h, s, Math.max(newL, 0.08));
  }

  return shades;
}

/**
 * Suntikkan shade warna sebagai CSS custom properties ke elemen <html>,
 * supaya semua utility class Tailwind (bg-primary-100, text-primary-700, dst)
 * otomatis ikut berubah tanpa perlu build ulang.
 */
export function applyColorShades(prefix, baseHex) {
  const shades = generateShades(baseHex);
  const root = document.documentElement;
  Object.entries(shades).forEach(([stop, hex]) => {
    root.style.setProperty(`--color-${prefix}-${stop}`, hex);
  });
  // Alias tanpa angka (dipakai class seperti bg-primary, text-accent)
  root.style.setProperty(`--color-${prefix}`, shades[500]);
}
