<!-- SWIPER CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

<section class="relative min-h-[455px] overflow-hidden">

    <!-- SWIPER -->
    <div class="swiper heroSwiper h-full relative z-0">
        <div class="swiper-wrapper">

            <?php foreach ($runTextBerita as $b): ?>
            <div class="swiper-slide relative h-full">

                <!-- BG -->
                <img src="<?= esc($b['gambar'] ?? base_url('uploads/galeri/default.jpg')) ?>"
                     class="absolute inset-0 w-full h-full object-cover z-0">

                <!-- OVERLAY -->
                <div class="absolute inset-0 bg-gradient-to-r from-[#001f1a]/90 via-[#001b17]/75 to-[#001510]/60 z-10"></div>

                <!-- KONTEN KIRI -->
                <div class="relative z-20 min-h-[455px] max-w-7xl mx-auto h-full px-6 py-20 flex items-center">
                    <div class="text-white max-w-2xl space-y-4">

                        <p class="text-xs uppercase tracking-widest text-emerald-400 mb-3
                                opacity-0 -translate-x-12 transition-all duration-500 ease-out" data-animate>
                            <?= esc($b['kategori'] ?? 'Berita') ?>
                        </p>

                        <h1 class="text-3xl md:text-4xl font-semibold leading-tight mb-5
                                opacity-0 -translate-x-12 transition-all duration-500 ease-out" data-animate>
                            <?= esc($b['judul']) ?>
                        </h1>

                        <?php if (!empty($b['tanggal'])): ?>
                        <div class="text-sm text-gray-300 mb-7
                                    opacity-0 -translate-x-12 transition-all duration-500 ease-out" data-animate>
                            <?= timeAgoOrDate($b['tanggal']) ?>
                        </div>
                        <?php endif; ?>

                        <a href="<?= base_url('berita/detail/'.$b['judul_seo']) ?>"
                            class="group relative inline-flex items-center gap-3 px-7 py-3 rounded-full
                                    border border-white/40 text-white overflow-hidden
                                    opacity-0 -translate-x-12 transition-all duration-500 ease-out"
                            data-animate>

                                <!-- EFFECT BG -->
                                <span class="absolute inset-0 bg-white scale-x-0 origin-left
                                            transition-transform duration-300 ease-out
                                            group-hover:scale-x-100"></span>

                                <!-- TEXT -->
                                <span class="relative z-10 font-medium transition-colors duration-300
                                            group-hover:text-black">
                                    Baca Selengkapnya
                                </span>

                                <!-- ARROW -->
                                <svg class="relative z-10 w-4 h-4 transform transition-all duration-300
                                            group-hover:translate-x-1 group-hover:text-black"
                                    fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>

                            </a>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- NAV KANAN -->
    <div class="absolute inset-0 z-30 hidden lg:flex items-center pointer-events-none">
        <div class="max-w-7xl mx-auto w-full px-6 flex justify-end">

            <div class="rounded-3xl p-6 bg-white/10 backdrop-blur-xl
                        border border-white/20 text-white w-[400px]
                        pointer-events-auto">

                <h3 class="text-sm uppercase tracking-widest mb-5">
                    Berita Terkait
                </h3>

                <div class="space-y-3">
                    <?php foreach ($runTextBerita as $idx => $nav): ?>
                    <div class="hero-nav p-2 rounded-xl cursor-pointer opacity-60 transition"
                         data-index="<?= $idx ?>">
                        <div class="flex gap-4">
                            <img src="<?= esc($nav['gambar'] ?? base_url('uploads/galeri/default.jpg')) ?>"
                                 class="w-16 h-16 rounded-lg object-cover">
                            <div>
                                <p class="text-xs uppercase text-emerald-300 mb-1"></p>
                                <h4 class="text-sm line-clamp-3 leading-snug">
                                    <?= esc($nav['judul']) ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>

</section>

<!-- SWIPER JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
const navItems = document.querySelectorAll('.hero-nav');

// Fungsi sinkron nav kanan
function syncNav(swiper){
    navItems.forEach(el =>
        el.classList.remove('opacity-100','bg-white/20','ring-1','ring-emerald-400')
    );
    const active = navItems[swiper.realIndex];
    if(active){
        active.classList.add('opacity-100','bg-white/20','ring-1','ring-emerald-400');
    }
}

// Helper untuk delay
function wait(ms){
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Fungsi animasi teks slide (fade-out dulu, baru fade-in)
async function animateSlide(swiper){
    // 1️⃣ Fade-out semua teks
    swiper.slides.forEach(slide => {
        slide.querySelectorAll('[data-animate]').forEach(el => {
            el.classList.remove('opacity-100','translate-x-0');
            el.classList.add('opacity-0','-translate-x-12');
        });
    });

    // tunggu fade-out selesai (500ms sesuai duration)
    await wait(500);

    // 2️⃣ Fade-in teks slide aktif
    const activeSlide = swiper.slides[swiper.activeIndex];
    if(activeSlide){
        const elems = activeSlide.querySelectorAll('[data-animate]');
        elems.forEach((el, idx) => {
            setTimeout(() => {
                el.classList.remove('opacity-0','-translate-x-12');
                el.classList.add('opacity-100','translate-x-0');
            }, idx * 150); // stagger
        });
    }
}

// Inisialisasi Swiper
const heroSwiper = new Swiper('.heroSwiper', {
    loop: true,
    effect: 'fade',
    speed: 900,
    autoplay: { delay: 5000 },
    on: {
        init: function(swiper){
            syncNav(swiper);
            animateSlide(swiper);
        },
        slideChange: function(swiper){
            syncNav(swiper);
            animateSlide(swiper);
        }
    }
});

// Klik nav kanan
navItems.forEach(item => {
    item.addEventListener('click', () => {
        heroSwiper.slideToLoop(+item.dataset.index);
    });
});
</script>

<!-- Animasi CSS -->
<style>
[data-animate] {
    opacity: 0;
    transform: translateX(-50px);
    transition: all 0.5s ease-out;
}
[data-animate].opacity-100 {
    opacity: 1;
    transform: translateX(0);
}
</style>
