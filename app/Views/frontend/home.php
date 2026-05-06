<?= $this->extend('frontend/layout/main_layout') ?>
<?= $this->section('hero') ?>
<div x-data="{ 
    open: false, 
    currentImages: [], 
    currentIndex: 0,
    currentTitle: '',
    currentDescription: '',
    openLightbox(item, index = 0) {
        this.currentImages = item.foto;
        this.currentTitle = item.judul;
        this.currentDescription = item.deskripsi;
        this.currentIndex = index;
        this.open = true;
        document.body.style.overflow = 'hidden';
    },
    closeLightbox() {
        this.open = false;
        document.body.style.overflow = '';
    },
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.currentImages.length;
    },
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.currentImages.length) % this.currentImages.length;
    }
}" @keydown.escape.window="closeLightbox()">

<section class="relative overflow-hidden">
  <!-- Background Foto + Overlay Gelap -->
  <div class="absolute inset-0">
    <!-- Foto background -->
    <div class="absolute inset-0 bg-cover bg-center"
         style="background-image: url('<?= base_url('uploads/banner/hero.jpg') ?>');">
    </div>

    <div class="absolute inset-0"
        style="background: linear-gradient(
          135deg,
          rgba(0, 35, 25, 0.98) 0%,
          rgba(0, 11, 14, 0.75) 60%,
          rgba(0, 23, 28, 0.5) 100%
        );">
    </div>
  </div>

  <!-- Content -->
  <div class="relative max-w-7xl mx-auto px-6 py-6 lg:py-14 text-white">
    <!-- FOTO STACK - Tampil di atas untuk MOBILE & TABLET -->
    <div id="mobile-stack-container" class="relative w-full h-[280px] lg:hidden mb-8">
      <?php if (!empty($kegiatanStack)): ?>
        <?php foreach ($kegiatanStack as $index => $item): 
          $count = count($item['foto']);
        ?>
          <div class="stack-set absolute inset-0 opacity-0 scale-90 translate-y-10 pointer-events-none transition-all duration-500 z-10" data-stack="<?= $index ?>">
            <div class="group relative w-full h-full cursor-pointer flex items-center justify-center font-notranslate opacity-60 hover:opacity-100 transition-opacity duration-500" @click="openLightbox(<?= htmlspecialchars(json_encode($item)) ?>, 0)">
                <?php if ($count >= 3): ?>
                    <img src="<?= $item['foto'][0] ?>" class="stack-img img-a shadow-2xl transform transition-all duration-500 hover:scale-105 hover:rotate-[-1deg] z-10">
                    <img src="<?= $item['foto'][1] ?>" class="stack-img img-c shadow-2xl transform transition-all duration-500 hover:scale-105 hover:rotate-6 z-20">
                    <img src="<?= $item['foto'][2] ?>" class="stack-img img-b shadow-2xl transform transition-all duration-500 hover:scale-110 hover:rotate-3 z-30">
                <?php elseif ($count == 2): ?>
                    <img src="<?= $item['foto'][0] ?>" class="stack-img img-c !left-[10%] !top-[15%] !w-[60%] !h-[60%] shadow-xl transform transition-all duration-500 hover:scale-105 hover:rotate-6 z-20">
                    <img src="<?= $item['foto'][1] ?>" class="stack-img img-b !left-[30%] !top-[25%] !w-[60%] !h-[60%] shadow-2xl transform transition-all duration-500 hover:scale-110 hover:rotate-3 z-30">
                <?php elseif ($count == 1): ?>
                    <img src="<?= $item['foto'][0] ?>" class="stack-img img-c shadow-2xl transform transition-all duration-500 hover:scale-105 z-20" style="left: 13% !important; top: 25% !important; width: 75% !important; height: 75% !important;">
                <?php else: ?>
                    <div class="stack-img img-b bg-emerald-900/20 flex items-center justify-center rounded-2xl shadow-2xl !static !w-[80%] !h-[90%]"><span translate="no" class="notranslate material-icons text-5xl opacity-20">image</span></div>
                <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

      <!-- KIRI TEXT (CAROUSEL) -->
      <div class="relative min-h-[115px] lg:min-h-[280px] reveal">
        <?php if (!empty($kegiatanStack)): ?>
            <?php foreach ($kegiatanStack as $index => $item): ?>
                <div class="hero-slide absolute top-5 left-0 w-full opacity-0 translate-y-10 transition-all duration-700 ease-out" data-slide="<?= $index ?>">
                  <h2 class="text-xl lg:text-4xl font-semibold leading-tight max-w-xl drop-shadow-lg text-center lg:text-left mx-auto lg:mx-0 line-clamp-4">
                    <?= $item['judul'] ?>
                  </h2>

                  <p class="hidden lg:block lg:line-clamp-4 mt-6 max-w-lg text-gray-200 text-lg drop-shadow-sm">
                    <?= !empty($item['deskripsi']) ? $item['deskripsi'] : "Dokumentasi kegiatan Bappeda Kabupaten Bungo yang dilaksanakan pada tanggal " . $item['tanggal'] . "." ?>
                  </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="hero-slide absolute top-5 left-0 w-full opacity-100 translate-y-0">
               <h2 class="text-xl lg:text-4xl font-semibold leading-tight max-w-xl drop-shadow-lg text-center lg:text-left mx-auto lg:mx-0">
                Dokumentasi <span class="text-emerald-400">Kegiatan Terkini</span>
               </h2>
               <p class="mt-6 max-w-lg text-gray-200 text-lg drop-shadow-sm hidden lg:block">
                Belum ada dokumentasi kegiatan yang diunggah ke sistem.
               </p>
            </div>
        <?php endif; ?>
      </div>

      <!-- FOTO STACK - Tampil di kanan untuk DESKTOP -->
      <div class="relative w-full h-[420px] hidden lg:block reveal">
        <?php if (!empty($kegiatanStack)): ?>
            <?php foreach ($kegiatanStack as $index => $item): 
              $count = count($item['foto']);
            ?>
                <div class="stack-set absolute inset-0 opacity-0 scale-90 translate-y-10 pointer-events-none transition-all duration-500 z-10" data-stack="<?= $index ?>">
                  <div class="group relative w-full h-full cursor-pointer flex items-center justify-center font-notranslate opacity-60 hover:opacity-100 transition-opacity duration-500" @click="openLightbox(<?= htmlspecialchars(json_encode($item)) ?>, 0)">
                    <?php if ($count >= 3): ?>
                        <img src="<?= $item['foto'][0] ?>" class="stack-img img-a shadow-2xl transform transition-all duration-500 hover:scale-105 hover:rotate-[-1deg] z-10">
                        <img src="<?= $item['foto'][1] ?>" class="stack-img img-c shadow-2xl transform transition-all duration-500 hover:scale-105 hover:rotate-6 z-20">
                        <img src="<?= $item['foto'][2] ?>" class="stack-img img-b shadow-2xl transform transition-all duration-500 hover:scale-110 hover:rotate-3 z-30">
                    <?php elseif ($count == 2): ?>
                        <img src="<?= $item['foto'][0] ?>" class="stack-img img-c !left-[20%] !top-[10%] !w-[50%] !h-[70%] shadow-xl transform transition-all duration-500 hover:scale-105 hover:rotate-6 z-20">
                        <img src="<?= $item['foto'][1] ?>" class="stack-img img-b !left-[40%] !top-[20%] !w-[50%] !h-[70%] shadow-2xl transform transition-all duration-500 hover:scale-110 hover:rotate-3 z-30">
                    <?php elseif ($count == 1): ?>
                        <img src="<?= $item['foto'][0] ?>" class="stack-img img-c shadow-2xl transform transition-all duration-500 hover:scale-105 z-20" style="left: 6% !important; top: 10% !important; width: 75% !important; height: 85% !important;">
                    <?php endif; ?>
                  </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Navigation Dots -->
    <?php if (!empty($kegiatanStack) && count($kegiatanStack) > 1): ?>
    <div class="flex justify-center gap-3 mt-5 reveal">
      <?php foreach ($kegiatanStack as $index => $item): ?>
        <button class="hero-dot w-3 h-3 rounded-full <?= $index === 0 ? 'bg-emerald-400 w-8' : 'bg-white/30' ?> transition-all duration-300" data-dot="<?= $index ?>"></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Cards -->
    <!-- Swiper Container -->
    <div class="swiper swiper-pd mySlider min-h-[145px] mb-10 mt-5">
        <div class="swiper-wrapper items-stretch">
            <?php foreach ($ppid_kategori as $cat): ?>
            <div class="swiper-slide h-auto">
                <a href="<?= base_url('ppid/' . $cat['slug_kategori']) ?>" class="block h-full w-full">
                    <div class="rounded-2xl p-3
                        bg-white/10 dark:bg-white/5
                        backdrop-blur-[6px]
                        border border-white/20
                        hover:bg-white/20 hover:border-emerald-500/50
                        transition-all duration-300
                        flex flex-col h-full w-full group shadow-lg reveal">

                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-sm uppercase tracking-widest text-emerald-400 font-bold group-hover:text-emerald-300 transition-colors">
                                <?= $cat['nama_kategori'] ?>
                            </h3>
                            <span translate="no" class="notranslate material-icons text-emerald-500/50 group-hover:text-emerald-400 transition-colors text-xl">file_download</span>
                        </div>

                        <p class="text-gray-100/90 leading-relaxed text-sm flex-grow">
                            <?= $cat['deskripsi'] ?>
                        </p>
                        
                        <div class="mt-4 flex items-center gap-2 text-[10px] text-emerald-400/60 font-bold uppercase tracking-tighter group-hover:text-emerald-400 transition-colors">
                            <span>Buka Dokumen</span>
                            <span translate="no" class="notranslate material-icons text-xs">arrow_forward</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach ?>

        </div>
    </div>



  </div>
</section>


<script>
// Hero Carousel Auto-Rotation
document.addEventListener("DOMContentLoaded", () => {
  let currentSlide = 0;
  const totalSlides = <?= !empty($kegiatanStack) ? count($kegiatanStack) : 1 ?>;
  const intervalTime = 6000; // 6 detik
  let autoPlayInterval;

  // Fungsi untuk menampilkan slide tertentu
  function showSlide(index) {
    // 1. FASE KELUAR: Sembunyikan SEMUA slide dan stack dulu
    document.querySelectorAll('.hero-slide').forEach((slide) => {
      slide.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
      slide.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none');
    });

    document.querySelectorAll('.stack-set').forEach((stack) => {
      stack.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
      stack.classList.add('opacity-0', 'scale-90', 'translate-y-10');
      stack.style.pointerEvents = 'none';
      stack.style.zIndex = "10";
    });

    // Update dots segera (biar responsif)
    document.querySelectorAll('.hero-dot').forEach((dot, i) => {
      if (i === index) {
        dot.classList.remove('bg-white/30', 'w-3');
        dot.classList.add('bg-emerald-400', 'w-8');
      } else {
        dot.classList.remove('bg-emerald-400', 'w-8');
        dot.classList.add('bg-white/30', 'w-3');
      }
    });

    // 2. FASE MASUK: Tampilkan slide tujuan setelah delay (Sequential)
    // Delay 500ms agar slide lama sempat "hilang" dulu di mata user
    setTimeout(() => {
      // Text Masuk
      const targetSlide = document.querySelector(`.hero-slide[data-slide="${index}"]`);
      if (targetSlide) {
        targetSlide.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
        targetSlide.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
      }

      // Foto Stack Masuk (Pop Up)
      // Perlu selector 'all' karena ada duplikat ID/class untuk mobile & desktop?
      // Tidak, data-stack unik per set (di mobile ada, desktop ada).
      // Kita pakai querySelectorAll agar kena dua-duanya (mobile & desktop)
      document.querySelectorAll(`.stack-set[data-stack="${index}"]`).forEach(stack => {
        stack.classList.remove('opacity-0', 'scale-90', 'translate-y-10');
        stack.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        stack.style.pointerEvents = 'auto';
        stack.style.zIndex = "40";
      });
    }, 500); // Waktu jeda "kosong"

    currentSlide = index;
  }

  // Auto-play
  function startAutoPlay() {
    autoPlayInterval = setInterval(() => {
      const nextSlide = (currentSlide + 1) % totalSlides;
      showSlide(nextSlide);
    }, intervalTime);
  }

  // Stop auto-play saat user klik manual
  function resetAutoPlay() {
    clearInterval(autoPlayInterval);
    startAutoPlay();
  }

  // Dots click handler
  document.querySelectorAll('.hero-dot').forEach((dot, index) => {
    dot.addEventListener('click', () => {
      showSlide(index);
      resetAutoPlay();
    });
  });

  // Inisialisasi: tampilkan slide pertama
  // Inisialisasi: tampilkan slide pertama dengan sedikit delay agar animasi jalan
  setTimeout(() => {
    showSlide(0);
    startAutoPlay();
  }, 100);

  // --- SWIPE SUPPORT (Mobile/Tablet) ---
  const mobileStack = document.getElementById('mobile-stack-container');
  let touchStartX = 0;
  let touchEndX = 0;
  
  if (mobileStack) {
    mobileStack.addEventListener('touchstart', e => {
      touchStartX = e.changedTouches[0].screenX;
    }, {passive: true});

    mobileStack.addEventListener('touchend', e => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    }, {passive: true});
  }

  function handleSwipe() {
    const threshold = 50; // Jarak minimum geser untuk dianggap swipe
    if (touchEndX < touchStartX - threshold) {
      // Swipe Left -> Next (Geser ke kiri = maju)
      const nextSlide = (currentSlide + 1) % totalSlides;
      showSlide(nextSlide);
      resetAutoPlay();
    }
    
    if (touchEndX > touchStartX + threshold) {
      // Swipe Right -> Prev (Geser ke kanan = mundur)
      const prevSlide = (currentSlide - 1 + totalSlides) % totalSlides;
      showSlide(prevSlide);
      resetAutoPlay();
    }
  }
});
</script>



<section class="relative w-full overflow-hidden
                bg-white dark:bg-transparent
                py-20">

  <!-- GLOW 1 -->
  <div class="absolute inset-0
              bg-[radial-gradient(circle_at_20%_30%,rgba(16,185,129,0.18),transparent_45%)]
              dark:bg-[radial-gradient(circle_at_20%_30%,rgba(34,211,238,0.22),transparent_45%)]">
  </div>

  <!-- GLOW 2 -->
  <div class="absolute inset-0
              bg-[radial-gradient(circle_at_80%_70%,rgba(99,102,241,0.18),transparent_45%)]
              dark:bg-[radial-gradient(circle_at_80%_70%,rgba(99,102,241,0.22),transparent_45%)]">
  </div>

  <!-- GRID -->
  <div class="absolute inset-0 opacity-50 dark:opacity-40">
    <svg width="100%" height="100%" class="block">
      <defs>
        <pattern id="grid-light" width="32" height="32" patternUnits="userSpaceOnUse">
          <path d="M32 0H0V32"
                fill="none"
                stroke="rgba(0,0,0,0.08)"
                stroke-width="1"/>
        </pattern>

        <pattern id="grid-dark" width="32" height="32" patternUnits="userSpaceOnUse">
          <path d="M32 0H0V32"
                fill="none"
                stroke="rgba(34,211,238,0.18)"
                stroke-width="1"/>
        </pattern>
      </defs>

      <!-- LIGHT -->
      <rect width="100%" height="100%"
            fill="url(#grid-light)"
            class="dark:hidden"/>

      <!-- DARK -->
      <rect width="100%" height="100%"
            fill="url(#grid-dark)"
            class="hidden dark:block"/>
    </svg>
  </div>

  <!-- INNER HIGHLIGHT -->
  <div class="pointer-events-none absolute inset-0 rounded-3xl
    shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]
    dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
  </div>

  <div class="mx-auto max-w-7xl px-6">

    <!-- HEADER -->
    <div class="mb-14 text-center reveal">
      <img
        src="https://bappeda.bungokab.go.id/assets/img/logo22.png"
        class="mx-auto w-28 rounded-xl
              border border-emerald-900/20
              bg-emerald-50/60 dark:bg-[#0b1713]
              p-3 backdrop-blur"
        alt=""
      >

      <h2 class="mt-4 text-3xl font-bold
                text-emerald-600 dark:text-emerald-400">
        BAPPEDA SIPPLaTu
      </h2>

      <p class="mt-2 max-w-xl mx-auto
                text-gray-600 dark:text-gray-400">
        Sistem Informasi Perencanaan Pembangunan dan Pengelolaan Data Terpadu
      </p>
    </div>


    <div translate="no" class="notranslate flex flex-wrap justify-center gap-4 sm:gap-6 reveal">
    
      <?php foreach ($aplikasi as $app): ?>
      <!-- <?= $app['nama_aplikasi'] ?> -->
      <a href="<?= $app['url'] ?>" target="_blank"
        class="group relative flex flex-col items-center justify-center reveal-item
                w-[100px] sm:w-[160px]
                rounded-2xl border border-emerald-900/20
                bg-white/70 dark:bg-[#0b1411]
                dark:border-white/10
                backdrop-blur-md p-4 sm:p-6
                transition-all duration-300
                hover:-translate-y-1 hover:shadow-xl
                hover:border-emerald-500/50 text-center">
        <div class="mb-2 sm:mb-4 flex h-14 w-14 sm:h-20 sm:w-20 items-center justify-center
                    rounded-full bg-emerald-500/10 dark:bg-emerald-500/20
                    text-white shadow-inner overflow-hidden border border-emerald-500/30">
          <img src="<?= $app['gambar'] ?>"
              class="rounded-full h-8 w-8 sm:h-12 sm:w-12 object-contain group-hover:scale-110 transition-transform duration-300">
        </div>
        <span class="text-[10px] sm:text-xs font-semibold text-gray-800 dark:text-gray-200
                    group-hover:text-emerald-500 transition line-clamp-1">
          <?= $app['nama_aplikasi'] ?>
        </span>
      </a>
      <?php endforeach; ?>

    </div>


  </div>
</section>

<!-- LANDSCAPE BANNER SLIDER -->
<!-- <?php if (!empty($bannersPanjang)): ?>
<section class="max-w-7xl mx-auto px-6 py-10 reveal">
    <div class="swiper bannerLongSwiper rounded-3xl overflow-hidden shadow-2xl shadow-emerald-900/10 border border-emerald-900/10">
        <div class="swiper-wrapper">
            <?php foreach ($bannersPanjang as $bp): ?>
            <div class="swiper-slide">
                <a href="<?= !empty($bp['url']) ? $bp['url'] : 'javascript:void(0)' ?>" class="block group relative overflow-hidden">
                    <img src="<?= $bp['img_url'] ?>" class="w-full h-auto aspect-[21/9] lg:aspect-[3/1] object-cover transform transition-transform duration-1000 group-hover:scale-105" alt="<?= $bp['judul'] ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-8">
                        <div class="text-white">
                            <h4 class="text-xl font-bold"><?= $bp['judul'] ?></h4>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="swiper-pagination !bottom-4"></div>
    </div>
</section>
<?php endif; ?> -->

<!-- cursor -->
<div id="cursor-dot"></div>
<div id="cursor-ring"></div>


<section class="max-w-7xl mx-auto px-6 py-16">
  <div class="row lg:flex lg:gap-12">
    <!-- MAIN NEWS (70%) -->
    <div class="lg:w-[70%]">
      <!-- Judul Section -->
      <div class="mb-12">
        <h2 class="text-3xl font-bold text-emerald-500">
          Berita Terbaru
        </h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Informasi terkini seputar kegiatan dan pengumuman Bappeda
        </p>
      </div>

      <!-- Grid Berita -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php if (!empty($berita_terbaru)): ?>
            <?php foreach (array_slice($berita_terbaru, 0, 4) as $berita): ?>
                <!-- Card Berita -->
                <div class="group rounded-2xl overflow-hidden shadow-md
                            bg-white dark:bg-[#0f1a16]
                            border border-gray-200/20 dark:border-emerald-900/30
                            transition-all hover:shadow-xl flex flex-col h-full reveal">
                  <div class="relative h-48 overflow-hidden">
                    <?php 
                        $imgUrl = $berita['gambar'];
                        if (empty($imgUrl)) {
                            $imgUrl = base_url('uploads/galeri/default.jpg');
                        } elseif (!str_starts_with($imgUrl, 'http')) {
                            $imgUrl = base_url('uploads/galeri/' . $imgUrl);
                        }
                    ?>
                    <img src="<?= $imgUrl ?>"
                         alt="<?= $berita['judul'] ?>"
                         class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110">
                    <!-- Category Badge -->
                    <?php if (!empty($berita['nama_kategori'])): ?>
                    <div class="absolute top-4 left-4">
                      <span class="px-3 py-1 bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-full shadow-lg">
                        <?= $berita['nama_kategori'] ?>
                      </span>
                    </div>
                    <?php endif; ?>
                  </div>
                  
                  <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-center gap-2 mb-3 text-[11px] text-gray-500 dark:text-emerald-400/60 font-medium font-notranslate">
                        <!-- Bagian kiri: Tanggal -->
                        <div class="flex items-center gap-2">
                            <span translate="no" class="notranslate material-icons text-sm">calendar_today</span>
                            <?= timeAgoOrDate($berita['tanggal']) ?>
                        </div>

                        <!-- Bagian kanan: Views -->
                        <div class="flex items-center gap-2">
                            <span translate="no" class="notranslate material-icons text-sm">visibility</span>
                            <span><?= $berita['total_view'] ?></span>
                        </div>
                    </div>
                        
                      

                    <h3 class="text-lg font-bold text-gray-900 dark:text-emerald-50 mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-2">
                      <?= $berita['judul'] ?>
                    </h3>
                    
                    <p class="text-gray-600 dark:text-emerald-100/70 text-sm mb-6 line-clamp-3 leading-relaxed">
                      <?= strip_tags($berita['isi_berita']) ?>
                    </p>
                    
                    <div class="mt-auto">
                        <a href="<?= base_url('berita/detail/' . $berita['judul_seo']) ?>" class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-sm group/btn">
                          <span>Selengkapnya</span>
                          <span translate="no" class="notranslate material-icons text-sm transform transition-transform group-hover/btn:translate-x-1">arrow_forward</span>
                        </a>
                    </div>
                  </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-10 text-center text-gray-500 italic">
                Belum ada berita terbaru.
            </div>
        <?php endif; ?>
      </div>

      <!-- Tombol More -->
      <div class="mt-14">
        <a href="<?= base_url('berita') ?>" 
           class="inline-flex items-center gap-3 px-10 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-full shadow-lg shadow-emerald-900/20 transition-all hover:-translate-y-1">
            <span>Lihat Semua Berita</span>
            <span translate="no" class="notranslate material-icons">arrow_forward</span>
        </a>
      </div>
    </div>

    <!-- SIDEBAR (30%) -->
    <div class="lg:w-[30%] mt-16 lg:mt-0">
        <div class="sticky top-24 space-y-8">
            <!-- Sidebar Header -->
            <div class="border-l-4 border-emerald-500 pl-4 py-1 mb-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white uppercase tracking-tight">Eksklusif</h3>
                <p class="text-xs text-gray-500">Profil & Penghargaan Instansi</p>
            </div>

            <!-- Square Banners Loop -->
            <?php if (!empty($bannersKotak)): ?>
                <?php foreach ($bannersKotak as $bk): ?>
                <div class="group relative overflow-hidden rounded-2xl shadow-lg shadow-emerald-900/5 aspect-square border border-emerald-900/10">
                    <img src="<?= $bk['img_url'] ?>" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105" alt="<?= $bk['judul'] ?>">
                    <a href="<?= !empty($bk['url']) ? $bk['url'] : 'javascript:void(0)' ?>" class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <span class="text-white font-bold text-sm"><?= $bk['judul'] ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default Profile Card if no banners -->
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 p-8 rounded-2xl shadow-xl text-center text-white">
                    <div class="w-24 h-24 mx-auto mb-4 rounded-full border-4 border-white/20 overflow-hidden">
                        <img src="<?= base_url('backend/img/undraw_profile.svg') ?>" class="w-full h-full object-cover">
                    </div>
                    <h4 class="font-bold text-lg">H. Mashuri, S.P., M.E.</h4>
                    <p class="text-xs opacity-80 uppercase tracking-widest mt-1">Bupati Bungo</p>
                </div>
            <?php endif; ?>

            <!-- Info Tambahan / Lokasi -->
            <div class="bg-gray-50 dark:bg-emerald-900/10 p-6 rounded-2xl border border-gray-200/50 dark:border-emerald-900/20">
                <h4 class="font-bold text-emerald-600 mb-4 flex items-center gap-2">
                    <span translate="no" class="notranslate material-icons text-lg">location_on</span>
                    Kontak Kami
                </h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Jl. RM. Thaher No.501, Rimbo Tengah, Kec. Rimbo Tengah, Kabupaten Bungo, Jambi 37211
                </p>
            </div>
        </div>
    </div>
  </div>
</section>


<!-- AGENDA KEGIATAN -->
<section class="relative">
<div class="relative max-w-7xl mx-auto px-6 py-16">
  <!-- Judul -->
  <div class="text-center mb-12 reveal">
    <h2 class="text-3xl font-bold text-emerald-500">
      Agenda Kegiatan
    </h2>
    <p class="mt-2 text-gray-600 dark:text-gray-400">
      Informasi agenda dan kegiatan resmi Bappeda Kabupaten Bungo
    </p>
  </div>

  <!-- Card -->
  <?php if (!empty($agenda)): ?>
  <div
    class="rounded-3xl
           bg-[rgba(15,31,26,0.08)] dark:bg-[#0f1f1a]
           border border-emerald-700/30
           shadow-[0_0_0_1px_rgba(16,185,129,0.05)]
           dark:shadow-lg reveal">

    <div class="p-8 md:p-12">

      <h3 class="text-2xl text-center font-bold text-emerald-600 dark:text-emerald-400 mb-3">
        <?= $agenda['judul'] ?>
      </h3>

      <p class="text-gray-700 dark:text-gray-300 text-center mb-8 drop-shadow-sm">
        <?= $agenda['deskripsi'] ?>
      </p>

      <!-- Info -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="flex items-center gap-4 p-4 rounded-xl
                    bg-[rgba(15,31,26,0.12)] dark:bg-[#0b1713]
                    border border-emerald-900/25">
          <span translate="no" class="notranslate material-icons text-emerald-500">
            event
          </span>
          <div>
            <p class="text-xs uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
              Waktu
            </p>
            <p class="text-gray-900 dark:text-white font-medium">
              <?= $agenda['tanggal_format'] ?>
            </p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 rounded-xl
                    bg-[rgba(15,31,26,0.12)] dark:bg-[#0b1713]
                    border border-emerald-900/25">
          <span translate="no" class="notranslate material-icons text-emerald-500">
            location_on
          </span>
          <div>
            <p class="text-xs uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
              Lokasi
            </p>
            <p class="text-gray-900 dark:text-white font-medium">
              <?= $agenda['lokasi'] ?>
            </p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 rounded-xl
                    bg-[rgba(15,31,26,0.12)] dark:bg-[#0b1713]
                    border border-emerald-900/25">
          <span translate="no" class="notranslate material-icons text-emerald-500">
            schedule
          </span>
          <div>
            <p class="text-xs uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
              Jam / Durasi
            </p>
            <p class="text-gray-900 dark:text-white font-medium">
              <?= !empty($agenda['jam']) ? $agenda['jam'] : 'Belum ditentukan' ?>
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="py-12 text-center text-gray-500 italic flex flex-col items-center gap-3">
    <span translate="no" class="notranslate material-icons text-4xl opacity-20">event_busy</span>
    <p>Belum ada agenda kegiatan yang dijadwalkan.</p>
  </div>
  <?php endif; ?>

  <!-- Tombol More Agenda -->
  <div class="mt-12 text-center">
    <a href="<?= base_url('agenda') ?>" 
       class="inline-flex items-center gap-3 px-10 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-full shadow-lg shadow-emerald-900/20 transition-all hover:-translate-y-1">
        <span>Lihat Semua Agenda</span>
        <span translate="no" class="notranslate material-icons">arrow_forward</span>
    </a>
  </div>
</div>
</section>




</section>

<!-- Enhanced Lightbox Modal -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[1001] flex items-center justify-center bg-[#020403]/95 backdrop-blur-3xl"
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
            
            <!-- Image Area -->
            <div class="flex-grow flex items-center justify-center px-6 md:px-20 py-6 min-h-0 mt-24">
                <div class="relative w-full h-full max-w-6xl flex items-center justify-center overflow-hidden rounded-3xl shadow-[0_40px_100px_-20px_rgba(0,0,0,0.8)] bg-black/20">
                    <img :src="currentImages[currentIndex]" class="max-w-full max-h-full rounded-3xl object-contain transition-all duration-700">
                </div>
            </div>
            
            <!-- Bottom Panel -->
            <div class="w-full bg-gradient-to-t from-black via-black/80 to-transparent backdrop-blur-xl border-t border-white/5 py-6 px-6">
                <div class="max-w-5xl mx-auto flex flex-col items-center">
                    
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

</div> <!-- End of x-data wrapper -->


<script>
function openImage(src) {
  const modal = document.getElementById('imageModal')
  const img = document.getElementById('modalImg')
  img.src = src
  modal.classList.remove('hidden')
  modal.classList.add('flex')
}

function closeImage() {
  const modal = document.getElementById('imageModal')
  modal.classList.add('hidden')
  modal.classList.remove('flex')
}
</script>



<script>
const revealObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('show')
      revealObserver.unobserve(entry.target)
    }
  })
}, { threshold: 0.2 })

document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el))

const items = document.querySelectorAll('.reveal-item')
const itemObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      items.forEach((el, i) => {
        setTimeout(() => el.classList.add('show'), i * 90)
      })
      itemObserver.disconnect()
    }
  })
}, { threshold: 0.2 })

if (items.length) itemObserver.observe(items[0])
</script>

<!-- <script>
    // Banners Panjang Slider
    const bannerLongSwiper = new Swiper('.bannerLongSwiper', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
    });
</script> -->
<?= $this->endSection() ?>
