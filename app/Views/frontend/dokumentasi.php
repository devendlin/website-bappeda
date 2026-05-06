<?= $this->extend('frontend/layout/main_layout') ?>

<?= $this->section('hero') ?>
<!-- BREADCRUMBS -->
<section class="relative py-20 bg-gradient-to-r from-[#0f241b] to-[#106a44] dark:from-[#032417] dark:to-[#004a2d] overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-repeat"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-6 text-center">
        <!-- Breadcrumbs adapted for dark background -->
        <ul class="flex justify-center items-center gap-2 text-xs uppercase notranslate mb-4 text-emerald-100/60">
            <li><a href="<?= base_url('/') ?>" class="hover:text-white transition">Home</a></li>
            <li>→</li>
            <li><span class="text-white">Dokumentasi Kegiatan</span></li>
        </ul>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 animate-fade-in-up reveal">
            Dokumentasi Kegiatan
        </h1>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="py-16 min-h-screen" x-data="{ 
    open: false, 
    currentImages: [], 
    currentIndex: 0,
    currentTitle: '',
    currentDescription: '',
    kegiatan: <?= htmlspecialchars(json_encode($initialKegiatan)) ?>,
    hasMore: <?= $hasMore ? 'true' : 'false' ?>,
    loading: false,
    offset: <?= $limit ?>,

    openLightbox(item, index = 0) {
        this.currentImages = item.foto;
        this.currentTitle = item.judul;
        this.currentDescription = item.deskripsi;
        this.currentIndex = index;
        this.open = true;
        document.body.style.overflow = 'hidden';
        
        // Update URL with ID parameter
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('id', item.id);
        window.history.pushState({}, '', newUrl);
    },
    closeLightbox() {
        this.open = false;
        document.body.style.overflow = '';
        
        // Remove ID parameter from URL
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('id');
        window.history.pushState({}, '', newUrl);
    },
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.currentImages.length;
    },
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.currentImages.length) % this.currentImages.length;
    },
    async loadMore() {
        if (this.loading || !this.hasMore) return;
        this.loading = true;
        try {
            const res = await fetch(`<?= base_url('dokumentasi/loadMore') ?>?offset=${this.offset}`);
            const data = await res.json();
            this.kegiatan = [...this.kegiatan, ...data.kegiatan];
            this.hasMore = data.hasMore;
            this.offset += 6;

            this.$nextTick(() => {
                window.dispatchEvent(new CustomEvent('content-updated'));
            });
        } catch (e) {
            console.error('Failed to load more:', e);
        } finally {
            this.loading = false;
        }
    },
    init() {
        // Auto-open modal if ID parameter exists
        const urlParams = new URLSearchParams(window.location.search);
        const dokId = urlParams.get('id');
        if (dokId) {
            // Use $nextTick to ensure data is loaded
            this.$nextTick(() => {
                const item = this.kegiatan.find(k => k.id == dokId);
                if (item) {
                    this.openLightbox(item);
                } else {
                    console.log('Dokumentasi ID not found:', dokId, 'Available IDs:', this.kegiatan.map(k => k.id));
                }
            });
        }
        
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && this.hasMore && !this.loading) {
                this.loadMore();
            }
        }, { rootMargin: '200px' });
        observer.observe(this.$refs.loadMoreTrigger);
    }
}" @keydown.escape.window="closeLightbox()">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <template x-for="(item, idx) in kegiatan" :key="item.id">
                 <div class="group relative bg-white dark:bg-[#0f1a16] rounded-3xl shadow-xl hover:shadow-emerald-500/10 transition-all duration-500 overflow-hidden border border-emerald-900/30 aspect-square md:aspect-[4/5] cursor-pointer reveal"
                     :class="'delay-' + ((idx % 3) * 100)"
                     @click="openLightbox(item)">
                    
                    <!-- Photos Container (Mosaic/Grid) -->
                    <div class="absolute inset-0 transition-transform duration-1000 group-hover:scale-105">
                        
                        <!-- Mosaic logic for card background -->
                        <div class="w-full h-full">
                            <template x-if="item.foto.length >= 3">
                                <div class="grid grid-cols-2 grid-rows-2 h-full gap-0.5">
                                    <img :src="item.foto[0]" class="col-span-1 row-span-2 w-full h-full object-cover">
                                    <img :src="item.foto[1]" class="col-span-1 row-span-1 w-full h-full object-cover">
                                    <img :src="item.foto[2]" class="col-span-1 row-span-1 w-full h-full object-cover">
                                </div>
                            </template>
                            <template x-if="item.foto.length == 2">
                                <div class="grid grid-cols-2 h-full gap-0.5">
                                    <img :src="item.foto[0]" class="w-full h-full object-cover">
                                    <img :src="item.foto[1]" class="w-full h-full object-cover">
                                </div>
                            </template>
                            <template x-if="item.foto.length == 1">
                                <img :src="item.foto[0]" class="w-full h-full object-cover">
                            </template>
                        </div>

                        <!-- Darker Overlay for Visibility -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-[#020403]/60 to-transparent opacity-100 group-hover:opacity-85 transition-opacity duration-500"></div>
                    </div>

                    <!-- Overlay Content -->
                    <div class="absolute inset-0 p-6 flex flex-col justify-end">
                        <div class="translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                            
                            <!-- Title - Smaller Font -->
                            <h3 class="text-lg md:text-xl font-extrabold text-white leading-tight mb-2 group-hover:text-emerald-300 transition-colors drop-shadow-lg" x-text="item.judul"></h3>
                            
                            <div class="flex justify-between items-center">
                                <!-- Date Badge - More Visible Color -->
                                <div class="inline-flex items-center gap-2 px-2 py-0.5 bg-[#32643e4f] rounded-lg border border-[#16d7853b]">
                                    <span translate="no" class="notranslate material-icons text-emerald-400 text-xs drop-shadow-lg">calendar_today</span>
                                    <span class="text-[9px] font-bold text-emerald-300 tracking-widest drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]" x-text="item.tanggal"></span>
                                </div>
                                
                                <!-- Photos Count Overlay -->
                                <div x-show="item.foto.length > 1" class="flex items-center gap-1.5 text-white text-[10px] font-bold tracking-widest">
                                    <span translate="no" class="notranslate material-icons text-[14px]">collections</span>
                                    <span x-text="item.foto.length + ' Pics'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hover Fullscreen Icon -->
                    <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 shadow-xl flex items-center justify-center text-white scale-75 group-hover:scale-100 transition-transform duration-500">
                            <span translate="no" class="notranslate material-icons text-xl">open_in_full</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Scroll Trigger & Loading Indicator -->
        <div class="text-center mt-16 min-h-[60px]" x-ref="loadMoreTrigger">
            <div x-show="loading" class="flex flex-col items-center gap-4 animate-fade-in">
                <svg class="animate-spin h-10 w-10 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-emerald-400/60 text-xs font-bold uppercase tracking-[0.2em]">Memuat Dokumentasi...</span>
            </div>
            
            <div x-show="!hasMore && kegiatan.length > 0" class="py-10">
                <div class="h-px w-20 bg-emerald-900/30 mx-auto mb-6"></div>
                <p class="text-gray-500 text-sm italic">Semua dokumentasi telah ditampilkan.</p>
            </div>
        </div>

    <!-- Enhanced Lightbox Modal -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-[#020403]/95 backdrop-blur-3xl"
         style="display: none;">
        
        <!-- Stronger Backdrop Shadow -->
        <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>

        <!-- Header Overlay -->
        <div class="absolute top-0 inset-x-0 p-6 flex justify-between items-start z-[110] bg-gradient-to-b from-black/95 via-black/40 to-transparent">
            <div class="flex flex-col pt-2">
                <h2 class="text-white text-[14px] md:text-xl font-extrabold tracking-tight drop-shadow-2xl mb-2" x-text="currentTitle"></h2>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 px-3 py-1 bg-emerald-500/40 rounded-lg text-emerald-300 text-[10px] font-black uppercase tracking-[0.2em] border border-emerald-500/30 backdrop-blur-md">
                        Dokumentasi
                    </div>
                    <span class="text-white/60 text-[10px] font-bold tracking-widest" x-text="(currentIndex + 1) + ' / ' + currentImages.length"></span>
                </div>
            </div>
            <button @click="closeLightbox()" class="w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-2xl bg-white/10 hover:bg-white/20 text-white border border-white/10 transition-all active:scale-90 shadow-2xl">
                <span translate="no" class="notranslate material-icons text-2xl">close</span>
            </button>
        </div>

        <!-- Navigation Buttons - Floating -->
        <button x-show="currentImages.length > 1" @click="prev()" class="absolute left-6 md:left-10 top-1/2 -translate-y-1/2 w-14 h-14 flex items-center justify-center rounded-2xl bg-black/20 hover:bg-emerald-600/40 text-white border border-white/10 backdrop-blur-md transition-all z-[110] active:scale-90">
            <span translate="no" class="notranslate material-icons text-3xl">west</span>
        </button>
        <button x-show="currentImages.length > 1" @click="next()" class="absolute right-6 md:right-10 top-1/2 -translate-y-1/2 w-14 h-14 flex items-center justify-center rounded-2xl bg-black/20 hover:bg-emerald-600/40 text-white border border-white/10 backdrop-blur-md transition-all z-[110] active:scale-90">
            <span translate="no" class="notranslate material-icons text-3xl">east</span>
        </button>

        <!-- Main Content Area - Rebalanced Spacing -->
        <div class="w-full h-full flex flex-col relative z-[105]">
            
            <!-- Image Area - More Growth -->
            <div class="flex-grow flex items-center justify-center px-6 md:px-20 py-6 min-h-0 mt-24">
                <div class="relative w-full h-full max-w-6xl flex items-center justify-center overflow-hidden rounded-3xl shadow-[0_40px_100px_-20px_rgba(0,0,0,0.8)] bg-black/20">
                    <img :src="currentImages[currentIndex]" class="max-w-full max-h-full rounded-3xl object-contain transition-all duration-700">
                </div>
            </div>
            
            <!-- Bottom Panel - More Spaced Out Bottom -->
            <div class="w-full bg-gradient-to-t from-black via-black/80 to-transparent backdrop-blur-xl border-t border-white/5 py-6 px-6">
                <div class="max-max-w-5xl mx-auto flex flex-col items-center">
                    
                    <!-- Description -->
                    <p class="text-emerald-100/70 text-[10px] md:text-base leading-relaxed mb-3 max-h-24 overflow-y-auto custom-scrollbar italic font-medium text-center drop-shadow-md" x-text="currentDescription"></p>
                    
                    <!-- Thumbnails Navigation Area -->
                    <div class="flex justify-center gap-4 overflow-x-auto max-w-full py-2 px-6 scroll-smooth no-scrollbar">
                        <template x-for="(img, idx) in currentImages" :key="idx">
                            <button @click="currentIndex = idx" 
                                    class="w-12 h-12 md:w-14 md:h-14 rounded-2xl overflow-hidden border-2 transition-all flex-shrink-0"
                                    :class="currentIndex === idx ? 'border-emerald-500 scale-110 shadow-[0_0_20px_rgba(16,106,68,0.4)]' : 'border-white/10 opacity-30 hover:opacity-100'">
                                <img :src="img" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lightbox / Modal Script (Simplified via Alpine.js) -->
<script>
    // Managed by Alpine.js in the section x-data
</script>
<?= $this->endSection() ?>
