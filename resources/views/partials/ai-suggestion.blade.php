{{-- ── AI SUGGESTION FLOATING BUTTON + POPUP ── --}}

{{-- Floating Button --}}
<button id="aiBubbleBtn"
        onclick="toggleAiPanel()"
        class="fixed bottom-8 right-8 z-[200] group
               w-14 h-14 rounded-2xl
               bg-gradient-to-br from-violet-600 to-teal-500
               shadow-[0_0_30px_rgba(124,58,237,0.5)]
               flex items-center justify-center
               transition-all duration-300 hover:scale-110 active:scale-95">
    <span class="absolute inset-0 rounded-2xl bg-violet-500 animate-ping opacity-20"></span>
    <span class="ms text-[26px] relative z-10" style="color:#ffffff" id="aiBubbleIcon">auto_awesome</span>
    <span id="aiDot" class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-teal-400
                             border-2 animate-pulse"
          style="border-color:var(--bg)"></span>
</button>

{{-- AI Panel --}}
<div id="aiPanel"
     class="fixed bottom-28 right-8 z-[199] w-80 hidden">

    <div class="rounded-2xl overflow-hidden"
         style="background:var(--bg-card);
                border:1px solid var(--border);
                box-shadow:var(--shadow-lg)">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4"
             style="background:var(--bg-subtle); border-bottom:1px solid var(--border)">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-teal-400
                            flex items-center justify-center shadow-lg">
                    <span class="ms text-[18px]" style="color:#ffffff">psychology</span>
                </div>
                <div>
                    <p class="text-sm font-bold leading-none" style="color:var(--text-1)">AI Advisor</p>
                    <p class="text-[10px] font-semibold mt-0.5" style="color:var(--accent-teal)" id="aiStatus">Menganalisis...</p>
                </div>
            </div>
            <button onclick="toggleAiPanel()"
                    class="w-7 h-7 rounded-lg flex items-center justify-center transition-all"
                    style="background:var(--bg-input); color:var(--text-3)"
                    onmouseenter="this.style.color='var(--text-1)'"
                    onmouseleave="this.style.color='var(--text-3)'">
                <span class="ms text-[18px]">close</span>
            </button>
        </div>

        {{-- Content --}}
        <div id="aiContent" class="p-4 space-y-3 max-h-96 overflow-y-auto"
             style="background:var(--bg-card)">
            {{-- Loading state --}}
            <div id="aiLoading" class="flex flex-col items-center justify-center py-8 gap-3">
                <div class="flex gap-1.5">
                    <span class="w-2 h-2 rounded-full animate-bounce"
                          style="background:var(--accent-violet); animation-delay:0ms"></span>
                    <span class="w-2 h-2 rounded-full animate-bounce"
                          style="background:var(--accent-violet); animation-delay:150ms"></span>
                    <span class="w-2 h-2 rounded-full animate-bounce"
                          style="background:var(--accent-violet); animation-delay:300ms"></span>
                </div>
                <p class="text-xs" style="color:var(--text-4)">Menganalisis keuangan Anda...</p>
            </div>
            {{-- Cards injected by JS --}}
            <div id="aiCards" class="space-y-3 hidden"></div>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-3 flex items-center justify-between"
             style="border-top:1px solid var(--border); background:var(--bg-subtle)">
            <p class="text-[10px]" style="color:var(--text-4)">Berdasarkan 30 hari terakhir</p>
            <button onclick="loadAiSuggestions(true)"
                    class="flex items-center gap-1 text-xs font-semibold transition-colors"
                    style="color:var(--accent-violet)"
                    onmouseenter="this.style.opacity='0.7'"
                    onmouseleave="this.style.opacity='1'">
                <span class="ms text-[14px]">refresh</span> Refresh
            </button>
        </div>
    </div>
</div>

<style>
#aiPanel.panel-open {
    animation: panelSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
#aiPanel.panel-close {
    animation: panelSlideDown 0.2s ease-in forwards;
}
@keyframes panelSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to   { opacity: 1; transform: translateY(0)    scale(1); }
}
@keyframes panelSlideDown {
    from { opacity: 1; transform: translateY(0)    scale(1); }
    to   { opacity: 0; transform: translateY(20px) scale(0.95); }
}
.ai-card-enter {
    animation: cardFadeIn 0.4s ease forwards;
}
@keyframes cardFadeIn {
    from { opacity: 0; transform: translateX(-10px); }
    to   { opacity: 1; transform: translateX(0); }
}
.typing-cursor::after {
    content: '|';
    animation: blink 0.7s infinite;
    color: var(--accent-violet);
}
@keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0; } }
</style>

<script>
let aiPanelOpen  = false;
let aiLoaded     = false;
let typingTimers = [];

// Warna kartu AI — pakai CSS variable agar sinkron dengan tema
function getAiCardColors(type) {
    const isDark = document.documentElement.classList.contains('dark');
    const map = {
        danger:  {
            bg:     isDark ? 'rgba(248,113,113,0.08)'  : 'rgba(220,38,38,0.06)',
            border: isDark ? 'rgba(248,113,113,0.3)'   : 'rgba(220,38,38,0.2)',
            icon:   'var(--accent-red)',
            badge:  isDark ? 'rgba(248,113,113,0.2)'   : 'rgba(220,38,38,0.1)',
            badgeTx: 'var(--accent-red)',
        },
        warning: {
            bg:     isDark ? 'rgba(251,191,36,0.08)'   : 'rgba(217,119,6,0.06)',
            border: isDark ? 'rgba(251,191,36,0.3)'    : 'rgba(217,119,6,0.2)',
            icon:   'var(--accent-yellow)',
            badge:  isDark ? 'rgba(251,191,36,0.2)'    : 'rgba(217,119,6,0.1)',
            badgeTx: 'var(--accent-yellow)',
        },
        success: {
            bg:     isDark ? 'rgba(45,212,191,0.08)'   : 'rgba(13,148,136,0.06)',
            border: isDark ? 'rgba(45,212,191,0.3)'    : 'rgba(13,148,136,0.2)',
            icon:   'var(--accent-teal)',
            badge:  isDark ? 'rgba(45,212,191,0.2)'    : 'rgba(13,148,136,0.1)',
            badgeTx: 'var(--accent-teal)',
        },
        info:    {
            bg:     isDark ? 'rgba(167,139,250,0.08)'  : 'rgba(124,58,237,0.06)',
            border: isDark ? 'rgba(167,139,250,0.3)'   : 'rgba(124,58,237,0.2)',
            icon:   'var(--accent-violet)',
            badge:  isDark ? 'rgba(167,139,250,0.2)'   : 'rgba(124,58,237,0.1)',
            badgeTx: 'var(--accent-violet)',
        },
    };
    return map[type] || map.info;
}

const typeLabels = {
    danger: 'Perhatian', warning: 'Peringatan', success: 'Bagus!', info: 'Tips',
};

function toggleAiPanel() {
    const panel = document.getElementById('aiPanel');
    const icon  = document.getElementById('aiBubbleIcon');

    if (!aiPanelOpen) {
        panel.classList.remove('hidden');
        panel.classList.remove('panel-close');
        panel.classList.add('panel-open');
        icon.textContent = 'close';
        aiPanelOpen = true;
        if (!aiLoaded) loadAiSuggestions();
    } else {
        panel.classList.remove('panel-open');
        panel.classList.add('panel-close');
        icon.textContent = 'auto_awesome';
        aiPanelOpen = false;
        setTimeout(() => panel.classList.add('hidden'), 200);
    }
}

function loadAiSuggestions(forceReload = false) {
    if (aiLoaded && !forceReload) return;

    typingTimers.forEach(clearTimeout);
    typingTimers = [];
    document.getElementById('aiLoading').classList.remove('hidden');
    document.getElementById('aiCards').classList.add('hidden');
    document.getElementById('aiCards').innerHTML = '';
    document.getElementById('aiStatus').textContent = 'Menganalisis...';
    aiLoaded = false;

    fetch('{{ route("ai.suggest") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('aiLoading').classList.add('hidden');
        document.getElementById('aiCards').classList.remove('hidden');
        document.getElementById('aiStatus').textContent = 'Analisis selesai ✓';
        document.getElementById('aiDot').classList.add('hidden');

        data.suggestions.forEach((s, i) => {
            const timer = setTimeout(() => renderCard(s, i), i * 600);
            typingTimers.push(timer);
        });

        aiLoaded = true;
    })
    .catch(() => {
        document.getElementById('aiLoading').innerHTML =
            `<p class="text-xs text-center py-4" style="color:var(--accent-red)">Gagal memuat saran. Coba refresh.</p>`;
    });
}

function renderCard(suggestion, index) {
    const c     = getAiCardColors(suggestion.type);
    const label = typeLabels[suggestion.type] || 'Info';

    const card = document.createElement('div');
    card.className = 'ai-card-enter rounded-xl p-4';
    card.style.cssText = `
        background: ${c.bg};
        border: 1px solid ${c.border};
        animation-delay: 0ms;
    `;

    card.innerHTML = `
        <div class="flex items-start gap-3">
            <span class="ms text-[22px] flex-shrink-0 mt-0.5" style="color:${c.icon}">${suggestion.icon}</span>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <p class="text-sm font-bold leading-tight" style="color:var(--text-1)">${suggestion.title}</p>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0"
                          style="background:${c.badge}; color:${c.badgeTx}">${label}</span>
                </div>
                <p class="text-xs leading-relaxed typing-text" style="color:var(--text-3)"
                   data-text="${escapeHtml(suggestion.message)}"></p>
                ${suggestion.action ? `
                <button onclick="openModal('quickAddModal')"
                        class="mt-2.5 text-xs font-bold flex items-center gap-1 transition-opacity hover:opacity-70"
                        style="color:${c.icon}">
                    <span class="ms text-[14px]">arrow_forward</span> ${suggestion.action}
                </button>` : ''}
            </div>
        </div>
    `;

    document.getElementById('aiCards').appendChild(card);

    const textEl = card.querySelector('.typing-text');
    textEl.classList.add('typing-cursor');
    typeText(textEl, suggestion.message, 0, 18);
}

function typeText(el, text, index, speed) {
    if (index < text.length) {
        el.textContent = text.substring(0, index + 1);
        const t = setTimeout(() => typeText(el, text, index + 1, speed), speed);
        typingTimers.push(t);
    } else {
        el.classList.remove('typing-cursor');
    }
}

function escapeHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Auto-load setelah 2 detik
setTimeout(() => { if (!aiLoaded) loadAiSuggestions(); }, 2000);
</script>
