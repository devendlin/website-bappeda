<?= $this->extend('roomloki/layout/login_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white/80 dark:bg-emerald-950/20 backdrop-blur-2xl shadow-2xl rounded-[2.5rem] overflow-hidden border border-white/20 dark:border-emerald-500/10 transition-all duration-500">
    <div class="p-8 sm:p-10">
        <!-- Brand Header -->
        <div class="flex flex-col items-center mb-10 text-center">
            <div class="relative mb-6">
                <div class="absolute -inset-4 bg-emerald-500/20 blur-2xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <img src="<?= base_url('uploads/favicon/logo.png') ?>" alt="Logo" class="w-14 h-14 object-contain relative transition-transform hover:scale-110 duration-500">
            </div>
            <h1 class="text-xl font-extrabold tracking-tight text-emerald-950 dark:text-emerald-50 bg-gradient-to-br from-emerald-600 to-emerald-800 bg-clip-text text-transparent">
                Siberkreasimu
            </h1>
            <p class="text-gray-500/80 dark:text-emerald-500/60 text-[13px] font-medium mt-1.5 uppercase tracking-[0.2em]">Dashboard Access</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-8 flex items-center p-4 bg-red-500/10 backdrop-blur-md text-red-600 dark:text-red-400 rounded-2xl border border-red-500/20" role="alert">
                <span class="material-icons mr-3 text-lg leading-tight">error_outline</span>
                <span class="text-[13px] font-semibold"><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <form class="space-y-6" method="post" action="<?= base_url('roomloki/auth') ?>" autocomplete="on">
            <?= csrf_field() ?>
            
            <div class="space-y-1.5">
                <label for="username" class="block text-[13px] font-bold text-emerald-900/60 dark:text-emerald-100/40 ml-1 uppercase tracking-wider">Username</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="material-icons text-emerald-900/40 dark:text-emerald-100/20 group-focus-within:text-emerald-600 transition-colors text-lg">person</span>
                    </div>
                    <input type="text" 
                        id="username" 
                        name="username" 
                        required
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 dark:bg-emerald-950/30 border border-gray-200/50 dark:border-emerald-500/10 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all outline-none text-gray-900 dark:text-white placeholder-gray-400/50 text-sm font-medium"
                        placeholder="Username"
                        autocomplete="username">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-[13px] font-bold text-emerald-900/60 dark:text-emerald-100/40 ml-1 uppercase tracking-wider">Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="material-icons text-emerald-900/40 dark:text-emerald-100/20 group-focus-within:text-emerald-600 transition-colors text-lg">lock</span>
                    </div>
                    <input type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 dark:bg-emerald-950/30 border border-gray-200/50 dark:border-emerald-500/10 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all outline-none text-gray-900 dark:text-white placeholder-gray-400/50 text-sm font-medium"
                        placeholder="••••••••"
                        autocomplete="current-password">
                </div>
            </div>

            <div class="flex items-center justify-between py-1">
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" class="peer sr-only">
                        <div class="w-5 h-5 bg-gray-100/50 dark:bg-emerald-950/30 border-2 border-gray-300/50 dark:border-emerald-900/30 rounded-lg peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                        <span class="material-icons absolute inset-0 text-white text-sm hidden peer-checked:flex items-center justify-center">check</span>
                    </div>
                    <span class="text-[13px] font-semibold text-gray-500 dark:text-emerald-500/60 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full flex items-center justify-center px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white text-sm font-bold rounded-2xl shadow-xl shadow-emerald-900/20 hover:shadow-emerald-500/30 transition-all duration-300">
                <span class="tracking-wide">Masuk Sekarang</span>
                <span class="material-icons ml-2 text-base">east</span>
            </button>
        </form>
    </div>
    
    <!-- Decorative Footer -->
    <div class="px-8 py-6 bg-gray-50/30 dark:bg-emerald-950/10 border-t border-white/10 dark:border-emerald-500/10 text-center">
        <p class="text-[10px] text-gray-400 dark:text-emerald-500/40 leading-relaxed uppercase tracking-[0.3em] font-bold">
            &copy; <?= date('Y') ?> Bappeda Kabupaten Bungo
        </p>
    </div>
</div>
<?= $this->endSection() ?>