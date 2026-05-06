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
            <li><span class="text-white"><?= esc($berita['judul']) ?></span></li>
        </ul>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 animate-fade-in-up">
            <?= esc($berita['judul']) ?>
        </h1>
    </div>
</section>

<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="wrapper" class="min-h-screen">



  <!-- CONTENT -->
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 w-full min-w-0">

      <!-- KOLOM KIRI: BERITA -->
      <div class="lg:col-span-8 col-span-12 w-full min-w-0 space-y-6">

        <!-- GAMBAR FEATURED -->
        <?php $path = FCPATH . 'uploads/thumbnail/' . $berita['gambar']; ?>
        <?php if($berita['gambar'] != 'null' && !empty($berita['gambar']) && file_exists($path)): ?>
          <div class="rounded-2xl overflow-hidden aspect-[16/10] sm:aspect-[3/2] bg-emerald-50 dark:bg-emerald-900/20 w-full">
            <img src="<?= base_url('uploads/thumbnail/'.$berita['gambar']) ?>"
                 class="w-full h-full object-cover"
                 alt="<?= esc($berita['judul']) ?>"
                 loading="lazy">
          </div>
        <?php endif; ?>

        <!-- ISI BERITA -->
        <div class="content-berita prose max-w-full dark:prose-invert text-gray-900 dark:text-emerald-200">
          <?= $berita['isi_halaman'] ?>
        </div>

        <!-- SHARE -->
        <div class="flex flex-wrap items-center gap-3 mt-6">
            <span class="font-semibold text-gray-700 dark:text-emerald-300">Share:</span>
            
            <!-- Facebook -->
            <a href="#" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-[#0f1a16] border border-emerald-900/30 rounded-full hover:bg-emerald-700 hover:text-white transition">
                <span translate="no" class="notranslate material-icons text-blue-600 dark:text-blue-400">facebook</span>
            </a>

            <!-- X/Twitter -->
            <a href="#" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-[#0f1a16] border border-emerald-900/30 rounded-full hover:bg-[#1DA1F2] hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5 text-[#1DA1F2]">
                    <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0 1.95-2.48 10.72 10.72 0 0 1-3.45 1.32 4.52 4.52 0 0 0-7.86 4.13A12.88 12.88 0 0 1 1.67 2.15a4.52 4.52 0 0 0 1.4 6.04 4.48 4.48 0 0 1-2.05-.56v.06a4.52 4.52 0 0 0 3.63 4.43 4.52 4.52 0 0 1-2.04.08 4.52 4.52 0 0 0 4.22 3.14A9.05 9.05 0 0 1 1 19.54a12.76 12.76 0 0 0 6.92 2.03c8.3 0 12.85-6.88 12.85-12.85 0-.2 0-.39-.01-.58A9.18 9.18 0 0 0 23 3z"/>
                </svg>
            </a>

            <!-- Email -->
            <a href="#" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-[#0f1a16] border border-emerald-900/30 rounded-full hover:bg-green-600 hover:text-white transition">
                <span translate="no" class="notranslate material-icons text-green-600 dark:text-green-400">email</span>
            </a>

            <!-- Link -->
            <a href="#" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-[#0f1a16] border border-emerald-900/30 rounded-full hover:bg-gray-600 hover:text-white transition">
                <span translate="no" class="notranslate material-icons text-gray-600 dark:text-gray-400">link</span>
            </a>

        </div>


      </div>

      <!-- SIDEBAR -->
      <div class="lg:col-span-4 col-span-12">
        <aside class="sticky top-28 space-y-5">
          <div class="rounded-2xl p-6 bg-white dark:bg-[#0f1a16] border border-emerald-900/30">
            <?= view_cell('App\Cells\SidebarCell::render') ?>
          </div>
        </aside>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
