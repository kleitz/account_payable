<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Purchase_order_model
 *
 * @author mchen
 */
class Credit_invoice_model extends CI_Model {
    public $po_status = array("In Progress", "To be Paid", "Closed");
    
    public $status_style = array(
     '<span class="label label-warning">In Progress</span>', 
     '<span class="label label-primary">To be Paid</span>', 
     '<span class="label label-success">Closed</span>'
    );
    public $inprogress = 0;
    public $tobepaid= 1;
    public $closed = 2;
    
    public function get_credit_invoice_list($start_date='', $end_date='', $field_search='', $keyword='', $date_search=0) {
        $field_date = 'po_date';
        switch ($date_search) {
            case 0:
                $field_date = 'po_date';
                break;
            case 1:
                $field_date = 'invoice_date';
                break;
            case 2:
                $field_date = 'receive_date';
                break;
        }
        $sql  = 'SELECT ci.*, c.supplier_name, b.branch_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS c ON ci.supplier_id=c.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON ci.branch_id=b.branch_id ';
        $sql .= 'WHERE  ';
        if ($start_date != '' && $end_date != ''){
            $sql .= ' ci.'.$field_date.' BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            if ($keyword != ''){
                $sql .= ' AND '.$field_search.' LIKE "%'.$keyword.'%" ';
            }
        } else {
            if ($keyword != ''){
                $sql .= ' '.$field_search.' LIKE "%'.$keyword.'%" ';
            } else {
                $sql .= ' ci.'.$field_date.' BETWEEN "" AND "" ';
            }
        }
        
        $sql .= 'ORDER BY last_update DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pp_detail($start_date='', $end_date='', $field_search='', $keyword='', $date_search=0) {
        $creditinvoice = $this->get_credit_invoice_list($start_date, $end_date, $field_search, $keyword, $date_search);
        $arr = array();
        if ($creditinvoice->num_rows()!=0){
            $in_ci = '';
            foreach ($creditinvoice->result() as $value) {
                $in_ci .= $value->credit_invoice_id.',';
            }
            $inci = substr($in_ci, 0, strlen($in_ci)-1);
            $sql = 'SELECT * FROM payment_process_detail WHERE credit_invoice_id IN('.$inci.')';
            $query = $this->db->query($sql);
            if ($query->num_rows()!=0){
                foreach ($query->result() as $value) {
                    $arr[$value->credit_invoice_id] = $value->pp_id;
                }
            }
            
        }
        return $arr;
    }
    
    public function get_credit_invoice_by_datenow() {
        $datenow = date('Y-m-d');
        $sql  = 'SELECT ci.*, c.supplier_name, b.branch_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS c ON ci.supplier_id=c.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON ci.branch_id=b.branch_id ';
        $sql .= 'WHERE ci.last_update LIKE "'.$datenow.'%" AND ci.po_status=0';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_credit_invoice_by_id($id=0) {
        $sql  = 'SELECT ci.*, c.supplier_name, b.branch_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS c ON ci.supplier_id=c.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON ci.branch_id=b.branch_id ';
        $sql .= 'WHERE ci.credit_invoice_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_credit_invoice_d_by_id($id=0) {
        $sql  = 'SELECT ci.*, c.supplier_name, c.address AS supplier_address FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS c ON ci.supplier_id=c.supplier_id ';
        $sql .= 'WHERE ci.credit_invoice_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_last_record() {
        $sql = 'SELECT * FROM credit_invoice ORDER BY last_update DESC LIMIT 1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_total_detail($credit_invoice_id = 0) {
        $sql  = 'SELECT SUM(amount) AS total_detail FROM credit_invoice_detail ';
        $sql .= 'WHERE credit_invoice_id='.$credit_invoice_id;
        $query = $this->db->query($sql);
        $total = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $total = $row->total_detail;
        }
        return $total;
    }
    
    public function get_file_by_credit_id($credit_id=0) {
        $sql  = 'SELECT * FROM credit_invoice_file ';
        $sql .= 'WHERE credit_invoice_id = '.$credit_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_credit_invoice() {
        $credit_invoice_number = $this->input->post('credit_invoice_number');
        $po_number = $this->input->post('po_number');
        $po_date = $this->input->post('po_date');
        $invoice = $this->input->post('invoice');
        $invoice_date = $this->input->post('invoice_date');
        $receive_no = $this->input->post('receive_no');
        $receive_date = $this->input->post('receive_date');
        $supplier_id = $this->input->post('supplier_id');
        $branch_id = $this->input->post('branch_id');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        
        $data = array(
            'credit_invoice_number' => $credit_invoice_number,
            'po_number' => $po_number,
            'po_date' => $po_date,
            'invoice' => $invoice,
            'invoice_date' => $invoice_date,
            'receive_no' => $receive_no,
            'receive_date' => $receive_date,
            'supplier_id' => $supplier_id,
            'branch_id' => $branch_id,
            'description' => $description,
            'amount' => $amount,
            'username' => $this->session->userdata('username'),
            'po_status' => $this->inprogress
        );
        $this->db->insert('credit_invoice', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'credit_invoice_number' => $credit_invoice_number,
            'po_number' => $po_number,
            'po_date' => $po_date,
            'invoice' => $invoice,
            'invoice_date' => $invoice_date,
            'receive_no' => $receive_no,
            'receive_date' => $receive_date,
            'supplier_id' => $supplier_id,
            'branch_id' => $branch_id,
            'description' => $description,
            'amount' => $amount
        );
        $this->log_activity_model->insert_log('Credit Invoice', 'Add', $log_desc);
        /*== end log ==*/
    }

    public function update_credit_invoice($id) {
        $credit_invoice_number = $this->input->post('credit_invoice_number');
        $po_number = $this->input->post('po_number');
        $po_date = $this->input->post('po_date');
        $invoice = $this->input->post('invoice');
        $invoice_date = $this->input->post('invoice_date');
        $receive_no = $this->input->post('receive_no');
        $receive_date = $this->input->post('receive_date');
        $supplier_id = $this->input->post('supplier_id');
        $branch_id = $this->input->post('branch_id');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        
        $data = array(
            'credit_invoice_number' => $credit_invoice_number,
            'po_number' => $po_number,
            'po_date' => $po_date,
            'invoice' => $invoice,
            'invoice_date' => $invoice_date,
            'receive_no' => $receive_no,
            'receive_date' => $receive_date,
            'supplier_id' => $supplier_id,
            'branch_id' => $branch_id,
            'description' => $description,
            'amount' => $amount,
            'username' => $this->session->userdata('username')
        );

        $this->db->where('credit_invoice_id', $id);
        $this->db->update('credit_invoice', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'credit_invoice_number' => $credit_invoice_number,
            'po_number' => $po_number,
            'po_date' => $po_date,
            'invoice' => $invoice,
            'invoice_date' => $invoice_date,
            'receive_no' => $receive_no,
            'receive_date' => $receive_date,
            'supplier_id' => $supplier_id,
            'branch_id' => $branch_id,
            'description' => $description,
            'amount' => $amount
        );
        $this->log_activity_model->insert_log('Purchase Order Note', 'Edit', $log_desc);
        /*== end log ==*/
    }
    
    public function update_file_name($id=0, $file='', $type='') {
        $data = array(
            'credit_invoice_id' => $id,
            'file_name'=> $file,
            'file_type'=> $type
        );
        $this->db->insert('credit_invoice_file', $data);
    }
    
    public function delete_by_id($id) {
        $po = $this->get_credit_invoice_by_id($id);
        if ($po->num_rows()!=0){
            $row = $po->row();
            /*== insert to log activity ==*/
            $this->load->model('log_activity_model');
            $log_desc = array(
                'po_date' => $row->po_date,
                'po_number' => $row->po_number,
                'supplier_id' => $row->supplier_id,
                'invoice' => $row->invoice,
                'description' => $row->description
            );
            $this->log_activity_model->insert_log('Credit Invoice', 'Delete', $log_desc);
            /*== end log ==*/
        }
        
        $this->db->where('credit_invoice_id', $id);
        $this->db->delete('credit_invoice');
    }
}
