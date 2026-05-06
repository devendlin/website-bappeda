<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Galeri extends BaseController
{
    public function index()
    {
        $folder = FCPATH . 'uploads/galeri/';
        $semuaGambar = array_diff(scandir($folder), ['.', '..']);

        // Buat array dengan waktu modifikasi untuk sorting
        $gambarDenganWaktu = [];
        foreach ($semuaGambar as $file) {
            $path = $folder . $file;
            if (is_file($path)) {
                $gambarDenganWaktu[$file] = filemtime($path);
            }
        }

        // Urutkan berdasarkan waktu terbaru (DESC)
        arsort($gambarDenganWaktu);

        // Ambil semua isi_berita, thumbnail, foto dokumentasi, dan icon aplikasi dari DB
        $db = \Config\Database::connect();
        $berita = $db->table('berita')->select('isi_berita, gambar')->get()->getResult();
        $dokumenFotos = $db->table('dokumentasi_foto')->select('file_foto')->get()->getResult();
        $aplikasi = $db->table('aplikasi')->select('gambar')->get()->getResult();

        // Gabungkan semua konten jadi satu string pencarian
        $semuaKonten = '';
        foreach ($berita as $row) {
            $semuaKonten .= $row->isi_berita . ' ' . $row->gambar;
        }
        foreach ($dokumenFotos as $row) {
            $semuaKonten .= ' ' . $row->file_foto;
        }
        foreach ($aplikasi as $row) {
            $semuaKonten .= ' ' . $row->gambar;
        }

        // Siapkan data untuk dikirim ke view
        $dataGambar = [];
        foreach ($gambarDenganWaktu as $file => $waktu) {
            if ($file === 'default.jpg') {
                continue;
            }
            $dataGambar[] = [
                'nama' => $file,
                'url' => base_url("uploads/galeri/" . $file),
                'dipakai' => str_contains($semuaKonten, $file),
                'waktu' => date('Y-m-d H:i:s', $waktu), // opsional jika ingin ditampilkan
            ];
        }

        return view('roomloki/galeri/galeri', [
            'gambar' => $dataGambar,
            'title' => 'Galeri'
        ]);
    }

    public function loadMoreGaleri()
    {
        $limit = (int) $this->request->getGet('limit') ?? 10;
        $offset = (int) $this->request->getGet('offset') ?? 0;

        $folder = FCPATH . 'uploads/galeri/';
        $semuaGambar = array_diff(scandir($folder), ['.', '..']);

        $gambarDenganWaktu = [];
        $ekstensiGambar = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        foreach ($semuaGambar as $file) {
            $path = $folder . $file;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (is_file($path) && in_array($ext, $ekstensiGambar)) {
                $gambarDenganWaktu[$file] = filemtime($path);
            }
        }

        arsort($gambarDenganWaktu);

        $gambarPotong = array_slice($gambarDenganWaktu, $offset, $limit, true);

        // Cek gambar terpakai di berita, dokumentasi, dan aplikasi
        $db = \Config\Database::connect();
        $berita = $db->table('berita')->select('isi_berita, gambar')->get()->getResult();
        $dokumenFotos = $db->table('dokumentasi_foto')->select('file_foto')->get()->getResult();
        $aplikasi = $db->table('aplikasi')->select('gambar')->get()->getResult();

        $semuaKonten = '';
        foreach ($berita as $row) {
            $semuaKonten .= $row->isi_berita . ' ' . $row->gambar;
        }
        foreach ($dokumenFotos as $row) {
            $semuaKonten .= ' ' . $row->file_foto;
        }
        foreach ($aplikasi as $row) {
            $semuaKonten .= ' ' . $row->gambar;
        }

        $hasil = [];
        foreach ($gambarPotong as $file => $waktu) {
            if ($file === 'default.jpg') {
                continue;
            }
            $hasil[] = [
                'nama' => $file,
                'url' => base_url("uploads/galeri/" . $file),
                'dipakai' => str_contains($semuaKonten, $file),
                'waktu' => date('Y-m-d H:i:s', $waktu)
            ];
        }

        return $this->response->setJSON($hasil);
    }

    public function upload()
    {
        $file = $this->request->getFile('gambar');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File tidak valid',
                'csrf_token' => csrf_hash() // tetap kirim token baru untuk sinkronisasi
            ]);
        }

        $namaFile = $file->getRandomName();
        $file->move(FCPATH . 'uploads/galeri/', $namaFile);

        // Cek apakah nama file dipakai di isi_berita, thumbnail, dokumentasi, atau aplikasi
        $db = \Config\Database::connect();
        $used = false;

        $konten = $db->table('berita')->select('isi_berita, gambar')->get()->getResult();
        $dokFotos = $db->table('dokumentasi_foto')->select('file_foto')->get()->getResult();
        $apps = $db->table('aplikasi')->select('gambar')->get()->getResult();

        foreach ($konten as $row) {
            if (str_contains($row->isi_berita, $namaFile) || str_contains($row->gambar, $namaFile)) {
                $used = true;
                break;
            }
        }
        if (!$used) {
            foreach ($dokFotos as $row) {
                if ($row->file_foto === $namaFile) {
                    $used = true;
                    break;
                }
            }
        }
        if (!$used) {
            foreach ($apps as $row) {
                if (str_contains($row->gambar, $namaFile)) {
                    $used = true;
                    break;
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'url'     => base_url('uploads/galeri/' . $namaFile),
                'waktu'   => date('Y-m-d H:i:s'),
                'dipakai' => $used,
            ],
            'csrf_token' => csrf_hash() // kirim token baru agar client update
        ]);
    }


    public function hapus_semua_unused()
    {
        $folder = FCPATH . 'uploads/galeri/';
        $semuaGambar = array_diff(scandir($folder), ['.', '..', 'default.jpg']);

        // Ambil semua referensi gambar dari DB
        $db = \Config\Database::connect();
        
        // Dari Berita (isi_berita dan gambar thumbnail)
        $berita = $db->table('berita')->select('isi_berita, gambar')->get()->getResult();
        $semuaKontenStr = '';
        foreach ($berita as $row) {
            $semuaKontenStr .= $row->isi_berita . ' ' . $row->gambar;
        }

        // Dari Dokumentasi (file_foto)
        $dokumenFotos = $db->table('dokumentasi_foto')->select('file_foto')->get()->getResult();
        foreach ($dokumenFotos as $row) {
            $semuaKontenStr .= ' ' . $row->file_foto;
        }

        // Dari Aplikasi (gambar)
        $aplikasi = $db->table('aplikasi')->select('gambar')->get()->getResult();
        foreach ($aplikasi as $row) {
            $semuaKontenStr .= ' ' . $row->gambar;
        }

        $berhasilDihapus = 0;
        foreach ($semuaGambar as $file) {
            // Jika nama file TIDAK ditemukan di string database
            if (!str_contains($semuaKontenStr, $file)) {
                $path = $folder . $file;
                if (is_file($path)) {
                    if (unlink($path)) {
                        $berhasilDihapus++;
                    }
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "Berhasil menghapus $berhasilDihapus foto tidak terpakai.",
            'deleted_count' => $berhasilDihapus,
            'csrf_token' => csrf_hash()
        ]);
    }

}
