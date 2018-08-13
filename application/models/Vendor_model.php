<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vendor_model
 *
 * @author hendramchen
 */
class Vendor_model extends CI_Model {

    public function get_vendor_list() {
        $sql  = 'SELECT * FROM vendor ';
        $sql .= 'ORDER BY vendor_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_vendor_by_id($id=0) {
        $sql  = 'SELECT * FROM vendor ';
        $sql .= 'WHERE vendor_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_vendor() {
        $vendor_name = $this->input->post('vendor_name');
        $description = $this->input->post('description');
        $phone = $this->input->post('phone');
        $data = array(
            'vendor_name' => $vendor_name,
            'description' => $description,
            'phone' => $phone
        );
        $this->db->insert('vendor', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'vendor_name' => $vendor_name,
            'description' => $description,
            'phone' => $phone
        );
        $this->log_activity_model->insert_log('Vendor', 'Add', $log_desc);
        /*== end log ==*/
        return $this->db->insert_id();
    }
    
    public function update_vendor() {
        $vendor_id = $this->input->post('vendor_id');
        $vendor_name = $this->input->post('vendor_name');
        $description = $this->input->post('description');
        $phone = $this->input->post('phone');
        $data = array(
            'vendor_name' => $vendor_name,
            'description' => $description,
            'phone' => $phone
        );
        $this->db->where('vendor_id', $vendor_id);
        $this->db->update('vendor', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'vendor_name' => $vendor_name,
            'description' => $description,
            'phone' => $phone
        );
        $this->log_activity_model->insert_log('Vendor', 'Edit', $log_desc);
        /*== end log ==*/
        return $this->db->affected_rows();
    }
    
    public function delete_by_id($id) {
        $this->db->where('vendor_id', $id);
        $this->db->delete('vendor');
    }
}