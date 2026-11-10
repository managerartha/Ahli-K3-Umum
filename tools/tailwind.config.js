/** Konfigurasi Tailwind untuk landing page (dipakai oleh tools/build-css.sh). */
module.exports = {
  content: ['../ahli-k3-umum/**/*.php', '../kebijakan-privasi/**/*.php'],
  theme: {
    extend: {
      fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
      colors: {
        brand: {
          navy: '#071C6E', blue: '#0D3BE8', royal: '#0A2CB7', accent: '#A6F634',
          accentHover: '#92E022', subtle: '#EEF3FF', dark: '#041040',
        },
      },
      boxShadow: {
        card: '0 10px 30px -5px rgba(7, 28, 110, 0.07), 0 4px 6px -2px rgba(7, 28, 110, 0.04)',
        btn: '0 4px 14px 0 rgba(13, 59, 232, 0.30)',
        lime: '0 4px 16px 0 rgba(166, 246, 52, 0.40)',
      },
    },
  },
};
