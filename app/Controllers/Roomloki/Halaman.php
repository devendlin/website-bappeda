<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\HalamanModel;
use App\Models\GaleriModel;

class Halaman extends BaseController
{
    public function index()
    {
        $data['title'] = "Daftar Halaman";
        return view('roomloki/halaman/halaman', $data);
    }
    public function ajaxList()
    {
        $request = service('request');
        $HalamanModel = new HalamanModel();
        $db = \Config\Database::connect();

        $columns = ['judul', 'isi_halaman', 'tanggal'];
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');

        // Total tanpa filter
        $totalRecords = $HalamanModel->countAll();

        // Query builder langsung dari DB, BUKAN dari model
        $builder = $db->table('halaman')->select('id_halaman, judul, isi_halaman, tanggal');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('judul', $search)
                    ->orLike('isi_halaman', $search)
                    ->groupEnd();
        }

        $filteredBuilder = clone $builder;
        $filteredRecords = $filteredBuilder->countAllResults();

        if (!empty($order)) {
            $colIndex = $order[0]['column'];
            $dir = $order[0]['dir'];
            $builder->orderBy($columns[$colIndex], $dir);
        } else {
            $builder->orderBy('tanggal', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResult();

        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $result[] = [
                'no' => $no++,
                'judul' => $row->judul,
                'isi_halaman' => $row->isi_halaman,
                'tanggal' => $row->tanggal,
                'aksi' => ' <div class="dropdown">
                                <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="dropdownMenu' . $row->id_halaman . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-stream"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu' . $row->id_halaman . '">
                                    <a class="dropdown-item" href="'.base_url().'roomloki/halaman/edit/' . $row->id_halaman . '">
                                        <i class="fas fa-pencil-alt text-warning"></i> Edit
                                    </a>
                                    <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modalHapus" data-id="' . $row->id_halaman . '">
                                        <i class="fas fa-trash text-danger"></i> Hapus
                                    </a>
                                </div>
                            </div>'
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $result
        ]);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah halaman';
        return view('roomloki/halaman/tambah', $data);
    }

    public function simpan()
    {
        helper(['form', 'text']);

        // Validasi input
        $rules = [
            'judul' => [
                'label' => 'Judul',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'Judul wajib diisi.',
                    'min_length' => 'Judul minimal 3 karakter.',
                ]
            ],
            'isi_halaman' => [
                'label' => 'Konten',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Konten tidak boleh kosong.',
                ]
            ],
            'thumbnail' => [
                'label' => 'Thumbnail',
                'rules' => 'permit_empty|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png,image/webp]',
                'errors' => [
                    'is_image' => 'File harus berupa gambar',
                    'mime_in' => 'Format gambar tidak didukung',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil data input
        $judul = $this->request->getPost('judul');
        $slug = url_title($judul, '-', true);
        $isi_halaman = $this->request->getPost('isi_halaman');

        // Simpan gambar jika ada
        
        $gambarFile = $this->request->getFile('thumbnail');
        if ($gambarFile && $gambarFile->isValid() && !$gambarFile->hasMoved()) {
            $gambarName = $gambarFile->getRandomName();
            $gambarFile->move(FCPATH . 'uploads/thumbnail/', $gambarName);
          
        }else{
            $gambarName = 'null';
            
        }
        
        // Simpan ke database (pakai model)
        $HalamanModel = new \App\Models\HalamanModel();
        $HalamanModel->save([
            'judul'        => $judul,
            'judul_seo'    => $slug,
            'isi_halaman'   => $isi_halaman,
            'gambar'       => $gambarName, 
            'tanggal'      => date('Y-m-d H:i:s'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        // Ambil ID terakhir yang disimpan
        $idBaru = $HalamanModel->getInsertID();
        return redirect()->to('/roomloki/halaman/edit/' . $idBaru)->with('success', 'halaman berhasil disimpan!');
    }

    public function edit($id)
    {
        $HalamanModel = new \App\Models\HalamanModel();
        $data['halaman'] = $HalamanModel->find($id);
        $data['title'] = 'Edit halaman';
        if (!$data['halaman']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("halaman tidak ditemukan.");
        }
        return view('roomloki/halaman/edit', $data);
    }

    public function update($id)
    {
        $HalamanModel = new \App\Models\HalamanModel();
        $dataLama = $HalamanModel->find($id);

        if (!$dataLama) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data tidak ditemukan");
        }

        $judul = $this->request->getPost('judul');
        $isi   = $this->request->getPost('isi_halaman');
        $slug  = url_title($judul, '-', true);
        $gambarName = $dataLama['gambar']; // default gambar lama

        // Proses upload jika ada file baru
        $gambarFile = $this->request->getFile('thumbnail');
        if ($gambarFile && $gambarFile->isValid() && !$gambarFile->hasMoved()) {
            $gambarName = $gambarFile->getRandomName();
            $gambarFile->move(FCPATH . 'uploads/thumbnail/', $gambarName);

            // (Opsional) Hapus file lama
            if (is_file(FCPATH . 'uploads/thumbnail/' . $dataLama['gambar'])) {
                unlink(FCPATH . 'uploads/thumbnail/' . $dataLama['gambar']);
            }
        }

        $HalamanModel->update($id, [
            'judul'        => $judul,
            'judul_seo'    => $slug,
            'isi_halaman'   => $isi,
            'gambar'       => $gambarName,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('roomloki/halaman/edit/' . $id))->with('success', 'halaman berhasil diperbarui.');
    }

    public function upload_image()
    {
        $file = $this->request->getFile('file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = $file->getClientExtension();
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowed)) {
                $newName = $file->getRandomName();
                $file->move('uploads/galeri', $newName);

                // Simpan nama gambar ke DB (tabel galeri)
                $galeriModel = new GaleriModel();
                $galeriModel->save([
                    'judul' => $newName
                ]);

                return $this->response->setJSON([
                    'url' => base_url('uploads/galeri/' . $newName)
                ]);
            }
        }

        return $this->response->setStatusCode(400)->setBody('Gagal upload gambar');
    }
    public function hapus($id = null)
    {
        $request = service('request');

        if ($request->getMethod() !== 'DELETE') {
            return $this->response->setStatusCode(405)->setJSON(['status' => false, 'message' => 'Method Not Allowed']);
        }

        $HalamanModel = new HalamanModel();

        $halaman = $HalamanModel->find($id);
        if (!$halaman) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        $HalamanModel->delete($id);

        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
    }


    public function image_list()
    {
        helper('filesystem');

        $uploadPath = FCPATH . 'uploads/galeri/';
        $baseUrl    = base_url('uploads/galeri/');

        // Ambil semua file dalam folder
        $files = scandir($uploadPath);

        // Buat array dengan file dan waktu modifikasi
        $fileData = [];
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) continue;

            $fullPath = $uploadPath . $file;
            if (is_file($fullPath) && preg_match('/\.(jpe?g|png|gif|webp)$/i', $file)) {
                $fileData[] = [
                    'file' => $file,
                    'time' => filemtime($fullPath),
                ];
            }
        }

        // Urutkan file berdasarkan waktu terbaru ke lama
        usort($fileData, function($a, $b) {
            return $b['time'] - $a['time'];
        });

        // Bangun URL-nya
        $images = [];
        foreach ($fileData as $item) {
            $images[] = $baseUrl . $item['file'];
        }

        return $this->response->setJSON($images);
    }

    
}
