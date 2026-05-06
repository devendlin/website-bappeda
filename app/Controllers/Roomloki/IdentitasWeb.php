<?php
namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use App\Models\IdentitasModel;

class IdentitasWeb extends BaseController
{
    public function index()
    {
        $model = new IdentitasModel();
        $data['identitas'] = $model->first(); // karena cuma 1 baris
        $data['title'] = "Identitas Website";
        return view('roomloki/identitas_web/identitas_web', $data);
    }

    public function update()
    {
        $model = new IdentitasModel();
        $id = $this->request->getPost('id_identitas');
        $oldData = $model->find($id);

        $data = [
            'nama_website' => $this->request->getPost('nama_website'),
            'alamat'       => $this->request->getPost('alamat'),
            'email'        => $this->request->getPost('email'),
            'keywords'     => $this->request->getPost('keywords'),
            'description'  => $this->request->getPost('description'),
        ];

        // Handle Favicon Upload
        $favicon = $this->request->getFile('favicon');
        if ($favicon && $favicon->isValid() && !$favicon->hasMoved()) {
            $newName = $favicon->getRandomName();
            $favicon->move(FCPATH . 'uploads/identitas', $newName);
            $data['favicon'] = $newName;

            // Delete old file if it exists and is not a URL
            if ($oldData && !empty($oldData['favicon']) && !filter_var($oldData['favicon'], FILTER_VALIDATE_URL)) {
                $oldFilePath = FCPATH . 'uploads/identitas/' . $oldData['favicon'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
        }

        // Handle Logo Upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/identitas', $newName);
            $data['logo'] = $newName;

            // Delete old file if it exists and is not a URL
            if ($oldData && !empty($oldData['logo']) && !filter_var($oldData['logo'], FILTER_VALIDATE_URL)) {
                $oldFilePath = FCPATH . 'uploads/identitas/' . $oldData['logo'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
        }

        $model->update($id, $data);

        return redirect()->to('/roomloki/identitas_web')->with('success', 'Data berhasil diperbarui.');
    }
}
