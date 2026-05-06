<?= $this->extend('frontend/layout/main_layout') ?>

<?= $this->section('hero') ?>
<div x-data="{ showShareModal: false, currentUrl: window.location.href, title: '<?= esc($berita['judul'], 'js') ?>' }">
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image:url('<?= esc($berita['gambar'] ?? base_url('uploads/galeri/default.jpg')) ?>');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#001f1a]/90 via-[#001b17]/75 to-[#001510]/60"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 text-white">
            <!-- BREADCRUMB -->
            <div class="text-sm text-white/70 mb-4">
                <a href="<?= base_url() ?>" class="hover:text-white">Home</a>
                <span class="mx-2">›</span>
                <a href="<?= base_url('berita') ?>" class="hover:text-white">News</a>
                <?php if (!empty($berita['kategori'])): ?>
                    <span class="mx-2">›</span>
                    <span class="text-white"><?= esc($berita['kategori']) ?></span>
                <?php endif; ?>
            </div>

            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8">
                <div class="max-w-4xl flex-1">
                    <h1 class="text-3xl md:text-4xl font-semibold leading-tight drop-shadow-lg">
                        <?= esc($berita['judul']) ?>
                    </h1>

                    <div class="mt-6 flex flex-col gap-2 text-gray-200">
                        <?php if (!empty($berita['tanggal'])): ?>
                            <span class="flex items-center gap-2 text-sm">
                                <span translate="no" class="notranslate material-icons text-base">event</span>
                                <?= timeAgoOrDate($berita['tanggal']) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (isset($berita['total_view'])): ?>
                            <span class="flex items-center gap-2 text-xs text-white/80">
                                <span translate="no" class="notranslate material-icons text-sm">visibility</span>
                                Dilihat <?= (int)$berita['total_view'] ?> kali
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <button @click="showShareModal = true" class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all text-white px-3 py-1 rounded-lg flex items-center gap-2 text-sm font-medium shadow-lg shadow-emerald-900/20">
                        <span translate="no" class="notranslate material-icons text-lg">share</span>
                        Bagikan Berita
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- SHARE MODAL -->
    <div x-show="showShareModal" 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <div @click.away="showShareModal = false"
              class="bg-white dark:bg-[#0d3728] border border-gray-200 dark:border-emerald-900/50 rounded-xl w-full max-w-sm p-6 relative shadow-2xl transform transition-all"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 scale-95 translate-y-4"
              x-transition:enter-end="opacity-100 scale-100 translate-y-0"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 scale-100 translate-y-0"
              x-transition:leave-end="opacity-0 scale-95 translate-y-4">

            <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-emerald-900/30 pb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-emerald-50">Bagikan Berita</h3>
                <button @click="showShareModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-emerald-400 transition-colors">
                    <span class="material-icons text-2xl">close</span>
                </button>
            </div>

            <div class="space-y-6">
                <div class="p-4 bg-gray-50 dark:bg-[#0f1a16] rounded-xl border border-gray-100 dark:border-emerald-900/30">
                    <p class="text-[10px] text-gray-500 dark:text-emerald-400/70 mb-1.5 uppercase tracking-wider font-bold">Judul Berita</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 line-clamp-2 leading-relaxed"><?= esc($berita['judul']) ?></p>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <!-- WhatsApp -->
                    <a :href="'https://wa.me/?text=' + encodeURIComponent(title + '\n' + currentUrl)" 
                       target="_blank"
                       class="flex flex-col items-center gap-2 group">
                        <div class="w-12 h-12 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-lg shadow-green-900/20 hover:scale-110 transition-transform">
                           <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <span class="text-[10px] text-gray-600 dark:text-gray-400 font-bold">WhatsApp</span>
                    </a>

                    <!-- Facebook -->
                    <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentUrl)" 
                       target="_blank" 
                       class="flex flex-col items-center gap-2 group">
                        <div class="w-12 h-12 rounded-full bg-[#1877F2] text-white flex items-center justify-center shadow-lg shadow-blue-900/20 hover:scale-110 transition-transform">
                           <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036c-2.148 0-2.971.956-2.971 3.035v.975h4.03l-.582 3.667h-3.448v7.98c-1.572.261-3.212.183-4.843 0z"/></svg>
                        </div>
                        <span class="text-[10px] text-gray-600 dark:text-gray-400 font-bold">Facebook</span>
                    </a>

                    <!-- Twitter / X -->
                    <a :href="'https://twitter.com/intent/tweet?url=' + encodeURIComponent(currentUrl) + '&text=' + encodeURIComponent(title)" 
                       target="_blank"
                       class="flex flex-col items-center gap-2 group">
                        <div class="w-12 h-12 rounded-full bg-black dark:bg-white text-white dark:text-black flex items-center justify-center shadow-lg shadow-gray-400/20 hover:scale-110 transition-transform">
                           <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </div>
                        <span class="text-[10px] text-gray-600 dark:text-gray-400 font-bold">Twitter</span>
                    </a>

                    <!-- Copy Link -->
                    <button @click="navigator.clipboard.writeText(currentUrl); $el.querySelector('.cp-icon').innerHTML='check'; setTimeout(() => $el.querySelector('.cp-icon').innerHTML='content_copy', 1500)" 
                            class="flex flex-col items-center gap-2 group">
                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-[#15201b] text-gray-600 dark:text-gray-300 flex items-center justify-center border border-gray-200 dark:border-emerald-900/30 hover:bg-gray-200 dark:hover:bg-[#1a2923] hover:scale-110 transition-all">
                           <span class="material-icons text-xl cp-icon">content_copy</span>
                        </div>
                        <span class="text-[10px] text-gray-600 dark:text-gray-400 font-bold">Salin Link</span>
                    </button>
                </div>

                <div class="pt-6 mt-2">
                    <button @click="showShareModal = false" class="w-full py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
         </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<article class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        <!-- KONTEN -->
        <div class="lg:col-span-2 prose dark:prose-invert max-w-none">
            <!-- ISI BERITA -->
            <article class="post type-post single-post pb-10">
                <div class="container max-w-4xl">

                    <!-- FOTO UTAMA -->
                    <?php if (!empty($berita['gambar'])): ?>
                    <div class="mb-6 rounded-2xl overflow-hidden">
                        <img src="<?= esc($berita['gambar']) ?>" class="w-full h-[420px] object-cover">
                    </div>
                    <?php endif; ?>

                    <!-- KONTEN -->
                    <div class="content-berita prose max-w-none dark:prose-invert">
                        <?= $berita['isi_berita'] ?>
                    </div>

                    <!-- BERITA TERKAIT -->
                    <?php if (!empty($relatedBerita)): ?>
                    <div class="mt-14">
                        <h3 class="text-lg font-semibold mb-6 border-b pb-3">
                            Berita Terkait
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach ($relatedBerita as $b): ?>
                                <a href="<?= base_url('berita/detail/'.$b['judul_seo']) ?>"
                                class="group flex gap-4 p-4 rounded-xl
                                    bg-white dark:bg-[#0f1a16]
                                    border border-emerald-900/30
                                    hover:shadow-lg transition">

                                <div class="w-28 h-20 rounded-lg overflow-hidden flex-shrink-0">
                                    <img src="<?= esc($b['gambar'] ?? base_url('uploads/galeri/default.jpg')) ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition">
                                </div>

                                <div class="flex-1">
                                    <h4 class="font-medium leading-snug group-hover:text-emerald-600">
                                        <?= esc($b['judul']) ?>
                                    </h4>

                                    <?php if (!empty($b['tanggal'])): ?>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <?= timeAgoOrDate($b['tanggal']) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </article>

        </div>

        <!-- SIDEBAR -->
        <aside class="lg:col-span-1">
            <div class="sticky top-28 space-y-6">
                <div class="rounded-2xl p-6
                            bg-white
                            dark:bg-[#0f1a16]
                            border border-emerald-900/30">
                    <?= view_cell('App\Cells\SidebarCell::render') ?>
                    
                </div>

            </div>
        </aside>

    </div>
</article>
<?= $this->endSection() ?>
