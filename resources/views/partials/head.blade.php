{{-- Shared <head> for all pages --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Apply theme BEFORE render — prevents flash --}}
<script>
(function(){
    const t = localStorage.getItem('theme') || 'dark';
    document.documentElement.classList.toggle('dark', t === 'dark');
})();
</script>

<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
tailwind.config = {
    darkMode: 'class',
    theme: {
        screens: { 'xs':'480px','sm':'640px','md':'768px','lg':'1024px','xl':'1280px','2xl':'1536px' },
        extend: { fontFamily: { sans: ['Manrope','sans-serif'] } },
    },
}
</script>

<style>
/* ═══════════════════════════════════════════════════════════════════════════
   CSS VARIABLES — single source of truth for both themes
   Semua nama alias juga didefinisikan agar sidebar/header tidak error
═══════════════════════════════════════════════════════════════════════════ */
:root {
    /* ── Light ── */
    --bg:                #f0f4f8;
    --bg-card:           #ffffff;
    --bg-sidebar:        #ffffff;
    --bg-header:         rgba(255,255,255,0.9);
    --bg-input:          #f1f5f9;
    --bg-hover:          rgba(0,0,0,0.04);
    --bg-subtle:         rgba(0,0,0,0.03);
    --bg-badge:          rgba(0,0,0,0.06);

    --border:            rgba(0,0,0,0.09);
    --divider:           rgba(0,0,0,0.07);

    --text-1:            #0f172a;
    --text-2:            #334155;
    --text-3:            #64748b;
    --text-4:            #94a3b8;

    /* Alias agar sidebar/header lama tidak error */
    --text-primary:      #0f172a;
    --text-secondary:    #334155;
    --text-muted:        #64748b;
    --input-bg:          #f1f5f9;
    --sidebar-active-text: #7c3aed;

    --accent-violet:     #7c3aed;
    --accent-teal:       #0d9488;
    --accent-red:        #dc2626;
    --accent-yellow:     #d97706;
    --accent-green:      #059669;
    --accent-pink:       #db2777;
    --accent-blue:       #2563eb;
    --accent-orange:     #ea580c;

    --shadow-sm:         0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md:         0 4px 12px rgba(0,0,0,0.08);
    --shadow-lg:         0 10px 30px rgba(0,0,0,0.1);

    --scrollbar:         rgba(124,58,237,0.25);
    --sidebar-active-bg: rgba(124,58,237,0.08);
    --sidebar-active-tx: #7c3aed;

    /* Icon container backgrounds — light mode */
    --icon-teal-bg:      rgba(13,148,136,0.12);
    --icon-red-bg:       rgba(220,38,38,0.10);
    --icon-violet-bg:    rgba(124,58,237,0.10);
    --icon-yellow-bg:    rgba(217,119,6,0.10);
    --icon-green-bg:     rgba(5,150,105,0.10);
    --icon-blue-bg:      rgba(37,99,235,0.10);
    --icon-pink-bg:      rgba(219,39,119,0.10);
    --icon-orange-bg:    rgba(234,88,12,0.10);

    /* Notification card colors — light mode */
    --notif-danger-bg:   rgba(220,38,38,0.07);
    --notif-danger-border: rgba(220,38,38,0.2);
    --notif-warning-bg:  rgba(217,119,6,0.07);
    --notif-warning-border: rgba(217,119,6,0.2);
    --notif-success-bg:  rgba(13,148,136,0.07);
    --notif-success-border: rgba(13,148,136,0.2);
}

.dark {
    /* ── Dark ── */
    --bg:                #0b1326;
    --bg-card:           rgba(255,255,255,0.04);
    --bg-sidebar:        #020617;
    --bg-header:         rgba(11,19,38,0.6);
    --bg-input:          rgba(255,255,255,0.06);
    --bg-hover:          rgba(255,255,255,0.04);
    --bg-subtle:         rgba(255,255,255,0.03);
    --bg-badge:          rgba(255,255,255,0.07);

    --border:            rgba(255,255,255,0.08);
    --divider:           rgba(255,255,255,0.06);

    --text-1:            #f1f5f9;
    --text-2:            #cbd5e1;
    --text-3:            #94a3b8;
    --text-4:            #64748b;

    /* Alias */
    --text-primary:      #f1f5f9;
    --text-secondary:    #cbd5e1;
    --text-muted:        #94a3b8;
    --input-bg:          rgba(255,255,255,0.06);
    --sidebar-active-text: #c4b5fd;

    --accent-violet:     #a78bfa;
    --accent-teal:       #2dd4bf;
    --accent-red:        #f87171;
    --accent-yellow:     #fbbf24;
    --accent-green:      #34d399;
    --accent-pink:       #f472b6;
    --accent-blue:       #60a5fa;
    --accent-orange:     #fb923c;

    --shadow-sm:         0 1px 3px rgba(0,0,0,0.3);
    --shadow-md:         0 4px 12px rgba(0,0,0,0.4);
    --shadow-lg:         0 10px 30px rgba(0,0,0,0.5);

    --scrollbar:         rgba(124,58,237,0.4);
    --sidebar-active-bg: rgba(124,58,237,0.2);
    --sidebar-active-tx: #c4b5fd;

    /* Icon container backgrounds — dark mode */
    --icon-teal-bg:      rgba(45,212,191,0.12);
    --icon-red-bg:       rgba(248,113,113,0.12);
    --icon-violet-bg:    rgba(167,139,250,0.12);
    --icon-yellow-bg:    rgba(251,191,36,0.12);
    --icon-green-bg:     rgba(52,211,153,0.12);
    --icon-blue-bg:      rgba(96,165,250,0.12);
    --icon-pink-bg:      rgba(244,114,182,0.12);
    --icon-orange-bg:    rgba(251,146,60,0.12);

    /* Notification card colors — dark mode (solid, tidak transparan) */
    --notif-danger-bg:   #3b1212;
    --notif-danger-border: #7f1d1d;
    --notif-warning-bg:  #2d1f07;
    --notif-warning-border: #78350f;
    --notif-success-bg:  #0d2e2a;
    --notif-success-border: #134e4a;
}

/* ═══════════════════════════════════════════════════════════════════════════
   BASE
═══════════════════════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }
html { transition: background 0.25s ease; }
body {
    font-family: 'Manrope', sans-serif;
    background: var(--bg);
    color: var(--text-2);
    min-height: 100vh;
}

/* ═══════════════════════════════════════════════════════════════════════════
   TYPOGRAPHY
   Aturan: text-white & heading hanya override di luar elemen gradient/badge
═══════════════════════════════════════════════════════════════════════════ */

/* Headings default ke text-1 */
h1, h2, h3, h4, h5, h6 { color: var(--text-1); }

/* .text-white di luar gradient element → text-1 */
.text-white { color: var(--text-1) !important; }

/* Elemen dengan gradient background: teks tetap putih */
[class*="bg-gradient-to-r"] .text-white,
[class*="bg-gradient-to-br"] .text-white,
[class*="bg-gradient-to-bl"] .text-white,
[class*="from-violet-6"] .text-white,
[class*="from-teal-6"] .text-white,
[class*="from-red-6"] .text-white,
[class*="from-pink-6"] .text-white,
[class*="from-blue-6"] .text-white,
[class*="from-orange-6"] .text-white,
[class*="from-yellow-6"] .text-white,
[class*="from-emerald-6"] .text-white { color: #ffffff !important; }

/* Tombol gradient: semua teks di dalamnya putih */
button[class*="from-violet"],
button[class*="from-teal"],
button[class*="from-red"],
a[class*="from-violet"],
a[class*="from-teal"] { color: #ffffff !important; }

button[class*="from-violet"] *,
button[class*="from-teal"] *,
button[class*="from-red"] *,
a[class*="from-violet"] *,
a[class*="from-teal"] * { color: #ffffff !important; }

/* Slate scale */
.text-slate-50, .text-slate-100, .text-slate-200 { color: var(--text-1) !important; }
.text-slate-300  { color: var(--text-2) !important; }
.text-slate-400  { color: var(--text-3) !important; }
.text-slate-500, .text-slate-600 { color: var(--text-4) !important; }
.text-slate-700, .text-slate-800, .text-slate-900 { color: var(--text-4) !important; }

/* Accent colors via CSS variable */
.text-violet-300, .text-violet-400, .text-violet-500 { color: var(--accent-violet); }
.text-teal-300,   .text-teal-400,   .text-teal-500   { color: var(--accent-teal); }
.text-red-300,    .text-red-400,    .text-red-500     { color: var(--accent-red); }
.text-yellow-300, .text-yellow-400, .text-yellow-500  { color: var(--accent-yellow); }
.text-green-300,  .text-green-400,  .text-green-500   { color: var(--accent-green); }
.text-pink-300,   .text-pink-400,   .text-pink-500    { color: var(--accent-pink); }
.text-blue-300,   .text-blue-400,   .text-blue-500    { color: var(--accent-blue); }
.text-orange-300, .text-orange-400, .text-orange-500  { color: var(--accent-orange); }

/* Gradient text */
.text-transparent { color: transparent !important; }
.bg-clip-text { -webkit-background-clip: text !important; background-clip: text !important; }

/* ═══════════════════════════════════════════════════════════════════════════
   ICON CONTAINERS — bg-{color}/15, bg-{color}/20, bg-{color}/10
   Gunakan CSS variable agar otomatis berubah per tema
═══════════════════════════════════════════════════════════════════════════ */
.bg-teal-500\/15,   .bg-teal-500\/10,   .bg-teal-500\/20   { background: var(--icon-teal-bg) !important; }
.bg-red-500\/15,    .bg-red-500\/10,    .bg-red-500\/20     { background: var(--icon-red-bg) !important; }
.bg-violet-500\/15, .bg-violet-500\/10, .bg-violet-500\/20  { background: var(--icon-violet-bg) !important; }
.bg-violet-600\/15, .bg-violet-600\/10, .bg-violet-600\/20  { background: var(--icon-violet-bg) !important; }
.bg-yellow-500\/15, .bg-yellow-500\/10, .bg-yellow-500\/20  { background: var(--icon-yellow-bg) !important; }
.bg-green-500\/15,  .bg-green-500\/10,  .bg-green-500\/20   { background: var(--icon-green-bg) !important; }
.bg-blue-500\/15,   .bg-blue-500\/10,   .bg-blue-500\/20    { background: var(--icon-blue-bg) !important; }
.bg-pink-500\/15,   .bg-pink-500\/10,   .bg-pink-500\/20    { background: var(--icon-pink-bg) !important; }
.bg-orange-500\/15, .bg-orange-500\/10, .bg-orange-500\/20  { background: var(--icon-orange-bg) !important; }

/* Badge teal/red di transaksi list */
.bg-teal-500\/10 { background: var(--icon-teal-bg) !important; }
.bg-red-500\/10  { background: var(--icon-red-bg) !important; }

/* ═══════════════════════════════════════════════════════════════════════════
   GLASS CARD
═══════════════════════════════════════════════════════════════════════════ */
.glass {
    background: var(--bg-card);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    transition: background 0.25s, border-color 0.25s, box-shadow 0.25s;
}

/* ═══════════════════════════════════════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════════════════════════════════════ */
#sidebar {
    background: var(--bg-sidebar) !important;
    border-right: 1px solid var(--border) !important;
    box-shadow: var(--shadow-lg);
}
.sidebar-link {
    color: var(--text-3);
    transition: all 0.2s;
    border-radius: 0.75rem;
}
.sidebar-link:hover {
    background: var(--bg-hover);
    color: var(--text-1);
}
.sidebar-link.active {
    background: var(--sidebar-active-bg);
    color: var(--sidebar-active-tx);
}

/* ═══════════════════════════════════════════════════════════════════════════
   HEADER
═══════════════════════════════════════════════════════════════════════════ */
.app-header {
    background: var(--bg-header) !important;
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border) !important;
}

/* ═══════════════════════════════════════════════════════════════════════════
   INPUTS & FORMS
═══════════════════════════════════════════════════════════════════════════ */
input:not([type="radio"]):not([type="checkbox"]):not([type="range"]),
select,
textarea {
    background: var(--bg-input) !important;
    color: var(--text-1) !important;
    border: 1px solid var(--border) !important;
    border-radius: 0.75rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
}
input:not([type="radio"]):not([type="checkbox"]):not([type="range"]):focus,
select:focus,
textarea:focus {
    border-color: var(--accent-violet) !important;
    box-shadow: 0 0 0 3px rgba(124,58,237,0.15) !important;
}
html:not(.dark) input:not([type="radio"]):not([type="checkbox"]):not([type="range"]):focus,
html:not(.dark) select:focus,
html:not(.dark) textarea:focus {
    box-shadow: 0 0 0 3px rgba(124,58,237,0.12) !important;
}
input::placeholder, textarea::placeholder { color: var(--text-4) !important; }
input[type="date"]::-webkit-calendar-picker-indicator { opacity: 0.5; cursor: pointer; }
.dark input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(0.7); }

/* Select option — harus solid color, tidak bisa pakai rgba transparan */
select option, select optgroup {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}
.dark select option, .dark select optgroup {
    background: #1e293b !important;
    color: #f1f5f9 !important;
}

/* ═══════════════════════════════════════════════════════════════════════════
   BORDERS & DIVIDERS
═══════════════════════════════════════════════════════════════════════════ */
[class*="border-white"] { border-color: var(--border) !important; }
[class*="divide-white"] > * + * { border-color: var(--divider) !important; }

/* ═══════════════════════════════════════════════════════════════════════════
   BACKGROUNDS — bg-white/* (subtle/badge) dan bg-slate-9*
   PENTING: Jangan override bg-{color}/{opacity} di sini, sudah ditangani
   oleh icon container section di atas
═══════════════════════════════════════════════════════════════════════════ */
.bg-white\/5  { background: var(--bg-subtle) !important; }
.bg-white\/10 { background: var(--bg-badge)  !important; }
.bg-white\/\[0\.03\] { background: var(--bg-subtle) !important; }

.bg-slate-900, .bg-slate-950 { background: var(--bg-card) !important; }

.hover\:bg-white\/5:hover  { background: var(--bg-hover) !important; }
.hover\:bg-white\/10:hover { background: var(--bg-badge) !important; }

/* ═══════════════════════════════════════════════════════════════════════════
   MODALS
═══════════════════════════════════════════════════════════════════════════ */
[id$="Modal"] > div > div {
    background: var(--bg-card) !important;
    border-color: var(--border) !important;
}

/* ═══════════════════════════════════════════════════════════════════════════
   TABLES (admin)
═══════════════════════════════════════════════════════════════════════════ */
.data-table thead { background: var(--bg-sidebar) !important; }
.data-table thead th { color: var(--text-3) !important; }
.data-table tbody tr:hover { background: var(--bg-hover) !important; }
.data-table td { color: var(--text-2) !important; border-color: var(--divider) !important; }

/* ═══════════════════════════════════════════════════════════════════════════
   SCROLLBAR
═══════════════════════════════════════════════════════════════════════════ */
::-webkit-scrollbar { width: 4px; height: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--scrollbar); border-radius: 4px; }
.trx-scroll {
    max-height: 600px;
    min-height: 0;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--scrollbar) transparent;
}

/* ═══════════════════════════════════════════════════════════════════════════
   COMPONENTS
═══════════════════════════════════════════════════════════════════════════ */
.glow-primary   { box-shadow: 0 0 20px rgba(124,58,237,0.25); }
.glow-secondary { box-shadow: 0 0 20px rgba(45,212,191,0.2); }
.glow-danger    { box-shadow: 0 0 20px rgba(248,113,113,0.2); }

.ms {
    font-family: 'Material Symbols Outlined';
    font-weight: normal; font-style: normal; font-size: 20px; line-height: 1;
    letter-spacing: normal; text-transform: none;
    display: inline-block; white-space: nowrap;
    direction: ltr; -webkit-font-smoothing: antialiased;
}

.toast-anim { animation: slideInRight 0.35s ease; }
.modal-anim { animation: scaleIn 0.25s ease; }
@keyframes slideInRight { from { transform:translateX(110%); opacity:0; } to { transform:translateX(0); opacity:1; } }
@keyframes scaleIn      { from { transform:scale(0.92);     opacity:0; } to { transform:scale(1);     opacity:1; } }

.trx-tab-btn { color: var(--text-4); border-radius: 0.75rem; }
.trx-tab-btn:hover { background: var(--bg-hover); color: var(--text-1); }
.trx-tab-btn[data-active="1"] { background: var(--accent-violet) !important; color: #fff !important; }

.theme-toggle {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    background: var(--bg-input);
    border: 1px solid var(--border);
    color: var(--text-3);
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}
.theme-toggle:hover { background: var(--bg-hover); color: var(--text-1); }

/* ═══════════════════════════════════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════════════════════════════════ */
@media (max-width: 1023px) { .main-with-sidebar { margin-left: 0 !important; } }
.mobile-menu-btn { display: none; }
@media (max-width: 1023px) { .mobile-menu-btn { display: flex; } }

/* ═══════════════════════════════════════════════════════════════════════════
   LIGHT MODE — fixes spesifik yang tidak bisa via variable saja
═══════════════════════════════════════════════════════════════════════════ */
html:not(.dark) body { background: var(--bg); }

/* Ambient blobs — redup di light */
html:not(.dark) .blob-violet { opacity: 0.06 !important; }
html:not(.dark) .blob-teal   { opacity: 0.05 !important; }
html:not(.dark) .blob-red    { opacity: 0.05 !important; }

/* Glass card di light — lebih solid */
html:not(.dark) .glass {
    background: rgba(255,255,255,0.92) !important;
    border-color: rgba(0,0,0,0.08) !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07), 0 4px 16px rgba(0,0,0,0.05) !important;
}

/* Sidebar di light */
html:not(.dark) #sidebar {
    background: #ffffff !important;
    border-right-color: rgba(0,0,0,0.08) !important;
    box-shadow: 4px 0 20px rgba(0,0,0,0.06) !important;
}

/* Header di light */
html:not(.dark) .app-header {
    background: rgba(255,255,255,0.95) !important;
    border-bottom-color: rgba(0,0,0,0.08) !important;
    box-shadow: 0 1px 8px rgba(0,0,0,0.06) !important;
}

/* Modal di light */
html:not(.dark) [id$="Modal"] > div {
    background: #ffffff !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
    /* Hapus backdrop-filter agar tidak ada efek blur tembus */
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}
html:not(.dark) [id$="Modal"] > div > div,
html:not(.dark) [id$="Modal"] > div > form {
    background: #ffffff !important;
}
html:not(.dark) [id$="Modal"] { background: rgba(0,0,0,0.4) !important; }

/* Progress bar track di light */
html:not(.dark) .bg-white\/5 { background: rgba(0,0,0,0.06) !important; }

/* Stats card di admin: teks di atas gradient tetap putih */
html:not(.dark) .glass .bg-gradient-to-br { opacity: 1; }
html:not(.dark) .bg-gradient-to-br .text-white,
html:not(.dark) .bg-gradient-to-r  .text-white { color: #ffffff !important; }

/* Tombol gradient di light: teks putih */
html:not(.dark) .bg-gradient-to-r.from-violet-600,
html:not(.dark) .bg-gradient-to-r.from-teal-600,
html:not(.dark) .bg-gradient-to-r.from-red-600,
html:not(.dark) .bg-gradient-to-br.from-violet-600,
html:not(.dark) .bg-gradient-to-br.from-teal-500 { color: #ffffff !important; }

html:not(.dark) .bg-gradient-to-r.from-violet-600 *,
html:not(.dark) .bg-gradient-to-r.from-teal-600 *,
html:not(.dark) .bg-gradient-to-r.from-red-600 *,
html:not(.dark) .bg-gradient-to-br.from-violet-600 *,
html:not(.dark) .bg-gradient-to-br.from-teal-500 * { color: #ffffff !important; }

/* Teks di dalam badge/pill berwarna tetap pakai warna accent */
html:not(.dark) .text-teal-400  { color: var(--accent-teal) !important; }
html:not(.dark) .text-teal-300  { color: var(--accent-teal) !important; }
html:not(.dark) .text-red-400   { color: var(--accent-red) !important; }
html:not(.dark) .text-violet-400 { color: var(--accent-violet) !important; }
html:not(.dark) .text-yellow-400 { color: var(--accent-yellow) !important; }
html:not(.dark) .text-green-400  { color: var(--accent-green) !important; }
html:not(.dark) .text-blue-400   { color: var(--accent-blue) !important; }
html:not(.dark) .text-orange-400 { color: var(--accent-orange) !important; }
html:not(.dark) .text-pink-400   { color: var(--accent-pink) !important; }

/* Teks di dalam elemen gradient tetap putih (override slate override) */
html:not(.dark) [class*="bg-gradient"] .text-slate-400,
html:not(.dark) [class*="bg-gradient"] .text-slate-500 { color: rgba(255,255,255,0.75) !important; }
</style>

{{-- Theme toggle + Chart.js theme bridge --}}
<script>
/* ── Helpers untuk Chart.js ── */
function getCssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

function getChartThemeColors() {
    const isDark = document.documentElement.classList.contains('dark');
    return {
        gridColor:   isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)',
        tickColor:   isDark ? '#64748b' : '#94a3b8',
        legendColor: isDark ? '#94a3b8' : '#64748b',
        tooltipBg:   isDark ? 'rgba(15,23,42,0.95)' : 'rgba(255,255,255,0.98)',
        tooltipTitle: isDark ? '#f1f5f9' : '#0f172a',
        tooltipBody:  isDark ? '#94a3b8' : '#64748b',
        tooltipBorder: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
    };
}

/* Simpan semua instance chart agar bisa di-update saat toggle tema */
window._chartInstances = [];

function registerChart(chart) {
    window._chartInstances.push(chart);
}

function updateAllCharts() {
    const c = getChartThemeColors();
    window._chartInstances.forEach(chart => {
        if (!chart || chart.destroyed) return;
        /* Update scales */
        if (chart.options.scales) {
            ['x','y'].forEach(axis => {
                if (chart.options.scales[axis]) {
                    if (chart.options.scales[axis].grid)
                        chart.options.scales[axis].grid.color = c.gridColor;
                    if (chart.options.scales[axis].ticks)
                        chart.options.scales[axis].ticks.color = c.tickColor;
                }
            });
        }
        /* Update legend */
        if (chart.options.plugins?.legend?.labels)
            chart.options.plugins.legend.labels.color = c.legendColor;
        /* Update tooltip */
        if (chart.options.plugins?.tooltip) {
            chart.options.plugins.tooltip.backgroundColor = c.tooltipBg;
            chart.options.plugins.tooltip.titleColor      = c.tooltipTitle;
            chart.options.plugins.tooltip.bodyColor       = c.tooltipBody;
            chart.options.plugins.tooltip.borderColor     = c.tooltipBorder;
        }
        chart.update('none');
    });
}

/* ── Toggle tema ── */
function toggleTheme() {
    const html  = document.documentElement;
    const isDark = html.classList.contains('dark');
    html.classList.toggle('dark', !isDark);
    localStorage.setItem('theme', isDark ? 'light' : 'dark');
    document.querySelectorAll('.theme-icon').forEach(el => {
        el.textContent = isDark ? 'dark_mode' : 'light_mode';
    });
    /* Update semua chart */
    updateAllCharts();
}

document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.documentElement.classList.contains('dark');
    document.querySelectorAll('.theme-icon').forEach(el => {
        el.textContent = isDark ? 'light_mode' : 'dark_mode';
    });
});
</script>
