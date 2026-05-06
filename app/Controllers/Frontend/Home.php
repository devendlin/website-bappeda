<?php

namespace App\Controllers\Frontend;
use App\Controllers\BaseController;

use App\Models\BeritaModel;
use App\Models\KategoriModel;
class Home extends BaseController
{
    public function index(): string
    {
        helper('text');
        $beritaModel = new BeritaModel();
        $kategoriModel = new KategoriModel();

        $slide_berita = $beritaModel
            ->select('berita.*, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view, kategori.nama_kategori, kategori.kategori_seo')
            ->join('berita_views', 'berita_views.id_berita = berita.id_berita', 'left')
            ->join('kategori', 'kategori.id_kategori = berita.id_kategori', 'left')
            ->groupBy('berita.id_berita') // PENTING: agar SUM tidak bikin hasil jadi 1 baris
            ->orderBy('berita.id_berita', 'DESC')
            ->limit(3)
            ->find();

        $berita_terbaru = $beritaModel
            ->select('berita.*, kategori.nama_kategori, kategori.kategori_seo, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view')
            ->join('kategori', 'kategori.id_kategori = berita.id_kategori', 'left')
            ->join('berita_views', 'berita_views.id_berita = berita.id_berita', 'left')
            ->groupBy('berita.id_berita')
            ->orderBy('id_berita', 'DESC')
            ->limit(12)
            ->find();

        $berita_tranding = $beritaModel
            ->select('berita.*, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view, kategori.nama_kategori')
            ->join('berita_views', 'berita_views.id_berita = berita.id_berita', 'left')
            ->join('kategori', 'kategori.id_kategori = berita.id_kategori', 'left')
            ->groupBy('berita.id_berita')
            ->orderBy('total_view', 'DESC')
            ->limit(8)
            ->findAll();



        // Dapatkan 3 kategori dengan berita terbanyak
        $kategoriTerbanyak = $kategoriModel->select('kategori.id_kategori, kategori.nama_kategori, kategori.kategori_seo, COUNT(berita.id_berita) AS total_berita')
            ->join('berita', 'berita.id_kategori = kategori.id_kategori', 'left')
            ->groupBy('kategori.id_kategori')
            ->orderBy('total_berita', 'DESC')
            ->limit(3)
            ->findAll();

        // Ambil berita dari tiap kategori
        $beritaPerKategori = [];
        foreach ($kategoriTerbanyak as $kategori) {
            $beritaPerKategori[$kategori['id_kategori']] = $beritaModel
                ->where('id_kategori', $kategori['id_kategori'])
                ->orderBy('id_berita', 'DESC')
                ->limit(3)
                ->findAll();
        }

        // Ambil Banner (Kotak & Panjang)
        $db = \Config\Database::connect();
        $allBanners = $db->table('banner')->orderBy('tgl_posting', 'DESC')->get()->getResultArray();
        
        $bannersKotak = [];
        $bannersPanjang = [];
        
        foreach ($allBanners as $b) {
            $b['img_url'] = base_url('uploads/banner/' . $b['gambar']);
            if ($b['tipe'] === 'kotak') {
                $bannersKotak[] = $b;
            } else {
                $bannersPanjang[] = $b;
            }
        }

        // Ambil 3 Dokumentasi Kegiatan terbaru untuk stack di hero
        $dokModel = new \App\Models\DokumentasiModel();
        $dokFotoModel = new \App\Models\DokumentasiFotoModel();
        
        $latestDokumentasi = $dokModel->orderBy('tanggal', 'DESC')->limit(3)->findAll();
        $kegiatanStack = [];

        foreach ($latestDokumentasi as $dok) {
            $fotos = $dokFotoModel->where('id_dokumentasi', $dok['id_dokumentasi'])->findAll();
            $photoUrls = [];
            foreach ($fotos as $f) {
                $photoUrls[] = base_url('uploads/galeri/' . $f['file_foto']);
            }
            
            $kegiatanStack[] = [
                'judul' => $dok['judul'],
                'tanggal' => date('d M Y', strtotime($dok['tanggal'])),
                'deskripsi' => $dok['deskripsi'],
                'foto' => $photoUrls
            ];
        }

        // Ambil PPID Kategori untuk slider hero
        $ppidKategoriModel = new \App\Models\PpidKategoriModel();
        $ppid_kategori = $ppidKategoriModel->findAll();

        // Ambil Daftar Aplikasi
        $aplikasiModel = new \App\Models\AplikasiModel();
        $aplikasi = $aplikasiModel->getAll();

        // Ambil Agenda terbaru
        $agendaModel = new \App\Models\AgendaModel();
        $agenda = $agendaModel->where('is_active', 1)->orderBy('tgl_pelaksanaan', 'DESC')->first();
        if ($agenda) {
            $agenda['tanggal_format'] = !empty($agenda['tgl_pelaksanaan']) ? date('d F Y', strtotime($agenda['tgl_pelaksanaan'])) : 'Belum ditentukan';
        }

        // Kirim ke view
        $data = [
            'title'             => 'Beranda',
            'slide_berita'      => $slide_berita,
            'berita_terbaru'    => $berita_terbaru,
            'berita_tranding'   => $berita_tranding,
            'kategoriTerbanyak' => $kategoriTerbanyak,
            'beritaPerKategori' => $beritaPerKategori,
            'kegiatanStack'     => $kegiatanStack,
            'bannersKotak'      => $bannersKotak,
            'bannersPanjang'    => $bannersPanjang,
            'agenda'            => $agenda,
            'ppid_kategori'     => $ppid_kategori,
            'aplikasi'          => $aplikasi,
        ];
        return view('frontend/home', $data);
    }
}
