<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ModelAuth extends CI_Model
{
    public function getUserByEmail($email)
    {
        return $this->db->get_where('user', ['user_email' => $email])->row_array();
    }

    public function registerUser($data)
    {
        return $this->db->insert('user', $data);
    }
}
