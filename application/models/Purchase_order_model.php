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
class Purchase_order_model extends CI_Model {
    public $po_status = array("Draft", "In Progress", "Closed");
    public $draft = 0;
    public $inprogress = 1;
    public $closed = 2;
    public function get_purchase_order_list($start_date='', $end_date='', $field_search='', $keyword='') {
        $sql  = 'SELECT po.*, c.supplier_name, b.branch_name FROM purchase_order AS po ';
        $sql .= 'INNER JOIN supplier AS c ON po.supplier_id=c.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON po.branch_id=b.branch_id ';
        $sql .= 'WHERE  ';
        if ($start_date != '' && $end_date != ''){
            $sql .= ' po.po_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            if ($keyword != ''){
                $sql .= ' AND '.$field_search.' LIKE "%'.$keyword.'%" ';
            }
        } else {
            if ($keyword != ''){
                $sql .= ' '.$field_search.' LIKE "%'.$keyword.'%" ';
            } else {
                $sql .= ' po.po_date BETWEEN "" AND "" ';
            }
        }
        
        $sql .= 'ORDER BY last_update DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_purchase_order_by_id($id=0) {
        $sql  = 'SELECT po.*, c.supplier_name, b.branch_name FROM purchase_order AS po ';
        $sql .= 'INNER JOIN supplier AS c ON po.supplier_id=c.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON po.branch_id=b.branch_id ';
        $sql .= 'WHERE po.po_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_purchase_order_d_by_id($id=0) {
        $sql  = 'SELECT po.*, c.supplier_name, c.address AS supplier_address FROM purchase_order AS po ';
        $sql .= 'INNER JOIN supplier AS c ON po.supplier_id=c.supplier_id ';
        $sql .= 'WHERE po.po_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_last_record() {
        $sql = 'SELECT * FROM purchase_order ORDER BY last_update DESC LIMIT 1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_total_detail($po_id = 0) {
        $sql  = 'SELECT SUM(amount) AS total_detail FROM purchase_order_detail ';
        $sql .= 'WHERE po_id='.$po_id;
        $query = $this->db->query($sql);
        $total = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $total = $row->total_detail;
        }
        return $total;
    }
    
    public function insert_purchase_order() {
        $po_trans = $this->input->post('po_trans');
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
            'po_trans' => $po_trans,
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
        $this->db->insert('purchase_order', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'po_trans' => $po_trans,
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

    public function update_purchase_order($id) {
        $po_trans = $this->input->post('po_trans');
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
            'po_trans' => $po_trans,
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

        $this->db->where('po_id', $id);
        $this->db->update('purchase_order', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'po_trans' => $po_trans,
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
    
    public function update_file_name($id=0, $field_name='', $filename='') {
        $data = array($field_name => $filename);
        $this->db->where('po_id', $id);
        $this->db->update('purchase_order', $data);
    }
    
    public function delete_by_id($id) {
        $po = $this->get_purchase_order_by_id($id);
        if ($po->num_rows()!=0){
            $row = $po->row();
            if (isset($row->file_po)){
                unlink('./assets/credit_invoice/po/' . $row->file_po);
            }
            if (isset($row->file_invoice)){
                unlink('./assets/credit_invoice/invoice/' . $row->file_invoice);
            }
            if (isset($row->file_upreceive)){
                unlink('./assets/credit_invoice/upreceive/' . $row->file_upreceive);
            }
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
        
        $this->db->where('po_id', $id);
        $this->db->delete('purchase_order');
    }
}
