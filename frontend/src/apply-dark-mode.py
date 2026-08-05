import re, glob

mapping = [
    ("bg-white", "dark:bg-slate-800"),
    ("bg-slate-50", "dark:bg-slate-900"),
    ("bg-slate-100", "dark:bg-slate-800"),
    ("text-slate-800", "dark:text-slate-100"),
    ("text-slate-700", "dark:text-slate-200"),
    ("text-slate-600", "dark:text-slate-300"),
    ("text-slate-500", "dark:text-slate-400"),
    ("text-slate-400", "dark:text-slate-500"),
    ("border-slate-100", "dark:border-slate-700"),
    ("border-slate-200", "dark:border-slate-700"),
]

files = glob.glob("src/**/*.jsx", recursive=True)
total_changes = 0

for fp in files:
    with open(fp, "r", encoding="utf-8") as f:
        content = f.read()
    original = content
    for light_cls, dark_cls in mapping:
        pattern = re.compile(r'\b' + re.escape(light_cls) + r'\b(?!/)')
        content, n = pattern.subn(f"{light_cls} {dark_cls}", content)
        total_changes += n
    if content != original:
        with open(fp, "w", encoding="utf-8") as f:
            f.write(content)
        print(f"{fp}: updated")

print(f"\nTotal penyisipan dark: variant: {total_changes}")