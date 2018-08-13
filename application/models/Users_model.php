<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users_model
 *
 * @author mchen
 */
class Users_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    } 
    
    public $priv_group_id_opt = array('1'=>'Admin AP', '2'=>'Admin Checked', '3'=>'Admin Approved');
    
    private $user_status = array(
        'non-active',
        'active'
    );
    
    public $user_status_nonactive = 0;
    public $user_status_active = 1;
    
    public function get_total_priv_group_id(){
        $sql  = 'SELECT priv_group_id, COUNT(priv_group_id) AS total_priv_group_id ';
        $sql .= 'FROM users GROUP BY priv_group_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_total_priv_by_level(){
        $sql  = 'SELECT priv_group_id, COUNT(priv_group_id) AS total_priv ';
        $sql .= 'FROM privilege_user GROUP BY priv_group_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_admins(){
        $sql = 'SELECT * FROM users WHERE user_type ='.$this->user_type_admin;
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_user_list() {
        $sql = 'SELECT * FROM users';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_user_login($username = '') {
        $sql = 'SELECT * FROM users WHERE username = "' . $username . '" ';
        $sql .= 'AND user_status = '.$this->user_status_active;
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_user_by_id($id = '') {
        $sql = 'SELECT * FROM users WHERE user_id =' . $id;
        $query = $this->db->query($sql);
        return $query;
    }

    public function username_check($username) {
        $sql = 'SELECT * FROM users WHERE username = "' . $username . '"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_user_id($username) {
        $sql = 'SELECT user_id FROM users WHERE username = "' . $username . '"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_user_by_level($level) {
        $sql = 'SELECT * FROM users WHERE priv_group_id = ' . $level;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_group_list() {
        $sql  = 'SELECT * FROM privilege_group';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_group_by_id($id=0) {
        $sql  = 'SELECT * FROM privilege_group ';
        $sql .= 'WHERE priv_group_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }

    public function insert_user($data) {
        $this->db->insert('users', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $fullname = $this->input->post('fullname');
        $gender = $this->input->post('gender');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $priv_group_id = $this->input->post('priv_group_id');
        $log_desc = array(
            'username' => $username,
            'fullname' => $fullname,
            'gender' => $gender,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'user level' => $priv_group_id,
            'user status' => 1
        );
        $this->log_activity_model->insert_log('Users', 'Add', $log_desc);
        /*== end log ==*/
        return $this->db->insert_id();
    }
    
    public function update_user($where, $data) {
        $this->db->update('users', $data, $where);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $fullname = $this->input->post('fullname');
        $gender = $this->input->post('gender');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $priv_group_id = $this->input->post('priv_group_id');
        $user_status = $this->input->post('user_status');
        $log_desc = array(
            'fullname' => $fullname,
            'gender' => $gender,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'user level' => $priv_group_id,
            'user status' => $user_status
        );
        $this->log_activity_model->insert_log('Users', 'Edit', $log_desc);
        /*== end log ==*/
        return $this->db->affected_rows();
    }
    
    public function delete_by_id($id) {
        /*== insert to log activity ==*/
        $users = $this->get_user_by_id($id);
        if ($users->num_rows()!=0){
            $row = $users->row();
            /*== insert to log activity ==*/
            $this->load->model('log_activity_model');

            $log_desc = array(
                'username' => $row->username,
                'fullname' => $row->fullname,
                'gender' => $row->gender,
                'address' => $row->address,
                'email' => $row->email,
                'phone' => $row->phone,
                'user level' => $row->priv_group_id,
                'user status' => 1
            );
            $this->log_activity_model->insert_log('Users', 'Delete', $log_desc);
            /*== end log ==*/
        }

        $this->db->where('user_id', $id);
        $this->db->delete('users');
    }
    
    public function update_file_name($id=0, $file='') {
        $data = array('photo'=>$file);
        $this->db->where('user_id', $id);
        $this->db->update('users', $data);
    }

}
