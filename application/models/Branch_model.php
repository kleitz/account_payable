<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Branch_model
 *
 * @author mchen
 */
class Branch_model extends CI_Model {
    
    public function get_branch_list() {
        $sql  = 'SELECT * FROM branch';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_branch_by_id($id=0) {
        $sql  = 'SELECT * FROM branch ';
        $sql .= 'WHERE branch_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_branch($data) {
        $this->db->insert('branch', $data);
        
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $branch_name = $this->input->post('branch_name');
        $address = $this->input->post('address');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $log_desc = array(
            'branch name' => $branch_name,
            'address' => $address,
            'email' => $email,
            'phone' => $phone
        );
        $this->log_activity_model->insert_log('Branch', 'Add', $log_desc);
        /*== end log ==*/
        return $this->db->insert_id();
    }
    
    public function update_branch($where, $data) {
        $this->db->update('branch', $data, $where);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $branch_name = $this->input->post('branch_name');
        $address = $this->input->post('address');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $log_desc = array(
            'branch name' => $branch_name,
            'address' => $address,
            'email' => $email,
            'phone' => $phone
        );
        $this->log_activity_model->insert_log('Branch', 'Edit', $log_desc);
        /*== end log ==*/
        return $this->db->affected_rows();
    }
    
    public function delete_by_id($id) {
        $branch = $this->get_branch_by_id($id);
        if ($branch->num_rows()!=0){
            $row = $branch->row();
            $log_desc = array(
                'branch name' => $row->branch_name,
                'address' => $row->address,
                'email' => $row->email,
                'phone' => $row->phone
            );
            /*== insert to log activity ==*/
            $this->load->model('log_activity_model');
            $this->log_activity_model->insert_log('Branch', 'Delete', $log_desc);
        }
        $this->db->where('branch_id', $id);
        $this->db->delete('branch');
    }
}
