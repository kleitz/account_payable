<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Creditor
 *
 * @author mchen
 */
class Creditor_model extends CI_Model {
    
    public function get_creditor_list() {
        $sql  = 'SELECT * FROM creditor ';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_creditor_by_id($id=0) {
        $sql  = 'SELECT * FROM creditor ';
        $sql .= 'WHERE creditor_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_creditor() {
        $creditor_name = $this->input->post('creditor_name');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $description = $this->input->post('description');
        $data = array(
            'creditor_name' => $creditor_name,
            'address' => $address,
            'phone' => $phone,
            'description' => $description
        );
        $this->db->insert('creditor', $data);
    }

    public function update_creditor($id) {
        $creditor_name = $this->input->post('creditor_name');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $description = $this->input->post('description');
        $data = array(
            'creditor_name' => $creditor_name,
            'address' => $address,
            'phone' => $phone,
            'description' => $description
        );

        $this->db->where('creditor_id', $id);
        $this->db->update('creditor', $data);
    }
    
    public function delete_by_id($id) {
        $this->db->where('creditor_id', $id);
        $this->db->delete('creditor');
    }
}
