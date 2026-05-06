<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BeritaModel;
use App\Models\HalamanModel;
use App\Models\KategoriModel;
class Berita extends BaseController
{
    public function index($page = 1)
    {
        helper('text');

        $beritaModel = new BeritaModel();
        $perPage = 6;

        $total = $beritaModel->countAllResults();

        $berita = $beritaModel
            ->select('berita.*, kategori.nama_kategori, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view')
            ->join('kategori', 'kategori.id_kategori = berita.id_kategori', 'left')
            ->join('berita_views', 'berita_views.id_berita = berita.id_berita', 'left')
            ->groupBy('berita.id_berita')
            ->orderBy('tanggal', 'DESC')
            ->findAll($perPage, 0);

        $initialNews = [];
        foreach ($berita as $b) {
            $initialNews[] = $this->formatBerita($b);
        }

        return view('frontend/berita', [
            'title' => 'Berita Terbaru',
            'initialNews' => $initialNews,
            'hasMore' => $total > $perPage,
            'limit' => $perPage
        ]);
    }

    public function loadMore()
    {
        helper('text');
        $offset = (int) $this->request->getGet('offset');
        $id_kategori = $this->request->getGet('id_kategori');
        $limit = 6;

        $beritaModel = new BeritaModel();
        
        $builder = $beritaModel
            ->select('berita.*, kategori.nama_kategori, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view')
            ->join('kategori', 'kategori.id_kategori = berita.id_kategori', 'left')
            ->join('berita_views', 'berita_views.id_berita = berita.id_berita', 'left')
            ->groupBy('berita.id_berita');

        if ($id_kategori) {
            $builder->where('berita.id_kategori', $id_kategori);
        }

        $total = $builder->countAllResults(false);

        $berita = $builder
            ->orderBy('tanggal', 'DESC')
            ->findAll($limit, $offset);

        $mapped = [];
        foreach ($berita as $b) {
            $mapped[] = $this->formatBerita($b);
        }

        return $this->response->setJSON([
            'berita' => $mapped,
            'hasMore' => $total > ($offset + $limit)
        ]);
    }

    private function formatBerita($b)
    {
        $img = $b['gambar'];
        if (empty($img)) {
            $img = base_url('uploads/galeri/default.jpg');
        } elseif (0 !== strpos($img, 'http')) {
            $img = base_url('uploads/galeri/' . $img);
        }

        return [
            'id' => $b['id_berita'],
            'judul' => $b['judul'],
            'judul_seo' => $b['judul_seo'],
            'gambar' => $img,
            'tanggal' => timeAgoOrDate($b['tanggal']),
            'isi' => character_limiter(strip_tags($b['isi_berita']), 120),
            'views' => $b['total_view'] ?? 0,
            'kategori' => $b['nama_kategori'] ?? 'Berita'
        ];
    }

    public function halaman($slug)
    {
        $halamanModel = new HalamanModel();

        $data = $halamanModel->where('judul_seo', $slug)->first();

        if (!$data) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Halaman tidak ditemukan");
        }

        return view('frontend/halaman_detail', [
            'berita' => $data,
            'title' => $data['judul']
        ]);
    }

    public function kategori_all()
    {
        $db = \Config\Database::connect();

        // Ambil nama kategori + jumlah berita + 5 berita terbaru per kategori
        $kategoriQuery = $db->table('kategori')->get()->getResultArray();

        $beritaTerkelompok = [];

        foreach ($kategoriQuery as $kategori) {
            $idKategori = $kategori['id_kategori'];
            $namaKategori = $kategori['nama_kategori'];

            // Ambil 5 berita per kategori dengan view count
            $berita = $db->table('berita')
                ->select('berita.*, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view')
                ->join('berita_views', 'berita_views.id_berita = berita.id_berita', 'left')
                ->where('berita.id_kategori', $idKategori)
                ->groupBy('berita.id_berita')
                ->orderBy('tanggal', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            // Hitung total berita di kategori ini
            $total = $db->table('berita')
                ->where('id_kategori', $idKategori)
                ->countAllResults();

            // Simpan ke array
            $beritaTerkelompok[] = [
                'nama_kategori' => $namaKategori,
                'kategori_seo' => $kategori['kategori_seo'],
                'gambar' => $berita,
                'total' => $total,
                'berita' => $berita
            ];
        }

        return view('frontend/kategori_all', [
            'kategoriBerita' => $beritaTerkelompok,
            'title' => 'Semua Kategori'
        ]);
    }


    public function kategori($slug, $page = 1)
    {
        helper('text');
        $kategoriModel = new KategoriModel();
        $beritaModel = new BeritaModel();

        $kategori = $kategoriModel->where('kategori_seo', $slug)->first();
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Kategori tidak ditemukan.");
        }

        $perPage = 6;
        $total = $beritaModel->where('id_kategori', $kategori['id_kategori'])->countAllResults();

        $berita = $beritaModel
            ->select('berita.*, kategori.nama_kategori, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view')
            ->join('kategori', 'kategori.id_kategori = berita.id_kategori', 'left')
            ->join('berita_views', 'berita_views.id_berita = berita.id_berita', 'left')
            ->where('berita.id_kategori', $kategori['id_kategori'])
            ->groupBy('berita.id_berita')
            ->orderBy('tanggal', 'DESC')
            ->findAll($perPage, 0);

        $initialNews = [];
        foreach ($berita as $b) {
            $initialNews[] = $this->formatBerita($b);
        }

        return view('frontend/kategori', [
            'title' => 'Kategori: ' . $kategori['nama_kategori'],
            'kategori' => $kategori,
            'slug' => $slug,
            'initialNews' => $initialNews,
            'hasMore' => $total > $perPage,
            'limit' => $perPage,
            'id_kategori' => $kategori['id_kategori']
        ]);
    }




    public function detail($slug)
    {
        helper(['text', 'timeAgo']);
        $beritaModel = new BeritaModel();
        
        $data = $beritaModel
            ->select('berita.*, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view')
            ->join('berita_views', 'berita.id_berita = berita_views.id_berita', 'left')
            ->where('judul_seo', $slug)
            ->groupBy('berita.id_berita')
            ->first();

        if (!$data) {
            return view('errors/html/error_404', [
                'title' => 'Halaman tidak ditemukan.',
                'message' => 'Halaman tidak ditemukan.'
            ]);
        }

        // Dapatkan previous dan next
        $prev = $beritaModel
            ->where('id_berita <', $data['id_berita'])
            ->orderBy('id_berita', 'DESC')
            ->first();

        $next = $beritaModel
            ->where('id_berita >', $data['id_berita'])
            ->orderBy('id_berita', 'ASC')
            ->first();


        $tags = explode(',', $data['tag'] ?? '');
        $idBerita = $data['id_berita'];
        $idKategori = $data['id_kategori'];

        $builder = $beritaModel->builder();
        $builder->where('id_berita !=', $idBerita);

        // Bangun kondisi OR
        $builder->groupStart();

        if (!empty($tags[0])) {
            foreach ($tags as $tag) {
                $builder->orLike('tag', trim($tag));
            }
        }

        // Tambahkan kategori sebagai OR juga
        $builder->orWhere('id_kategori', $idKategori);

        $builder->groupEnd();

        $builder->orderBy('tanggal', 'DESC')->limit(5);
        $relatedBerita = $builder->get()->getResultArray();

        
        $this->trackBeritaView($data['id_berita']);

        $this->data['meta'] = array_merge($this->data['meta'], [
            'description' => character_limiter(strip_tags($data['isi_berita']), 150),
            'keywords' => $data['tag'] ?? $this->identitas['keywords'],
            'image' => $data['gambar'],
            'type' => 'article',
            'prefix_title' => false,
        ]);

        // Injeksi ulang ke view global
        service('renderer')->setData(['meta' => $this->data['meta']], 'raw');
        return view('frontend/berita_detail', [
            'prev'   => $prev,
            'next'   => $next,
            'berita' => $data,
            'relatedBerita' => $relatedBerita,
            'title' => $data['judul'] ?? 'Berita Tidak Ditemukan'
        ]);
    }
}
