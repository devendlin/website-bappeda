<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use App\Models\PpidKategoriModel;

class PpidKategori extends BaseController
{
    public function index()
    {
        $data['title'] = "Kategori Dokumen PPID";
        return view('roomloki/ppid_kategori/index', $data);
    }

    public function ajaxList()
    {
        $request = service('request');
        $model = new PpidKategoriModel();
        
        $columns = ['id_kategori', 'nama_kategori', 'slug_kategori', 'icon'];
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');

        $totalRecords = $model->countAll();

        $builder = $model->builder()
                         ->select('ppid_kategori.*, COUNT(ppid_dokumen.id_dokumen) as total_dokumen')
                         ->join('ppid_dokumen', 'ppid_dokumen.id_kategori = ppid_kategori.id_kategori', 'left')
                         ->groupBy('ppid_kategori.id_kategori');

        if (!empty($search)) {
            $builder->like('nama_kategori', $search);
        }

        $filteredRecords = $builder->countAllResults(false);

        if (!empty($order)) {
            $builder->orderBy($columns[$order[0]['column']], $order[0]['dir']);
        } else {
            $builder->orderBy('id_kategori', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResult();

        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $result[] = [
                'info' => '
                    <div class="py-0">
                        <div class="d-flex align-items-center mb-1">
                            <div class="font-weight-bold text-dark" style="font-size:0.89rem;">'.$row->nama_kategori.'</div>
                        </div>
                        <span class="badge mr-2 text-white" style="background-color: #10b981; padding: 4px 8px; font-weight: 500;">'.$row->total_dokumen.' Dokumen</span>
                        '.($row->deskripsi ? '<div class="small text-muted text-truncate" style="max-width: 800px;">'.$row->deskripsi.'</div>' : '').'
                    </div>',
                'aksi' => '
                    <div class="text-right d-flex justify-content-end">
                        <div class="dropdown">
                            <button class="btn btn-xs dropdown-toggle rounded-lg px-2" type="button" id="dropdownMenu' . $row->id_kategori . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #5d5ebc; color: white; border: none;">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in border-0" aria-labelledby="dropdownMenu' . $row->id_kategori . '">
                                <a class="dropdown-item py-2" href="'.base_url('roomloki/ppid/dokumen/'.$row->id_kategori).'">
                                    <i class="fas fa-folder-open fa-sm fa-fw mr-2 text-primary"></i> Kelola Dokumen
                                </a>
                                <a class="dropdown-item py-2" href="'.base_url('roomloki/ppid/kategori/edit/'.$row->id_kategori).'">
                                    <i class="fas fa-edit fa-sm fa-fw mr-2 text-warning"></i> Edit Kategori
                                </a>
                                <div class="dropdown-divider mx-2"></div>
                                <a class="dropdown-item py-2 text-danger btn-hapus" href="#" data-id="' . $row->id_kategori . '">
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
        $data['title'] = 'Tambah Kategori PPID';
        return view('roomloki/ppid_kategori/tambah', $data);
    }

    public function simpan()
    {
        try {
            $nama = $this->request->getPost('nama_kategori');
            $desc = $this->request->getPost('deskripsi');
            $icon = $this->request->getPost('icon') ?: 'description';
            
            if (empty($nama)) {
                return $this->response->setStatusCode(400)->setJSON(['message' => 'Nama kategori wajib diisi']);
            }

            $model = new PpidKategoriModel();
            $data = [
                'nama_kategori' => $nama,
                'slug_kategori' => url_title($nama, '-', true),
                'deskripsi'     => $desc,
                'icon'          => $icon
            ];

            if (!$model->save($data)) {
                throw new \RuntimeException('Gagal menyimpan kategori: ' . implode(', ', $model->errors()));
            }

            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'message' => 'Kategori berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            log_message('error', '[PPID Kategori Error] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => $e->getMessage(),
                'csrf_token' => csrf_hash()
            ]);
        }
    }

    public function edit($id)
    {
        $model = new PpidKategoriModel();
        $data['kategori'] = $model->find($id);
        $data['title'] = 'Edit Kategori PPID';
        
        if (!$data['kategori']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        return view('roomloki/ppid_kategori/edit', $data);
    }

    public function update($id)
    {
        $model = new PpidKategoriModel();
        $nama = $this->request->getPost('nama_kategori');
        
        $model->update($id, [
            'nama_kategori' => $nama,
            'slug_kategori' => url_title($nama, '-', true),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'icon'          => $this->request->getPost('icon') ?: 'description'
        ]);

        return redirect()->to(base_url('roomloki/ppid/kategori'))->with('success', 'Kategori diperbarui');
    }

    public function hapus($id)
    {
        $model = new PpidKategoriModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => true]);
    }
}
