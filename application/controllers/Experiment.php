<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Experiment
 *
 * @author hendramchen
 */
class Experiment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('supplier_report_model');
        $this->load->model('general_model');
    }
    
    public function get_payment_process_debit() {
        $sql = 'SELECT pp.* FROM payment_process AS pp 
        INNER JOIN supplier AS s ON pp.supplier_id=s.supplier_id
        WHERE pp.pp_number LIKE "SC%" ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'supplier_id:'.$value->supplier_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'debit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pp_date,
                    'supplier_id' => $value->supplier_id,
                    'pp_id' => $value->pp_id,
                    'debit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('supplier_balance', $data_query);
        }
        return $query;
    }
    
    public function get_payment_process_credit() {
        $sql = 'SELECT pp.*, pv.pv_id, pv.pv_date, pv.trans_id FROM payment_process AS pp 
        INNER JOIN supplier AS s ON pp.supplier_id=s.supplier_id
        INNER JOIN payment_voucher AS pv ON pp.pp_id=pv.pp_id
        WHERE pp.pp_number LIKE "SC%" AND pp_status=4 ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $trans_id = isset($value->trans_id)? $value->trans_id:0;
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'supplier_id:'.$value->supplier_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'credit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pv_date,
                    'supplier_id' => $value->supplier_id,
                    'pp_id' => $value->pp_id,
                    'pv_id' => $value->pv_id,
                    'trans_id' => $trans_id,
                    'credit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('supplier_balance', $data_query);
        }
        return $query;
    }
    
    public function get_project_debit() {
        $sql = 'SELECT pp.* FROM payment_process AS pp 
        INNER JOIN vendor AS v ON pp.vendor_id=v.vendor_id
        WHERE pp.pp_number LIKE "PR%" ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'vendor_id:'.$value->vendor_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'debit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pp_date,
                    'vendor_id' => $value->vendor_id,
                    'pp_id' => $value->pp_id,
                    'debit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('project_balance', $data_query);
        }
        return $query;
    }
    
    public function get_project_credit() {
        $sql = 'SELECT pp.*, pv.pv_id, pv.pv_date, pv.trans_id FROM payment_process AS pp 
        INNER JOIN vendor AS v ON pp.vendor_id=v.vendor_id
        INNER JOIN payment_voucher AS pv ON pp.pp_id=pv.pp_id
        WHERE pp.pp_number LIKE "PR%" AND pp_status=4 ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $trans_id = isset($value->trans_id)? $value->trans_id:0;
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'vendor_id:'.$value->vendor_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'credit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pv_date,
                    'vendor_id' => $value->vendor_id,
                    'pp_id' => $value->pp_id,
                    'pv_id' => $value->pv_id,
                    'trans_id' => $trans_id,
                    'credit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('project_balance', $data_query);
        }
        return $query;
    }
    
    public function get_thirdparty_debit() {
        $sql = 'SELECT pp.* FROM payment_process AS pp 
        INNER JOIN third_party AS t ON pp.third_party_id=t.third_party_id
        WHERE pp.pp_number LIKE "OT%" ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'third_party_id:'.$value->third_party_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'debit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pp_date,
                    'third_party_id' => $value->third_party_id,
                    'pp_id' => $value->pp_id,
                    'debit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('third_party_balance', $data_query);
        }
        return $query;
    }
    
    public function get_thirdparty_credit() {
        $sql = 'SELECT pp.*, pv.pv_id, pv.pv_date, pv.trans_id FROM payment_process AS pp 
        INNER JOIN third_party AS t ON pp.third_party_id=t.third_party_id
        INNER JOIN payment_voucher AS pv ON pp.pp_id=pv.pp_id
        WHERE pp.pp_number LIKE "OT%" AND pp_status=4 ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $trans_id = isset($value->trans_id)? $value->trans_id:0;
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'third_party_id:'.$value->third_party_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'credit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pv_date,
                    'third_party_id' => $value->third_party_id,
                    'pp_id' => $value->pp_id,
                    'pv_id' => $value->pv_id,
                    'trans_id' => $trans_id,
                    'credit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('third_party_balance', $data_query);
        }
        return $query;
    }
    
    public function update_outstanding() {
        $sql = 'SELECT third_party_id, pv_number FROM payment_process WHERE third_party_id !=0';
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $data = array(
                    'third_party_id' => $value->third_party_id
                );
                $this->db->where('pv_number', $value->pv_number);
                $this->db->update('outstanding', $data);
            }
        }
    }
    
}
