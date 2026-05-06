<?= $this->extend('frontend/layout/main_layout') ?>
<?= $this->section('hero') ?>

<!-- BREADCRUMBS -->
<div class="py-3 
            bg-emerald-50/60 
            dark:bg-[#0f1a16]
            text-emerald-800 
            dark:text-emerald-300">

  <div class="max-w-7xl mx-auto px-6">
    <ul class="flex justify-center items-center gap-2 text-xs uppercase notranslate">
      <li>
        <a href="<?= base_url('/') ?>" class="hover:text-emerald-600">
          Home
        </a>
      </li>
      <li class="opacity-60">→</li>
      <li>
        <span class="text-emerald-700 dark:text-emerald-400">
          404
        </span>
      </li>
    </ul>
  </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="wrapper" class="overflow-x-hidden min-h-screen">

  <!-- HEADER KATEGORI -->
  <header class="max-w-7xl mx-auto px-6 py-6 text-center">
    <h2 class="text-2xl lg:text-3xl font-bold
               text-gray-900
               dark:text-emerald-200">
      404
    </h2>
  </header>

  <!-- CONTENT -->
  <div class="max-w-7xl mx-auto px-6 py-10">
    <!-- WRAPPER UTAMA -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 w-full min-w-0">

 
      <!-- KOLOM KIRI -->
      <div class="lg:col-span-8 col-span-12 w-full min-w-0">

        <div class="space-y-6">

          

          
          
            <article
                class="group relative w-full mx-auto
                        rounded-2xl overflow-hidden
                        border border-emerald-900/20
                        bg-white dark:bg-[#0f1a16]
                        p-8 md:p-10
                        text-center">

                <!-- Glow -->
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition">
                    <div class="absolute -top-20 -left-20 w-60 h-60 bg-emerald-500/20 blur-3xl"></div>
                    <div class="absolute -bottom-20 -right-20 w-60 h-60 bg-emerald-500/10 blur-3xl"></div>
                </div>

                <!-- Content -->
                <div class="relative z-10 space-y-5">
                    <!-- Icon -->
                    <div class="mx-auto w-16 h-16 rounded-full
                                    bg-emerald-500/10
                                    flex items-center justify-center
                                    text-emerald-500">
                        <span class="material-icons text-3xl notranslate" translate="no">
                            search_off
                        </span>
                    </div>

                    <!-- Title -->
                    <h2 class="text-2xl md:text-3xl font-bold">
                    Halaman Tidak Ditemukan
                    </h2>

                    <!-- Description -->
                    <?php if (ENVIRONMENT !== 'development') : ?>
                        <?= nl2br(esc($message)) ?>
                    <?php else : ?>
                        <?= lang('Errors.sorryCannotFind') ?>
                    <?php endif; ?>

                    <!-- Action -->
                    <div class="pt-4">
                    <a href="/"
                        class="inline-flex items-center gap-2
                                px-6 py-3 rounded-full
                                bg-emerald-500 text-black font-semibold
                                hover:bg-emerald-400 transition">
                        <span class="material-icons text-xl notranslate" translate="no">
                        arrow_back
                        </span>
                        Kembali ke Beranda
                    </a>
                    </div>
                </div>

            </article>


        </div>

        

      </div>

      <!-- SIDEBAR -->
      <div class="lg:col-span-4 col-span-12">
        <aside class="sticky top-[116px] space-y-5">
          <div class="rounded-2xl p-6
                      bg-white
                      dark:bg-[#0f1a16]
                      border border-emerald-900/30">
            <?= view_cell('App\Cells\SidebarCell::render') ?>
          </div>
        </aside>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
