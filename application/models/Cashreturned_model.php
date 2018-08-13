<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cashreturned_model
 *
 * @author Hendra McHen
 */
class Cashreturned_model extends CI_Model {
    
    public function get_cash_returned_list($start_date='', $end_date='', $field_search='', $keyword='') {
        $sql  = 'SELECT c.*, b.branch_name, r.cash_receive_status FROM cash_return AS c ';
        $sql .= 'INNER JOIN branch AS b ON b.branch_id=c.branch_id ';
        $sql .= 'INNER JOIN cash_receive AS r ON c.cash_receive_id=r.cash_receive_id ';
        $sql .= 'WHERE  ';
        if ($start_date != '' && $end_date != ''){
            $sql .= ' c.cash_return_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            if ($keyword != ''){
                $sql .= ' AND '.$field_search.' LIKE "%'.$keyword.'%" ';
            }
        } else {
            if ($keyword != ''){
                $sql .= ' '.$field_search.' LIKE "%'.$keyword.'%" ';
            } else {
                $sql .= ' c.cash_return_date BETWEEN "" AND "" ';
            }
        }
        $sql .= 'ORDER BY last_update DESC';
        
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cash_returned_by_id($id=0) {
        $sql  = 'SELECT c.*, b.branch_name FROM cash_return AS c ';
        $sql .= 'INNER JOIN branch AS b ON b.branch_id=c.branch_id ';
        $sql .= 'WHERE cash_return_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_last_record() {
        $sql = 'SELECT * FROM cash_return ORDER BY last_update DESC LIMIT 1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_file_by_cash_return_id($cash_return_id=0) {
        $sql = 'SELECT * FROM cash_return_file WHERE cash_return_id='.$cash_return_id;
        $query = $this->db->query($sql);
        return $query;
    }
   
    public function insert_cashreturned() {
        $cash_receive_id = $this->input->post('cash_receive_id');
        $cash_return_date = $this->input->post('cash_return_date');
        $cash_return_number = $this->general_model->get_generate_number('RT', 'cash_return', 'cash_return_id');
        $account_from = $this->input->post('account_from');
        $account_to = $this->input->post('account_to');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $return_mode = $this->input->post('return_mode');
        $remark = $this->input->post('remark');
        $branch_id = $this->input->post('branch_id');
        // update description | 2018-06-16
        $description = '';
        /* update status cash receive */
        $this->load->model('cashreceived_model');
        $datareceive = $this->cashreceived_model->get_cash_received_by_id($cash_receive_id);
        if ($datareceive->num_rows()!=0){
            $row = $datareceive->row();
            $paid_off = $row->paid_off + $amount;
            $amount_receive = $row->amount;
            $description = 'Return: '.$remark;
            if ($amount == $amount_receive){
                $cash_receive_status = 2;
            }
            
            if ($amount > $amount_receive){
                $cash_receive_status = 2;
                $paid_off = $amount_receive;
                $sisa = $amount - $amount_receive;
                // insert receive
                /*
                 * QOI ==> QTR (receive)
                 * QTR ==> QOI (return)
                 */
                $this->insert_cashreceived_from_return($account_from, $account_to, $sisa);
            }
            if (($amount < $amount_receive) && ($paid_off == $amount_receive)){
                $cash_receive_status = 2;
            }
            if (($amount < $amount_receive) && ($paid_off < $amount_receive)){
                $cash_receive_status = 1;
            }
            if (($amount < $amount_receive) && ($paid_off > $amount_receive)){
                $cash_receive_status = 2;
                $paid_off = $amount_receive;
                $sisa = $paid_off - $amount_receive;
                // insert receive
                $this->insert_cashreceived_from_return($account_from, $account_to, $sisa);
            }
            $dataup = array(
                'paid_off' => $paid_off,
                'cash_receive_status' => $cash_receive_status
            );
            $this->db->where('cash_receive_id', $cash_receive_id);
            $this->db->update('cash_receive', $dataup);
            if ($cash_receive_status == 2 && strlen($row->pv_number)!=0){
                $dataos = array(
                    'outstanding_status' => 1
                );
                $this->db->where('pv_number', $row->pv_number);
                $this->db->update('outstanding', $dataos);
            }
            
            if ($cash_receive_status == 2 && strlen($row->pv_number)== 0){
                $dataos = array(
                    'outstanding_status' => 1
                );
                $this->db->where('pv_number', $row->cash_receive_number);
                $this->db->update('outstanding', $dataos);
            }
        }
        // proses ledger

        /* 
        Account		Debit	Credit
        =============================
        Receiveable QTR		1
        Petty Bank QTR	1
        Petty Bank QOI		1
        Loan QOI	1
        */
       $this->load->model('trans_model');
       $trans_id = $this->trans_model->insert_transaction_cash_return($cash_return_date, $account_from, $account_to, $amount, $cash_return_number, $description);
       // insert cash return
       $data = array(
            'cash_receive_id' => $cash_receive_id,
            'cash_return_date' => $cash_return_date,
            'cash_return_number' => $cash_return_number,
            'account_from' => $account_from,
            'account_to' => $account_to,
            'amount' => $amount,
            'return_mode' => $return_mode,
            'remark' => $remark,
            'branch_id' => $branch_id,
            'trans_id' => $trans_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('cash_return', $data);
    }
    
    public function update_cashreturned() {
        $cash_return_id = $this->input->post('cash_return_id');
        $cash_receive_id = $this->input->post('cash_receive_id');
        $cash_return_date = $this->input->post('cash_return_date');
        $account_from = $this->input->post('account_from');
        $account_to = $this->input->post('account_to');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $return_mode = $this->input->post('return_mode');
        $remark = $this->input->post('remark');
        $branch_id = $this->input->post('branch_id');
        
        $data = array(
            'cash_receive_id' => $cash_receive_id,
            'cash_return_date' => $cash_return_date,
            'account_from' => $account_from,
            'account_to' => $account_to,
            'amount' => $amount,
            'return_mode' => $return_mode,
            'remark' => $remark,
            'branch_id' => $branch_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->where('cash_return_id', $cash_return_id);
        $this->db->update('cash_return', $data);
    }
    
    public function update_cashreturned_date() {
        $cash_return_id = $this->input->post('cash_return_id');
        $trans_id = $this->input->post('trans_id');
        $cash_return_date = $this->input->post('cash_return_date');
        
        $data = array(
            'cash_return_date' => $cash_return_date
        );
        $this->db->where('cash_return_id', $cash_return_id);
        $this->db->update('cash_return', $data);
        
        $datatrans = array(
            'trans_date' => $cash_return_date
        );
        
        $this->db->where('trans_id', $trans_id);
        $this->db->update('transactions', $datatrans);
    }
    
    public function update_file_name($id=0, $file='', $type='') {
        $data = array(
            'cash_return_id' => $id,
            'file_name'=> $file,
            'file_type'=> $type
        );
        $this->db->insert('cash_return_file', $data);
    }
    
    public function insert_cashreceived_from_return($account_from=0, $account_to=0, $amount=0) {
        //$cash_receive_id = $this->input->post('cash_receive_id');
        $cash_receive_date = date('Y-m-d');
        $cash_receive_number = $this->general_model->get_generate_number('RC', 'cash_receive', 'cash_receive_id');
        $remark = 'Money from Cash Back';
        /* get branch from account */
        $branch_id = $this->get_branch_from_account($account_to);

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
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('cash_receive', $data);
        
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
