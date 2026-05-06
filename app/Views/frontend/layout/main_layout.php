<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <?php 
        // Fallback safety for global variables
        $identitas = $identitas ?? ['nama_website' => 'Bappeda Bungo', 'description' => '', 'keywords' => '', 'author' => ''];
        $meta = $meta ?? ['description' => '', 'keywords' => '', 'author' => '', 'image' => '', 'favicon' => '', 'type' => 'website'];

        $page_title = (isset($title) ? $title . ' - ' : '') . ($identitas['nama_website'] ?? 'Bappeda Bungo');
        $page_description = $meta['description'] ?? $identitas['description'] ?? '';
        $page_url = current_url();
    ?>
    <title><?= $page_title ?></title>
    <meta name="description" content="<?= esc($page_description) ?>">
    <meta name="keywords" content="<?= esc($meta['keywords'] ?? $identitas['keywords']) ?>">
    <meta name="author" content="<?= esc($meta['author'] ?? $identitas['nama_website']) ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= $page_url ?>" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= $meta['type'] ?? 'website' ?>">
    <meta property="og:url" content="<?= $page_url ?>">
    <meta property="og:title" content="<?= esc($page_title) ?>">
    <meta property="og:description" content="<?= esc($page_description) ?>">
    <meta property="og:image" content="<?= $meta['image'] ?>">
    <meta property="og:site_name" content="<?= esc($identitas['nama_website']) ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= $page_url ?>">
    <meta property="twitter:title" content="<?= esc($page_title) ?>">
    <meta property="twitter:description" content="<?= esc($page_description) ?>">
    <meta property="twitter:image" content="<?= $meta['image'] ?>">

    <link rel="shortcut icon" href="<?= $meta['favicon'] ?>" />
    <link rel="icon" type="image/png" href="<?= $meta['favicon'] ?>" />
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="<?= base_url('css/tailwind.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        // Robust theme management
        const ThemeManager = {
            getStoredTheme() {
                try {
                    return localStorage.getItem('theme') || 'system';
                } catch (e) {
                    return 'system';
                }
            },
            setStoredTheme(theme) {
                try {
                    if (theme === 'system') {
                        localStorage.removeItem('theme');
                    } else {
                        localStorage.setItem('theme', theme);
                    }
                } catch (e) {}
            },
            apply() {
                const stored = this.getStoredTheme();
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = stored === 'dark' || (stored === 'system' && systemDark);
                
                document.documentElement.classList.toggle('dark', isDark);
                
                // Dispatch event
                window.dispatchEvent(new CustomEvent('theme-changed', { 
                    detail: { isDark, theme: stored, systemIsDark: systemDark } 
                }));
                
                console.log(`[Theme] Mode: ${stored}, SystemDark: ${systemDark}, AppliedDark: ${isDark}`);
                
                if (typeof updateThemeUI === 'function') updateThemeUI();
            },
            cycle() {
                const current = this.getStoredTheme();
                const modes = ['system', 'dark', 'light'];
                const next = modes[(modes.indexOf(current) + 1) % modes.length];
                this.setStoredTheme(next);
                this.apply();
            }
        };

        // Immediate application
        ThemeManager.apply();

        // System listener (Support both modern and old syntax)
        const mq = window.matchMedia('(prefers-color-scheme: dark)');
        const listener = () => {
            if (ThemeManager.getStoredTheme() === 'system') ThemeManager.apply();
        };
        try { mq.addEventListener('change', listener); } catch(e) { mq.addListener(listener); }
        
        // Polyfill
        window.toggleTheme = () => ThemeManager.cycle();
    </script>

    <style>
        /* Hilangkan toolbar Google Translate */
        .goog-te-banner-frame,
        .goog-te-balloon-frame,
        .goog-logo-link,
        .goog-te-gadget,
        .skiptranslate {
            display: none !important;
        }
        .notranslate * {
            translate: no !important;
        }

        body {
            top: 0 !important;
        }
        /* FIX equal height swiper */
        .mySlider .swiper-wrapper {
            align-items: stretch;
        }

        .mySlider .swiper-slide {
            height: auto !important;   /* MATIKAN height:100% bawaan swiper */
            display: flex;
        }
        [x-cloak] {
            display: none !important;
        }
        .swiper-pd {
            padding-left:5px!important;
            padding-right:5px!important;
            z-index: 0 !important;
        }

        /* SCROLL REVEAL ANIMATIONS */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.9s cubic-bezier(0.17, 0.55, 0.55, 1);
            will-change: opacity, transform;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Reveal delay helpers */
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
        .delay-500 { transition-delay: 500ms; }
    </style>

</head>

<body class="bg-gray-100 text-gray-800 dark:bg-[#0a0f0d] dark:text-gray-200 antialiased">

<?= view_cell('App\Cells\HeaderCell::display') ?>

<!-- HERO -->
<?= $this->renderSection('hero') ?>

<!-- CONTENT -->
<main class="max-w-7xl mx-auto">
    <?= $this->renderSection('content') ?>
</main>

<!-- FOOTER -->
<footer class="border-t border-gray-300 dark:border-emerald-900/30">
    <div translate="no" class="notranslate max-w-7xl mx-auto px-6 py-6 text-xs text-gray-600 dark:text-gray-400">
        © <?= date('Y') ?> Badan Perencanaan Pembangunan Daerah Kabupaten Bungo
    </div>
</footer>

<!-- === LOGIC MENU NAVBAR=== -->
<script>
let lastScroll = 0;

const topBar = document.getElementById('top-bar');
const brand  = document.getElementById('brand-header');
const header = document.getElementById('main-header');

const searchOverlay = document.getElementById('mobile-search');
const menuOverlay   = document.getElementById('mobile-menu');

const desktopSubmenus = document.querySelectorAll('.desktop-submenu');

function updateOverlayPosition() {
  const topBarHeight = topBar && !topBar.classList.contains('invisible') ? topBar.offsetHeight : 0;
  const brandHeight  = brand && !brand.classList.contains('invisible') ? brand.offsetHeight : 0;
  const headerHeight = header ? header.offsetHeight : 0;
  const totalTop = topBarHeight + brandHeight + headerHeight;
  if(searchOverlay) searchOverlay.style.top = totalTop + 'px';
  if(menuOverlay) menuOverlay.style.top   = totalTop + 'px';
}

function updateOverlayColor() {
  if(!header) return;
  const isGreen = header.classList.contains('bg-[#106a44]');
  const isDark  = document.documentElement.classList.contains('dark');

  if (isGreen) {
    if(searchOverlay) { searchOverlay.style.backgroundColor = '#106a44'; searchOverlay.style.color = 'white'; }
    if(menuOverlay) { menuOverlay.style.backgroundColor   = '#106a44'; menuOverlay.style.color   = 'white'; }
    return;
  }

  if (isDark) {
    if(searchOverlay) { searchOverlay.style.backgroundColor = '#0d1512'; searchOverlay.style.color = 'white'; }
    if(menuOverlay) { menuOverlay.style.backgroundColor   = '#0d1512'; menuOverlay.style.color   = 'white'; }
  } else {
    if(searchOverlay) { searchOverlay.style.backgroundColor = 'white'; searchOverlay.style.color = 'black'; }
    if(menuOverlay) { menuOverlay.style.backgroundColor   = 'white'; menuOverlay.style.color   = 'black'; }
  }
}

function updateHeaderIconColor() {
  const icons = document.querySelectorAll('.icon-header');
  if(!header) return;
  const isGreen = header.classList.contains('bg-[#106a44]');
  const isDark  = document.documentElement.classList.contains('dark');

  icons.forEach(icon => {
    icon.classList.remove('text-gray-600','text-gray-300','text-white');
    if (isGreen) {
      icon.classList.add('text-white');
    } else {
      icon.classList.add(isDark ? 'text-gray-300' : 'text-gray-600');
    }
  });
}

function updateDesktopSubmenuColor() {
  if(!header) return;
  const isGreen = header.classList.contains('bg-[#106a44]');
  const isDark  = document.documentElement.classList.contains('dark');

  desktopSubmenus.forEach(menu => {
    if (isGreen) {
      menu.style.backgroundColor = '#106a44';
      menu.style.color = 'white';
    } else if (isDark) {
      menu.style.backgroundColor = '#0d1512';
      menu.style.color = 'white';
    } else {
      menu.style.backgroundColor = 'white';
      menu.style.color = 'black';
    }
  });
}

const handleScroll = () => {
  const current = window.scrollY;
  const isSticky = current > 100;
  if(!header) return;

  if (isSticky) {
    topBar?.classList.add('invisible','-translate-y-full','pointer-events-none');
    brand?.classList.add('invisible','-translate-y-full','pointer-events-none','h-0','overflow-hidden');

    header.classList.add('fixed','top-0','left-0','right-0','z-50','shadow-md');
    header.classList.remove('bg-white','dark:bg-[#0d1512]');
    header.classList.add('bg-[#106a44]','text-white');
  } else {
    topBar?.classList.remove('invisible','-translate-y-full','pointer-events-none');
    brand?.classList.remove('invisible','-translate-y-full','pointer-events-none','h-0','overflow-hidden');

    header.classList.remove('fixed','top-0','left-0','right-0','z-50','shadow-md');
    header.classList.remove('bg-[#106a44]','text-white');
    header.classList.add('bg-white','dark:bg-[#0d1512]');
  }

  updateOverlayColor();
  updateHeaderIconColor();
  updateDesktopSubmenuColor();
  updateOverlayPosition();
  
  lastScroll = current;
};

window.addEventListener('scroll', handleScroll);

window.addEventListener('theme-changed', () => {
    updateOverlayColor();
    updateHeaderIconColor();
    updateDesktopSubmenuColor();
    updateOverlayPosition();
    handleScroll();
});

document.addEventListener('DOMContentLoaded', () => {
    updateHeaderIconColor();
    updateDesktopSubmenuColor();
    handleScroll();
});
</script>

<!-- TOGGLE SCRIPT -->
<script>
function updateThemeUI() {
    const icon = document.getElementById('theme-icon');
    const tooltipText = document.getElementById('theme-tooltip-text');
    const tooltipIcon = document.getElementById('theme-tooltip-icon');
    if (!icon) return;
    
    const stored = ThemeManager.getStoredTheme();
    if (stored === 'system') {
        icon.textContent = 'brightness_auto';
        if (tooltipText) tooltipText.textContent = 'Mode: Auto';
        if (tooltipIcon) tooltipIcon.textContent = 'settings_brightness';
    } else if (stored === 'dark') {
        icon.textContent = 'dark_mode';
        if (tooltipText) tooltipText.textContent = 'Mode: Dark';
        if (tooltipIcon) tooltipIcon.textContent = 'nightlight_round';
    } else {
        icon.textContent = 'light_mode';
        if (tooltipText) tooltipText.textContent = 'Mode: Light';
        if (tooltipIcon) tooltipIcon.textContent = 'light_mode';
    }
}
document.addEventListener('DOMContentLoaded', updateThemeUI);
</script>

<!-- GTRANSLATE -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Integration functions here */
    function GTranslateGetCurrentLang() {
        var match = document.cookie.match(/googtrans=([^;]+)/);
        if (!match) return null;
        return match[1].split('/')[2];
    }
    function GTranslateFireEvent(element, event) {
        try {
            if (document.createEventObject) {
                var evt = document.createEventObject();
                element.fireEvent('on' + event, evt);
            } else {
                var evt = document.createEvent('HTMLEvents');
                evt.initEvent(event, true, true);
                element.dispatchEvent(evt);
            }
        } catch (e) {}
    }
    function doGTranslate(selectObj) {
        var lang_pair = selectObj.value;
        if (!lang_pair) return;
        var lang = lang_pair.split('|')[1];
        var teCombo = null;
        var selects = document.getElementsByTagName('select');
        for (var i = 0; i < selects.length; i++) {
            if (selects[i].className.indexOf('goog-te-combo') !== -1) {
                teCombo = selects[i];
                break;
            }
        }
        if (!teCombo) {
            setTimeout(function () { doGTranslate(selectObj); }, 500);
        } else {
            teCombo.value = lang;
            GTranslateFireEvent(teCombo, 'change');
        }
    }
    var langSelect = document.getElementById('lang-select');
    if (langSelect) {
        // CSR: Auto-select based on cookie
        const currentLang = GTranslateGetCurrentLang();
        if (currentLang) {
            langSelect.value = `id|${currentLang}`;
        }
        langSelect.addEventListener('change', function () { doGTranslate(this); });
    }
});
function googleTranslateElementInit2() {
    new google.translate.TranslateElement({ pageLanguage: 'id', autoDisplay: false }, 'google_translate_element2');
}
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>

<!-- SWIPER -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if(document.querySelector('.mySlider')) {
        new Swiper('.mySlider', {
          loop: true,
          spaceBetween: 24,
          slidesPerView: 1,
          autoplay: { delay: 3000, disableOnInteraction: false },
          pagination: { el: '.swiper-pagination', clickable: true },
          breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
        });
    }
});
</script>

<!-- SCROLL REVEAL -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const observerOptions = { root: null, rootMargin: '0px', threshold: 0.15 };
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const observeElements = () => {
            document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
        };
        observeElements();
        window.addEventListener('content-updated', observeElements);
    });
</script>


<!-- AI CHAT ASSISTANT -->
<div x-data="{ 
    open: false, 
    messages: [
        { role: 'assistant', text: 'Halo! Saya asisten virtual Bappeda Bungo. Ada yang bisa saya bantu terkait perencanaan pembangunan atau layanan kami?' }
    ],
    userInput: '',
    loading: false,
    assistants: {},
    selectedAssistant: 'siska',
    async init() {
        await this.fetchAssistants();
    },
    async fetchAssistants() {
        try {
            const res = await fetch('<?= base_url('chat-ai/assistants') ?>');
            const data = await res.json();
            if (data.status === 'success') {
                this.assistants = data.assistants;
            }
        } catch (e) {
            console.error('Failed to fetch assistants');
        }
    },
    async sendMessage() {
        if (!this.userInput.trim() || this.loading) return;
        
        const text = this.userInput;
        this.messages.push({ role: 'user', text: text });
        this.userInput = '';
        this.loading = true;
        
        this.$nextTick(() => { this.scrollToBottom(); });

        try {
            const formData = new FormData();
            formData.append('message', text);
            formData.append('assistant', this.selectedAssistant);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            const res = await fetch('<?= base_url('chat-ai/ask') ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                this.messages.push({ role: 'assistant', text: data.response });
            } else {
                this.messages.push({ role: 'assistant', text: 'Maaf: ' + data.message });
            }
        } catch (e) {
            this.messages.push({ role: 'assistant', text: 'Terjadi kesalahan teknis. Mohon coba lagi nanti.' });
        } finally {
            this.loading = false;
            this.$nextTick(() => { this.scrollToBottom(); });
        }
    },
    scrollToBottom() {
        const container = this.$refs.chatBody;
        container.scrollTop = container.scrollHeight;
    }
}" class="fixed bottom-6 right-6 z-[100] font-sans">

    <!-- Chat Button -->
    <button @click="open = !open" 
            class="w-14 h-14 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 group relative">
        <span x-show="!open" translate="no" class="notranslate material-icons text-3xl">chat</span>
        <span x-show="open" translate="no" class="notranslate material-icons text-3xl">close</span>
        
        <!-- Pulse Effect when closed -->
        <span x-show="!open" class="absolute inset-0 rounded-full bg-emerald-500 animate-ping opacity-20 group-hover:hidden"></span>
    </button>

    <!-- Chat Window -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="absolute bottom-20 right-0 w-[90vw] md:w-[400px] h-[500px] bg-white dark:bg-[#0d1512] rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-emerald-900/10 overflow-hidden flex flex-col"
         style="display: none;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#0f241b] to-[#106a44] p-5 flex items-center gap-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-xl flex items-center justify-center border border-white/20">
                <span translate="no" class="notranslate material-icons text-emerald-300 text-2xl">auto_awesome</span>
            </div>
            <div class="flex-grow">
                <div class="flex justify-between items-center">
                    <h3 class="text-white font-bold text-sm tracking-tight">Asisten AI Bappeda</h3>
                    
                    <!-- Assistant Selector -->
                    <select x-model="selectedAssistant" 
                            class="bg-black/20 text-white text-[10px] rounded border border-white/10 px-2 py-1 outline-none focus:ring-1 focus:ring-emerald-400">
                        <template x-for="(asst, key) in assistants" :key="key">
                            <option :value="key" x-text="asst.name"></option>
                        </template>
                    </select>
                </div>
                
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[10px] text-emerald-100/70 font-medium uppercase tracking-widest" x-text="assistants[selectedAssistant]?.description || 'Online Aktif'"></span>
                </div>
            </div>
        </div>

        <!-- Chat Body -->
        <div x-ref="chatBody" class="flex-grow overflow-y-auto p-5 space-y-4 custom-scrollbar bg-gray-50/50 dark:bg-transparent">
            <template x-for="(msg, idx) in messages" :key="idx">
                <div :class="msg.role === 'assistant' ? 'flex justify-start' : 'flex justify-end'">
                    <div :class="msg.role === 'assistant' 
                                 ? 'bg-white dark:bg-[#15201b] text-gray-800 dark:text-emerald-50 rounded-2xl rounded-tl-none border border-emerald-900/5' 
                                 : 'bg-emerald-600 text-white rounded-2xl rounded-tr-none'"
                         class="max-w-[85%] p-3.5 text-sm shadow-sm leading-relaxed">
                        <p x-text="msg.text" class="whitespace-pre-wrap"></p>
                    </div>
                </div>
            </template>
            
            <!-- Loading Indicator -->
            <div x-show="loading" class="flex justify-start animate-fade-in text-emerald-600 dark:text-emerald-400">
                <div class="bg-white dark:bg-[#15201b] px-4 py-3 rounded-2xl rounded-tl-none border border-emerald-900/5 flex gap-1.5">
                    <div class="w-1.5 h-1.5 bg-current rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                    <div class="w-1.5 h-1.5 bg-current rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                    <div class="w-1.5 h-1.5 bg-current rounded-full animate-bounce"></div>
                </div>
            </div>
        </div>

        <!-- Footer / Input -->
        <div class="p-4 bg-white dark:bg-[#101815] border-t border-emerald-900/10">
            <form @submit.prevent="sendMessage()" class="relative flex items-center">
                <input type="text" 
                       x-model="userInput" 
                       placeholder="Tanyakan sesuatu..."
                       class="w-full pl-4 pr-12 py-3 bg-gray-100 dark:bg-white/5 border-0 focus:ring-2 focus:ring-emerald-500 rounded-2xl text-sm dark:text-white outline-none">
                <button type="submit" 
                        :disabled="!userInput.trim() || loading"
                        class="absolute right-1 w-10 h-10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 disabled:opacity-30 transition-all">
                    <span translate="no" class="notranslate material-icons text-2xl">send</span>
                </button>
            </form>
            <p class="text-[9px] text-center text-gray-400 mt-3 uppercase tracking-widest font-bold opacity-50">Powered by Bappeda AI Assistant</p>
        </div>

    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 106, 68, 0.1); border-radius: 20px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(16, 106, 68, 0.3); }
</style>

</body>
</html>
