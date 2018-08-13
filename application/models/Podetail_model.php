<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Podetail_model
 *
 * @author mchen
 */
class Podetail_model extends CI_Model {
    
    public function get_detail_by_id($id = 0) {
        $sql  = 'SELECT * FROM purchase_order_detail ';
        $sql .= 'WHERE po_detail_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_detail_by_poid($poid=0) {
        $sql  = 'SELECT * FROM purchase_order_detail ';
        $sql .= 'WHERE po_id='.$poid;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_purchase_order($id=0) {
        $sql  = 'SELECT po.*, c.supplier_name, c.address AS supplier_address, b.branch_name FROM purchase_order AS po ';
        $sql .= 'INNER JOIN supplier AS c ON po.supplier_id=c.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON po.branch_id=b.branch_id ';
        $sql .= 'WHERE po.po_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_sum_amount_by_po($po_id = 0) {
        $sql  = 'SELECT SUM(amount) AS total FROM purchase_order_detail ';
        $sql .= 'WHERE po_id='.$po_id;
        $query = $this->db->query($sql);
        $total = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $total = $row->total;
        }
        return $total;
    }
    
    public function insert_detail() {       
        $po_id = $this->input->post('po_id');
        $item_code = $this->input->post('item_code');
        $quantity = $this->input->post('quantity');
        $item_name = $this->input->post('item_name');
        $price = $this->general_model->change_decimal($this->input->post('price'));
        //$discount = $this->input->post('discount');
        
        $data = array(
            'po_id' => $po_id,
            'item_code' => $item_code,
            'quantity' => $quantity,
            'item_name' => $item_name,
            'price' => $price,
            'discount' => 0,
            'amount' => $price * $quantity
        );
        $this->db->insert('purchase_order_detail', $data);
        /* update total from purchase order */
        $total = $this->get_sum_amount_by_po($po_id);
        $this->update_purchase_order($po_id, $total);
    }

    public function update_detail($id) {
        $po_id = $this->input->post('po_id');
        $item_code = $this->input->post('item_code');
        $quantity = $this->input->post('quantity');
        $item_name = $this->input->post('item_name');
        $price = $this->input->post('price');
        //$discount = $this->input->post('discount');
        
        $data = array(
            'po_id' => $po_id,
            'item_code' => $item_code,
            'quantity' => $quantity,
            'item_name' => $item_name,
            'price' => $price,
            'discount' => 0,
            'amount' => $price * $quantity
        );

        $this->db->where('po_detail_id', $id);
        $this->db->update('purchase_order_detail', $data);
        /* update total from purchase order */
        $total = $this->get_sum_amount_by_po($po_id);
        $this->update_purchase_order($po_id, $total);
    }
    
    public function delete_approve($id){
        $detail = $this->get_detail_by_id($id);
        $po_id = 0;
        if ($detail->num_rows()!=0){
            $row = $detail->row();
            $po_id = $row->po_id;
        }
        $this->db->where('po_detail_id', $id);
        $this->db->delete('purchase_order_detail');
        $enc_po_id = $this->general_model->encrypt_value($po_id);
        $back = '/podetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_02. '/'.$enc_po_id;
        redirect($back);
    }
    
    public function delete_by_id($id) {
        $this->db->where('po_detail_id', $id);
        $this->db->delete('purchase_order_detail');
    }
    
    public function update_purchase_order($id=0, $total=0) {     
        $data = array(
            'total' => $total,
            'po_status' => 1
        );

        $this->db->where('po_id', $id);
        $this->db->update('purchase_order', $data);
    }
}
