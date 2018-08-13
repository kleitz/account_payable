<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Openingbalance_model
 *
 * @author Hendra McHen
 */
class Openingbalance_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_opening_balance($start_date='', $end_date=''){
        $sql  = 'SELECT op.*, b.branch_name FROM opening_balance AS op ';
        $sql .= 'INNER JOIN branch AS b ON op.branch_id=b.branch_id ';
        $sql .= 'WHERE op.opening_balance_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_opening_balance_by_id($id=0){
        $sql  = 'SELECT op.*, b.branch_name FROM opening_balance AS op ';
        $sql .= 'INNER JOIN branch AS b ON op.branch_id=b.branch_id ';
        $sql .= 'WHERE op.opening_balance_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_openingbalance_file_by_op_id($op_id=0) {
        $sql  = 'SELECT * FROM opening_balance_file ';
        $sql .= 'WHERE opening_balance_id = '.$op_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_opening_balance() {
        $opening_balance_number = $this->input->post('opening_balance_number');
        $opening_balance_date = $this->input->post('opening_balance_date');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');

        
        $account_from = $this->get_account_by_branch_id('Withdrawal', $branch_id);
        $account_to = $this->get_account_by_branch_id('Petty', $branch_id);
        // insert to transaction
        $trans_id = $this->insert_transaction_by_param($opening_balance_date, $opening_balance_number, 0, $account_from, $account_to, $description, $amount, $opening_balance_number);
        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->load->model('ledger_model');
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount);
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0);
        // insert opening balance
        $data = array(
            'opening_balance_number' => $opening_balance_number,
            'opening_balance_date' => $opening_balance_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'trans_id' => $trans_id
        );
        $this->db->insert('opening_balance', $data);
    }
    
    public function update_opening_balance() {
        $opening_balance_id = $this->input->post('opening_balance_id');
        $opening_balance_number = $this->input->post('opening_balance_number');
        $opening_balance_date = $this->input->post('opening_balance_date');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        
        // delete trans by opening_balance_number
        $this->db->where('trans_code', $opening_balance_number);
        $this->db->delete('transactions');
        
        $account_from = $this->get_account_by_branch_id('Withdrawal', $branch_id);
        $account_to = $this->get_account_by_branch_id('Petty', $branch_id);
        // insert to transaction
        $trans_id = $this->insert_transaction_by_param($opening_balance_date, $opening_balance_number,0, $account_from, $account_to, $description, $amount, $opening_balance_number);
        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->load->model('ledger_model');
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount);
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0);
        
        // insert opening balance
        $data = array(
            'opening_balance_number' => $opening_balance_number,
            'opening_balance_date' => $opening_balance_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'trans_id' => $trans_id
        );
        $this->db->insert('opening_balance', $data);
    }
    
    public function delete_opening_balance($id=0) {
        $data = $this->get_opening_balance_by_id($id);
        $number = '';
        if ($data->num_rows()!=0){
            $row = $data->row();
            $number = $row->opening_balance_number;
        }
        $this->db->where('opening_balance_id', $id);
        $this->db->delete('opening_balance');
        
        // delete trans by opening_balance_number
        $this->db->where('trans_code', $number);
        $this->db->delete('transactions');
    }
    
    public function get_account_by_branch_id($account_name='', $branch_id=0) {
        $sql  = 'SELECT account_id FROM account WHERE ';
        $sql .= 'account_name LIKE "'.$account_name.'%" AND  branch_id='.$branch_id;
        $query = $this->db->query($sql);
        $account_id = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $account_id = $row->account_id;
        }
        return $account_id;
    }
    
    public function insert_transaction_by_param($trans_date='', $trans_code='', $trans_type=0, $account_from=0, $account_to=0, $description='', $amount=0, $pv_number='') {
        $data = array(
            'trans_date' => $trans_date,
            'trans_code' => $trans_code,
            'trans_type' => $trans_type,
            'account_id' => $account_from,
            'account_relation' => $account_to,
            'description' => $description,
            'amount' => $amount,
            'user_id' => $this->session->userdata('user_id'),
            'pv_number' => $pv_number
        );

        $this->db->insert('transactions', $data);
        $trans_id = $this->db->insert_id(); /*get trans id*/
        return $trans_id;
    }
}