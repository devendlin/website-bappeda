<?= $this->extend('frontend/layout/main_layout') ?>

<?= $this->section('hero') ?>
<!-- BREADCRUMBS -->
<section class="relative py-20 bg-gradient-to-r from-[#0f241b] to-[#106a44] dark:from-[#032417] dark:to-[#004a2d] overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-repeat"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-6 text-center">
        <ul class="flex justify-center items-center gap-2 text-xs uppercase notranslate mb-4 text-emerald-100/60">
            <li><a href="<?= base_url('/') ?>" class="hover:text-white transition">Home</a></li>
            <li>→</li>
            <li><span class="text-white">Agenda Kegiatan</span></li>
        </ul>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 animate-fade-in-up reveal">
            Agenda Kegiatan
        </h1>
        <p class="text-emerald-100/80 max-w-2xl mx-auto text-sm md:text-base reveal delay-100">
            Ikuti berbagai kegiatan dan acara resmi di lingkungan Bappeda Kabupaten Bungo.
        </p>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="py-16 min-h-screen bg-gray-50/30 dark:bg-transparent">
    <div class="max-w-5xl mx-auto px-6">
        
        <?php if (!empty($agendas)): ?>
            <div class="space-y-8">
                <?php foreach ($agendas as $idx => $a): ?>
                    <div class="group bg-white dark:bg-[#0f1a16] rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-emerald-900/10 flex flex-col md:flex-row reveal delay-<?= ($idx % 4) * 100 ?>">
                        <!-- Date Badge (Side) -->
                        <div class="md:w-48 bg-emerald-600 dark:bg-emerald-800 p-6 flex flex-col items-center justify-center text-white text-center">
                            <?php if (!empty($a['tgl_pelaksanaan'])): ?>
                                <span class="text-3xl font-bold"><?= date('d', strtotime($a['tgl_pelaksanaan'])) ?></span>
                                <span class="text-xs uppercase tracking-widest font-semibold opacity-80"><?= date('M Y', strtotime($a['tgl_pelaksanaan'])) ?></span>
                            <?php else: ?>
                                <span translate="no" class="notranslate material-icons text-3xl mb-1">event_note</span>
                                <span class="text-[10px] uppercase font-bold opacity-80">Waktu Belum Ada</span>
                            <?php endif; ?>
                            <div class="w-8 h-1 bg-white/20 my-3 rounded-full"></div>
                            <span class="text-[10px] font-medium opacity-90 uppercase">
                                <?= !empty($a['jam']) ? $a['jam'] : 'Belum ditentukan' ?>
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="flex-grow p-8">
                            <div class="flex flex-wrap items-center gap-4 mb-4">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-tight">
                                    <span translate="no" class="notranslate material-icons text-sm">location_on</span>
                                    <span><?= $a['lokasi'] ?></span>
                                </div>
                            </div>

                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-emerald-50 mb-4 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                <?= $a['judul'] ?>
                            </h2>

                            <p class="text-gray-600 dark:text-emerald-100/70 text-sm leading-relaxed mb-6">
                                <?= $a['deskripsi'] ?>
                            </p>

                            <div class="pt-4 border-t border-gray-100 dark:border-emerald-900/20 flex justify-between items-center">
                                <span class="text-[10px] text-gray-400 uppercase font-medium">Status: Agenda Aktif</span>
                                <div class="flex gap-2">
                                    <?php if(isset($a['url']) && !empty($a['url'])): ?>
                                        <a href="<?= $a['url'] ?>" class="text-emerald-600 font-bold text-xs hover:underline flex items-center gap-1">
                                            Info Detail <span translate="no" class="notranslate material-icons text-xs">open_in_new</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="py-20 text-center flex flex-col items-center gap-4">
                <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-900/10 rounded-full flex items-center justify-center text-emerald-300">
                    <span translate="no" class="notranslate material-icons text-5xl">event_busy</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Tidak Ada Agenda</h3>
                    <p class="text-gray-500 text-sm mt-1">Saat ini belum ada jadwal kegiatan resmi yang dipublikasikan.</p>
                </div>
                <a href="<?= base_url('/') ?>" class="mt-4 px-6 py-2 bg-emerald-600 text-white rounded-full font-bold text-sm hover:bg-emerald-700 transition">Kembali ke Beranda</a>
            </div>
        <?php endif; ?>

    </div>
</section>
<?= $this->endSection() ?>
