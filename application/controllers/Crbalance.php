<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Crbalance
 *
 * @author Hendra McHen
 */
class Crbalance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('supplier_report_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';
    
    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_11) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                $data['list'] = $this->get_cash_request_summary();
                /* ===== start datatable ===== */
                $data['datatable_title'] = '';
                $data['footer_total'] = '';
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Cash Request Summary', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/crbalance_report.php';
                
                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_cash_request_summary() {
        $sql = 'SELECT cr.employee_id, e.full_name, SUM(crb.debit)AS debit_total, 
        SUM(crb.credit) AS credit_total FROM cash_request_balance AS crb
        INNER JOIN cash_request AS cr ON cr.cash_request_id=crb.cash_request_id
        INNER JOIN employee AS e ON e.employee_id=cr.employee_id
        GROUP BY cr.employee_id
        ORDER BY e.full_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cashrequest() {
        $sql = 'SELECT e.full_name, SUM(cr.amount) AS total FROM cash_request AS cr
        INNER JOIN employee AS e ON cr.employee_id=e.employee_id
        GROUP BY cr.employee_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function detail($string = '', $employee_id=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                
                $data['detail'] = $this->get_balance_detail($employee_id);
                $employee_name = $this->get_employee_name($employee_id);
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Cash Request Balance';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
    
                    alltotal1 = api
                                .column(5, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(5).footer() ).html(
                                numeral(alltotal1).format("0,0.00")
                        );
                        
                    alltotal2 = api
                                .column(6, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(6).footer() ).html(
                                numeral(alltotal2).format("0,0.00")
                        );
                    
                    alltotal3 = api
                                .column(7, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(7).footer() ).html(
                                numeral(alltotal3).format("0,0.00")
                        );
                }';
                /* ===== end datatable ===== */
                $data['footer_total'] = $footer_total;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header($employee_name, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/crbalance_detail.php';                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_cr_balance($employee_id=0, $branch=array()) {
        $sql = 'SELECT cr.cash_request_number, cr.cash_request_date, e.full_name, b.branch_name, 
        SUM(crb.debit) AS debit_total, SUM(crb.credit) AS credit_total 
        FROM cash_request_balance AS crb
        INNER JOIN cash_request AS cr ON crb.cash_request_id=cr.cash_request_id
        INNER JOIN employee AS e ON cr.employee_id=e.employee_id
        INNER JOIN branch AS b ON cr.branch_id=b.branch_id
        WHERE cr.employee_id='.$employee_id.'    
        GROUP BY cr.branch_id 
        ORDER BY cr.branch_id';
        $query = $this->db->query($sql);
        $q = $this->db->query($sql);
        
        $array_tbl = array(array());
        $array_tbl[0][0] = 'A';
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[0][$k] = 0;
            $k++;
        }
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $k = 1;
                $array_tbl[0][0] = $value->full_name;
                foreach ($branch as $b) {
                    if ($b == $value->branch_name){
                        $array_tbl[0][$k] = $value->debit_total - $value->credit_total;
                    }
                    $k++;
                }
            }
        }
        return $array_tbl;
    }
    
    public function get_branch_list() {
        $sql  = 'SELECT DISTINCT b.branch_name FROM branch AS b 
        ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        $branch = array();
        if ($query->num_rows() != 0){
            foreach ($query->result() as $value) {
                $branch[] = $value->branch_name;
            }
        }
        return $branch;
    }
    
    public function get_branch_name($branch_id=0) {
        $sql  = 'SELECT branch_name FROM branch WHERE branch_id='.$branch_id;
        $query = $this->db->query($sql);
        $branch = '';
        if ($query->num_rows() != 0){
            $row = $query->row();
            $branch = $row->branch_name;
        }
        return $branch;
    }
    
    public function get_branch_id() {
        $sql  = 'SELECT DISTINCT b.branch_id FROM branch AS b 
        ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        $branch = array();
        if ($query->num_rows() != 0){
            foreach ($query->result() as $value) {
                $branch[] = $value->branch_id;
            }
        }
        return $branch;
    }
    
    public function get_employee_list() {
        $sql  = 'SELECT employee_id FROM employee ';
        $sql .= 'ORDER BY full_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_employee_name($employee_id=0) {
        $sql  = 'SELECT full_name FROM employee ';
        $sql .= 'WHERE employee_id='.$employee_id;
        $query = $this->db->query($sql);
        $name = '';
        if ($query->num_rows() != 0){
            $row = $query->row();
            $name = $row->full_name;
        }
        return $name;
    }
    
    public function get_balance_detail($employee_id=0) {
        $sql  = 'SELECT crb.cash_request_id, crb.balance_date, cr.cash_request_number, b.branch_name, cr.cash_request_status, 
        SUM(crb.debit) AS debit_total,  SUM(crb.credit) AS credit_total, cr.employee_id FROM cash_request_balance AS crb
        INNER JOIN cash_request AS cr ON cr.cash_request_id=crb.cash_request_id
        INNER JOIN branch AS b ON cr.branch_id=b.branch_id
        WHERE cr.employee_id='.$employee_id.' 
        GROUP BY cr.cash_request_id
        ORDER BY cr.cash_request_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function mdetail($string = '', $cash_request_id=0, $employee_id=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                
                $data['detail'] = $this->get_more_detail($cash_request_id);
                $employee_name = '<a href="'. site_url('crbalance/detail/'.$string.'/'.$employee_id).'">'.$this->get_employee_name($employee_id).'</a>';
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Cash Request Balance';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
    
                    alltotal1 = api
                                .column(2, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(2).footer() ).html(
                                numeral(alltotal1).format("0,0.00")
                        );
                        
                    alltotal2 = api
                                .column(3, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(3).footer() ).html(
                                numeral(alltotal2).format("0,0.00")
                        );
                    
                    alltotal3 = api
                                .column(4, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(4).footer() ).html(
                                numeral(alltotal3).format("0,0.00")
                        );
                }';
                /* ===== end datatable ===== */
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header($employee_name, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/crbalance_more_detail.php';                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_more_detail($cash_request_id=0) {
        $sql  = 'SELECT * FROM cash_request_balance WHERE cash_request_id='.$cash_request_id.' ';
        $sql .= 'ORDER BY balance_date';
        $query = $this->db->query($sql);
        return $query;
    }
}
