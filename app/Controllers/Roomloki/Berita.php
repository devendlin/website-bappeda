<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BeritaModel;
use App\Models\GaleriModel;
use App\Models\KategoriModel;
use App\Models\TagModel;

class Berita extends BaseController
{
    public function index()
    {
        $data['title'] = "Daftar Berita";
        return view('roomloki/berita/berita', $data);
    }
    public function ajaxList()
    {

        helper('tanggal');
        $request = service('request');
        $beritaModel = new BeritaModel();
        $db = \Config\Database::connect();

        $columns = ['judul', 'tanggal', 'aksi'];
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');

        // Query builder
        $builder = $db->table('berita')
                      ->select('berita.id_berita, berita.judul, berita.isi_berita, berita.tanggal, kategori.nama_kategori, users.username, berita.id_user')
                      ->join('kategori', 'kategori.id_kategori = berita.id_kategori', 'left')
                      ->join('users', 'users.id_user = berita.id_user', 'left');

        // Total records after JOIN but before search
        $totalRecords = (clone $builder)->countAllResults();

        $currentUserRole = session()->get('role');
        $currentUserId   = session()->get('id_user');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('judul', $search)
                    ->orLike('isi_berita', $search)
                    ->groupEnd();
        }

        $filteredRecords = (clone $builder)->countAllResults();

        if (!empty($order)) {
            $colIndex = $order[0]['column'];
            $dir = $order[0]['dir'];
            $builder->orderBy($columns[$colIndex], $dir);
        } else {
            $builder->orderBy('tanggal', 'DESC');
        }

        if ($length <= 0) {
            $data = $builder->get()->getResult();
        } else {
            $data = $builder->limit($length, $start)->get()->getResult();
        }

        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $tanggalFormatted = formatTanggalIndo($row->tanggal);
            $dt = new \DateTime($row->tanggal);
            $jam = $dt->format('H:i');
            
            // ROLE CHECK BASED ON ID
            $canManage = ($currentUserRole === 'superadmin' || $row->id_user == $currentUserId);
            $btnAksi = '---';

            if ($canManage) {
                $btnAksi = ' <div class="dropdown">
                                <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="dropdownMenu' . $row->id_berita . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-stream"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu' . $row->id_berita . '">
                                    <a class="dropdown-item" href="'.base_url().'roomloki/berita/edit/' . $row->id_berita . '">
                                        <i class="fas fa-pencil-alt text-warning"></i> Edit
                                    </a>
                                    <a class="dropdown-item text-danger btn-hapus" href="#" data-id="' . $row->id_berita . '">
                                        <i class="fas fa-trash text-danger"></i> Hapus
                                    </a>
                                </div>
                            </div>';
            }

            $result[] = [
                'no' => $no++,
                'judul' => $row->judul.'<br><span class="badge badge-success badge-style-light">'.$row->nama_kategori.'</span> <i style="font-size:10px;"><i class="fas fa-calendar-week"></i> '.$tanggalFormatted.' <i class="fas fa-clock"></i> '.$jam.' (By: @'.($row->username ?? 'System').')</i>',
                'isi_berita' => $row->isi_berita,
                'tanggal' => $row->tanggal,
                'aksi' => $btnAksi
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $result,
            'csrf_token' => csrf_hash(),
        ]);
    }

    public function tambah()
    {
        $kategoriModel = new KategoriModel();
        $tagModel = new TagModel();
        $data['kategori'] = $kategoriModel->getAll();
        $data['tags'] = $tagModel->getTag();
        $data['title'] = 'Tambah Berita';
        return view('roomloki/berita/tambah', $data);
    }

    public function simpan()
    {
        helper(['form', 'text']);
        helper('summernote');

        // Validasi input
        $rules = [
            'judul' => [
                'label' => 'Judul',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'Judul tidak boleh kosong.',
                    'min_length' => 'Judul minimal 3 karakter.',
                ]
            ],
            'kategori' => [
                'label' => 'Kategori',
                'rules' => 'required',
                'errors' => [
                    'required'   => 'Kategori tidak boleh kosong.',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('roomloki/berita/tambah'))->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil data input
        $judul = $this->request->getPost('judul');
        $tags = $this->request->getPost('tags') ?? []; // default ke array kosong
        $tagString = implode(',', (array)$tags);
        $slug = url_title($judul, '-', true);
        $isi_berita = $this->request->getPost('isi_berita');
        $id_kategori = $this->request->getPost('kategori');
        $tanggalInput = $this->request->getPost('tanggal'); 

        if ($tanggalInput) {
            $tanggalFinal = $tanggalInput;
        } else {
            $tanggalFinal = date('Y-m-d H:i:s');
        }

        // Simpan gambar jika ada
        
        $gambarFile = $this->request->getPost('thumbnail');
        
        // Simpan ke database (pakai model)
        $beritaModel = new \App\Models\BeritaModel();
        $beritaModel->save([
            'judul'        => $judul,
            'judul_seo'    => $slug,
            'isi_berita'   => bersihkanSummernoteHTML($isi_berita),
            'id_kategori'  => $id_kategori,
            'gambar'       => $gambarFile, 
            'tag'          => $tagString,
            'tanggal'      => $tanggalFinal,
            'id_user'      => session()->get('id_user'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        // Ambil ID terakhir yang disimpan
        $idBaru = $beritaModel->getInsertID();
        return redirect()->to('/roomloki/berita/edit/' . $idBaru)->with('success', 'Berita berhasil disimpan.<br><a href="'.base_url().'berita/detail/'.$slug.'" target="_blank">'.base_url().'berita/detail/'.$slug.'</a>');
    }

    public function edit($id)
    {
        helper('summernote');
        $beritaModel = new \App\Models\BeritaModel();
        $data['berita'] = $beritaModel->find($id);
        $data['title'] = 'Edit Berita';
        if (!$data['berita']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Berita tidak ditemukan.");
        }

        // ROLE CHECK
        if (session()->get('role') !== 'superadmin' && $data['berita']['id_user'] != session()->get('id_user')) {
            return redirect()->to(base_url('roomloki/berita'))->with('error', 'Anda tidak memiliki akses untuk mengedit berita ini.');
        }
        $kategoriModel = new KategoriModel();
        $tagModel = new TagModel();
        $data['kategori'] = $kategoriModel->getAll();
        $data['tags'] = $tagModel->getTag();
        return view('roomloki/berita/edit', $data);
    }

    public function update($id)
    {

        helper(['form', 'text']);
        helper('summernote');
        $beritaModel = new \App\Models\BeritaModel();
        $dataLama = $beritaModel->find($id);

        // Validasi input
        $rules = [
            'judul' => [
                'label' => 'Judul',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'Judul wajib diisisss.',
                    'min_length' => 'Judul minimal 3 karakter.',
                ]
            ],
            'kategori' => [
                'label' => 'Kategori',
                'rules' => 'required',
                'errors' => [
                    'required'   => 'Kategori wajib diisi.',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (!$dataLama) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data tidak ditemukan");
        }

        // ROLE CHECK
        if (session()->get('role') !== 'superadmin' && $dataLama['id_user'] != session()->get('id_user')) {
            return redirect()->to(base_url('roomloki/berita'))->with('error', 'Anda tidak memiliki akses untuk mengubah berita ini.');
        }

        $judul = $this->request->getPost('judul');
        $isi   = $this->request->getPost('isi_berita');
        $tags = $this->request->getPost('tags') ?? []; // default ke array kosong
        $tagString = implode(',', (array)$tags);
        $slug  = url_title($judul, '-', true);
        $id_kategori = $this->request->getPost('kategori');
        $tanggalInput = $this->request->getPost('tanggal'); 


        $gambarFile = $this->request->getPost('thumbnail');
        // Proses upload jika ada file baru
        if ($gambarFile=='') { 
            $gambarName = $dataLama['gambar'];
        }else{
            $gambarName = $this->request->getPost('thumbnail');
        }

        $beritaModel->update($id, [
            'judul'        => $judul,
            'judul_seo'    => $slug,
            'isi_berita'   => bersihkanSummernoteHTML($isi),
            'gambar'       => $gambarName,
            'updated_at'   => date('Y-m-d H:i:s'),
            'id_kategori'  => $id_kategori,
            'tag'          => $tagString,
            'tanggal'      => $tanggalInput,
        ]);

        return redirect()->to(base_url('roomloki/berita/edit/' . $id))->with('success', 'Berita berhasil diperbarui.<br><a href="'.base_url().'berita/detail/'.$slug.'" target="_blank">'.base_url().'berita/detail/'.$slug.'</a>');
    }

    public function upload_image()
    {
        $file = $this->request->getFile('file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowed)) {
                $uploadPath = 'uploads/galeri/';
                $baseName = pathinfo($file->getRandomName(), PATHINFO_FILENAME);

                if ($ext === 'png') {
                    // Konversi PNG ke JPG
                    $tempPath = $file->getTempName();
                    $newName = $baseName . '.jpg';
                    $outputPath = FCPATH . $uploadPath . $newName;

                    $image = imagecreatefrompng($tempPath);
                    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                    $white = imagecolorallocate($bg, 255, 255, 255);
                    imagefill($bg, 0, 0, $white);
                    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                    imagejpeg($bg, $outputPath, 85);
                    imagedestroy($image);
                    imagedestroy($bg);
                } else {
                    // Format lain langsung disimpan
                    $newName = $baseName . '.' . $ext;
                    $file->move($uploadPath, $newName);
                }

                return $this->response->setJSON([
                    'url' => base_url($uploadPath . $newName),
                    'csrf_token' => csrf_hash(),
                ]);
            }
        }

        return $this->response->setStatusCode(400)->setBody('Gagal upload gambar');
    }
    
    public function hapus($id)
    {
        $request = service('request');

        if ($request->getMethod() !== 'DELETE') {
            return $this->response->setStatusCode(405)->setJSON(['status' => false, 'message' => 'Method Not Allowed']);
        }
        $db = \Config\Database::connect();
        $beritaModel = new BeritaModel();
        $berita = $beritaModel->find($id);

        if (!$berita) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Berita tidak ditemukan']);
        }

        // ROLE CHECK
        if (session()->get('role') !== 'superadmin' && $berita['id_user'] != session()->get('id_user')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Anda tidak memiliki izin menghapus berita ini']);
        }

        $builderViews = $db->table('berita_views');
    
        // Hapus view berdasarkan id_berita
        $builderViews->where('id_berita', $id)->delete();
        $beritaModel = new BeritaModel();
        if ($beritaModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'reloadTabel' => 'tabelBerita'
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error']);
        }
    }

    public function hapus_gambar()
    {
        $data = $this->request->getRawInput();
        $nama = basename($data['nama_file'] ?? '');
        $folder = FCPATH . 'uploads/galeri/';
        $path = realpath($folder . $nama);

        // Cegah path traversal dan pastikan path adalah file, bukan direktori
        if (!$path || strpos($path, realpath($folder)) !== 0 || !is_file($path)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'File tidak valid']);
        }

        if (unlink($path)) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menghapus file']);
    }




    public function image_list()
    {
        helper('filesystem');

        $uploadPath = FCPATH . 'uploads/galeri/';
        $baseUrl    = base_url('uploads/galeri/');

        // Ambil semua file di direktori
        $files = scandir($uploadPath);

        // Ambil semua gambar yang dipakai di berita dan thumbnail
        $db = \Config\Database::connect();
        $usedImages = [];

        // Ambil isi_berita dan thumbnail
        $builder = $db->table('berita');
        $builder->select('isi_berita, gambar');
        $query = $builder->get();

        foreach ($query->getResult() as $row) {
            // Cari nama file di isi_berita (kalau pakai tag <img src="...">)
            preg_match_all('/uploads\/galeri\/([^"\']+)/i', $row->isi_berita, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $img) {
                    $usedImages[] = basename($img); // hanya nama file
                }
            }

            // Tambahkan gambar juga
            if ($row->gambar) {
                $usedImages[] = basename($row->gambar);
            }
        }

        // Ambil semua foto di dokumentasi
        $dokFotos = $db->table('dokumentasi_foto')->select('file_foto')->get()->getResult();
        foreach ($dokFotos as $df) {
            $usedImages[] = $df->file_foto;
        }

        // Ambil semua icon/gambar di aplikasi
        $apps = $db->table('aplikasi')->select('gambar')->get()->getResult();
        foreach ($apps as $app) {
            if ($app->gambar) {
                $usedImages[] = basename($app->gambar);
            }
        }

        // Buat array data file
        $fileData = [];
        foreach ($files as $file) {
            if (in_array($file, ['.', '..', 'default.jpg'])) continue; //skip default.jpg

            $fullPath = $uploadPath . $file;
            if (is_file($fullPath) && preg_match('/\.(jpe?g|png|gif|webp)$/i', $file)) {
                $fileData[] = [
                    'file' => $file,
                    'url'  => $baseUrl . $file,
                    'used' => in_array($file, $usedImages),
                    'time' => filemtime($fullPath),
                ];
            }
        }

        // Urutkan terbaru
        usort($fileData, function ($a, $b) {
            return $b['time'] - $a['time'];
        });

        $page = (int) $this->request->getGet('page') ?: 1;
        $limit = (int) $this->request->getGet('limit') ?: 24;
        $offset = ($page - 1) * $limit;

        // Ambil data sesuai offset dan limit
        $pagedData = array_slice($fileData, $offset, $limit);

        return $this->response->setJSON([
            'data' => $pagedData,
            'page' => $page,
            'limit' => $limit,
            'total' => count($fileData),
            'has_more' => ($offset + $limit) < count($fileData)
        ]);
    }


    
}
