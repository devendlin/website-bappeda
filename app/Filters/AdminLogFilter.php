<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminLogFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $path = uri_string();
            $method = strtoupper($request->getMethod());

            // 1. Filter: Hanya area admin 'roomloki'
            if (stripos($path, 'roomloki') === false) return;

            // 2. Exclude: Aktivitas background saja
            $excluded = ['ajaxList', 'loadMore', 'image_list', 'logadmin', 'auth'];
            foreach ($excluded as $key) {
                if (stripos($path, $key) !== false) return;
            }

            // Identitas Admin
            $session = \Config\Services::session();
            $id_user = $session->get('id_user') ?? 0;
            $username = $session->get('username') ?? 'Admin';

            // Abaikan GET jika belum login
            if ($method === 'GET' && !$id_user) return;

            // 3. Deteksi MODUL
            $module = "System";
            if (stripos($path, 'berita') !== false) $module = "Berita";
            elseif (stripos($path, 'agenda') !== false) $module = "Agenda";
            elseif (stripos($path, 'galeri') !== false || stripos($path, 'hapus_gambar') !== false) $module = "Galeri";
            elseif (stripos($path, 'ppid') !== false) $module = "PPID";
            elseif (stripos($path, 'dokumentasi') !== false) $module = "Dokumentasi";
            elseif (stripos($path, 'users') !== false) $module = "User";
            elseif (stripos($path, 'aplikasi') !== false) $module = "Aplikasi";
            elseif (stripos($path, 'banner') !== false) $module = "Banner";

            $aksi = "";
            $data_payload = null;

            if ($method === 'GET') {
                if (preg_match('/(tambah|add)/i', $path)) $aksi = "Membuka Form Tambah $module";
                elseif (preg_match('/edit/i', $path)) $aksi = "Membuka Form Edit $module";
                else $aksi = "Melihat Daftar $module";

                // Throttling GET
                $db = \Config\Database::connect();
                $exists = $db->table('log_admin')->where(['id_user' => $id_user, 'url' => $path, 'tanggal' => date('Y-m-d'), 'method' => 'GET'])->get()->getRow();
                if ($exists) {
                    $db->table('log_admin')->where('id_log', $exists->id_log)->increment('jumlah_akses', 1);
                    return;
                }
            } else {
                // Tangkap Payload
                $postData = $request->getPost();
                if (empty($postData)) $postData = $request->getRawInput();
                if (empty($postData)) $postData = $request->getJSON(true) ?? [];

                // --- CAPTURE FILE INFO (Sangat Penting untuk Upload Galeri) ---
                $files = $request->getFiles();
                if (!empty($files)) {
                    foreach ($files as $key => $file) {
                        if (is_array($file)) {
                            foreach ($file as $f) {
                                if ($f->isValid()) $postData['uploaded_file'][] = $f->getName();
                            }
                        } else {
                            if ($file->isValid()) $postData['uploaded_file'] = $file->getName();
                        }
                    }
                }
                
                // ACTIONS: Identifikasi detail aksi (Tambah vs Edit)
                if (stripos($path, 'upload') !== false) {
                    $aksi = "Upload File ke $module";
                } elseif (stripos($path, 'hapus_semua_unused') !== false) {
                    $aksi = "Pembersihan Galeri ($module)";
                    $postData = ['keterangan' => 'Membersihkan file sampah yang tidak terpakai di folder uploads'];
                } elseif (preg_match('/(hapus|delete)/i', $path) || $method === 'DELETE') {
                    $aksi = "Menghapus $module";
                } elseif (preg_match('/(update|edit|ubah)/i', $path)) {
                    // Prioritas URL keyword: jika ada kata update/edit di link, pasti Mengubah
                    $aksi = "Mengubah $module";
                } else {
                    // Logika Cerdas: Cek apakah ini Edit atau Tambah berdasarkan keberadaan ID di data (untuk URL generic seperti 'simpan')
                    $isEdit = false;
                    foreach ($postData as $key => $val) {
                        if ((strpos($key, 'id_') === 0 || $key === 'id' || stripos($key, 'id') !== false) && !empty($val) && is_numeric($val)) {
                            $isEdit = true;
                            break;
                        }
                    }

                    if ($isEdit) {
                        $aksi = "Mengubah $module";
                    } else {
                        $aksi = "Menambah $module";
                    }
                }

                // Fallback ID dari URL jika data post tetap kosong (biasanya hapus via URL segments)
                if (empty($postData) && $aksi === "Menghapus $module") {
                    $segments = explode('/', $path);
                    $postData = ['id_ref' => end($segments)];
                }

                if (!empty($postData)) {
                    $forbidden = ['password', 'pass', 'csrf_test_name', 'csrf_hash', 'thumbnail', 'foto', 'gambar'];
                    foreach ($forbidden as $f) { if (isset($postData[$f])) unset($postData[$f]); }
                    $data_payload = json_encode($postData, JSON_PARTIAL_OUTPUT_ON_ERROR);
                }
            }

            // 4. Simpan Log
            $db = \Config\Database::connect();
            $db->table('log_admin')->insert([
                'id_user'      => $id_user,
                'username'     => $username,
                'url'          => $path,
                'method'       => $method,
                'aksi'         => $aksi,
                'data_payload' => $data_payload,
                'ip_address'   => $_SERVER['REMOTE_ADDR'] ?? '::1',
                'user_agent'   => substr((string)$request->getUserAgent(), 0, 255),
                'tanggal'      => date('Y-m-d'),
                'waktu'        => date('Y-m-d H:i:s'),
                'jumlah_akses' => 1
            ]);

        } catch (\Exception $e) {
            log_message('error', '[LogFilter] Error: ' . $e->getMessage());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
