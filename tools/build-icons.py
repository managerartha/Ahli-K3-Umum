#!/usr/bin/env python3
"""
Buat assets/css/icons.css: hanya ikon Font Awesome yang dipakai landing page,
sebagai SVG di dalam CSS (tanpa memuat paket Font Awesome lengkap ±300 KB).

Pemakaian (setelah menambah/mengganti ikon di landing page):
    npm i --prefix /tmp/fa @fortawesome/fontawesome-free@6.5.1
    python3 tools/build-icons.py /tmp/fa/node_modules/@fortawesome/fontawesome-free
Markup tetap sama: <i class="fa-solid fa-check"></i>
"""
import glob, os, re, sys, urllib.parse

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
FA = sys.argv[1]
SOURCES = ['ahli-k3-umum/index.php', 'ahli-k3-umum-bnsp/index.php', 'includes/reviews_section.php']
STYLES = {'fa-solid': 'solid', 'fa-regular': 'regular', 'fa-brands': 'brands'}
NOT_ICONS = {'fa-solid', 'fa-regular', 'fa-brands', 'fa-fw', 'fa-spin', 'fa-lg', 'fa-xl', 'fa-2x'}

# Nama alias Font Awesome 6 (misal laptop-house → house-laptop).
alias = {}
yml = open(os.path.join(FA, 'metadata/icons.yml'), encoding='utf-8').read()
for block in re.split(r'\n(?=[a-z0-9-]+:\n)', yml):
    m = re.match(r'([a-z0-9-]+):\n', block)
    if not m: continue
    name = m.group(1)
    am = re.search(r'\n  aliases:\n    names:\n((?:      - .+\n)+)', block + '\n')
    if am:
        for a in re.findall(r'- (.+)', am.group(1)): alias[a.strip().strip("'\"")] = name

text = ''.join(open(os.path.join(ROOT, p), encoding='utf-8').read() for p in SOURCES)
names = sorted({n for n in re.findall(r'\bfa-[a-z0-9-]+', text) if n not in NOT_ICONS})
regular_pairs = set(re.findall(r'fa-regular\s+(fa-[a-z0-9-]+)', text))

def svg(style, name):
    base = name[3:]
    for cand in (base, alias.get(base, '')):
        if not cand: continue
        f = os.path.join(FA, 'svgs', style, cand + '.svg')
        if os.path.exists(f): return open(f, encoding='utf-8').read()
    return None

def rule(selector, src):
    w, h = map(int, re.search(r'viewBox="0 0 (\d+) (\d+)"', src).groups())
    body = re.sub(r'<!--.*?-->', '', src).strip()
    uri = 'data:image/svg+xml,' + urllib.parse.quote(body, safe=' =:/"\'')
    uri = uri.replace('"', "'")
    return f'{selector}{{--fa-i:url("{uri}");width:{w / h:.4f}em}}'

out = ['/* Dibuat oleh tools/build-icons.py dari Font Awesome Free 6.5.1 (ikon: CC BY 4.0). Jangan edit manual. */',
       # Meniru ikon font: kotak setinggi line-height, gambar 1em di tengah, garis dasar sama.
       # flex-shrink:0 = ikon tidak mengecil di samping teks panjang (sama seperti ikon font).
       # ::before berisi karakter tak terlihat agar ikon punya garis dasar teks seperti ikon font.
       '.fa-solid,.fa-regular,.fa-brands{display:inline-block;line-height:1;width:1em;height:1em;height:1lh;font-style:normal;'
       'flex-shrink:0;overflow:hidden;vertical-align:-.125em;vertical-align:calc(-.125em - (1lh - 1em) / 2);background-color:currentColor;'
       '-webkit-mask:var(--fa-i) center/100% 1em no-repeat;mask:var(--fa-i) center/100% 1em no-repeat}',
       '.fa-solid::before,.fa-regular::before,.fa-brands::before{content:"\\200b"}']
missing = []
for n in names:
    src = svg('solid', n) or svg('brands', n) or svg('regular', n)
    if not src: missing.append(n); continue
    out.append(rule('.' + n, src))
    if n in regular_pairs and svg('regular', n):
        out.append(rule('.fa-regular.' + n, svg('regular', n)))
if missing:
    sys.exit('Ikon tidak ditemukan: ' + ', '.join(missing))
dst = os.path.join(ROOT, 'assets/css/icons.css')
open(dst, 'w', encoding='utf-8').write('\n'.join(out) + '\n')
print(f'{len(names)} ikon → {dst} ({os.path.getsize(dst) // 1024} KB)')
