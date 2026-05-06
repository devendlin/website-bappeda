<?php
namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use App\Models\IdentitasModel;

class Profil extends BaseController
{
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        
        $id_user = session()->get('id_user');
        $user = $this->db->table('users')->where('id_user', $id_user)->get()->getRow();
        $data = [
            'user' => $user,
            'title'        => 'Profil Saya'
        ];
        return view('roomloki/profil/index', $data);
    }

    public function updateProfil()
    {
        $id_user = session()->get('id_user');
        $nama     = $this->request->getPost('nama_lengkap');
        $email    = $this->request->getPost('email');
        $telp     = $this->request->getPost('no_telp');
        $password = $this->request->getPost('password');

        $data = [
            'nama_lengkap' => $nama,
            'email'        => $email,
            'no_telp'      => $telp
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->db->table('users')->where('id_user', $id_user)->update($data);

        return redirect()->to(base_url('roomloki/profil'))->with('success', 'Profil berhasil diperbarui.');
    }

}
