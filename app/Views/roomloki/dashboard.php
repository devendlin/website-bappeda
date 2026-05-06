<!-- app/Views/admin/berita.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <section class="col-lg-6 connectedSortable">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Application Buttons</h6>
            </div>
            <div class="card-body text-center">
                <?php $role = session()->get('role'); ?>

                <?php if ($role == 'superadmin'): ?>
                    <a href="<?=base_url()?>roomloki/identitas_web" class="btn btn-app">
                        <div class="widget-stats-icon widget-list-item-green"><i class="fa fa-th"></i></div> Identitas
                    </a>
                    <a href="<?=base_url()?>roomloki/menu" class="btn btn-app">
                        <div class="widget-stats-icon widget-list-item-blue"><i class="fas fa-th-large"></i></div> Menu
                    </a>
                    <a href="<?=base_url()?>roomloki/users" class="btn btn-app">
                        <div class="widget-stats-icon widget-list-item-yellow"><i class="fas fa-users"></i></div> Users
                    </a>
                    <a href="<?=base_url()?>roomloki/halaman" class="btn btn-app">
                        <div class="widget-stats-icon widget-list-item-red"><i class="fas fa-file-alt"></i></div> Halaman
                    </a>
                <?php endif; ?>

                <!-- Yang ini bisa ditampilkan ke semua role -->
                <a href="<?=base_url()?>roomloki/berita" class="btn btn-app">
                    <div class="widget-stats-icon widget-list-item-yellow"><i class="fas fa-file-invoice"></i></div> Berita
                </a>
                <a href="<?=base_url()?>roomloki/kategori" class="btn btn-app">
                    <div class="widget-stats-icon widget-list-item-purple"><i class="fas fa-bars"></i></div> Kategori
                </a>
                <a href="<?=base_url()?>roomloki/tags" class="btn btn-app">
                    <div class="widget-stats-icon widget-list-item-green"><i class="fas fa-tag"></i></div> Tag Berita
                </a>
                <a href="<?=base_url()?>roomloki/galeri" class="btn btn-app">
                    <div class="widget-stats-icon widget-list-item-red"><i class="fas fa-camera"></i></div> Gallery
                </a>
                <a href="<?=base_url()?>roomloki/banner" class="btn btn-app">
                    <div class="widget-stats-icon widget-list-item-yellow"><i class="fas fa-window-maximize"></i></div> Banner
                </a>
                <a href="<?= base_url('roomloki/logadmin') ?>" class="btn btn-app">
                    <div class="widget-stats-icon widget-list-item-purple"><i class="fas fa-list"></i></div> Activity Log
                </a>
                <a href="<?= base_url('roomloki/profil') ?>" class="btn btn-app">
                    <div class="widget-stats-icon widget-list-item-blue"><i class="fas fa-user"></i></div> Profil
                </a>
                <a href="<?= base_url('roomloki/users') ?>" class="btn btn-app">
                    <div class="widget-stats-icon widget-list-item-green"><i class="fas fa-users"></i></div> Manajemen Users
                </a>
            </div>
        </div>

    </section>
    <section class="col-lg-6 connectedSortable">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Grafik Pengunjung (7 Hari Terakhir)</h6>
                <span class="badge badge-primary badge-style-light"> <?=$total_pengunjung?> pengunjung</span>
            </div>
            <div class="card-body">
                <label for="chartType">Pilih Jenis Grafik:</label>
                <select id="chartType" class="form-select w-25 mb-3">
                    <option value="bar">Bar</option>
                    <option value="line">Line</option>
                </select>

                <canvas id="grafikPengunjung"></canvas>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('content_js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?= json_encode($labels) ?>;
    const dataJumlah = <?= json_encode($jumlah) ?>;
    const ctx = document.getElementById('grafikPengunjung').getContext('2d');

    let chart;

    function renderChart(type) {
        if (chart) chart.destroy(); // Hapus grafik lama jika ada

        chart = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pengunjung',
                    data: dataJumlah,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(100, 100, 255, 0.6)'
                    ],
                    borderColor: '#36A2EB',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              layout: {
                padding: {
                  bottom: 30
                }
              },
              plugins: {
                legend: {
                  labels: {
                    font: { size: 12 }
                  }
                },
                tooltip: {
                  bodyFont: { size: 11 },
                  titleFont: { size: 12 }
                }
              },
              scales: type === 'pie' ? {} : {
                y: {
                  beginAtZero: true,
                  ticks: {
                    stepSize: 10,
                    font: { size: 11 }
                  }
                },
                x: {
                  ticks: {
                    font: { size: 11 },
                    maxRotation: 45,
                    minRotation: 30,
                    autoSkip: false
                  }
                }
              }
            }

        });
    }

    // Render awal dengan jenis "line"
    renderChart('bar');

    // Event ganti chart type
    document.getElementById('chartType').addEventListener('change', function () {
        const selectedType = this.value;
        renderChart(selectedType);
    });
</script>

<?= $this->endSection() ?>