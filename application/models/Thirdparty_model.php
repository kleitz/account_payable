<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Thirdparty_model
 *
 * @author Hendra McHen
 */
class Thirdparty_model extends CI_Model {

    public function get_third_party_list() {
        $sql  = 'SELECT * FROM third_party ';
        $sql .= 'ORDER BY third_party_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_third_party_by_id($id=0) {
        $sql  = 'SELECT * FROM third_party ';
        $sql .= 'WHERE third_party_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_third_party() {
        $third_party_name = $this->input->post('third_party_name');
        $description = $this->input->post('description');
        $data = array(
            'third_party_name' => $third_party_name,
            'description' => $description
        );
        $this->db->insert('third_party', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'third_party_name' => $third_party_name,
            'description' => $description
        );
        $this->log_activity_model->insert_log('Third Party', 'Add', $log_desc);
        /*== end log ==*/
        return $this->db->insert_id();
    }
    
    public function update_third_party() {
        $third_party_id = $this->input->post('third_party_id');
        $third_party_name = $this->input->post('third_party_name');
        $description = $this->input->post('description');
        $data = array(
            'third_party_name' => $third_party_name,
            'description' => $description
        );
        $this->db->where('third_party_id', $third_party_id);
        $this->db->update('third_party', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'third_party_name' => $third_party_name,
            'description' => $description
        );
        $this->log_activity_model->insert_log('Third Party', 'Edit', $log_desc);
        /*== end log ==*/
        return $this->db->affected_rows();
    }
    
    public function delete_by_id($id) {
        $this->db->where('third_party_id', $id);
        $this->db->delete('third_party');
    }
}