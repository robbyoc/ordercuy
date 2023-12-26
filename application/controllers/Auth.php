<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    const MESSAGES = [
        'name' => 'Mohon isi dengan nama anda !',
        'email_required' => 'Mohon isi dengan alamat email anda !',
        'email_valid' => 'Mohon isi dengan alamat email yang valid !',
        'email_unique' => 'Alamat Email sudah terdaftar !',
        'password_required' => 'Mohon masukkan minimal 8 karakter !',
        'login_fail' => 'Email atau password salah !',
        'inactive_account' => 'Akun anda telah dinonaktifkan !',
        'registration_success' => 'Registrasi berhasil. Silahkan login menggunakan akun anda !',
        'registration_fail' => 'Gagal melakukan registrasi !'
    ];

    public function index()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email', [
            'required' => self::MESSAGES['email_required'],
            'valid_email' => self::MESSAGES['email_valid']
        ]);
        $this->form_validation->set_rules('password', 'Password', 'trim|required', [
            'required' => self::MESSAGES['password_required']
        ]);

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Ordercuy | Login';
            $this->loadAuthView('login', $data);
        } else {
            $this->login();
        }
    }

    private function loadAuthView($view, $data)
    {
        $this->load->view('templates/auth_header', $data);
        $this->load->view('auth/' . $view);
        $this->load->view('templates/auth_footer');
    }

    private function login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->ModelAuth->getUserByEmail($email);

        if ($user) {
            $this->checkActivation($user, $password);
        } else {
            $this->redirectWithMessage(self::MESSAGES['login_fail']);
        }
    }

    private function checkActivation($user, $password)
    {
        if ($user['is_active'] == 1 && password_verify($password, $user['user_password'])) {
            $this->setUserDataAndRedirect($user);
        } else {
            $this->handleLoginError($user['is_active']);
        }
    }

    private function handleLoginError($is_active)
    {
        $message = $is_active == 0 ? self::MESSAGES['inactive_account'] : self::MESSAGES['login_fail'];
        $this->redirectWithMessage($message);
    }

    private function setUserDataAndRedirect($user)
    {
        $data = [
            'user_id' => $user['user_id'],
            'user_email' => $user['user_email'],
            'user_fullname' => $user['user_fullname'],
            'is_admin' => $user['is_admin']
        ];
        $this->session->set_userdata($data);

        if ($user['is_admin'] == 1) {
            redirect('Admin');
        } else {
            redirect('User');
        }
    }

    public function register()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim', ['required' => self::MESSAGES['name']]);
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.user_email]', [
            'required' => self::MESSAGES['email_required'],
            'valid_email' => self::MESSAGES['email_valid'],
            'is_unique' => self::MESSAGES['email_unique']
        ]);
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[8]', [
            'required' => self::MESSAGES['password_required']
        ]);

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Ordercuy | Registrasi';
            $this->loadAuthView('register', $data);
        } else {
            $this->handleRegistration();
        }
    }

    private function handleRegistration()
    {
        if ($this->registerUser()) {
            $this->redirectWithMessage(self::MESSAGES['registration_success'], 'success');
        } else {
            $this->redirectWithMessage(self::MESSAGES['registration_fail']);
        }
    }

    private function registerUser()
    {
        $data = [
            'user_email' => htmlspecialchars($this->input->post('email', true)),
            'user_password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'user_fullname' => htmlspecialchars($this->input->post('name', true))
        ];

        return $this->ModelAuth->registerUser($data);
    }

    public function logout()
    {
        $this->session->unset_userdata(['user_id', 'user_email', 'user_fullname', 'is_admin']);
        $this->redirectWithMessage('Anda telah logout.', 'success');
    }

    private function redirectWithMessage($message, $type = 'danger')
    {
        $this->session->set_flashdata('message', '<div class="alert alert-' . $type . '" role="alert">' . $message . '</div>');
        redirect('Auth');
    }
}
