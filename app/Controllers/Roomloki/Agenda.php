<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use App\Models\AgendaModel;

class Agenda extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new AgendaModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Agenda Kegiatan'
        ];
        return view('roomloki/agenda/index', $data);
    }

    public function ajaxList()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        
        $columns = ['id_agenda', 'judul', 'tgl_pelaksanaan', 'lokasi', 'is_active', 'aksi'];
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');

        $builder = $db->table('agenda');

        $totalRecords = $db->table('agenda')->countAllResults();

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('judul', $search)
                    ->orLike('lokasi', $search)
                    ->orLike('deskripsi', $search)
                    ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        if (!empty($order)) {
            $builder->orderBy($columns[$order[0]['column']], $order[0]['dir']);
        } else {
            $builder->orderBy('tgl_pelaksanaan', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResult();

        $result = [];
        foreach ($data as $row) {
            $status = $row->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>';
            $result[] = [
                'id_agenda' => $row->id_agenda,
                'judul' => '
                    <div class="font-weight-bold text-dark">'.$row->judul.'</div>
                    <div class="small text-muted text-truncate" style="max-width: 300px;">'.$row->deskripsi.'</div>',
                'tgl_pelaksanaan' => date('d M Y', strtotime($row->tgl_pelaksanaan)),
                'lokasi' => $row->lokasi,
                'is_active' => $status,
                'aksi' => '
                    <div class="text-right d-flex justify-content-end">
                        <div class="dropdown">
                            <button class="btn btn-xs dropdown-toggle rounded-lg px-2" type="button" id="dropdownMenu' . $row->id_agenda . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #5d5ebc; color: white; border: none;">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in border-0" aria-labelledby="dropdownMenu' . $row->id_agenda . '">
                                <a class="dropdown-item py-2" href="'.base_url('roomloki/agenda/edit/'.$row->id_agenda).'">
                                    <i class="fas fa-edit fa-sm fa-fw mr-2 text-warning"></i> Edit
                                </a>
                                <div class="dropdown-divider mx-2"></div>
                                <a class="dropdown-item py-2 text-danger btn-hapus" href="#" data-id="' . $row->id_agenda . '">
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
            'title' => 'Tambah Agenda Kegiatan'
        ];
        return view('roomloki/agenda/tambah', $data);
    }

    public function simpan()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul' => 'required',
            'tgl_pelaksanaan' => 'permit_empty|valid_date',
            'jam' => 'permit_empty',
            'lokasi' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors(), 'csrf_token' => csrf_hash()]);
        }

        try {
            $this->model->insert([
                'judul' => $this->request->getPost('judul'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'tgl_pelaksanaan' => !empty($this->request->getPost('tgl_pelaksanaan')) ? $this->request->getPost('tgl_pelaksanaan') : null,
                'jam' => $this->request->getPost('jam'),
                'lokasi' => $this->request->getPost('lokasi'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ]);

            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'redirect' => base_url('roomloki/agenda')
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
        $agenda = $this->model->find($id);
        if (!$agenda) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $data = [
            'title' => 'Edit Agenda Kegiatan',
            'agenda' => $agenda
        ];
        return view('roomloki/agenda/edit', $data);
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul' => 'required',
            'tgl_pelaksanaan' => 'permit_empty|valid_date',
            'jam' => 'permit_empty',
            'lokasi' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors(), 'csrf_token' => csrf_hash()]);
        }

        try {
            $this->model->update($id, [
                'judul' => $this->request->getPost('judul'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'tgl_pelaksanaan' => !empty($this->request->getPost('tgl_pelaksanaan')) ? $this->request->getPost('tgl_pelaksanaan') : null,
                'jam' => $this->request->getPost('jam'),
                'lokasi' => $this->request->getPost('lokasi'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ]);

            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'redirect' => base_url('roomloki/agenda')
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => $e->getMessage(),
                'csrf_token' => csrf_hash()
            ]);
        }
    }

    public function hapus($id)
    {
        if (strtoupper($this->request->getMethod()) !== 'DELETE') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Method Not Allowed']);
        }

        if ($this->model->delete($id)) {
            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'reloadTabel' => 'tabelAgenda'
            ]);
        }
        
        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
