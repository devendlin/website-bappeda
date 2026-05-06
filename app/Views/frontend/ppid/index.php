<?= $this->extend('frontend/layout/main_layout') ?>

<?= $this->section('hero') ?>
<section class="relative py-10 bg-gradient-to-r from-[#0f241b] to-[#106a44] dark:from-[#032417] dark:to-[#004a2d] overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] bg-repeat"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-6 text-center">
        <ul class="flex justify-center items-center gap-2 text-[10px] uppercase tracking-widest mb-6 text-emerald-100/60">
            <li><a href="<?= base_url('/') ?>" class="hover:text-white transition">Beranda</a></li>
            <li><span class="opacity-40">/</span></li>
            <li><span class="text-white">PPID</span></li>
        </ul>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 tracking-tight animate-fade-in-up reveal">
            Publikasi <span class="text-emerald-400">Dokumen</span>
        </h1>
        
        <p class="max-w-2xl mx-auto text-emerald-50/80 text-lg mb-10 leading-relaxed reveal delay-100">
            Akses informasi publik dan dokumen perencanaan pembangunan Daerah Kabupaten Bungo secara transparan dan akuntabel.
        </p>

        <div class="max-w-2xl mx-auto relative group">
            <form action="<?= base_url('ppid/search') ?>" method="GET" class="relative">
                <div class="relative flex items-center p-1 rounded-2xl bg-white/10 dark:bg-white/5 backdrop-blur-xl border border-white/20 shadow-2xl overflow-hidden ring-1 ring-white/10 focus-within:ring-emerald-500/40 focus-within:bg-white/15 focus-within:border-emerald-500/30 base-transition transition-all duration-300">
                    <span translate="no" class="notranslate material-icons ml-5 text-white/50 group-focus-within:text-emerald-400 transition-all duration-300">search</span>
                    <input type="text" name="q" placeholder="Cari dokumen (misal: RKPD 2024)..." 
                           class="w-full pl-4 pr-32 py-5 bg-transparent border-0 focus:ring-0 transition-all text-white placeholder:text-white/40 text-lg outline-none">
                    <button type="submit" class="absolute right-2 px-8 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-white rounded-xl font-black uppercase tracking-wider text-xs transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="py-16 min-h-screen">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="flex items-end justify-between mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-emerald-100 flex items-center gap-3">
                    <span class="w-2 h-8 bg-emerald-500 rounded-full"></span>
                    Kategori Dokumen
                </h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Pilih jenis dokumen yang ingin Anda telusuri</p>
            </div>
            <div class="hidden md:block text-right">
                <span class="text-sm font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400 px-4 py-2 rounded-full border border-emerald-400 dark:border-emerald-800/30">
                    <?= count($categories) ?> Kategori Tersedia
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($categories as $idx => $cat): ?>
            <a href="<?= base_url('ppid/'.$cat['slug_kategori']) ?>" 
               class="group relative bg-white dark:bg-[#0f1a16] p-8 rounded-3xl border border-emerald-900/30 dark:border-emerald-900/30 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden reveal delay-<?= ($idx % 4) * 100 ?>">
                
                <!-- Glow Effect on Hover -->
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/5 group-hover:bg-emerald-500/10 rounded-full blur-3xl transition-colors"></div>
                
                <div class="mb-6 w-14 h-14 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                    <span translate="no" class="notranslate material-icons text-3xl"><?= $cat['icon'] ?></span>
                </div>

                <h3 class="text-2xl font-black text-gray-900 dark:text-emerald-100 mb-3 tracking-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                    <?= $cat['nama_kategori'] ?>
                </h3>
                
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-6 line-clamp-2">
                    <?= $cat['deskripsi'] ?>
                </p>

                <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-50 dark:border-emerald-900/10">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-500 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        Buka Dokumen <span translate="no" class="notranslate material-icons text-[16px]">arrow_forward</span>
                    </span>
                    <span class="text-lg font-black text-gray-300 dark:text-emerald-100 group-hover:text-emerald-600 transition-colors">
                        <?= $cat['total_dokumen'] ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?= $this->endSection() ?>
