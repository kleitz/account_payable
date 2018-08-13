<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Supplier_model
 *
 * @author mchen
 */
class Supplier_model extends CI_Model {
    
    public function get_supplier_list() {
        $sql  = 'SELECT * FROM supplier ';
        $sql .= 'ORDER BY supplier_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_by_id($id=0) {
        $sql  = 'SELECT * FROM supplier ';
        $sql .= 'WHERE supplier_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_from_invoice() {
        $sql  = 'SELECT ci.*, s.supplier_name, b.branch_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS s ON ci.supplier_id=s.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON ci.branch_id=b.branch_id ';
        $sql .= 'WHERE ci.po_status = 0 ';
        $sql .= 'ORDER BY s.supplier_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_supplier($data) {
        $this->db->insert('supplier', $data);

        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $supplier_name = $this->input->post('supplier_name');
        $address = $this->input->post('address');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');            
        $account_name = $this->input->post('account_name');
        $account_number = $this->input->post('account_number');
        $bank_name = $this->input->post('bank_name');
        $supplier_type = $this->input->post('supplier_type');
        $supplier_opt = array('Cash Supplier', 'Credit Supplier');
        $log_desc = array(
            'supplier name' => $supplier_name,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'account name' => $account_name,
            'account number' => $account_number,
            'bank name' => $bank_name,
            'supplier type' => $supplier_opt[$supplier_type]
        );
        $this->log_activity_model->insert_log('Supplier', 'Add', $log_desc);
        /*== end log ==*/
        
        return $this->db->insert_id();
    }
    
    public function update_supplier($where, $data) {
        $this->db->update('supplier', $data, $where);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $supplier_name = $this->input->post('supplier_name');
        $address = $this->input->post('address');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $account_name = $this->input->post('account_name');
        $account_number = $this->input->post('account_number');
        $bank_name = $this->input->post('bank_name');
        $supplier_type = $this->input->post('supplier_type');
        $supplier_opt = array('Cash Supplier', 'Credit Supplier');
        $log_desc = array(
            'supplier name' => $supplier_name,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'account name' => $account_name,
            'account number' => $account_number,
            'bank name' => $bank_name,
            'supplier type' => $supplier_opt[$supplier_type]
        );
        $this->log_activity_model->insert_log('Supplier', 'Edit', $log_desc);
        /*== end log ==*/
        return $this->db->affected_rows();
    }
    
    public function delete_by_id($id) {
        $supplier = $this->get_supplier_by_id($id);
        if ($supplier->num_rows()!=0){
            $row = $supplier->row();
            /*== insert to log activity ==*/
            $this->load->model('log_activity_model');
            $supplier_type = array('Cash Supplier', 'Credit Supplier');
            $log_desc = array(
                'supplier name' => $row->supplier_name,
                'address' => $row->address,
                'email' => $row->email,
                'phone' => $row->phone,
                'account name' => $row->account_name,
                'account number' => $row->account_number,
                'bank name' => $row->bank_name,
                'supplier type' => $supplier_type[$row->supplier_type]
            );
            $this->log_activity_model->insert_log('Supplier', 'Delete', $log_desc);
            /*== end log ==*/
        }
        $this->db->where('supplier_id', $id);
        $this->db->delete('supplier');
    }
}
