<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ppdetail_model
 *
 * @author mchen
 */
class Ppdetail_model extends CI_Model {
    public function get_payment_process($id=0) {
        $sql  = 'SELECT pp.* FROM payment_process AS pp ';
        $sql .= 'WHERE pp.pp_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_payment_process_by_id($id=0) {
        $sql  = 'SELECT pp.*, s.supplier_name FROM payment_process AS pp ';
        $sql .= 'INNER JOIN supplier AS s ON pp.supplier_id=s.supplier_id ';
        $sql .= 'WHERE pp.pp_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_invoice_list($supplier_id = 0, $branch_id = 0) {
        $sql  = 'SELECT ci.*, s.supplier_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS s ON ci.supplier_id=s.supplier_id ';
        $sql .= 'WHERE ci.po_status=0 AND s.supplier_id='.$supplier_id.' AND ci.branch_id='.$branch_id.' ';
        $sql .= 'ORDER BY po_date DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_detail_by_pp_id($pp_id=0) {
        $sql  = 'SELECT * FROM payment_process_detail ';
        $sql .= 'WHERE pp_id = '.$pp_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_detail_by_id($pp_detail_id=0) {
        $sql  = 'SELECT * FROM payment_process_detail ';
        $sql .= 'WHERE pp_detail_id = '.$pp_detail_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_credit_invoice_check_view($branch_id=0, $supplier_id=0) {
        $sql  = 'SELECT ci.*, c.supplier_id, c.supplier_name, c.address AS supplier_address, ';
        $sql .= 'd.branch_id, d.branch_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN branch AS d ON ci.branch_id=d.branch_id ';
        $sql .= 'INNER JOIN supplier AS c ON ci.supplier_id=c.supplier_id ';
        $sql .= 'WHERE ci.po_status=0 AND d.branch_id='.$branch_id.' AND c.supplier_id='.$supplier_id.' ';
        $sql .= 'ORDER BY po_date DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pp_detail_by_pp_id($pp_id=0) {
        $sql  = 'SELECT ppd.*, ci.po_number, ci.total, ci.description ';
        $sql .= 'FROM payment_process_detail AS ppd ';
        $sql .= 'INNER JOIN credit_invoice AS ci ON ppd.credit_invoice_id=ci.credit_invoice_id ';
        $sql .= 'WHERE ppd.pp_id = '.$pp_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_total_po($in_str='') {
        $sql  = 'SELECT SUM(ci.total) AS total FROM credit_invoice AS ci ';
        $sql .= 'WHERE ci.credit_invoice_id IN('.$in_str.')';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_credit_invoice_by_id($id=0) {
        $sql  = 'SELECT ci.*, c.supplier_name, d.branch_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN branch AS d ON ci.branch_id=d.branch_id ';
        $sql .= 'INNER JOIN supplier AS c ON ci.supplier_id=c.supplier_id ';
        $sql .= 'WHERE ci.credit_invoice_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pp_detail_by_id($id=0) {
        $sql  = 'SELECT * FROM payment_process_detail ';
        $sql .= 'WHERE pp_detail_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_credit_invoice_file($clause_in='') {
        $sql  = 'SELECT * FROM credit_invoice_file AS ci ';
        $sql .= 'WHERE ci.credit_invoice_id IN('.$clause_in.')';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function delete_by_id($id){
        $ppdetail = $this->get_pp_detail_by_id($id);
        if ($ppdetail->num_rows()!=0){
            $row = $ppdetail->row();
            $credit_invoice_id = $row->credit_invoice_id;
            if ($credit_invoice_id != 0){
                /* update credit invoice */
                $dataup = array('po_status'=>0);
                $this->db->where('credit_invoice_id', $credit_invoice_id);
                $this->db->update('credit_invoice', $dataup);
            }
            // delete pp detail
            $this->db->where('pp_detail_id', $id);
            $this->db->delete('payment_process_detail');
            // update pp
            $detail = $this->get_detail_by_pp_id($row->pp_id);
            if ($detail->num_rows()!=0){
                $totalall = 0;
                foreach ($detail->result() as $value) {
                    $totalall = $totalall + $value->total;
                }
                $datapp = array(
                    'total' => $totalall
                );

                $this->db->where('pp_id', $row->pp_id);
                $this->db->update('payment_process', $datapp);
            } else {
                $datapp = array(
                    'total' => 0,
                    'pp_status' => 0
                );

                $this->db->where('pp_id', $row->pp_id);
                $this->db->update('payment_process', $datapp);
            }
        }
    }
    
    public function get_user_by_id($id = '') {
        $sql = 'SELECT * FROM users WHERE user_id =' . $id;
        $query = $this->db->query($sql);
        $fullname = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $fullname = $row->fullname;
        }
        return $fullname;
    }
    
    public function get_payment_process_file_by_pp_id($pp_id=0) {
        $sql  = 'SELECT * FROM payment_process_file ';
        $sql .= 'WHERE pp_id = '.$pp_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function update_file_name($id=0, $file='', $type='') {
        $data = array(
            'pp_id' => $id,
            'file_name'=> $file,
            'file_type'=> $type
        );
        $this->db->insert('payment_process_file', $data);
    }
    
    public function insert_ppgeneral() {
        $pp_id = $this->input->post('pp_id');
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        if ($unit == ''){
            $unit = 1;
        }
        $price = $this->general_model->change_decimal($this->input->post('price'));
        if ($price == ''){
            $price = 0;
        }
        $total = $this->general_model->change_decimal($this->input->post('total'));
        if ($total == ''){
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } else {
                $total = 0;
            }            
        } else {
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } 
        }
        
         // get data pp
        $data_pp = $this->get_pp_join_branch_by_id($pp_id);
        $branch_id = 0;
        $act_title = '';
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $branch_id = $row->branch_id;
            $act_title = $row->branch_name;
        }
        
        $data = array(
            'pp_id' => $pp_id,
            'act_title' => $act_title,
            'branch_id' => $branch_id,
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        $this->db->insert('payment_process_detail', $data);
        // update pp
        $detail = $this->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            $totalall = 0;
            foreach ($detail->result() as $value) {
                $totalall = $totalall + $value->total;
            }
            $datapp = array(
                'total' => $totalall
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function update_ppgeneral() {
        $pp_detail_id = $this->input->post('pp_detail_id');
        $pp_id = $this->input->post('pp_id');
        //$act_title = $this->input->post('act_title');
        //$branch_id = $this->input->post('branch_id');
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        if ($unit == ''){
            $unit = 1;
        }
        $price = $this->general_model->change_decimal($this->input->post('price'));
        if ($price == ''){
            $price = 0;
        }
        $total = $this->general_model->change_decimal($this->input->post('total'));
        if ($total == ''){
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } else {
                $total = 0;
            }            
        } else {
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } 
        }
        
        $data = array(
            //'act_title' => $act_title,
            //'branch_id' => $branch_id,
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        
        $this->db->where('pp_detail_id', $pp_detail_id);
        $this->db->update('payment_process_detail', $data);
        // update pp
        $detail = $this->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            $totalall = 0;
            foreach ($detail->result() as $value) {
                $totalall = $totalall + $value->total;
            }
            $datapp = array(
                'total' => $totalall
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function insert_ppsupplier() {
        $pp_id = $this->input->post('pp_id');
        $checked = $this->input->post('chk_invoice');
        
        $data_query = array();
        // get data pp
        $data_pp = $this->get_pp_join_branch_by_id($pp_id);
        $branch_id = 0;
        $act_title = '';
        $total_pp = 0;
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $branch_id = $row->branch_id;
            $act_title = $row->branch_name;
            $total_pp = $row->total;
        }
        
        if (isset($checked)){
            $i = 0;
            $total_all = 0;
            $credit_invoice_id = 0;
            $in_po = implode(',', $checked);
            $data_po = $this->get_po_by_in($in_po);
            if ($data_po->num_rows()!=0){
                foreach ($data_po->result() as $val) {
                    $total_all += $val->amount;
                    $data = array(
                        'pp_id' => $pp_id,
                        'act_title' => $act_title,
                        'branch_id' => $branch_id,
                        'job_order' => $val->po_number,
                        'description' => $val->description,
                        'unit' => 1,
                        'price' => $val->amount,
                        'total' => $val->amount,
                        'credit_invoice_id'=>$val->credit_invoice_id
                    );
                    $data_query[$i] = $data;
                    $i++;
                    /* update credit invoice */
                    $dataup = array('po_status'=>1);
                    $this->db->where('credit_invoice_id', $val->credit_invoice_id);
                    $this->db->update('credit_invoice', $dataup);
                }
                $this->db->insert_batch('payment_process_detail', $data_query);
            }
            /* update payment process */
            /* get data total pp supplier*/
            $total = $total_pp + $total_all;
            $datapp = array(
                'total' => $total
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function insert_ppexpense() {        
        $pp_id = $this->input->post('pp_id');
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        if ($unit == ''){
            $unit = 1;
        }
        $price = $this->general_model->change_decimal($this->input->post('price'));
        if ($price == ''){
            $price = 0;
        }
        $total = $this->general_model->change_decimal($this->input->post('total'));
        if ($total == ''){
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } else {
                $total = 0;
            }            
        } else {
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } 
        }
        
        // get data pp
        $data_pp = $this->get_pp_join_branch_by_id($pp_id);
        $branch_id = 0;
        $act_title = '';
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $branch_id = $row->branch_id;
            $act_title = $row->branch_name;
        }
        
        $data = array(
            'pp_id' => $pp_id,
            'act_title' => $act_title,
            'branch_id' => $branch_id,
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        $this->db->insert('payment_process_detail', $data);
        // update pp
        $detail = $this->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            $totalall = 0;
            foreach ($detail->result() as $value) {
                $totalall = $totalall + $value->total;
            }
            $datapp = array(
                'total' => $totalall
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function update_ppexpense() {    
        $pp_detail_id = $this->input->post('pp_detail_id');
        $pp_id = $this->input->post('pp_id');
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        if ($unit == ''){
            $unit = 1;
        }
        $price = $this->general_model->change_decimal($this->input->post('price'));
        if ($price == ''){
            $price = 0;
        }
        $total = $this->general_model->change_decimal($this->input->post('total'));
        if ($total == ''){
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } else {
                $total = 0;
            }            
        } else {
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } 
        }
        
        // get data pp
        $data_pp = $this->get_pp_join_branch_by_id($pp_id);
        $branch_id = 0;
        $act_title = '';
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $branch_id = $row->branch_id;
            $act_title = $row->branch_name;
        }
        
        $data = array(
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        $this->db->where('pp_detail_id', $pp_detail_id);
        $this->db->update('payment_process_detail', $data);
        // update pp
        $detail = $this->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            $totalall = 0;
            foreach ($detail->result() as $value) {
                $totalall = $totalall + $value->total;
            }
            $datapp = array(
                'total' => $totalall
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function insert_ppoutstanding() {        
        $pp_id = $this->input->post('pp_id');
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        if ($unit == ''){
            $unit = 1;
        }
        $price = $this->general_model->change_decimal($this->input->post('price'));
        if ($price == ''){
            $price = 0;
        }
        $total = $this->general_model->change_decimal($this->input->post('total'));
        if ($total == ''){
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } else {
                $total = 0;
            }            
        } else {
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } 
        }
        
        // get data pp
        $data_pp = $this->get_pp_join_branch_by_id($pp_id);
        $branch_id = 0;
        $act_title = '';
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $branch_id = $row->branch_id;
            $act_title = $row->branch_name;
        }
        
        $data = array(
            'pp_id' => $pp_id,
            'act_title' => $act_title,
            'branch_id' => $branch_id,
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        $this->db->insert('payment_process_detail', $data);
        // update pp
        $detail = $this->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            $totalall = 0;
            foreach ($detail->result() as $value) {
                $totalall = $totalall + $value->total;
            }
            $datapp = array(
                'total' => $totalall
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function update_ppoutstanding() {    
        $pp_detail_id = $this->input->post('pp_detail_id');
        $pp_id = $this->input->post('pp_id');
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        if ($unit == ''){
            $unit = 1;
        }
        $price = $this->general_model->change_decimal($this->input->post('price'));
        if ($price == ''){
            $price = 0;
        }
        $total = $this->general_model->change_decimal($this->input->post('total'));
        if ($total == ''){
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } else {
                $total = 0;
            }            
        } else {
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } 
        }
        
        // get data pp
        $data_pp = $this->get_pp_join_branch_by_id($pp_id);
        $branch_id = 0;
        $act_title = '';
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $branch_id = $row->branch_id;
            $act_title = $row->branch_name;
        }
        
        $data = array(
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        $this->db->where('pp_detail_id', $pp_detail_id);
        $this->db->update('payment_process_detail', $data);
        // update pp
        $detail = $this->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            $totalall = 0;
            foreach ($detail->result() as $value) {
                $totalall = $totalall + $value->total;
            }
            $datapp = array(
                'total' => $totalall
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function insert_ppproject() {        
        $pp_id = $this->input->post('pp_id');
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        if ($unit == ''){
            $unit = 1;
        }
        $price = $this->general_model->change_decimal($this->input->post('price'));
        if ($price == ''){
            $price = 0;
        }
        $total = $this->general_model->change_decimal($this->input->post('total'));
        if ($total == ''){
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } else {
                $total = 0;
            }            
        } else {
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } 
        }
        
        // get data pp
        $data_pp = $this->get_pp_join_branch_by_id($pp_id);
        $branch_id = 0;
        $act_title = '';
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $branch_id = $row->branch_id;
            $act_title = $row->branch_name;
        }
        
        $data = array(
            'pp_id' => $pp_id,
            'act_title' => $act_title,
            'branch_id' => $branch_id,
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        $this->db->insert('payment_process_detail', $data);
        // update pp
        $detail = $this->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            $totalall = 0;
            foreach ($detail->result() as $value) {
                $totalall = $totalall + $value->total;
            }
            $datapp = array(
                'total' => $totalall
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function update_ppproject() {    
        $pp_detail_id = $this->input->post('pp_detail_id');
        $pp_id = $this->input->post('pp_id');
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        if ($unit == ''){
            $unit = 1;
        }
        $price = $this->general_model->change_decimal($this->input->post('price'));
        if ($price == ''){
            $price = 0;
        }
        $total = $this->general_model->change_decimal($this->input->post('total'));
        if ($total == ''){
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } else {
                $total = 0;
            }            
        } else {
            if ($unit >= 1 && $price > 0){
                $total = $unit * $price;
            } 
        }
        
        // get data pp
        $data_pp = $this->get_pp_join_branch_by_id($pp_id);
        $branch_id = 0;
        $act_title = '';
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $branch_id = $row->branch_id;
            $act_title = $row->branch_name;
        }
        
        $data = array(
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        $this->db->where('pp_detail_id', $pp_detail_id);
        $this->db->update('payment_process_detail', $data);
        // update pp
        $detail = $this->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            $totalall = 0;
            foreach ($detail->result() as $value) {
                $totalall = $totalall + $value->total;
            }
            $datapp = array(
                'total' => $totalall
            );

            $this->db->where('pp_id', $pp_id);
            $this->db->update('payment_process', $datapp);
        }
    }
    
    public function get_po_by_in($in) {
        $sql  = 'SELECT * FROM credit_invoice ';
        $sql .= 'WHERE credit_invoice_id IN('.$in.')';
        $query = $this->db->query($sql);
        return $query;
    }
    
     public function get_pp_join_branch_by_id($id=0) {
        $sql  = 'SELECT pp.*, b.branch_name FROM payment_process AS pp ';
        $sql .= 'INNER JOIN branch AS b ON pp.branch_id=b.branch_id ';
        $sql .= 'WHERE pp.pp_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cash_request_number($cash_request_id=0) {
        $sql  = 'SELECT cash_request_number FROM cash_request ';
        $sql .= 'WHERE cash_request_id='.$cash_request_id;
        $query = $this->db->query($sql);
        $cash_request_number = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $cash_request_number = $row->cash_request_number;
        }
        return $cash_request_number;
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
    
    // update 28  July 2018
    public function get_detail_join_invoice($pp_id=0) {
        $sql  = 'SELECT ppd.*, ci.credit_invoice_number FROM payment_process_detail AS ppd ';
        $sql .= 'INNER JOIN credit_invoice AS ci ON ppd.credit_invoice_id=ci.credit_invoice_id ';
        $sql .= 'WHERE ppd.pp_id = '.$pp_id;
        $query = $this->db->query($sql);
        return $query;
    }
}
