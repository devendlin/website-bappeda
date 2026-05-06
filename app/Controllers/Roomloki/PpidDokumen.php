<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use App\Models\PpidDokumenModel;
use App\Models\PpidKategoriModel;

class PpidDokumen extends BaseController
{
    public function index($id_kategori = null)
    {
        $catModel = new PpidKategoriModel();
        $kategori = $id_kategori ? $catModel->find($id_kategori) : null;
        
        $data = [
            'title' => $kategori ? "Dokumen: " . $kategori['nama_kategori'] : "Semua Dokumen PPID",
            'id_kategori' => $id_kategori,
            'kategori' => $kategori
        ];
        return view('roomloki/ppid_dokumen/index', $data);
    }

    public function ajaxList($id_kategori = null)
    {
        $request = service('request');
        $db = \Config\Database::connect();
        
        $columns = ['id_dokumen', 'judul_dokumen', 'nama_kategori', 'views', 'downloads', 'tgl_upload', 'aksi'];
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');

        $builder = $db->table('ppid_dokumen')
                      ->select('ppid_dokumen.*, ppid_kategori.nama_kategori')
                      ->join('ppid_kategori', 'ppid_kategori.id_kategori = ppid_dokumen.id_kategori');

        if ($id_kategori) {
            $builder->where('ppid_dokumen.id_kategori', $id_kategori);
        }

        $totalRecords = $id_kategori 
            ? $db->table('ppid_dokumen')->where('id_kategori', $id_kategori)->countAllResults()
            : $db->table('ppid_dokumen')->countAllResults();

        if (!empty($search)) {
            $builder->like('judul_dokumen', $search);
        }

        $filteredRecords = $builder->countAllResults(false);

        if (!empty($order)) {
            $builder->orderBy($columns[$order[0]['column']], $order[0]['dir']);
        } else {
            $builder->orderBy('id_dokumen', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResult();

        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            // Determine Link
            if (strpos($row->file_pdf, 'drive:') === 0) {
                // It is a google drive file
                $fileId = substr($row->file_pdf, 6);
                $previewUrl = "https://drive.google.com/file/d/" . $fileId . "/preview";
            } else {
                // Local file
                $previewUrl = base_url('uploads/ppid/'.$row->file_pdf);
            }

            $result[] = [
                'judul' => '
                    <div class="py-1">
                        <div class="font-weight-bold text-dark mb-1" style="font-size:1.05rem;">'.$row->judul_dokumen.'</div>
                        <div class="d-flex align-items-center small flex-wrap">
                            <span class="badge badge-success mr-2" style="background-color: #10b981; padding: 4px 8px; font-weight: 500;">'.$row->nama_kategori.'</span>
                            <span class="text-muted mr-3">
                                <i class="fas fa-eye fa-sm"></i> '.$row->views.'
                            </span>
                            <span class="text-muted mr-3">
                                <i class="fas fa-download fa-sm"></i> '.$row->downloads.'
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-calendar-alt fa-sm"></i> '.date('d/m/Y', strtotime($row->tgl_upload)).'
                            </span>
                        </div>
                    </div>',
                'aksi' => '
                    <div class="text-right d-flex justify-content-end">
                        <div class="dropdown">
                            <button class="btn btn-xs dropdown-toggle rounded-lg px-2" type="button" id="dropdownMenu' . $row->id_dokumen . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #5d5ebc; color: white; border: none;">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in border-0" aria-labelledby="dropdownMenu' . $row->id_dokumen . '">
                                <a class="dropdown-item py-2 btn-preview" href="'.$previewUrl.'" data-title="'.esc($row->judul_dokumen).'" target="_blank">
                                    <i class="fas fa-search fa-sm fa-fw mr-2 text-primary"></i> Pratinjau
                                </a>
                                <a class="dropdown-item py-2" href="'.base_url('roomloki/ppid/dokumen/edit/'.$row->id_dokumen).'">
                                    <i class="fas fa-edit fa-sm fa-fw mr-2 text-warning"></i> Edit
                                </a>
                                <div class="dropdown-divider mx-2"></div>
                                <a class="dropdown-item py-2 text-danger btn-hapus" href="#" data-id="' . $row->id_dokumen . '">
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

    public function tambah($id_kategori = null)
    {
        $catModel = new PpidKategoriModel();
        $data['title'] = 'Unggah Dokumen PPID';
        $data['categories'] = $catModel->findAll();
        $data['selected_category'] = $id_kategori;
        return view('roomloki/ppid_dokumen/tambah', $data);
    }

    public function simpan()
    {
        $validation = \Config\Services::validation();
        $uploadMethod = $this->request->getPost('upload_method');
        $maxSize = ($uploadMethod === 'drive') ? 20480 : 1024; // 20MB vs 1MB

        $validation->setRules([
            'judul_dokumen' => 'required',
            'id_kategori'   => 'required',
            'file_pdf'      => "uploaded[file_pdf]|ext_in[file_pdf,pdf]|max_size[file_pdf,$maxSize]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            log_message('error', 'POST Data: ' . print_r($this->request->getPost(), true));
            log_message('error', 'FILES Data: ' . print_r($_FILES, true));
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
        }

        try {
            $file = $this->request->getFile('file_pdf');
            if (!$file->isValid()) {
                throw new \RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
            }

            $id_kategori = $this->request->getPost('id_kategori');
            $uploadMethod = $this->request->getPost('upload_method');
            
            $fileStoredName = '';

            if ($uploadMethod === 'drive') {
                 // Upload to Google Drive logic
                 // Since we need to upload the temp file, we rely on the file object
                 try {
                     // Check if service exists
                     $driveService = new \App\Libraries\GoogleDriveService();
                     
                     // We need the temp path. CodeIgniter's ValidatedFile is already moved? No, isValid() just checks.
                     // We can use getTempName()
                     $tempPath = $file->getTempName();
                     $originalName = $file->getName(); // or ClientName
                     
                     // Upload to Drive
                     $fileId = $driveService->uploadToDrive($tempPath, $this->request->getPost('judul_dokumen') . '.pdf');
                     
                     $fileStoredName = 'drive:' . $fileId;
                     
                 } catch (\Exception $e) {
                      throw new \RuntimeException('Gagal upload ke Google Drive: ' . $e->getMessage());
                 }

            } else {
                // Local Upload Logic
                $fileName = $file->getRandomName();
                $uploadPath = FCPATH . 'uploads/ppid/';
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                if (!$file->move($uploadPath, $fileName)) {
                    throw new \RuntimeException('Gagal memindahkan file ke direktori tujuan.');
                }
                $fileStoredName = $fileName;
            }

            $model = new PpidDokumenModel();
            $saveData = [
                'id_kategori'   => $id_kategori,
                'judul_dokumen' => $this->request->getPost('judul_dokumen'),
                'deskripsi'     => $this->request->getPost('deskripsi'),
                'file_pdf'      => $fileStoredName,
                'tgl_upload'    => $this->request->getPost('tgl_upload') ?: date('Y-m-d H:i:s'),
                'views'         => 0,
                'downloads'     => 0
            ];

            if (!$model->save($saveData)) {
                throw new \RuntimeException('Gagal menyimpan data ke database: ' . implode(', ', $model->errors()));
            }

            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'redirect' => base_url('roomloki/ppid/dokumen/' . $id_kategori)
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[PPID Upload Error] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'csrf_token' => csrf_hash()
            ]);
        }
    }

    public function edit($id)
    {
        $model = new PpidDokumenModel();
        $catModel = new PpidKategoriModel();
        
        $data['dokumen'] = $model->find($id);
        $data['categories'] = $catModel->findAll();
        $data['title'] = 'Edit Dokumen PPID';
        
        if (!$data['dokumen']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        return view('roomloki/ppid_dokumen/edit', $data);
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();
        $uploadMethod = $this->request->getPost('upload_method');
        $hasFile = $this->request->getFile('file_pdf') && $this->request->getFile('file_pdf')->isValid();
        
        $rules = [
            'judul_dokumen' => 'required',
            'id_kategori'   => 'required',
        ];

        if ($hasFile) {
            $maxSize = ($uploadMethod === 'drive') ? 20480 : 1024;
            $rules['file_pdf'] = "uploaded[file_pdf]|ext_in[file_pdf,pdf]|max_size[file_pdf,$maxSize]";
        }

        if (!$validation->setRules($rules)->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
        }

        try {
            $model = new PpidDokumenModel();
            $dataLama = $model->find($id);
            if (!$dataLama) {
                throw new \RuntimeException('Data tidak ditemukan.');
            }

            $updateData = [
                'id_kategori'   => $this->request->getPost('id_kategori'),
                'judul_dokumen' => $this->request->getPost('judul_dokumen'),
                'deskripsi'     => $this->request->getPost('deskripsi'),
                'tgl_upload'    => $this->request->getPost('tgl_upload'),
            ];

            $file = $this->request->getFile('file_pdf');
            if ($file && $file->isValid()) {
                // 1. Hapus file lama (Lokal atau Drive)
                $oldFile = $dataLama['file_pdf'];
                if (strpos($oldFile, 'drive:') === 0) {
                    try {
                        $fileId = substr($oldFile, 6);
                        $driveService = new \App\Libraries\GoogleDriveService();
                        $driveService->deleteFile($fileId);
                    } catch (\Exception $e) {
                        log_message('error', '[PPID Update-Delete old file Error] ' . $e->getMessage());
                    }
                } else {
                    if (is_file(FCPATH . 'uploads/ppid/' . $oldFile)) {
                        unlink(FCPATH . 'uploads/ppid/' . $oldFile);
                    }
                }

                // 2. Upload file baru
                if ($uploadMethod === 'drive') {
                    try {
                        $driveService = new \App\Libraries\GoogleDriveService();
                        $tempPath = $file->getTempName();
                        $fileId = $driveService->uploadToDrive($tempPath, $this->request->getPost('judul_dokumen') . '.pdf');
                        $updateData['file_pdf'] = 'drive:' . $fileId;
                    } catch (\Exception $e) {
                        throw new \RuntimeException('Gagal upload ke Google Drive: ' . $e->getMessage());
                    }
                } else {
                    $fileName = $file->getRandomName();
                    $uploadPath = FCPATH . 'uploads/ppid/';
                    if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
                    $file->move($uploadPath, $fileName);
                    $updateData['file_pdf'] = $fileName;
                }
            }

            $model->update($id, $updateData);

            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash(),
                'redirect' => base_url('roomloki/ppid/dokumen/' . $updateData['id_kategori'])
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[PPID Update Error] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'csrf_token' => csrf_hash()
            ]);
        }
    }

    public function hapus($id)
    {
        $model = new PpidDokumenModel();
        $data = $model->find($id);
        if ($data) {
            $fileName = $data['file_pdf'];
            
            if (strpos($fileName, 'drive:') === 0) {
                // Hapus dari Google Drive
                try {
                    $fileId = substr($fileName, 6);
                    $driveService = new \App\Libraries\GoogleDriveService();
                    $driveService->deleteFile($fileId);
                } catch (\Exception $e) {
                    log_message('error', '[PPID Delete Error] ' . $e->getMessage());
                }
            } else {
                // Hapus file lokal
                if (is_file(FCPATH . 'uploads/ppid/' . $fileName)) {
                    unlink(FCPATH . 'uploads/ppid/' . $fileName);
                }
            }
            
            $model->delete($id);
        }
        return $this->response->setJSON(['status' => true]);
    }
}
