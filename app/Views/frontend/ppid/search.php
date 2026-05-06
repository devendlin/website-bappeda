<?= $this->extend('frontend/layout/main_layout') ?>

<?= $this->section('hero') ?>
<section class="relative py-20 bg-gradient-to-r from-[#032417] to-[#004a2d] overflow-hidden">
    <div class="relative max-w-7xl mx-auto px-6 text-center">
        <ul class="flex justify-center items-center gap-2 text-[10px] uppercase tracking-widest mb-6 text-emerald-100/60">
            <li><a href="<?= base_url('/') ?>" class="hover:text-white transition">Beranda</a></li>
            <li><span class="opacity-40">/</span></li>
            <li><a href="<?= base_url('ppid') ?>" class="hover:text-white transition">PPID</a></li>
            <li><span class="opacity-40">/</span></li>
            <li><span class="text-white">Pencarian</span></li>
        </ul>

        <h1 class="text-3xl md:text-4xl font-black text-white mb-2">
            Hasil Pencarian: <span class="text-emerald-400">"<?= esc($keyword) ?>"</span>
        </h1>
        <p class="text-emerald-100/60 font-medium tracking-wide uppercase text-xs">
            Ditemukan <?= count($dokumen) ?> dokumen yang relevan
        </p>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="py-16 min-h-screen" x-data="{ 
    showPdf: false, 
    pdfUrl: '', 
    pdfTitle: '',
    openPdf(url, title, id) {
        this.pdfUrl = url;
        this.pdfTitle = title;
        this.showPdf = true;
        document.body.style.overflow = 'hidden';
        if (id) fetch('<?= base_url('ppid/track-view') ?>/' + id);
    },
    closePdf() {
        this.showPdf = false;
        this.pdfUrl = '';
        document.body.style.overflow = '';
    }
}" @keydown.escape.window="closePdf()">
    <div class="max-w-7xl mx-auto px-6">
        
        <?php if (empty($dokumen)): ?>
            <div class="bg-white dark:bg-[#0d1512] rounded-3xl p-12 text-center border border-emerald-900/20 dark:border-emerald-900/30">
                <div class="w-20 h-20 bg-red-50 dark:bg-red-900/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span translate="no" class="notranslate material-icons text-4xl text-red-300">search_off</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-emerald-100 mb-2">Tidak ditemukan dokumen</h3>
                <p class="text-gray-500 dark:text-gray-400">Gunakan kata kunci lain atau telusuri berdasarkan kategori di halaman utama PPID.</p>
                <a href="<?= base_url('ppid') ?>" class="inline-flex items-center gap-2 mt-8 text-emerald-600 dark:text-emerald-400 font-bold hover:gap-3 transition-all">
                    <span translate="no" class="notranslate material-icons">arrow_back</span> Kembali ke PPID
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($dokumen as $doc): ?>
                <div class="group bg-white dark:bg-[#0d1512] p-6 rounded-2xl border border-emerald-900/20 dark:border-emerald-900/20 shadow-sm hover:shadow-xl hover:border-emerald-500/30 transition-all duration-300">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        <div class="flex-shrink-0">
                             <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl flex items-center justify-center text-emerald-600 border border-emerald-100 dark:border-emerald-900/20 shadow-sm">
                                <span translate="no" class="notranslate material-icons text-3xl">picture_as_pdf</span>
                            </div>
                        </div>
                        
                        <div class="flex-grow">
                             <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-emerald-400/10 text-emerald-500 dark:text-emerald-400 text-[9px] font-black uppercase tracking-widest mb-2 border border-emerald-400/10">
                                <span translate="no" class="notranslate material-icons text-[12px]">folder</span>
                                <?= $doc['nama_kategori'] ?>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-emerald-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-tight">
                                <?= $doc['judul_dokumen'] ?>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 italic"><?= $doc['deskripsi'] ?></p>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4">
                                <div class="flex items-center gap-1.5 text-[10px] font-black text-gray-400 dark:text-emerald-600 uppercase tracking-widest">
                                    <span translate="no" class="notranslate material-icons text-[14px]">calendar_today</span>
                                    <?= date('d M Y', strtotime($doc['tgl_upload'])) ?>
                                </div>
                                <div class="hidden md:block w-1 h-1 rounded-full bg-gray-200 dark:bg-emerald-800"></div>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <span translate="no" class="notranslate material-icons text-[14px]">visibility</span>
                                        <?= number_format($doc['views'] ?? 0) ?>
                                    </div>
                                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-[11px] font-bold text-blue-600 dark:text-blue-400">
                                        <span translate="no" class="notranslate material-icons text-[14px]">file_download</span>
                                        <?= number_format($doc['downloads'] ?? 0) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 md:flex-shrink-0 border-t md:border-t-0 border-gray-50 dark:border-emerald-900/20 pt-4 md:pt-0">
                            <?php 
                                $isDrive = strpos($doc['file_pdf'], 'drive:') === 0;
                                $isMissing = false;

                                if ($isDrive) {
                                    $fileId = substr($doc['file_pdf'], 6);
                                    $previewUrl = "https://drive.google.com/file/d/" . $fileId . "/preview";
                                    $downloadUrl = "https://drive.google.com/uc?export=download&id=" . $fileId;
                                } else {
                                    $localPath = FCPATH . 'uploads/ppid/' . $doc['file_pdf'];
                                    if (file_exists($localPath)) {
                                        $previewUrl = base_url('uploads/ppid/'.$doc['file_pdf']);
                                        $downloadUrl = base_url('uploads/ppid/'.$doc['file_pdf']);
                                    } else {
                                        $isMissing = true;
                                        $previewUrl = '#';
                                        $downloadUrl = '#';
                                    }
                                }
                            ?>
                            
                            <?php if (!$isMissing): ?>
                                <button @click="openPdf('<?= $previewUrl ?>', '<?= esc($doc['judul_dokumen']) ?>', '<?= $doc['id_dokumen'] ?>')" 
                                   class="flex items-center gap-2 px-5 py-3 rounded-xl bg-gray-50 hover:bg-emerald-50 dark:bg-[#15201b] dark:hover:bg-emerald-900/20 text-gray-600 hover:text-emerald-600 dark:text-emerald-400 font-bold text-sm transition-all flex-grow md:flex-grow-0 justify-center">
                                    <span translate="no" class="notranslate material-icons text-lg">visibility</span> Lihat
                                </button>
                                <a href="<?= $downloadUrl ?>" download @click="fetch('<?= base_url('ppid/track-download') ?>/<?= $doc['id_dokumen'] ?>')"
                                   class="flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/10 transition-all active:scale-95 flex-grow md:flex-grow-0 justify-center">
                                    <span translate="no" class="notranslate material-icons text-lg">file_download</span> Download
                                </a>
                            <?php else: ?>
                                <button disabled 
                                   class="flex items-center gap-2 px-5 py-3 rounded-xl bg-red-50 text-red-400 font-bold text-sm flex-grow md:flex-grow-0 justify-center w-full cursor-not-allowed opacity-80">
                                    <span translate="no" class="notranslate material-icons text-lg">error_outline</span> Arsip Tidak Tersedia
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- PDF Viewer Modal -->
    <div x-show="showPdf" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex flex-col bg-black/90 backdrop-blur-sm"
         style="display: none;">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-black/40 border-b border-white/10">
            <h3 class="text-white font-bold truncate pr-10" x-text="pdfTitle"></h3>
            <button @click="closePdf()" class="text-white hover:text-red-500 transition-colors p-2">
                <span translate="no" class="notranslate material-icons text-3xl">close</span>
            </button>
        </div>

        <!-- PDF Loader / Iframe -->
        <div class="flex-grow relative overflow-hidden bg-gray-900/50">
            <template x-if="pdfUrl">
                <iframe :src="pdfUrl" class="w-full h-full border-0" allow="autoplay"></iframe>
            </template>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
