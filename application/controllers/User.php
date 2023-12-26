<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function index()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelUser->getUserData();
        $data['order_data'] = $this->ModelUser->getOrderData();
        $this->load->view('user/home', $data);
    }

    public function home()
    {
        $this->index();
    }

    public function order()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelUser->getUserData();
        $data['order_data'] = $this->ModelUser->getOrderData();
        $this->load->view('user/order', $data);
    }

    public function addToCart($menu_name)
    {
        $this->checkAuth();

        if ($this->input->post('quantity')) {
            $quantity = $this->input->post('quantity');
            $this->ModelUser->addToCart($menu_name, $quantity);
            redirect('userOrder');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Jumlah menu tidak valid.</div>');
            redirect('userOrder');
        }
    }

    public function cartItems()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelUser->getUserData();
        $data['order_cart'] = $this->cart->contents();
        $data['menus'] = $this->ModelUser->findByNames(array_column($data['order_cart'], 'name'));
        $this->load->view('user/order_cart', $data);
    }

    public function increaseQuantity($rowid)
    {
        $this->checkAuth();
        $this->ModelUser->increaseQuantity($rowid);
        redirect('userCart');
    }

    public function decreaseQuantity($rowid)
    {
        $this->checkAuth();
        $this->ModelUser->decreaseQuantity($rowid);
        redirect('userCart');
    }

    public function removeFromCart($rowid)
    {
        $this->checkAuth();
        $this->cart->get_item($rowid);
        $this->cart->remove($rowid);
        redirect('userCart');
    }

    public function submitOrder()
    {
        $this->checkAuth();
        $this->ModelUser->submitOrder();
        redirect('userOrder');
    }

    public function orderHistory()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelUser->getUserData();
        $data['order_history'] = $this->ModelUser->getOrderHistory();
        $this->load->view('user/order_history', $data);
    }

    public function profile()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelUser->getUserData();
        $this->load->view('user/profile', $data);
    }

    public function editProfile()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelUser->getUserData();
        $this->load->view('user/profile_edit', $data);
    }

    public function updateProfile()
    {
        $this->checkAuth();
        $this->form_validation->set_rules('user_fullname', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('user_email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('user_password', 'Password', 'trim|min_length[8]');

        if ($this->form_validation->run() == false) {
            $this->load->view('user/profile_edit');
        } else {
            $this->ModelUser->updateProfile();
            redirect('userProfile');
        }
    }

    public function deactivateAccount()
    {
        $this->checkAuth();
        $this->ModelUser->deactivateAccount();
        redirect('Auth');
    }

    public function deleteAccount()
    {
        $this->checkAuth();
        $this->ModelUser->deleteAccount();
        redirect('Auth');
    }

    private function checkAuth()
    {
        if (!$this->ModelUser->is_logged_in()) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Anda harus melakukan login terlebih dahulu !</div>');
            redirect('Auth');
        }
    }
}
