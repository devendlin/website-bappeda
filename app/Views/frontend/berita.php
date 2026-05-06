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
            <li><span class="text-white"><?= esc($title) ?></span></li>
        </ul>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 animate-fade-in-up reveal">
            <?= esc($title) ?>
        </h1>
    </div>
</section>

  <!-- CONTENT -->
  <div id="wrapper" class="min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-10" x-data="{
      berita: <?= htmlspecialchars(json_encode($initialNews)) ?>,
      hasMore: <?= $hasMore ? 'true' : 'false' ?>,
      loading: false,
      offset: <?= $limit ?>,

      async loadMore() {
          if (this.loading || !this.hasMore) return;
          this.loading = true;
          try {
              const res = await fetch(`<?= base_url('berita/loadMore') ?>?offset=${this.offset}`);
              const data = await res.json();
              this.berita = [...this.berita, ...data.berita];
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
          this.$nextTick(() => {
              window.dispatchEvent(new CustomEvent('content-updated'));
          });

          const observer = new IntersectionObserver((entries) => {
              if (entries[0].isIntersecting && this.hasMore && !this.loading) {
                  this.loadMore();
              }
          }, { rootMargin: '400px' });
          
          if (this.$refs.loadMoreTrigger) {
              observer.observe(this.$refs.loadMoreTrigger);
          }
      }
  }">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 w-full min-w-0">

      <!-- KOLOM KIRI -->
      <div class="lg:col-span-8 col-span-12 w-full min-w-0">

        <div x-show="berita.length === 0" class="py-10">
          <h2 class="text-sm uppercase font-semibold text-gray-700 dark:text-emerald-300">
            Berita tidak ditemukan
          </h2>
        </div>

        <div class="space-y-6">

          <template x-for="(daftar, idx) in berita" :key="daftar.id">
            <article class="group bg-white dark:bg-[#0f1a16] rounded-2xl border border-emerald-900/20 overflow-hidden w-full reveal"
                     :class="'delay-' + ((idx % 3) * 100)">

              <div class="grid grid-cols-1 md:grid-cols-12 gap-4 p-4 lg:p-6 items-start w-full min-w-0">
                
                <!-- GAMBAR -->
                <div class="md:col-span-4 col-span-12 w-full min-w-0">
                  <div class="rounded-xl overflow-hidden aspect-[16/10] sm:aspect-[3/2] bg-emerald-50 dark:bg-emerald-900/20 w-full">
                    <img :src="daftar.gambar"
                         @error="$el.src='<?= base_url('uploads/galeri/default.jpg') ?>'"
                         loading="lazy"
                         alt=""
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                  </div>
                </div>

                <!-- TEKS -->
                <div class="md:col-span-8 col-span-12 w-full min-w-0 space-y-3">
                  
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider rounded" x-text="daftar.kategori"></span>
                  </div>

                  <h3 class="text-base sm:text-lg lg:text-xl font-semibold leading-snug text-gray-900 dark:text-emerald-200 line-clamp-3 lg:line-clamp-2 break-words">
                    <a :href="'<?= base_url('berita/detail/') ?>' + daftar.judul_seo"
                       class="hover:text-emerald-500 transition break-words" x-text="daftar.judul">
                    </a>
                  </h3>

                  <!-- ISI BERITA -->
                  <p class="hidden md:block text-sm text-gray-700 dark:text-emerald-300/70 md:line-clamp-2 break-words" x-text="daftar.isi"></p>

                  <div class="flex items-center justify-between pt-2">
                    <a :href="'<?= base_url('berita/detail/') ?>' + daftar.judul_seo"
                       class="inline-block text-xs uppercase font-semibold text-emerald-700 dark:text-emerald-400 border-b border-emerald-700 hover:text-emerald-500 hover:border-emerald-500 transition">
                      Read more
                    </a>

                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-emerald-400/70">
                      <span x-text="daftar.tanggal"></span>
                      <span class="opacity-50">·</span>
                      <span class="inline-flex items-center gap-1">
                        <span translate="no" class="notranslate material-icons text-sm">visibility</span>
                        <span x-text="daftar.views"></span>
                      </span>
                    </div>
                  </div>

                </div>
              </div>
            </article>
          </template>

        </div>

        <!-- Scroll Trigger & Loading Indicator -->
        <div class="text-center mt-12 min-h-[60px]" x-ref="loadMoreTrigger">
            <div x-show="loading" class="flex flex-col items-center gap-4 animate-fade-in">
                <div class="flex gap-1.5">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce"></div>
                </div>
                <span class="text-emerald-400/60 text-[10px] font-bold uppercase tracking-[0.2em]">Memuat Berita...</span>
            </div>
            
            <div x-show="!hasMore && berita.length > 0" class="py-10">
                <div class="h-px w-20 bg-emerald-900/30 mx-auto mb-6"></div>
                <p class="text-gray-500 text-xs italic">Semua berita telah ditampilkan.</p>
            </div>
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
</div>

<?= $this->endSection() ?>
