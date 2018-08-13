<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cash_model
 *
 * @author mchen
 */
class Asset_model extends CI_Model {

    public $table_cash = 'asset';
    public $table_transaction = 'transactions';
    public $table_account = 'account';

    public function get_cash_saldo($date='') {
        $sql  = 'SELECT c.trans_id, t.trans_date, a.account_code, ';
        $sql .= 'a.account_name, c.debit, c.credit, SUM(c.debit) AS debit_saldo, SUM(c.credit) AS credit_saldo ';
        $sql .= 'FROM ' .$this->table_cash. ' AS c ';
        $sql .= 'INNER JOIN ' .$this->table_account. ' AS a ON a.account_id=c.account_id ';
        $sql .= 'INNER JOIN ' .$this->table_transaction. ' AS t ON t.trans_id=c.trans_id ' ;
        if ($date != ''){
            $sql .= 'WHERE t.trans_date="' . $date . '" ';
        }
        $sql .= 'GROUP BY a.account_code ';
        $sql .= 'ORDER BY a.account_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cash_saldo_with_dates($date='') {
        $sql  = 'SELECT c.trans_id, t.trans_date, a.account_code, ';
        $sql .= 'a.account_name, c.debit, c.credit, SUM(c.debit) AS debit_saldo, SUM(c.credit) AS credit_saldo ';
        $sql .= 'FROM ' .$this->table_cash. ' AS c ';
        $sql .= 'INNER JOIN ' .$this->table_account. ' AS a ON a.account_id=c.account_id ';
        $sql .= 'INNER JOIN ' .$this->table_transaction. ' AS t ON t.trans_id=c.trans_id ' ;
        if ($date != ''){
            $sql .= 'WHERE t.trans_date IN(' . $date . ') ';
        }
        $sql .= 'GROUP BY a.account_code ';
        $sql .= 'ORDER BY a.account_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }

    public function insert_cash($trans_id, $account_kas, $debit, $credit) {
        $kas_data = array(
            'trans_id' => $trans_id,
            'account_id' => $account_kas,
            'debit' => $debit,
            'credit' => $credit
        );
        $this->db->insert($this->table_cash, $kas_data);
    }
    
    public function update_cash_by_trans_id($trans_id, $account_kas, $debit, $credit) {
        $kas_data = array(
            'account_id' => $account_kas,
            'debit' => $debit,
            'credit' => $credit
        );
        $this->db->where('trans_id', $trans_id);
        $this->db->update($this->table_cash, $kas_data);
    }
    
    public function get_cash_by_period($period_month='') {
        $sql  = 'SELECT c.trans_id, t.trans_date, c.debit, c.credit
        FROM tb_cash AS c 
        INNER JOIN tb_transaction AS t ON t.trans_id=c.trans_id 
        WHERE t.trans_date LIKE "'.$period_month.'%"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    /* insert to tb_cash_period */
    public function insert_cash_period($period_id=0, $cash_date='', $cash_total=0) {
        $data = array(
            'period_id' => $period_id,
            'cash_date' => $cash_date,
            'cash_total' => $cash_total
        );
        $this->db->insert('tb_cash_period', $data);
    }

}
