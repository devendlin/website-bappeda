<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Siberkreasimu - Login">
    <title>Siberkreasimu - Login</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/tailwind.css') ?>" rel="stylesheet">
    
    <script>
        const ThemeManager = {
            getStoredTheme() {
                try { return localStorage.getItem('theme') || 'system'; } catch (e) { return 'system'; }
            },
            apply() {
                const stored = this.getStoredTheme();
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = stored === 'dark' || (stored === 'system' && systemDark);
                document.documentElement.classList.toggle('dark', isDark);
            }
        };
        ThemeManager.apply();
        const mq = window.matchMedia('(prefers-color-scheme: dark)');
        const listener = () => { if (ThemeManager.getStoredTheme() === 'system') ThemeManager.apply(); };
        try { mq.addEventListener('change', listener); } catch(e) { mq.addListener(listener); }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #050807;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 106, 68, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(16, 106, 68, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 106, 68, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(16, 106, 68, 0.1) 0px, transparent 50%);
        }
        
        .premium-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
        }

        .premium-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: 0.03;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        .dark body {
            background-color: #020403;
        }

        [x-cloak] { display: none !important; }
        
        /* Smooth transitions for interactive elements */
        .btn-premium {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>

<body class="text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center p-4 sm:p-6 antialiased overflow-hidden">
    <div class="premium-bg"></div>

    <div class="w-full max-w-sm relative z-10">
        <?= $this->renderSection('content') ?>
    </div>

</body>

</html>