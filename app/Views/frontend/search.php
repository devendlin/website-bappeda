<?= $this->extend('frontend/layout/main_layout') ?>

<?= $this->section('content') ?>

<section class="py-12 px-6 min-h-[60vh]">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-emerald-900 dark:text-emerald-50 mb-2">
            Hasil Pencarian
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            Menampilkan hasil untuk kata kunci: <span class="font-bold text-emerald-600">"<?= esc($keyword) ?>"</span>
        </p>

        <?php if (empty($results)): ?>
            <div class="p-10 text-center bg-gray-50 dark:bg-[#0d1512] rounded-xl border border-gray-200 dark:border-emerald-900/30">
                <span class="material-icons text-6xl text-gray-300 mb-4">search_off</span>
                <p class="text-gray-500">Tidak ditemukan data yang cocok dengan kata kunci tersebut.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($results as $res): ?>
                    <a href="<?= $res['link'] ?>" class="block group">
                        <div class="bg-white dark:bg-[#0d1512] p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 dark:border-emerald-900/20 flex gap-4">
                            
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <span class="material-icons"><?= $res['icon'] ?></span>
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400">
                                        <?= esc($res['type']) ?>
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        <?= esc($res['date']) ?>
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 group-hover:text-emerald-600 transition-colors">
                                    <?= esc($res['title']) ?>
                                </h3>
                                
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 line-clamp-2">
                                    <?= esc($res['desc']) ?>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</section>

<?= $this->endSection() ?>
