<h3 class="text-sm uppercase tracking-widest text-emerald-500 mb-4 text-center">
        Popular Now
    </h3>

    <ul class="space-y-4 text-sm">
        <?php foreach ($berita_tranding as $i => $b): ?>
        <li class="flex items-center gap-3">
            <span class="text-emerald-500 font-semibold min-w-[18px]">
                <?= $i + 1 ?>
            </span>

            <a href="<?= base_url('berita/detail/'.$b['judul_seo']) ?>"
               class="w-16 h-12 rounded overflow-hidden flex-shrink-0">
                <img src="<?= esc($b['gambar'] ?? base_url('uploads/galeri/default.jpg')) ?>"
                     class="w-full h-full object-cover"
                     alt="<?= esc($b['judul']) ?>">
            </a>

            <div class="flex-1">
                <a href="<?= base_url('berita/detail/'.$b['judul_seo']) ?>"
                   class="line-clamp-2 block leading-snug hover:text-emerald-500">
                    <?= esc($b['judul']) ?>
                </a>

                <div class="mt-1 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <?php if (!empty($b['tanggal'])): ?>
                        <span><?= timeAgoOrDate($b['tanggal']) ?></span>
                    <?php endif; ?>
                    <span class="flex items-center gap-1">
                        <span  translate="no" class="notranslate material-icons text-[14px]">visibility</span>
                        <?= esc($b['total_view'] ?? 0) ?>
                    </span>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>