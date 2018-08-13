<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Report_model
 *
 * @author JUNA
 */
class Report_model extends CI_Model {
    
    public function get_mutation_report() {
        $sql = 'SELECT * FROM period';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_mutation_report_by_id($id=0) {
        $sql = 'SELECT * FROM period WHERE period_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pv_id($pv_number='') {
        $sql  = 'SELECT * FROM payment_voucher WHERE pv_number="'.$pv_number.'" ';
        $query = $this->db->query($sql);
        $pv_enc = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $pv_enc = $this->general_model->encrypt_value($row->pv_id);
        }
        return $pv_enc;
    }
    
    public function get_op_id($number='') {
        $sql  = 'SELECT * FROM opening_balance WHERE opening_balance_number="'.$number.'" ';
        $query = $this->db->query($sql);
        $enc_number = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $enc_number = $this->general_model->encrypt_value($row->opening_balance_id);
        }
        return $enc_number;
    }
    
    public function get_rb_id($number='') {
        $sql  = 'SELECT * FROM receive_bank WHERE receive_bank_number="'.$number.'" ';
        $query = $this->db->query($sql);
        $enc_number = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $enc_number = $this->general_model->encrypt_value($row->receive_bank_id);
        }
        return $enc_number;
    }
    
    public function get_rc_id($number='') {
        $sql  = 'SELECT * FROM cash_receive WHERE cash_receive_number="'.$number.'" ';
        $query = $this->db->query($sql);
        $enc_number = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $enc_number = $this->general_model->encrypt_value($row->cash_receive_id);
        }
        return $enc_number;
    }
    
    public function get_rt_id($number='') {
        $sql  = 'SELECT * FROM cash_return WHERE cash_return_number="'.$number.'" ';
        $query = $this->db->query($sql);
        $enc_number = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $enc_number = $this->general_model->encrypt_value($row->cash_return_id);
        }
        return $enc_number;
    }
}
