<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use App\Models\AplikasiModel;

class Aplikasi extends BaseController
{
    public function index()
    {
        $data['title'] = "Daftar Aplikasi";
        return view('roomloki/aplikasi/index', $data);
    }

    public function ajaxList()
    {
        $request = service('request');
        $model = new AplikasiModel();
        
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');
        $draw = (int) $request->getPost('draw');

        $totalRecords = $model->countAll();
        $data = $model->orderBy('urutan', 'ASC')->limit($length, $start)->findAll();

        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $result[] = [
                'no' => $no++,
                'nama_aplikasi' => $row['nama_aplikasi'],
                'url' => '<a href="' . $row['url'] . '" target="_blank">' . $row['url'] . '</a>',
                'gambar' => '<img src="' . $row['gambar'] . '" style="height:40px; border-radius:5px;">',
                'urutan' => $row['urutan'],
                'aksi' => '
                    <button class="btn btn-xs btn-warning btn-edit" data-id="' . $row['id_aplikasi'] . '" data-nama="' . $row['nama_aplikasi'] . '" data-url="' . $row['url'] . '" data-gambar="' . $row['gambar'] . '" data-urutan="' . $row['urutan'] . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-xs btn-danger btn-hapus" data-id="' . $row['id_aplikasi'] . '">
                        <i class="fas fa-trash"></i>
                    </button>'
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $result
        ]);
    }

    public function simpan()
    {
        $model = new AplikasiModel();
        
        $data = [
            'nama_aplikasi' => $this->request->getPost('nama_aplikasi'),
            'url' => $this->request->getPost('url'),
            'gambar' => $this->request->getPost('gambar'),
            'urutan' => $this->request->getPost('urutan') ?? 0,
        ];

        $id = $this->request->getPost('id_aplikasi');

        if ($id) {
            $model->update($id, $data);
        } else {
            $model->save($data);
        }

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function hapus($id)
    {
        $model = new AplikasiModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'ok']);
    }
}
