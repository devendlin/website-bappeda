<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>404 - Halaman Tidak Ditemukan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { background-color: #f8fafc; color: #1e293b; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .container { text-align: center; padding: 2rem; max-width: 500px; width: 100%; }
        .error-code { font-size: 6rem; font-weight: 900; color: #10b981; margin: 0; line-height: 1; }
        .error-title { font-size: 1.5rem; font-weight: 700; margin-top: 1rem; color: #0f172a; }
        .error-message { color: #64748b; margin-top: 1rem; line-height: 1.6; }
        .btn { display: inline-block; margin-top: 2rem; padding: 0.75rem 1.5rem; background-color: #10b981; color: white; text-decoration: none; border-radius: 9999px; font-weight: 600; transition: background-color 0.2s; }
        .btn:hover { background-color: #059669; }
    </style>
</head>
<body>
    <div class="container">
        <p class="error-code">404</p>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-message">
            <?php if (ENVIRONMENT !== 'development') : ?>
                Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.
            <?php else : ?>
                <?= nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) ?>
            <?php endif ?>
        </p>
        <a href="/" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>
