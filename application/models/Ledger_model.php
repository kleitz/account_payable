<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ledger_model
 *
 * @author mchen
 */
class Ledger_model extends CI_Model {

    public $table_ledger = 'ledger';
    public $table_trans = 'transactions';
    public $table_account = 'account';

    public function get_mutation($start_date='', $end_date='') {
        $sql  = 'SELECT * FROM ' .$this->table_trans. ' AS t ';
        $sql .= 'WHERE t.trans_date BETWEEN "' . $start_date . '" AND "'.$end_date.'" ';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_ledger($date) {
        $sql  = 'SELECT ';
        $sql .= 'b.trans_id, t.trans_date, a.account_code, ';
        $sql .= 'a.account_name, b.debit, b.credit ';
        $sql .= 'FROM ' .$this->table_ledger. ' AS b ';
        $sql .= 'INNER JOIN ' .$this->table_trans. ' AS t ON t.trans_id=b.trans_id ';
        $sql .= 'INNER JOIN ' .$this->table_account. ' AS a ON a.account_id=b.account_id ';
        //$sql .= 'WHERE t.trans_date="' . $date . '" ';
        $sql .= 'ORDER BY b.ledger_id DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_ledger_by_account($account_id=0, $start_date='', $end_date='') {
        $sql  = 'SELECT l.*, t.trans_date, t.description, l.account_id, ';
        $sql .= 'a.debit AS tdebit, a.credit AS tcredit, t.pv_number FROM ledger AS l ';
        $sql .= 'INNER JOIN transactions AS t ON t.trans_id=l.trans_id ';
        $sql .= 'INNER JOIN account AS a ON a.account_id=l.account_id ';
        $sql .= 'WHERE l.account_id='.$account_id.' AND t.trans_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'ORDER BY t.trans_date ASC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pp_number_by_account($account_id=0, $start_date='', $end_date='') {
        $sql  = 'SELECT t.pv_number FROM ledger AS l ';
        $sql .= 'INNER JOIN transactions AS t ON t.trans_id=l.trans_id ';
        $sql .= 'INNER JOIN account AS a ON a.account_id=l.account_id ';
        $sql .= 'WHERE l.account_id='.$account_id.' AND t.trans_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'ORDER BY t.trans_date ASC';
        $query = $this->db->query($sql);
        $in = '';
        $array = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $in .= '"'.$value->pv_number.'",';
            }
            $in = substr($in, 0, strlen($in)-1);
            $sql = 'SELECT nota_from, pv_number FROM payment_voucher WHERE pv_number IN('.$in.')';
            $query1 = $this->db->query($sql);
            
            if ($query1->num_rows()!=0){
                foreach ($query1->result() as $value) {
                    $array[$value->pv_number] = $value->nota_from;
                }
            }
        }
        
        return $array;
    }
    
    public function account_ledger() {
        $sql  = 'SELECT a.* FROM ledger AS l ';
        $sql .= 'INNER JOIN account AS a ON l.account_id=a.account_id ';
        $sql .= 'GROUP BY a.account_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_trial_balance($start_date='', $end_date='') {
        $sql = 'SELECT 
        b.trans_id, t.trans_date, a.account_code, a.debit AS tdebit, a.credit AS tcredit, 
        a.account_name,  SUM(b.debit) AS total_debit, SUM(b.credit) AS total_credit
        FROM ledger AS b 
        INNER JOIN transactions AS t ON t.trans_id=b.trans_id 
        INNER JOIN account AS a ON a.account_id=b.account_id 
        WHERE t.trans_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"  
        GROUP BY account_code';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_ledger_in_date($date) {
        $sql  = 'SELECT p.debit AS posisi, ';
        $sql .= 'b.trans_id, t.trans_date, a.account_code, ';
        $sql .= 'a.account_name, b.debit, b.credit, SUM(b.debit) AS debit_saldo, ';
        $sql .= 'SUM(b.credit) AS credit_saldo ';
        $sql .= 'FROM ' .$this->table_ledger. ' AS b ';
        $sql .= 'INNER JOIN ' .$this->table_trans. ' AS t ON t.trans_id=b.trans_id ';
        $sql .= 'INNER JOIN ' .$this->table_account. ' AS a ON a.account_id=b.account_id ';
        $sql .= 'INNER JOIN ' .$this->table_account_type. ' AS p ON a.account_type_id=p.account_type_id ';
        $sql .= 'WHERE t.trans_date IN (' . $date . ') ';
        $sql .= 'GROUP BY a.account_code ';
        $sql .= 'ORDER BY a.account_code ASC';
        $query = $this->db->query($sql);
        return $query;
    }

    public function insert_ledger($trans_id=0, $account_id=0, $debit_post=0, $credit_post=0, $remark='*') {
        $trans_data = array(
            'trans_id' => $trans_id,
            'account_id' => $account_id,
            'debit' => $debit_post,
            'credit' => $credit_post,
            'remark' => $remark
        );
        $this->db->insert($this->table_ledger, $trans_data);
    }
    
    public function update_ledger($clauses, $account_id, $debit_post, $credit_post) {
        $trans_data = array(
            'account_id' => $account_id,
            'debit' => $debit_post,
            'credit' => $credit_post
        );
        $this->db->where($clauses);
        $this->db->update($this->table_ledger, $trans_data);
    }
    
    public function delete_ledger_by_trans_id($trans_id=0){
        $this->db->where('trans_id', $trans_id);
        $this->db->delete($this->table_ledger);
    }
    
    public function insert_received($trans_id, $account_id, $debit, $credit) {
        $trans_data = array(
            'trans_id' => $trans_id,
            'account_id' => $account_id,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $debit
        );
        $this->db->insert('received', $trans_data);
    }
    
    public function update_received($trans_id, $account_id, $debit, $credit) {
        $trans_data = array(
            'account_id' => $account_id,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $debit
        );

        $this->db->where('trans_id', $trans_id);
        $this->db->update('received', $trans_data);
    }
    
    public function update_received_credit($trans_received_id = 0) {
        $debit = 0;
        $balance = 0;
        $total = $this->get_transaction_by_received_id($trans_received_id);
        $received = $this->get_received($trans_received_id);
        if ($received->num_rows()!=0){
            $row = $received->row();
            $debit = $row->debit;
            $balance = $debit - $total;
        }

        $trans_data = array(
            'credit' => $total,
            'balance' => $balance
        );
        
        $this->db->where('trans_id', $trans_received_id);
        $this->db->update('received', $trans_data);
    }
    
    public function delete_received_credit($trans_id, $credit) {
        $credit_total = 0;
        $balance = 0;
        $received = $this->get_received($trans_id);
        if ($received->num_rows()!=0){
            $row = $received->row();
            $credit_total = $row->credit - $credit;
            $balance = $row->debit - $credit_total;
        }

        $trans_data = array(
            'credit' => $credit_total,
            'balance' => $balance
        );
        
        $this->db->where('trans_id', $trans_id);
        $this->db->update('received', $trans_data);
    }
    
    public function get_received($trans_id=0) {
        $sql = 'SELECT * FROM received WHERE trans_id='.$trans_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_transaction_by_received_id($trans_received_id = 0){
        $sql  = 'SELECT SUM(amount) AS total FROM transactions WHERE trans_received_id='.$trans_received_id;
        $query = $this->db->query($sql);
        $total = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $total = $row->total;
        }
        return $total;
    }
    
    public function get_mutation_status($account_id=0) {
        $sql = 'SELECT * FROM account WHERE account_id='.$account_id;
        $query = $this->db->query($sql);
        $debit = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $debit = $row->debit;
        }
        return $debit;
    }
    
    public function get_previous_balance($account_id=0, $start_date='', $end_date='') {
        $debit = $this->get_mutation_status($account_id);
        $sql  = 'SELECT SUM(L.debit) AS total_debit, SUM(L.credit) AS total_credit FROM ledger AS L 
        INNER JOIN transactions AS t ON L.trans_id=t.trans_id
        WHERE  L.account_id='.$account_id.' AND trans_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $query = $this->db->query($sql);
        $total = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            if ($debit == 1){
                $total = $row->total_debit - $row->total_credit;
            } else {
                $total = $row->total_credit - $row->total_debit;
            }
        }
        return $total;
    }

}
