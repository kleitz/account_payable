<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Bank_model
 *
 * @author Hendra McHen
 */
class Bank_model extends CI_Model {

    public function get_bank_account_list() {
        $sql  = 'SELECT b.*, o.branch_name FROM bank_account AS b ';
        $sql .= 'INNER JOIN branch AS o ON b.branch_id=o.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_bank_account_by_id($id=0) {
        $sql  = 'SELECT ba.*, b.branch_name FROM bank_account AS ba ';
        $sql .= 'INNER JOIN branch AS b ON ba.branch_id=b.branch_id ';
        $sql .= 'WHERE bank_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }

    public function insert_bank_account() {
        $bank_code = $this->input->post('bank_code');
        $bank_name = $this->input->post('bank_name');
        $bank_address = $this->input->post('bank_address');
        $bank_account_name = $this->input->post('bank_account_name');
        $bank_account_no = $this->input->post('bank_account_no');
        $bank_remark = $this->input->post('bank_remark');
        $bank_status = $this->input->post('bank_status');
        $branch_id = $this->input->post('branch_id');
        $data = array(
            'bank_code' => $bank_code,
            'bank_name' => $bank_name,
            'bank_address' => $bank_address,
            'bank_account_name' => $bank_account_name,
            'bank_account_no' => $bank_account_no,
            'bank_remark' => $bank_remark,
            'bank_status' => $bank_status,
            'branch_id' => $branch_id
        );
        $this->db->insert('bank_account', $data);
    }

    public function update_bank_account($id) {
        $bank_code = $this->input->post('bank_code');
        $bank_name = $this->input->post('bank_name');
        $bank_address = $this->input->post('bank_address');
        $bank_account_name = $this->input->post('bank_account_name');
        $bank_account_no = $this->input->post('bank_account_no');
        $bank_remark = $this->input->post('bank_remark');
        $bank_status = $this->input->post('bank_status');
        $branch_id = $this->input->post('branch_id');
        $data = array(
            'bank_code' => $bank_code,
            'bank_name' => $bank_name,
            'bank_address' => $bank_address,
            'bank_account_name' => $bank_account_name,
            'bank_account_no' => $bank_account_no,
            'bank_remark' => $bank_remark,
            'bank_status' => $bank_status,
            'branch_id' => $branch_id
        );

        $this->db->where('bank_id', $id);
        $this->db->update('bank_account', $data);
    }
    
    public function delete_by_id($id) {
        $this->db->where('bank_id', $id);
        $this->db->delete('bank_account');
    }
}