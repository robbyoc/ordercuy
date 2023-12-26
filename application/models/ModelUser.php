<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ModelUser extends CI_Model
{
    public function is_logged_in()
    {
        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('user', ['user_id' => $user_id, 'is_active' => 1])->row_array();

        return !empty($user);
    }

    public function findByName($menuName)
    {
        return $this->db->where('menu_name', $menuName)->limit(1)->get('menu')->row();
    }

    public function findByNames($menuNames)
    {
        return !$menuNames ? [] : $this->db->where_in('menu_name', $menuNames)->get('menu')->result();
    }

    public function getMenuNames()
    {
        $this->db->select('menu_name');
        $query = $this->db->get('menu');

        $menuNames = [];
        foreach ($query->result() as $row) {
            $menuNames[] = $row->menu_name;
        }

        return $menuNames;
    }

    public function getMenuPrices($menuNames)
    {
        $menuPrices = [];

        if (!$menuNames) {
            return $menuPrices;
        }

        $this->db->select('menu_name, menu_price')
            ->where_in('menu_name', $menuNames);
        $query = $this->db->get('menu');

        foreach ($query->result() as $row) {
            $menuPrices[$row->menu_name] = $row->menu_price;
        }

        return $menuPrices;
    }

    public function getMenuItems($menu_names)
    {
        if (empty($menu_names)) {
            return [];
        }

        $this->db->select('menu_name, menu_price')
            ->where_in('menu_name', $menu_names);

        return $this->db->get('menu')->result_array();
    }

    public function getCartItemsByUserId($user_id)
    {
        $this->db->select('cart.*, menu.menu_name, menu.menu_price')
            ->from('cart')
            ->join('detail', 'cart.cart_id = detail.cart_id', 'left')
            ->join('menu', 'detail.menu_id = menu.menu_id', 'left')
            ->where('cart.user_id', $user_id);

        return $this->db->get()->result_array();
    }

    public function clearCartByUserId($user_id)
    {
        $this->db->where('user_id', $user_id)
            ->where_not_in('cart_id', $this->db->select('cart_id')->from('detail')->get_compiled_select())
            ->delete('cart');
    }

    public function getUserData()
    {
        $user_id = $this->session->userdata('user_id');
        return $this->db->get_where('user', ['user_id' => $user_id])->row_array();
    }

    public function getOrderHistory()
    {
        $user_id = $this->session->userdata('user_id');

        $this->db->select('cart.cart_id, cart.order_date, detail.quantity, menu.menu_name, menu.menu_price, detail.subtotal, status.status_name')
            ->from('detail')
            ->join('menu', 'detail.menu_id = menu.menu_id')
            ->join('cart', 'detail.cart_id = cart.cart_id')
            ->join('status', 'cart.status_id = status.status_id')
            ->where('cart.user_id', $user_id)
            ->order_by('cart.order_date DESC, detail.menu_id');

        return $this->db->get()->result_array();
    }

    public function getOrderData()
    {
        $user_id = $this->session->userdata('user_id');
        $menuNames = $this->getMenuNames();

        $order_data = [
            'menu_prices' => $this->getMenuPrices($menuNames),
            'menu_items' => $this->getMenuItems($menuNames),
            'order_cart' => $this->getCartItemsByUserId($user_id),
        ];

        return $order_data;
    }

    public function getMenuStock($menu_name)
    {
        $menu_stock = $this->db->where('menu_name', $menu_name)
            ->get('menu')
            ->row()->menu_stock;
        return $menu_stock;
    }

    public function addToCart($menu_name, $quantity = 1)
    {
        $menu = $this->findByName($menu_name);

        if ($menu) {
            $user_id = $this->getUserData()['user_id'];
            $data = [
                'id' => $menu->menu_id,
                'qty' => $quantity,
                'price' => $menu->menu_price,
                'name' => $menu->menu_name,
                'user_id' => $user_id,
            ];

            $this->cart->insert($data);
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Menu tidak ada!</div>');
        }
    }

    public function increaseQuantity($rowid)
    {
        $cart_item = $this->cart->get_item($rowid);

        if ($cart_item) {
            $new_quantity = $cart_item['qty'] + 1;
            $this->cart->update([
                'rowid' => $rowid,
                'qty' => $new_quantity
            ]);
        }
    }

    public function decreaseQuantity($rowid)
    {
        $cart_item = $this->cart->get_item($rowid);

        if ($cart_item) {
            $new_quantity = $cart_item['qty'] - 1;

            if ($new_quantity > 0) {
                $this->cart->update([
                    'rowid' => $rowid,
                    'qty' => $new_quantity
                ]);
            } else {
                $this->cart->remove($rowid);
            }
        }
    }

    public function submitOrder()
    {
        if ($this->cart->total_items() > 0) {
            $user = $this->getUserData();
            $order_data = [
                'user_id' => $user['user_id'],
                'total_price' => $this->cart->total(),
                'order_date' => null
            ];

            $this->db->insert('cart', $order_data);
            $order_id = $this->db->insert_id();

            $order_details = [];
            $insufficientStock = [];

            foreach ($this->cart->contents() as $item) {
                $menu_id = $item['id'];
                $quantity = $item['qty'];

                $menu = $this->db->where('menu_id', $menu_id)
                    ->get('menu')
                    ->row();

                if ($menu) {
                    $menu_name = str_replace('_', ' ', ucwords($menu->menu_name));
                    $menu_stock = $menu->menu_stock;

                    if ($quantity > $menu_stock) {
                        $insufficientStock[] = [
                            'menu_name' => $menu_name,
                            'insufficient_stock' => $menu_stock
                        ];
                    } else {
                        $order_details[] = [
                            'cart_id' => $order_id,
                            'menu_id' => $menu_id,
                            'quantity' => $quantity,
                            'subtotal' => $item['subtotal']
                        ];
                    }
                }
            }

            if (empty($insufficientStock)) {

                $this->db->insert_batch('detail', $order_details);

                $this->db->where('cart_id', $order_id)
                    ->update('cart', ['is_done' => null]);

                $this->cart->destroy();
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Pesanan berhasil dibuat.</div>');
            } else {
                $message = '<div class="alert alert-danger" role="alert">Pesanan tidak dapat diproses. Stok menu tidak mencukupi.<br>Sisa Stok : ';
                foreach ($insufficientStock as $stock) {
                    $message .= "{$stock['menu_name']} = {$stock['insufficient_stock']}, ";
                }
                $message .= '</div>';
                $this->session->set_flashdata('message', $message);
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Keranjang belanja kosong.</div>');
        }
    }

    public function updateProfile()
    {
        $user_fullname = $this->input->post('user_fullname');
        $user_email = $this->input->post('user_email');
        $user_password = $this->input->post('user_password');
        $data = ['user_fullname' => $user_fullname, 'user_email' => $user_email];

        if (!empty($user_password)) {
            $data['user_password'] = password_hash($user_password, PASSWORD_DEFAULT);
        }

        $this->db->where('user_id', $this->session->userdata('user_id'))
            ->update('user', $data);

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Profil berhasil diperbarui</div>');
    }

    public function deactivateAccount()
    {
        $data = ['is_active' => 0];
        $this->db->where('user_id', $this->session->userdata('user_id'))
            ->update('user', $data);

        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_email');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Akun berhasil dinonaktifkan.</div>');
    }

    public function deleteAccount()
    {
        $this->db->where('user_id', $this->session->userdata('user_id'))
            ->delete('user');

        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_email');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Akun berhasil dihapus.</div>');
    }
}
