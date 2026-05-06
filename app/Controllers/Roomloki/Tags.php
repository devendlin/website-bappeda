<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\TagModel;

class Tags extends BaseController
{
    public function index()
    {
        $data['title'] = "Daftar Tag";
        return view('roomloki/tag/tag', $data);
    }
    public function ajaxList()
    {
        $request = service('request');
        $TagModel = new TagModel();
        $db = \Config\Database::connect();

        $columns = ['nama_tag'];
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');

        // Total tanpa filter
        $totalRecords = $TagModel->countAll();

        // Query builder langsung dari DB, BUKAN dari model
        $builder = $db->table('tag')->select('id_tag, nama_tag, tag_seo');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('nama_tag', $search)
                    ->groupEnd();
        }

        $filteredBuilder = clone $builder;
        $filteredRecords = $filteredBuilder->countAllResults();

        if (!empty($order)) {
            $colIndex = $order[0]['column'];
            $dir = $order[0]['dir'];
            $builder->orderBy($columns[$colIndex], $dir);
        } else {
            $builder->orderBy('id_tag', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResult();

        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $result[] = [
                'no' => $no++,
                'nama_tag' => $row->nama_tag,
                'tag_seo	' => $row->tag_seo	,
                'aksi' => ' 
                            <a class="btn btn-xs btn-danger" href="#" data-toggle="modal" data-target="#modalHapus" data-id="' . $row->id_tag . '">
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
        $data['title'] = 'Tambah tag';
        return view('roomloki/tag/tambah', $data);
    }

    public function simpan()
    {

        // Ambil data input        
        $tag = $this->request->getPost('nama_tag');
        $slug = url_title($tag, '-', true);

        if (!$tag) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Tag kosong']);
        }

        $model = new TagModel();
        $model->save([
            'nama_tag' => $tag,
            'tag_seo' => $slug,
        ]);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function edit($id)
    {
        $TagModel = new \App\Models\TagModel();
        $data['tag'] = $TagModel->find($id);
        $data['title'] = 'Edit tag';
        if (!$data['tag']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("tag tidak ditemukan.");
        }
        return view('roomloki/tag/edit', $data);
    }

    public function update($id)
    {
        $TagModel = new \App\Models\TagModel();
        $dataLama = $TagModel->find($id);

        if (!$dataLama) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data tidak ditemukan");
        }

        $judul = $this->request->getPost('judul');
        $isi   = $this->request->getPost('isi_tag');
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

        $TagModel->update($id, [
            'judul'        => $judul,
            'judul_seo'    => $slug,
            'isi_tag'   => $isi,
            'gambar'       => $gambarName,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('roomloki/tag/edit/' . $id))->with('success', 'tag berhasil diperbarui.');
    }

    
    public function hapus($id = null)
    {
        $request = service('request');

        if ($request->getMethod() !== 'DELETE') {
            return $this->response->setStatusCode(405)->setJSON(['status' => false, 'message' => 'Method Not Allowed']);
        }

        $TagModel = new TagModel();

        $tag = $TagModel->find($id);
        if (!$tag) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        $TagModel->delete($id);

        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
    }

    
}
