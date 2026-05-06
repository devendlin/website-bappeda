<?= $this->extend('frontend/layout/main_layout') ?>
<?= $this->section('hero') ?>
<?= view_cell('App\Cells\HeaderberitaCell::render') ?>
<?= $this->endSection() ?>  

<?= $this->section('content') ?>
    
<!-- CONTENT -->
<div class="max-w-7xl mx-auto px-6 py-16">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

    <!-- KONTEN -->
    <div class="lg:col-span-2 prose dark:prose-invert max-w-none">

      <?php foreach ($kategoriBerita as $kategori): ?>
      <section class="mb-8">

        <!-- BLOCK HEADER -->
        <div class="mb-4 border-b border-emerald-900/30 pb-2 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-emerald-200">
                <span><?= esc($kategori['nama_kategori']) ?></span>
            </h2>
            <a href="<?= base_url('kategori/' . $kategori['kategori_seo']) ?>"
                class="inline-flex items-center gap-2

                        bg-white dark:bg-[#0f1a16]
                        text-emerald-700 dark:text-emerald-400

                        
                        ring-1 ring-emerald-900
                        px-3 py-1.5 rounded-lg
                        transition duration-200

                        hover:bg-emerald-700 dark:hover:bg-emerald-600
                        hover:text-white">

                <span>Lainnya</span>
                <span>→</span>
            </a>
        </div>
        
        <!-- LIST BERITA -->
        <div class="grid grid-cols-1 gap-4">
          <?php foreach ($kategori['berita'] as $berita): ?>

          <article class="
              bg-white
              dark:bg-[#0f1a16]
              rounded-xl p-3
              border border-emerald-900/15
              hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)]
              dark:hover:shadow-[0_10px_30px_rgba(0,0,0,0.25)]
              hover:border-emerald-500/40
              transition-all duration-300
            ">


            <div class="flex items-center gap-4">

              <!-- TEXT + META -->
              <div class="flex-1 min-w-0">

                <h3 class="text-base lg:text-lg font-medium leading-snug
                           text-gray-900 dark:text-emerald-200 mb-2">

                  <a class="hover:text-emerald-500 transition-colors duration-200 line-clamp-2"
                     href="<?= base_url('berita/detail/' . $berita['judul_seo']) ?>">
                    <?= esc($berita['judul']) ?>
                  </a>

                </h3>

                <div class="flex items-center gap-3 text-xs mt-1
                            text-gray-600 dark:text-emerald-300/70">

                  <span><?= timeAgoOrDate($berita['tanggal']) ?></span>
                  <span class="opacity-40">·</span>

                  <span class="inline-flex items-center gap-1">
                    <span translate="no" class="notranslate material-icons text-sm">visibility</span>
                    <?= esc($berita['total_view'] ?? 0) ?>
                  </span>

                </div>

              </div>

              <!-- GAMBAR -->
              <div class="w-20 h-14 rounded-lg overflow-hidden flex-shrink-0 border border-emerald-900/15">
                <img class="w-full h-full object-cover"
                     onerror="this.onerror=null;this.src='<?= base_url('uploads/galeri/default.jpg') ?>';"
                     src="<?= esc($berita['gambar']) ?>"
                     loading="lazy"
                     alt="">
              </div>

            </div>

          </article>

          <?php endforeach; ?>
        </div>

      </section>
      <?php endforeach; ?>

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
</div>

<?= $this->endSection() ?>
