<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cashreceived_model
 *
 * @author Hendra McHen
 */
class Cashreceived_model extends CI_Model {
    
    public $receive_status = array('Receive to be return', 'Return not full', 'Paid off');
    
    public function get_cash_received_list($start_date='', $end_date='', $branch_name='') {
        $sql  = 'SELECT c.*, b.branch_name FROM cash_receive AS c ';
        $sql .= 'INNER JOIN branch AS b ON b.branch_id=c.branch_id ';
        $sql .= 'WHERE c.cash_receive_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        if ($branch_name != ''){
            $sql .= 'AND b.branch_name="'.$branch_name.'"';
        }
        /*
        $sql .= 'WHERE  ';
        if ($start_date != '' && $end_date != ''){
            $sql .= ' c.cash_receive_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            if ($keyword != ''){
                $sql .= ' AND '.$field_search.' LIKE "%'.$keyword.'%" ';
            }
        } else {
            if ($keyword != ''){
                $sql .= ' '.$field_search.' LIKE "%'.$keyword.'%" ';
            } else {
                $sql .= ' c.cash_receive_date BETWEEN "" AND "" ';
            }
        }
         */
        $sql .= 'ORDER BY last_update DESC';
        
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cash_received_by_id($id=0) {
        $sql  = 'SELECT c.*, b.branch_name FROM cash_receive AS c ';
        $sql .= 'INNER JOIN branch AS b ON b.branch_id=c.branch_id ';
        $sql .= 'WHERE c.cash_receive_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cash_received_by_status($status = 2) {
        $sql  = 'SELECT c.*, b.branch_name FROM cash_receive AS c ';
        $sql .= 'INNER JOIN branch AS b ON b.branch_id=c.branch_id ';
        $sql .= 'WHERE c.cash_receive_status < '.$status;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_last_record() {
        $sql = 'SELECT * FROM cash_receive ORDER BY last_update DESC LIMIT 1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_account_by_id($id=0) {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'WHERE account_id='.$id.' ORDER BY account_code';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_file_by_cash_receive_id($cash_receive_id=0) {
        $sql  = 'SELECT * FROM cash_receive_file ';
        $sql .= 'WHERE cash_receive_id='.$cash_receive_id;
        $query = $this->db->query($sql);
        return $query;
    }
   
    public function insert_cashreceived() {
        //$cash_receive_id = $this->input->post('cash_receive_id');
        $cash_receive_date = $this->input->post('cash_receive_date');
        $cash_receive_number = $this->input->post('cash_receive_number');
        $account_from = $this->input->post('account_from');
        $account_to = $this->input->post('account_to');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $remark = $this->input->post('remark');
        /* get branch from account */
        $branch_id = 0;
        $account_data = $this->get_account_by_id($account_to);
        if ($account_data->num_rows()!=0){
            $row = $account_data->row();
            $branch_id = $row->branch_id;
        }
        
        $this->load->model('trans_model');
        $trans_id = $this->trans_model->insert_transaction_from_cashreceive($cash_receive_date, $cash_receive_number, $account_from, $account_to, $remark, $amount, $branch_id);
        // insert cash received
        $data = array(
            'cash_receive_date' => $cash_receive_date,
            'cash_receive_number' => $cash_receive_number,
            'account_from' => $account_from,
            'account_to' => $account_to,
            'amount' => $amount,
            'remark' => $remark,
            'branch_id' => $branch_id,
            'paid_off' => 0,
            'cash_receive_status' => 0,
            'trans_id' => $trans_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('cash_receive', $data);
        
        /* 
         * ===== OUTSTANDING TYPE =====
         * O/S CASH REQUEST = 1
         * O/S OUTLET = 2
         * O/S THIRD PARTY = 3
         */
        $branch_id = $this->get_branch_from_account($account_from);
        $outstanding_number = $this->general_model->get_generate_number('OS', 'outstanding', 'outstanding_id');
        $dataos = array(
            'outstanding_number' => $outstanding_number,
            'outstanding_date' => $cash_receive_date,
            'outstanding_description' => $remark,
            'amount' => $amount,
            'outstanding_status' => 0,
            'branch_id' => $branch_id,
            'pv_number' => $cash_receive_number,
            'outstanding_type' => 2,
            'trans_id' => $trans_id
        );
        $this->db->insert('outstanding', $dataos);
    }
    
    public function update_cashreceived() {
        $cash_receive_date = $this->input->post('cash_receive_date');
        $cash_receive_number = $this->input->post('cash_receive_number');
        $cash_receive_id = $this->input->post('cash_receive_id');
        $account_from = $this->input->post('account_from');
        $account_to = $this->input->post('account_to');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $remark = $this->input->post('remark');
        /* get branch from account */
        $branch_id = 0;
        $account_data = $this->get_account_by_id($account_to);
        if ($account_data->num_rows()!=0){
            $row = $account_data->row();
            $branch_id = $row->branch_id;
        }
        
        $data = array(
            'cash_receive_date' => $cash_receive_date,
            'account_from' => $account_from,
            'account_to' => $account_to,
            'amount' => $amount,
            'remark' => $remark,
            'branch_id' => $branch_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->where('cash_receive_id', $cash_receive_id);
        $this->db->update('cash_receive', $data);
        ////////////
        // get cash_receive by id
        $cashreceive = $this->get_cash_received_by_id($cash_receive_id);
        $pv_number = '';
        if ($cashreceive->num_rows()!=0){
            $row = $cashreceive->row();
            $pv_number = $row->pv_number;
        }
        // get transaction by $cash_receive_number
        $trans_id = $this->get_transaction_by_cash_receive_number($cash_receive_number, $pv_number);
        if ($trans_id != 0){
            $this->load->model('trans_model');
            $this->trans_model->update_transaction_from_cashreceive($trans_id, $cash_receive_date, $account_from, $account_to, $remark, $amount);
        }
        
        /* 
         * ===== OUTSTANDING TYPE =====
         * O/S CASH REQUEST = 1
         * O/S OUTLET = 2
         * O/S THIRD PARTY = 3
         */
        $branch_id = $this->get_branch_from_account($account_from);
        $oustanding_id = $this->get_outstanding_by_cash_receive_number($cash_receive_number);
        if ($oustanding_id != 0){
            $outstanding_number = $this->general_model->get_generate_number('OS', 'outstanding', 'outstanding_id');
            $dataos = array(
                'outstanding_number' => $outstanding_number,
                'outstanding_date' => $cash_receive_date,
                'outstanding_description' => $remark,
                'amount' => $amount,
                'branch_id' => $branch_id,
                'pv_number' => $cash_receive_number,
                'outstanding_type' => 2,
                'trans_id' => $trans_id
            );
            $this->db->where('oustanding_id', $oustanding_id);
            $this->db->update('outstanding', $dataos);
        }
        
    }
    
    public function delete_by_id($id=0) {
        $this->db->where('cash_receive_id', $id);
        $this->db->delete('cash_receive');
    }
    
    public function update_file_name($id=0, $file='', $type='') {
        $data = array(
            'cash_receive_id' => $id,
            'file_name'=> $file,
            'file_type'=> $type
        );
        $this->db->insert('cash_receive_file', $data);
    }
    
    public function insert_cashreceived_from_pv($pv_date= '', $account_from=0, $account_to=0, $amount=0, $description='', $pv_number='', $branch_id=0, $trans_id=0) {
        $cash_receive_date = $pv_date; //date('Y-m-d');
        $cash_receive_number = $this->general_model->get_generate_number('RC', 'cash_receive', 'cash_receive_id');
        
        $data = array(
            'cash_receive_date' => $cash_receive_date,
            'cash_receive_number' => $cash_receive_number,
            'account_from' => $account_from,
            'account_to' => $account_to,
            'amount' => $amount,
            'remark' => $description,
            'branch_id' => $branch_id,
            'paid_off' => 0,
            'cash_receive_status' => 0,
            'pv_number' => $pv_number,
            'trans_id' => $trans_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('cash_receive', $data);
        
    }
    
    public function get_transaction_by_cash_receive_number($cash_receive_number='', $pv_number='') {
        $sql  = 'SELECT * FROM transactions ';
        $sql .= 'WHERE pv_number IN("'.$cash_receive_number.'","'.$pv_number.'")';
        $query = $this->db->query($sql);
        $trans_id = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $trans_id = $row->trans_id;
        }
        return $trans_id;
    }
    
    public function get_outstanding_by_cash_receive_number($cash_receive_number='') {
        $sql = 'SELECT * FROM outstanding WHERE pv_number="'.$cash_receive_number.'"';
        $query = $this->db->query($sql);
        $outstanding_id = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $outstanding_id = $row->outstanding_id;
        }
        return $outstanding_id;
    }
    
    public function get_branch_from_account($account_id=0) {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'WHERE account_id='.$account_id;
        $query = $this->db->query($sql);
        $branch_id = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $branch_id = $row->branch_id;
        }
        return $branch_id;
    }
}
