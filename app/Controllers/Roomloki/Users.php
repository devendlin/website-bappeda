<?php
// app/Controllers/Users.php
namespace App\Controllers\Roomloki;
use App\Controllers\BaseController;
use App\Models\UserModel;
use Hermawan\DataTables\DataTable;

class Users extends BaseController
{
    public function index()
    {
        $data['title'] = "Manejemen Users";
        return view('roomloki/users/index', $data);
    }

    public function ajax()
    {
        $db = db_connect();
        $builder = $db->table('users')
            ->select('username, nama_lengkap, email, no_telp, blokir, id_user')
            ->where('role', 'admin');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->add('aksi', function($row){
                return '
                    <button class="btn btn-warning btn-sm" onclick="editUser(\'' . $row->username . '\')">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteUser(\'' . $row->id_user . '\')">Hapus</button>
                ';
            })
            ->toJson(true);
    }

    public function get()
    {
        $model = new UserModel();
        $username = $this->request->getPost('username');

        $user = $model->where('username', $username)->first();

        return $this->response->setJSON($user);
    }

    public function save()
    {
        $model = new UserModel();
        $data  = $this->request->getPost();
        $mode  = $data['mode'] ?? '';

        if ($mode === 'edit') {
            $user = $model->where('username', $data['username'])->first();

            if (!$user) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan']);
            }

            if (!empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            } else {
                unset($data['password']);
            }

            $model->update($user['id_user'], $data);
        } else {
            if ($model->where('username', $data['username'])->first()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Username sudah digunakan!']);
            }

            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $model->insert($data);
        }

        return $this->response->setJSON(['status' => 'success']);
    }





    public function delete($id)
    {
        $model = new UserModel();

        // Pastikan user ada dulu
        if (!$model->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan']);
        }

        $model->delete($id);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'User berhasil dihapus',
            'csrf_token' => csrf_hash()
        ]);
    }
}
