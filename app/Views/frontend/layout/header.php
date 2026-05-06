<!-- TOP BAR -->
<?php 
    $identitas = $identitas ?? ['nama_website' => 'Bappeda Bungo'];
    $logo_url = $logo_url ?? base_url('uploads/favicon/logo.png');
?>
<div id="top-bar" class="bg-gradient-to-r from-[#0f241b] to-[#106a44] dark:from-[#0b1a14] dark:to-[#0f241b]">
    <div class="max-w-7xl mx-auto px-6 py-2 relative flex items-center text-xs text-white">

        <!-- TENGAH -->
        <span class="absolute left  text-center">
            <?= esc(formatTanggalIndo()) ?>
        </span>

        <!-- KANAN -->
        <div class="ml-auto flex items-center gap-3">

            <!-- DROPDOWN BAHASA -->
            <select
                id="lang-select"
                class="notranslate bg-transparent border border-white/40
                    text-xs rounded-md px-2 py-1
                    text-white focus:outline-none"
                translate="no"
            >
                <option value="id|id">🇮🇩 Indonesia</option>
                <option value="id|en">🇬🇧 English</option>
                <option value="id|es">🇪🇸 Español</option>
                <option value="id|zh-CN">🇨🇳 中文</option>
                <option value="id|fr">🇫🇷 Français</option>
                <option value="id|ar">🇸🇦 العربية</option>
                <option value="id|ja">🇯🇵 日本語</option>
            </select>

            <div id="google_translate_element2" class="hidden"></div>


            <!-- TOGGLE DARK/LIGHT with Custom Tooltip -->
            <div x-data="{ show: false }" class="relative flex items-center justify-center">
                <button onclick="toggleTheme()" 
                        @mouseenter="show = true" 
                        @mouseleave="show = false"
                        class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-white/10 transition-all duration-300 active:scale-90"
                        aria-label="Toggle Theme">
                    <span id="theme-icon" translate="no" class="notranslate material-icons text-xl block leading-none">brightness_auto</span>
                </button>
                
                <!-- Styled Tooltip -->
                <div x-show="show" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     x-cloak
                     class="absolute top-full right-0 mt-2 z-[999] pointer-events-none">
                    <div class="px-3 py-1.5 rounded-lg shadow-2xl border whitespace-nowrap
                                bg-white/95 text-emerald-900 border-emerald-100 backdrop-blur-sm
                                dark:bg-[#0d1512]/95 dark:text-emerald-400 dark:border-emerald-500/30">
                        <div class="flex items-center gap-2">
                             <span id="theme-tooltip-icon" translate="no" class="notranslate material-icons text-sm">settings_brightness</span>
                             <span id="theme-tooltip-text" class="text-[10px] font-bold uppercase tracking-wider">Auto Mode</span>
                        </div>
                        <!-- Arrow -->
                        <div class="absolute -top-1 right-3 w-2 h-2 rotate-45 
                                    bg-white border-t border-l border-emerald-100
                                    dark:bg-[#0d1512] dark:border-emerald-500/30"></div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
    <!-- DESKTOP HEADER -->
    <div id="brand-header" translate="no" class="notranslate bg-white dark:bg-[#050807] hidden md:block border-b border-gray-200 dark:border-emerald-900/60">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-center gap-5">
            <img src="<?= $logo_url ?>" alt="Logo <?= $identitas['nama_website'] ?>" class="h-[70px] object-contain drop-shadow-sm">
            
            <div class="flex flex-col">
                <h1 class="text-[40px] font-black tracking-[0.1em] text-emerald-900 dark:text-emerald-50 leading-none mb-2">
                    BAPPEDA
                </h1>
                <div class="flex items-center gap-4 w-full">
                    <div class="h-[2.5px] w-[6.5px] flex-grow bg-emerald-600/60 rounded-full"></div>
                    <p class="text-[9px] font-bold tracking-[0.1em] text-emerald-700/80 dark:text-emerald-400/70 uppercase whitespace-nowrap">
                        Pemerintah Kabupaten Bungo
                    </p>
                    <div class="h-[2.5px] w-[6.5px] flex-grow bg-emerald-600/60 rounded-full"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<header id="main-header"
  class="bg-white dark:bg-[#0d1512] transition-all duration-300 group"
  x-data="{ searchOpen: false, mobileMenuOpen: false }">

  <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between md:hidden relative">

    <!-- LOGO -->
    <div class="flex items-center gap-3">
        <img src="<?= $logo_url ?>" alt="Logo" class="h-10">

        <div class="leading-tight notranslate" translate="no">
            <p
            class="text-sm font-semibold
                    text-emerald-600
                    group-[.text-white]:text-white
                    dark:text-emerald-400">
            BAPPEDA
            </p>

            <p
            class="text-[10px]
                    text-gray-500
                    group-[.text-white]:text-emerald-100
                    dark:text-gray-400">
            Pemkab Bungo
            </p>
        </div>
    </div>


    <!-- ICONS -->
    <div class="flex items-center gap-2 relative z-20">
        <button
            @click="searchOpen = !searchOpen; if(searchOpen) mobileMenuOpen=false"
            class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-400/30 dark:hover:bg-gray-800 transition">
            <span class="material-icons icon-header text-2xl notranslate" translate="no">search</span>
        </button>

        <button
            @click="mobileMenuOpen = !mobileMenuOpen; if(mobileMenuOpen) searchOpen=false"
            class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-400/30 dark:hover:bg-gray-800 transition">
            <span class="material-icons icon-header text-2xl notranslate" translate="no">menu</span>
        </button>
        </div>
    </div>

    <!-- DESKTOP NAVBAR -->
    <div id="desktop-nav"
        class="hidden md:flex items-center justify-between max-w-7xl mx-auto px-6 py-4
                relative z-50 group">

        <nav class="flex gap-8 text-[13px] uppercase font-medium items-center">

            <?php foreach ($menu as $m): ?>

                <?php if (empty($m['submenu'])): ?>
                    <!-- MENU TANPA SUBMENU -->
                    <a href="<?= base_url($m['link']) ?>">
                        <?= esc($m['nama_menu']) ?>
                    </a>

                <?php else: ?>
                    <!-- MENU DENGAN SUBMENU -->
                    <div class="relative">
                        <button class="flex items-center uppercase gap-1 peer">
                            <?= esc($m['nama_menu']) ?>
                            <span class="material-icons text-base notranslate" translate="no">expand_more</span>

                            <!-- hover bridge -->
                            <div class="absolute left-0 top-full h-6 w-full"></div>
                        </button>

                        <div
                            class="desktop-submenu absolute left-0 top-full w-44 mt-[1.45rem]
                                hidden peer-hover:block hover:block
                                bg-white text-black
                                dark:bg-[#0d1512] dark:text-white
                                rounded-b-lg shadow-lg z-50">

                            <?php foreach ($m['submenu'] as $s): ?>
                                <a href="<?= base_url($s['link_sub']) ?>"
                                class="block px-4 py-2 hover:bg-black/10">
                                    <?= esc($s['nama_sub']) ?>
                                </a>
                            <?php endforeach ?>

                        </div>
                    </div>
                <?php endif ?>

            <?php endforeach ?>

        </nav>

        <!-- SEARCH -->
        <form action="<?= base_url('search') ?>" method="get" class="relative w-64">
            <input type="text" name="q" placeholder="Search"
            class="w-full rounded-full px-4 py-2 text-sm
                    bg-[rgba(15,31,26,0.06)] dark:bg-[#0f1a16]
                    border border-gray-300/40 dark:border-emerald-900/40
                    focus:outline-none focus:ring-1 focus:ring-emerald-500/60">

            <button type="submit" class="absolute inset-y-0 right-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-emerald-500">
                <span class="material-icons notranslate" translate="no">search</span>
            </button>
        </form>
    </div>



    <!-- MOBILE SEARCH OVERLAY -->
    <div x-cloak x-show="searchOpen" x-transition id="mobile-search"
        class="bg-white dark:bg-[#0d1512] fixed inset-x-0 z-50 md:hidden rounded-b-lg shadow-lg overflow-hidden">
        <div class="mx-4 p-4 relative">
            <form action="<?= base_url('search') ?>" method="get">
                <input type="text" name="q" placeholder="Search"
                    class="w-full rounded-full px-4 py-2 text-sm bg-[rgba(15,31,26,0.06)] dark:bg-[#0f1a16] border border-gray-300/30 dark:border-emerald-900/30 focus:outline-none focus:ring-1 focus:ring-emerald-500/60">
                <button type="submit" class="absolute inset-y-0 right-6 flex items-center text-gray-500 dark:text-gray-400 hover:text-emerald-500">
                    <span class="material-icons notranslate" translate="no">search</span>
                </button>
            </form>
        </div>
    </div>

    <!-- MOBILE MENU OVERLAY -->
    <div x-cloak x-show="mobileMenuOpen" x-transition id="mobile-menu"
        class="bg-white dark:bg-[#0d1512] fixed inset-x-0 z-50 md:hidden rounded-b-lg shadow-lg overflow-auto">
        <div class="mx-4 p-6">
            <nav class="px-6 py-4 text-sm uppercase font-medium space-y-3">

                <?php foreach ($menu as $m): ?>

                    <?php if (empty($m['submenu'])): ?>
                        <a href="<?= base_url($m['link']) ?>" class="block">
                            <?= esc($m['nama_menu']) ?>
                        </a>

                    <?php else: ?>
                        <div x-data="{ open:false }">
                            <button @click="open=!open"
                                class="w-full flex justify-between uppercase items-center">
                                <?= esc($m['nama_menu']) ?>
                                <span class="material-icons notranslate" translate="no"
                                    x-text="open ? 'expand_less' : 'expand_more'"></span>
                            </button>

                            <div x-show="open" x-transition class="ml-4 mt-2 space-y-2">
                                <?php foreach ($m['submenu'] as $s): ?>
                                    <a href="<?= base_url($s['link_sub']) ?>" class="block">
                                        <?= esc($s['nama_sub']) ?>
                                    </a>
                                <?php endforeach ?>
                            </div>
                        </div>
                    <?php endif ?>

                <?php endforeach ?>

            </nav>

        </div>
    </div>
</header>