<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ModelAdmin extends CI_Model
{
    public function is_logged_in()
    {
        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('user', ['user_id' => $user_id, 'is_active' => 1])->row_array();

        return !empty($user);
    }

    public function is_admin()
    {
        $user_id = $this->session->userdata('user_id');
        $userData = $this->db->get_where('user', ['user_id' => $user_id, 'is_active' => 1])->row_array();

        return isset($userData['is_admin']) && $userData['is_admin'] == 1;
    }

    public function getUserData()
    {
        $user_id = $this->session->userdata('user_id');
        return $this->db->get_where('user', ['user_id' => $user_id])->row_array();
    }

    public function getTotalUsers()
    {
        return $this->db->count_all('user');
    }

    public function getTotalCarts()
    {
        return $this->db->count_all('cart');
    }

    public function getCartTotalPrices()
    {
        $this->db->select_sum('total_price')
            ->where('is_done', 1);
        $query = $this->db->get('cart');
        return $query->row()->total_price;
    }

    public function getAllOrders()
    {
        $this->db->select('cart.cart_id, cart.order_date, user.user_fullname, GROUP_CONCAT(CONCAT(detail.quantity, " ", menu.menu_name) SEPARATOR ",<br>") as menu_list, SUM(menu.menu_price * detail.quantity) as total_price, status.status_name, cart.status_id')
            ->from('cart')
            ->join('user', 'user.user_id = cart.user_id')
            ->join('detail', 'detail.cart_id = cart.cart_id')
            ->join('menu', 'menu.menu_id = detail.menu_id')
            ->join('status', 'status.status_id = cart.status_id')
            ->group_by('cart.cart_id')
            ->order_by('cart.order_date', 'desc');

        return $this->db->get()->result_array();
    }

    public function getAllMenu()
    {
        return $this->db->get('menu')->result_array();
    }

    public function addMenu($menu_data)
    {
        $this->db->insert('menu', $menu_data);
    }

    public function updateMenu($menu_id, $menu_data)
    {
        $this->db->where('menu_id', $menu_id)
            ->update('menu', $menu_data);
    }

    public function getMenuById($menu_id)
    {
        return $this->db->get_where('menu', ['menu_id' => $menu_id])->row_array();
    }

    public function getMenuImageById($menu_id)
    {
        $this->db->select('menu_image');
        $this->db->where('menu_id', $menu_id);
        $query = $this->db->get('menu');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->menu_image;
        }

        return null;
    }

    public function deleteMenu($menu_id)
    {
        $this->db->delete('menu', array('menu_id' => $menu_id));
    }

    public function getReportData()
    {
        $this->db->select('menu.menu_name, SUM(detail.quantity) as total_quantity, menu.menu_price, SUM(detail.subtotal) as total_subtotal')
            ->from('menu')
            ->join('detail', 'menu.menu_id = detail.menu_id', 'left')
            ->join('cart', 'detail.cart_id = cart.cart_id', 'left')
            ->where('is_done', 1)
            ->group_by('menu.menu_id');

        return $this->db->get()->result_array();
    }

    public function getReportEarning()
    {
        $this->db->select_sum('total_price')
            ->from('cart')
            ->where('is_done', 1);

        $result = $this->db->get()->row_array();

        return $result['total_price'];
    }

    public function getUsers()
    {
        return $this->db->get('user')->result_array();
    }

    public function deactivateAcc($user_id)
    {
        $this->db->where('user_id', $user_id)
            ->update('user', array('is_active' => 0));
    }

    public function activateAcc($user_id)
    {
        $this->db->where('user_id', $user_id)
            ->update('user', array('is_active' => 1));
    }

    public function deleteAcc($user_id)
    {
        $this->db->where('user_id', $user_id)
            ->delete('user');
    }

    public function updateProfile($userId, $data)
    {
        $this->db->where('user_id', $userId)
            ->update('user', $data);
    }

    public function getOrderStatus($cart_id)
    {
        $result = $this->db->select('status_id')
            ->where('cart_id', $cart_id)
            ->get('cart')
            ->row_array();

        return isset($result['status_id']) ? $result['status_id'] : null;
    }

    public function updateOrderStatus($cart_id, $status_id)
    {
        $this->db->where('cart_id', $cart_id)->update('cart', ['status_id' => $status_id]);

        if ($status_id == 5) {

            $this->db->where('cart_id', $cart_id)->update('cart', ['is_done' => 1]);

            $cartDetails = $this->db->where('cart_id', $cart_id)->get('detail')->result();

            foreach ($cartDetails as $detail) {
                $menu_id = $detail->menu_id;
                $quantity = $detail->quantity;

                $currentStock = $this->db->where('menu_id', $menu_id)->get('menu')->row()->menu_stock;

                $newStock = $currentStock - $quantity;

                $this->db->where('menu_id', $menu_id)->update('menu', ['menu_stock' => $newStock]);
            }
        }
    }
}
