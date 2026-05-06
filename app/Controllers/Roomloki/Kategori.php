<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KategoriModel;
use App\Models\GaleriModel;

class Kategori extends BaseController
{
    public function index()
    {
        $data['title'] = "Daftar Kategori";
        return view('roomloki/kategori/kategori', $data);
    }
    public function ajaxList()
    {
        $request = service('request');
        $KategoriModel = new KategoriModel();
        $db = \Config\Database::connect();

        $columns = ['nama_kategori'];
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');

        // Total tanpa filter
        $totalRecords = $KategoriModel->countAll();

        // Query builder langsung dari DB, BUKAN dari model
        $builder = $db->table('kategori')->select('id_kategori, nama_kategori, kategori_seo');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('nama_kategori', $search)
                    ->groupEnd();
        }

        $filteredBuilder = clone $builder;
        $filteredRecords = $filteredBuilder->countAllResults();

        if (!empty($order)) {
            $colIndex = $order[0]['column'];
            $dir = $order[0]['dir'];
            $builder->orderBy($columns[$colIndex], $dir);
        } else {
            $builder->orderBy('id_kategori', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResult();

        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $result[] = [
                'no' => $no++,
                'nama_kategori' => $row->nama_kategori,
                'kategori_seo	' => $row->kategori_seo	,
                'aksi' => ' 
                            <a class="btn btn-xs btn-danger" href="#" data-toggle="modal" data-target="#modalHapus" data-id="' . $row->id_kategori . '">
                                <i class="fas fa-trash"></i> Hapus
                            </a>'
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
        $data['title'] = 'Tambah kategori';
        return view('roomloki/kategori/tambah', $data);
    }

    public function simpan()
    {

        // Ambil data input        
        $kategori = $this->request->getPost('nama_kategori');
        $slug = url_title($kategori, '-', true);

        if (!$kategori) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Kategori kosong']);
        }

        $model = new KategoriModel();
        $model->save([
            'nama_kategori' => $kategori,
            'kategori_seo' => $slug,
        ]);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function edit($id)
    {
        $KategoriModel = new \App\Models\KategoriModel();
        $data['kategori'] = $KategoriModel->find($id);
        $data['title'] = 'Edit kategori';
        if (!$data['kategori']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("kategori tidak ditemukan.");
        }
        return view('roomloki/kategori/edit', $data);
    }

    public function update($id)
    {
        $KategoriModel = new \App\Models\KategoriModel();
        $dataLama = $KategoriModel->find($id);

        if (!$dataLama) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data tidak ditemukan");
        }

        $judul = $this->request->getPost('judul');
        $isi   = $this->request->getPost('isi_kategori');
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

        $KategoriModel->update($id, [
            'judul'        => $judul,
            'judul_seo'    => $slug,
            'isi_kategori'   => $isi,
            'gambar'       => $gambarName,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('roomloki/kategori/edit/' . $id))->with('success', 'kategori berhasil diperbarui.');
    }

    
    public function hapus($id = null)
    {
        $request = service('request');

        if ($request->getMethod() !== 'DELETE') {
            return $this->response->setStatusCode(405)->setJSON(['status' => false, 'message' => 'Method Not Allowed']);
        }

        $KategoriModel = new KategoriModel();

        $kategori = $KategoriModel->find($id);
        if (!$kategori) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        $KategoriModel->delete($id);

        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
    }

    
}
