<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Receiveinbank_model
 *
 * @author Hendra McHen
 */
class Receiveinbank_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_receive_bank($start_date='', $end_date='', $branch_name=''){
        $sql  = 'SELECT rb.*, b.branch_name FROM receive_bank AS rb ';
        $sql .= 'INNER JOIN branch AS b ON rb.branch_id=b.branch_id ';
        $sql .= 'WHERE rb.receive_bank_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        if ($branch_name != ''){
            $sql .= 'AND b.branch_name="'.$branch_name.'"';
        }
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_receive_bank_by_id($id=0){
        $sql  = 'SELECT rb.*, b.branch_name FROM receive_bank AS rb ';
        $sql .= 'INNER JOIN branch AS b ON rb.branch_id=b.branch_id ';
        $sql .= 'WHERE rb.receive_bank_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_receive_bank_by_rb_id($rb_id=0) {
        $sql  = 'SELECT * FROM receive_bank_file ';
        $sql .= 'WHERE receive_bank_id = '.$rb_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_receive_bank() {
        $receive_bank_number = $this->input->post('receive_bank_number');
        $receive_bank_date = $this->input->post('receive_bank_date');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $outstanding_id = $this->input->post('outstanding_id');
        $receive_type = $this->input->post('receive_type');

        
        $account_from = $this->get_account_by_branch_id('Retained Earnings', $branch_id);
        $account_to = $this->get_account_by_branch_id('Petty', $branch_id);
        // insert to transaction
        $trans_id = $this->insert_transaction_by_param($receive_bank_date, $receive_bank_number, 0, $account_from, $account_to, $description, $amount, $receive_bank_number);
        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->load->model('ledger_model');
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount, 'RIB');
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0, 'RIB');
        // insert receive in bank
        $data = array(
            'receive_bank_number' => $receive_bank_number,
            'receive_bank_date' => $receive_bank_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'outstanding_id' => $outstanding_id,
            'trans_id' => $trans_id,
            'receive_type' => $receive_type
        );
        $this->db->insert('receive_bank', $data);
        // update outstanding status
        $dataos = array('outstanding_status'=>1);
        $this->db->where('outstanding_id', $outstanding_id);
        $this->db->update('outstanding', $dataos);
    }
    
    public function update_receive_bank() {
        $receive_bank_id = $this->input->post('receive_bank_id');
        $receive_bank_number = $this->input->post('receive_bank_number');
        $receive_bank_date = $this->input->post('receive_bank_date');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $outstanding_id = $this->input->post('outstanding_id');
        $receive_type = $this->input->post('receive_type');
        // get receive bank
        $receivebank = $this->get_receive_bank_by_id($receive_bank_id);
        if ($receivebank->num_rows()!=0){
            $row = $receivebank->row();
            $os_before_id = $row->outstanding_id;
            if ($os_before_id != 0 && ($outstanding_id != $os_before_id)){
                // update outstanding status
                $dataos1 = array('outstanding_status'=>0);
                $this->db->where('outstanding_id', $os_before_id);
                $this->db->update('outstanding', $dataos1);
            }
        }
        // delete trans by receive_bank_number
        $this->db->where('trans_code', $receive_bank_number);
        $this->db->delete('transactions');
        
        $account_from = $this->get_account_by_branch_id('Retained Earnings', $branch_id);
        $account_to = $this->get_account_by_branch_id('Petty', $branch_id);
        // insert to transaction
        $trans_id = $this->insert_transaction_by_param($receive_bank_date, $receive_bank_number,0, $account_from, $account_to, $description, $amount, $receive_bank_number);
        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->load->model('ledger_model');
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount);
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0);
        // insert receive in bank        
        $data = array(
            'receive_bank_number' => $receive_bank_number,
            'receive_bank_date' => $receive_bank_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'outstanding_id' => $outstanding_id,
            'trans_id' => $trans_id,
            'receive_type' => $receive_type
        );
        $this->db->insert('receive_bank', $data);
        // update outstanding status
        $dataos = array('outstanding_status'=>1);
        $this->db->where('outstanding_id', $outstanding_id);
        $this->db->update('outstanding', $dataos);
    }
    
    public function delete_receive_bank($id=0) {
        $data = $this->get_receive_bank_by_id($id);
        $number = '';
        if ($data->num_rows()!=0){
            $row = $data->row();
            $number = $row->receive_bank_number;
        }
        $this->db->where('receive_bank_id', $id);
        $this->db->delete('receive_bank');
        
        // delete trans by receive_bank_number
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
    
    public function insert_receive_bank_v2() {        
        $receive_bank_number = $this->input->post('receive_bank_number');
        $receive_bank_date = $this->input->post('receive_bank_date');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $cash_request_id = $this->input->post('cash_request_id');
        
        $account_from = $this->get_account_by_branch_id('Retained Earnings', $branch_id);
        $account_to = $this->get_account_by_branch_id('Petty', $branch_id);
        // insert to transaction
        $trans_id = $this->insert_transaction_by_param($receive_bank_date, $receive_bank_number, 0, $account_from, $account_to, $description, $amount, $receive_bank_number);
        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->load->model('ledger_model');
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount, 'RIB');
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0, 'RIB');
        // insert receive in bank
        $data = array(
            'receive_bank_number' => $receive_bank_number,
            'receive_bank_date' => $receive_bank_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'cash_request_id' => $cash_request_id,
            'trans_id' => $trans_id
        );
        $this->db->insert('receive_bank', $data);
        $receive_bank_id = $this->db->insert_id();
        // insert to cash request balance
        $this->insert_to_cr_balance($receive_bank_date, $cash_request_id, $trans_id, $receive_bank_number, 0, $amount);
        return $receive_bank_id;
    }
    
    public function insert_to_cr_balance($balance_date='', $cash_request_id=0, $trans_id=0, $pv_number='', $debit=0, $credit=0) {
        $data = array(
            'balance_date' => $balance_date,
            'cash_request_id' => $cash_request_id,
            'trans_id' => $trans_id,
            'pv_number' => $pv_number,
            'debit' => $debit,
            'credit' => $credit
        );

        $this->db->insert('cash_request_balance', $data);
    }
}