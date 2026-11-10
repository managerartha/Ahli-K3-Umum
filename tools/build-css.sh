#!/usr/bin/env bash
# Build CSS landing page dari kelas Tailwind yang dipakai di file PHP.
# Jalankan ulang setiap kali landing page diubah: bash tools/build-css.sh
set -euo pipefail
cd "$(dirname "$0")"
npx --yes tailwindcss@3.4.17 -c tailwind.config.js -i landing.css -o ../assets/css/ahli-k3-umum.css --minify
