<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use App\Models\DokumentasiModel;
use App\Models\DokumentasiFotoModel;

class Dokumentasi extends BaseController
{
    protected $model;
    protected $fotoModel;

    public function __construct()
    {
        $this->model = new DokumentasiModel();
        $this->fotoModel = new DokumentasiFotoModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dokumentasi Kegiatan'
        ];
        return view('roomloki/dokumentasi/index', $data);
    }

    public function ajaxList()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        
        $columns = ['id_dokumentasi', 'judul', 'tanggal', 'aksi'];
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');

        $builder = $db->table('dokumentasi')
                      ->select('dokumentasi.*, (SELECT file_foto FROM dokumentasi_foto WHERE id_dokumentasi = dokumentasi.id_dokumentasi LIMIT 1) as cover');

        $totalRecords = $db->table('dokumentasi')->countAllResults();

        if (!empty($search)) {
            $builder->like('judul', $search);
            $builder->orLike('deskripsi', $search);
        }

        $filteredRecords = $builder->countAllResults(false);

        if (!empty($order)) {
            $builder->orderBy($columns[$order[0]['column']], $order[0]['dir']);
        } else {
            $builder->orderBy('tanggal', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResult();

        $result = [];
        foreach ($data as $row) {
            $cover = $row->cover ? base_url('uploads/galeri/' . $row->cover) : base_url('backend/img/undraw_profile.svg');
            $result[] = [
                'judul' => '
                    <div class="d-flex align-items-center">
                        <img src="'.$cover.'" class="rounded mr-3" style="width: 50px; height: 50px; object-cover;">
                        <div>
                            <div class="font-weight-bold text-dark">'.$row->judul.'</div>
                            <div class="small text-muted"><i class="fas fa-calendar-alt fa-sm"></i> '.date('d/m/Y', strtotime($row->tanggal)).'</div>
                        </div>
                    </div>',
                'aksi' => '
                    <div class="text-right d-flex justify-content-end">
                        <div class="dropdown">
                            <button class="btn btn-xs dropdown-toggle rounded-lg px-2" type="button" id="dropdownMenu' . $row->id_dokumentasi . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #5d5ebc; color: white; border: none;">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in border-0" aria-labelledby="dropdownMenu' . $row->id_dokumentasi . '">
                                <a class="dropdown-item py-2" href="'.base_url('roomloki/dokumentasi/edit/'.$row->id_dokumentasi).'">
                                    <i class="fas fa-edit fa-sm fa-fw mr-2 text-warning"></i> Edit
                                </a>
                                <div class="dropdown-divider mx-2"></div>
                                <a class="dropdown-item py-2 text-danger btn-hapus" href="#" data-id="' . $row->id_dokumentasi . '">
                                    <i class="fas fa-trash fa-sm fa-fw mr-2 text-danger"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>'
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $result,
            'csrf_token' => csrf_hash()
        ]);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tambah Dokumentasi Kegiatan'
        ];
        return view('roomloki/dokumentasi/tambah', $data);
    }

    public function simpan()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul' => 'required',
            'tanggal' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors(), 'csrf_token' => csrf_hash()]);
        }

        try {
            $this->model->transStart();

            $id_dokumentasi = $this->model->insert([
                'judul' => $this->request->getPost('judul'),
                'tanggal' => $this->request->getPost('tanggal'),
                'deskripsi' => $this->request->getPost('deskripsi')
            ]);

            $foto_galeri = $this->request->getPost('foto_galeri');
            if (!empty($foto_galeri) && is_array($foto_galeri)) {
                foreach ($foto_galeri as $url) {
                    $this->fotoModel->insert([
                        'id_dokumentasi' => $id_dokumentasi,
                        'file_foto' => basename($url)
                    ]);
                }
            }

            $this->model->transComplete();

            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'redirect' => base_url('roomloki/dokumentasi')
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => $e->getMessage(),
                'csrf_token' => csrf_hash()
            ]);
        }
    }

    public function edit($id)
    {
        $kegiatan = $this->model->find($id);
        if (!$kegiatan) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $data = [
            'title' => 'Edit Dokumentasi Kegiatan',
            'kegiatan' => $kegiatan,
            'foto' => $this->fotoModel->where('id_dokumentasi', $id)->findAll()
        ];
        return view('roomloki/dokumentasi/edit', $data);
    }

    public function update($id)
    {
        // Similar to simpan, handles updates and new photos
        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul' => 'required',
            'tanggal' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors(), 'csrf_token' => csrf_hash()]);
        }

        try {
            $this->model->transStart();

            $this->model->update($id, [
                'judul' => $this->request->getPost('judul'),
                'tanggal' => $this->request->getPost('tanggal'),
                'deskripsi' => $this->request->getPost('deskripsi')
            ]);

            $foto_galeri = $this->request->getPost('foto_galeri');
            if (!empty($foto_galeri) && is_array($foto_galeri)) {
                foreach ($foto_galeri as $url) {
                    $this->fotoModel->insert([
                        'id_dokumentasi' => $id,
                        'file_foto' => basename($url)
                    ]);
                }
            }

            $this->model->transComplete();

            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'redirect' => base_url('roomloki/dokumentasi')
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => $e->getMessage(),
                'csrf_token' => csrf_hash()
            ]);
        }
    }

    public function hapusFoto($id_foto)
    {
        // Hanya hapus baris di database, file fisik tetap di Galeri agar bisa dipakai Berita
        $this->fotoModel->delete($id_foto);
        return $this->response->setJSON(['status' => 'ok', 'csrf_token' => csrf_hash()]);
    }

    public function hapus($id)
    {
        // Hanya hapus data kegiatan, foto fisik tetap tinggal di Galeri (Single Source of Assets)
        $this->model->delete($id);
        return $this->response->setJSON(['status' => 'ok', 'csrf_token' => csrf_hash()]);
    }
}
