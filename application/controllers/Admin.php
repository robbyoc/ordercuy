<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function index()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelAdmin->getUserData();
        $data['cart_total_prices'] = $this->ModelAdmin->getCartTotalPrices();
        $data['total_carts'] = $this->ModelAdmin->getTotalCarts();
        $data['total_users'] = $this->ModelAdmin->getTotalUsers();
        $this->load->view('admin/home', $data);
    }

    public function home()
    {
        $this->index();
    }

    public function order()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelAdmin->getUserData();
        $data['orders'] = $this->ModelAdmin->getAllOrders();
        $this->load->view('admin/order', $data);
    }

    public function updateOrderStatus($cart_id, $action)
    {
        $this->checkAuth();

        $currentStatus = $this->ModelAdmin->getOrderStatus($cart_id);

        if ($currentStatus == 1 && $action == 'accept') {
            $this->ModelAdmin->updateOrderStatus($cart_id, 2);
        } elseif ($currentStatus == 1 && $action == 'reject') {
            $this->ModelAdmin->updateOrderStatus($cart_id, 6);
        } elseif ($currentStatus > 1 && $currentStatus < 5 && $action == 'update') {
            $this->ModelAdmin->updateOrderStatus($cart_id, $currentStatus + 1);
        }

        redirect('adminOrder');
    }

    public function menu()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelAdmin->getUserData();
        $data['menu_items'] = $this->ModelAdmin->getAllMenu();
        $this->load->view('admin/menu', $data);
    }

    public function addMenu()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelAdmin->getUserData();

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('menu_name', 'Menu Name', 'required');
            $this->form_validation->set_rules('menu_price', 'Menu Price', 'required|numeric');

            if ($this->form_validation->run()) {
                $menu_data = [
                    'menu_name' => $this->input->post('menu_name'),
                    'menu_price' => $this->input->post('menu_price'),
                    'menu_image' => '',
                    'menu_stock' => $this->input->post('menu_stock')
                ];

                $upload_config['upload_path'] = './assets/img/makanan/';
                $upload_config['allowed_types'] = 'gif|jpg|jpeg|png';
                $upload_config['max_size'] = 2048;
                $upload_config['file_name'] = str_replace(' ', '_', strtolower($this->input->post('menu_name')));

                $this->upload->initialize($upload_config);

                if ($this->upload->do_upload('menu_image')) {
                    $upload_data = $this->upload->data();

                    $menu_data['menu_image'] = $upload_data['file_name'];

                    $this->ModelAdmin->addMenu($menu_data);

                    $image_path = $upload_config['upload_path'] . $upload_data['file_name'];

                    $resize_config['image_library'] = 'gd2';
                    $resize_config['source_image'] = $image_path;
                    $resize_config['maintain_ratio'] = FALSE;
                    $resize_config['width'] = 450;
                    $resize_config['height'] = 300;

                    $this->image_lib->initialize($resize_config);
                    $this->image_lib->resize();

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil Mengupload menu</div>');

                    redirect('adminMenu');
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Gagal Mengupload menu</div>');
                }
            }
        }

        $this->load->view('admin/menu_add', $data);
    }

    public function editMenu($menu_id)
    {
        $this->checkAuth();
        $data['user'] = $this->ModelAdmin->getUserData();

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('menu_name', 'Menu Name', 'required');
            $this->form_validation->set_rules('menu_price', 'Menu Price', 'required|numeric');

            if ($this->form_validation->run()) {
                $menu_data = [
                    'menu_name' => $this->input->post('menu_name'),
                    'menu_price' => $this->input->post('menu_price'),
                    'menu_stock' => $this->input->post('menu_stock')
                ];

                $existing_image = $this->ModelAdmin->getMenuImageById($menu_id);

                if (!empty($_FILES['menu_image']['name'])) {
                    if ($existing_image) {
                        $existing_image_path = './assets/img/makanan/' . $existing_image;
                        if (file_exists($existing_image_path)) {
                            unlink($existing_image_path);
                        }
                    }

                    $upload_config['upload_path'] = './assets/img/makanan/';
                    $upload_config['allowed_types'] = 'gif|jpg|jpeg|png';
                    $upload_config['max_size'] = 2048;
                    $upload_config['file_name'] = str_replace(' ', '_', strtolower($this->input->post('menu_name')));

                    $this->upload->initialize($upload_config);

                    if ($this->upload->do_upload('menu_image')) {
                        $upload_data = $this->upload->data();

                        $menu_data['menu_image'] = $upload_data['file_name'];

                        $image_path = $upload_config['upload_path'] . $upload_data['file_name'];
                        $resize_config['image_library'] = 'gd2';
                        $resize_config['source_image'] = $image_path;
                        $resize_config['maintain_ratio'] = FALSE;
                        $resize_config['width'] = 450;
                        $resize_config['height'] = 300;

                        $this->image_lib->initialize($resize_config);
                        $this->image_lib->resize();
                    } else {
                        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Gagal Mengedit Menu</div>');
                        redirect('adminMenu');
                    }
                }

                $this->ModelAdmin->updateMenu($menu_id, $menu_data);

                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil Mengupdate Menu</div>');
                redirect('adminMenu');
            }
        }

        $data['menu_item'] = $this->ModelAdmin->getMenuById($menu_id);
        $this->load->view('admin/menu_edit', $data);
    }

    public function deleteMenu($menu_id)
    {
        $this->checkAuth();
        $this->ModelAdmin->deleteMenu($menu_id);

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil Menghapus Menu</div>');

        redirect('adminMenu');
    }

    public function report()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelAdmin->getUserData();
        $data['report_data'] = $this->ModelAdmin->getReportData();
        $data['menu_items'] = $this->ModelAdmin->getAllMenu();
        $data['report_earning'] = $this->ModelAdmin->getReportEarning();

        $this->load->view('admin/report', $data);
    }

    public function manageAcc()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelAdmin->getUserData();
        $data['users'] = $this->ModelAdmin->getUsers();
        $this->load->view('admin/account', $data);
    }

    public function adminEditProfile()
    {
        $this->checkAuth();
        $data['user'] = $this->ModelUser->getUserData();
        $this->load->view('admin/profile_edit', $data);
    }

    public function adminUpdateProfile()
    {
        $this->checkAuth();
        $this->form_validation->set_rules('user_fullname', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('user_email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('user_password', 'Password', 'trim|min_length[8]');

        if ($this->form_validation->run() == false) {
            $this->load->view('admin/profile_edit');
        } else {
            $userId = $this->session->userdata('user_id');
            $data = [
                'user_fullname' => $this->input->post('user_fullname'),
                'user_email' => $this->input->post('user_email')
            ];
            $user_password = $this->input->post('user_password');

            if (!empty($user_password)) {
                $data['user_password'] = password_hash($user_password, PASSWORD_DEFAULT);
            }

            $this->ModelAdmin->updateProfile($userId, $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Profil berhasil diperbarui</div>');
            redirect('manageAcc');
        }
    }

    public function deactivateAcc($user_id)
    {
        $this->checkAuth();
        $this->ModelAdmin->deactivateAcc($user_id);

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil Menonaktifkan Akun</div>');

        redirect('manageAcc');
    }

    public function activateAcc($user_id)
    {
        $this->checkAuth();
        $this->ModelAdmin->activateAcc($user_id);

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil Mengaktifkan Akun</div>');

        redirect('manageAcc');
    }

    public function deleteAcc($user_id)
    {
        $this->checkAuth();
        $this->ModelAdmin->deleteAcc($user_id);

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil Menghapus Akun</div>');

        redirect('manageAcc');
    }

    private function checkAuth()
    {
        if (!$this->ModelAdmin->is_logged_in()) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Anda harus melakukan login terlebih dahulu !</div>');
            redirect('Auth');
        }

        $isAdmin = $this->ModelAdmin->is_admin();
        if ($isAdmin) {
            return;
        }

        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Anda tidak memiliki akses ke halaman Admin !</div>');
        redirect('Auth');
    }
}
