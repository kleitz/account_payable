<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Supplier_report_model
 *
 * @author Hendra McHen
 */
class Supplier_report_model extends CI_Model {
    public $status_style = array(
     '<span class="label label-warning">In Progress</span>', 
     '<span class="label label-primary">To be Paid</span>', 
     '<span class="label label-success">Closed</span>'
    );
    
    public function get_supplier_report($start_date='', $end_date='') {
        $sql  = 'SELECT s.supplier_id,  s.supplier_name, b.branch_name, pp.pp_date, pp.total ';
        $sql .= 'FROM payment_process AS pp ';
        $sql .= 'INNER JOIN supplier AS s ON pp.supplier_id=s.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON pp.branch_id=b.branch_id ';
        $sql .= 'WHERE '; /* pp.pp_status < 4 AND pp_status = 3 (approved) */ 
        //if ($start_date != '' && $end_date != ''){
            //$sql .= ' AND pp.pp_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        //}
        $sql .= ' pp.pp_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'ORDER BY s.supplier_name ASC ';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_detail($supplier_id = 0) {
        $sql  = 'SELECT ci.invoice_date,  s.supplier_name, b.branch_name, ';
        $sql .= 'ci.amount, d.pp_id, pp.pp_number, ci.po_status FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS s ON ci.supplier_id=s.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON ci.branch_id=b.branch_id ';
        $sql .= 'INNER JOIN payment_process_detail AS d ON ci.credit_invoice_id=d.credit_invoice_id ';
        $sql .= 'INNER JOIN payment_process AS pp ON d.pp_id=pp.pp_id ';
        $sql .= 'WHERE s.supplier_id='.$supplier_id;
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_supplier() {
        $sql  = 'SELECT  DISTINCT s.supplier_name  FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS s ON ci.supplier_id=s.supplier_id ';
        $sql .= 'WHERE ci.po_status < 2 ';
        $sql .= 'ORDER BY s.supplier_name ASC ';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_name($supplier_id = 0) {
        $sql  = 'SELECT  supplier_name  FROM supplier ';
        $sql .= 'WHERE supplier_id='.$supplier_id;
        $query = $this->db->query($sql);
        $supplier_name = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $supplier_name = $row->supplier_name;
        }
        return $supplier_name;
    }
    
    public function get_branch() {
        $sql  = 'SELECT  branch_name  FROM branch ';
        $sql .= 'ORDER BY branch_id ASC ';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_from_invoice() {
        $sql  = 'SELECT ci.*, s.supplier_name, b.branch_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS s ON ci.supplier_id=s.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON ci.branch_id=b.branch_id ';
        $sql .= 'WHERE ci.po_status !=2 ';
        $sql .= 'ORDER BY s.supplier_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_outlet($outlet=0, $period_date='') {
        $sql  = 'SELECT s.supplier_name, ci.invoice_date, ci.amount ';
        $sql .= 'FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS s ON ci.supplier_id=s.supplier_id ';
        $sql .= 'WHERE ci.branch_id='.$outlet.' ';//AND ci.invoice_date BETWEEN "'.$period_date.'" AND  "2017-12-31"';
        $query = $this->db->query($sql);
        return $query;
    }
}