<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \App\Models\UserModel;
class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/roomloki/dashboard');
        }

        return view('roomloki/auth/login');
    }

    public function login()
    {
        $username = esc($this->request->getPost('username'));
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'isLoggedIn' => true,
                'id_user'    => $user['id_user'],
                'username'   => $user['username'],
                'role'       => $user['role']
            ]);

            return redirect()->to('/roomloki/dashboard');
        }

        return redirect()->back()->with('error', 'Login gagal. Cek username atau password.');
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('roomloki');
    }
}
