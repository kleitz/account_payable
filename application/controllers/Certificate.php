<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Certificate
 *
 * @author Hendra McHen
 */
class Certificate extends CI_Controller {

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
            if (($module == $this->asik_model->report_01) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                /* end privilege */
                $this->load->helper('form');
                $data['pagecode'] = $string;
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                
                $branch = $this->get_branch_list();
                $data['branch'] = $branch;
                
                $array_tbl = array(array());
                $array_tbl[0][0] = '<strong>Opening Balance</strong>';
                $array_tbl[1][0] = '<strong>Receipt in Bank</strong>';
                $array_tbl[2][0] = ' # From Revenue Bank';
                $array_tbl[3][0] = ' # Borrow Received';
                $array_tbl[4][0] = '<strong>Payment from Bank</strong>';
                $array_tbl[5][0] = ' # Expenses';
                $array_tbl[6][0] = ' # Outstanding';
                $array_tbl[7][0] = ' # Borrow Given';
                $array_tbl[8][0] = ' # Borrow Returned';
                $array_tbl[9][0] = '<strong>Closing Balance</strong>';
                
                if ($button == 0){
                    $opening = $this->get_closing_balance($start_date, $end_date);
                    $revenue = $this->get_from_revenue_bank($start_date, $end_date);
                    $received = $this->get_received($start_date, $end_date);
                    $expenses = $this->get_expense($start_date, $end_date);
                    $outstanding = $this->get_outstanding($start_date, $end_date);
                    $borrow_given = $this->get_borrow_given($start_date, $end_date);
                    $borrow_returned = $this->get_borrow_returned($start_date, $end_date);
                } else {
                    $year = date('Y');
                    $month = date('m');
                    $day = date('d');
                    switch ($button) {
                        case 1:
                            $start_date = date('Y-m-d');
                            $end_date = date('Y-m-d');
                            break;
                        case 2:
                            $start_date = date('Y-m-d',strtotime("-1 days"));
                            $end_date = date('Y-m-d',strtotime("-1 days"));
                            break;
                        case 3:
                            $signupdate = $year.'-'.$month.'-'.$day;
                            $signupweek = date("W",strtotime($signupdate));

                            $dto = new DateTime();
                            $start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                            $end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                            break;
                        case 4:
                            $start_date = $year.'-'.$month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 5:
                            if ($month == 1){
                                $last_month = '12';
                                $year = $year - 1;
                            } else {
                                $last_month = $month - 1;
                            }
                            $start_date = $year.'-0'.$last_month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                    }
                    $opening = $this->get_closing_balance($start_date, $end_date);
                    $revenue = $this->get_from_revenue_bank($start_date, $end_date);
                    $received = $this->get_received($start_date, $end_date);
                    $expenses = $this->get_expense($start_date, $end_date);
                    $outstanding = $this->get_outstanding($start_date, $end_date);
                    $borrow_given = $this->get_borrow_given($start_date, $end_date);
                    $borrow_returned = $this->get_borrow_returned($start_date, $end_date);
                }
                /*============================================================*/            
                //==================== Opening Balance =====================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[0][$k] = 0;
                        $k++;
                    }
                    $array_tbl[0][$k] = 0;
                    if ($opening->num_rows()!=0){
                        foreach ($opening->result() as $value) {
                            $k = 1;
                            foreach ($branch as $b) {
                                if ($value->branch_name == $b){
                                    $array_tbl[0][$k] = $value->total;
                                }
                                $k++;
                            }
                            $array_tbl[0][$k] = 0;
                        }
                    }
                    
                    // ==================== Receipt in Bank ====================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[1][$k] = 0;
                        $k++;
                    }
                    $array_tbl[1][$k] = 0;
                    
                    // ==================== from revenue bank ==================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[2][$k] = 0;
                        $k++;
                    }
                    $array_tbl[2][$k] = 0;
                    if ($revenue->num_rows()!=0){
                        foreach ($revenue->result() as $value) {
                            $k = 1;
                            foreach ($branch as $b) {
                                if ($value->branch_name == $b){
                                    $array_tbl[2][$k] = $value->total;
                                }
                                $k++;
                            }
                            $array_tbl[2][$k] = 0;
                        }
                    }
                    
                    // ==================== borrow received ====================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[3][$k] = 0;
                        $k++;
                    }
                    $array_tbl[3][$k] = 0;
                    if ($received->num_rows()!=0){
                        foreach ($received->result() as $value) {
                            $k = 1;
                            foreach ($branch as $b) {
                                if ($value->branch_name == $b){
                                    $array_tbl[3][$k] = $value->total;
                                }
                                $k++;
                            }
                            $array_tbl[3][$k] = 0;
                        }
                    }
                    
                    // ==================== Get Total Receipt in Bank ==========
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[1][$k] = $array_tbl[2][$k] + $array_tbl[3][$k];
                        $k++;
                    }
                    $array_tbl[1][$k] = $array_tbl[2][$k] + $array_tbl[3][$k];
                    // =========================================================
                    
                    
                    // ==================== Payment from Bank ==================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[4][$k] = $k;
                        $k++;
                    }
                    $array_tbl[4][$k] = $k;
                    
                    // ======================== Expenses =======================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[5][$k] = 0;
                        $k++;
                    }
                    $array_tbl[5][$k] = 0;
                    if ($expenses->num_rows()!=0){
                        foreach ($expenses->result() as $value) {
                            $k = 1;
                            foreach ($branch as $b) {
                                if ($value->branch_name == $b){
                                    $array_tbl[5][$k] = $value->total;
                                }
                                $k++;
                            }
                            $array_tbl[5][$k] = 0;
                        }
                    }
                    
                    // ======================= Outstanding =====================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[6][$k] = 0;
                        $k++;
                    }
                    $array_tbl[6][$k] = 0;
                    if ($outstanding->num_rows()!=0){
                        foreach ($outstanding->result() as $value) {
                            $k = 1;
                            foreach ($branch as $b) {
                                if ($value->branch_name == $b){
                                    $array_tbl[6][$k] = $value->total;
                                }
                                $k++;
                            }
                            $array_tbl[6][$k] = 0;
                        }
                    }
                    
                    // ======================Borrow Given ======================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[7][$k] = 0;
                        $k++;
                    }
                    $array_tbl[7][$k] = 0;
                    if ($borrow_given->num_rows()!=0){
                        foreach ($borrow_given->result() as $value) {
                            $k = 1;
                            foreach ($branch as $b) {
                                if ($value->branch_name == $b){
                                    $array_tbl[7][$k] = $value->total;
                                }
                                $k++;
                            }
                            $array_tbl[7][$k] = 0;
                        }
                    }
                    
                    // ===================== Borrow Return =====================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[8][$k] = 0;
                        $k++;
                    }
                    $array_tbl[8][$k] = 0;
                    if ($borrow_returned->num_rows()!=0){
                        foreach ($borrow_returned->result() as $value) {
                            $k = 1;
                            foreach ($branch as $b) {
                                if ($value->branch_name == $b){
                                    $array_tbl[8][$k] = $value->total;
                                }
                                $k++;
                            }
                            $array_tbl[8][$k] = 0;
                        }
                    }
                    
                    // ==================== Get Total Payment from Bank ========
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[4][$k] = $array_tbl[5][$k] + $array_tbl[6][$k] + $array_tbl[7][$k] + $array_tbl[8][$k];
                        $k++;
                    }
                    $array_tbl[4][$k] = $array_tbl[5][$k] + $array_tbl[6][$k] + $array_tbl[7][$k] + $array_tbl[8][$k];
                    //==========================================================
                    
                    // ==================== Closing Balance ====================
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[9][$k] = 0;
                        $k++;
                    }
                    $array_tbl[9][$k] = 0;
                    // ==================== Get total Closing Balance ========
                    $k = 1;
                    foreach ($branch as $b) {
                        $array_tbl[9][$k] = $array_tbl[0][$k] + $array_tbl[1][$k] - $array_tbl[4][$k];
                        $k++;
                    }
                    $array_tbl[9][$k] = $array_tbl[0][$k] + $array_tbl[1][$k] - $array_tbl[4][$k];
                /*============================================================*/ 
                
                $data['array_tbl'] = $array_tbl; 
                
                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Summary Report';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };';
                    
                    $strtotal = '';
                $footer_total .= $strtotal . '}';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Summary Report', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/summary_report.php';
                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
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
    
    public function get_expense($start_date='', $end_date='') {
        $sql  = 'SELECT b.branch_name, SUM(ex.amount) AS total FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_from_revenue_bank($start_date='', $end_date='') {
        $sql = 'SELECT SUM(op.amount)AS total, b.branch_name FROM opening_balance AS op
        INNER JOIN branch AS b ON b.branch_id=op.branch_id
        WHERE op.opening_balance_date BETWEEN "'.$start_date.'" AND "'.$end_date.' "
        GROUP BY op.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_outstanding($start_date='', $end_date='') {
        $sql  = 'SELECT SUM(os.amount) AS total, b.branch_name  FROM outstanding AS os 
        INNER JOIN branch AS b ON os.branch_id=b.branch_id 
        WHERE os.outstanding_status = 0 
        AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY os.branch_id ORDER BY b.branch_id';        
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_received($start_date='', $end_date='') {
        $sql  = 'SELECT SUM(cr.amount) AS total, b.branch_name FROM cash_receive AS cr 
        INNER JOIN branch AS b ON cr.branch_id=b.branch_id 
        INNER JOIN account AS a ON cr.account_from=a.account_id 
        WHERE cr.cash_receive_status < 2 
        AND cr.cash_receive_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY cr.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_borrow_given($start_date='', $end_date='') {
        $sql = 'SELECT SUM(cr.amount) AS total, b.branch_name FROM cash_receive AS cr 
        INNER JOIN account AS a ON cr.account_from=a.account_id 
        INNER JOIN branch AS b ON a.branch_id=b.branch_id 
        WHERE cr.cash_receive_status < 2 
        AND cr.cash_receive_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY cr.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_borrow_returned($start_date='', $end_date='') {
        $sql = 'SELECT SUM(ct.amount) AS total, b.branch_name FROM cash_return AS ct
        INNER JOIN branch AS b ON b.branch_id=ct.branch_id
        WHERE ct.cash_return_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY ct.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_closing_balance($start_date='', $end_date='') {
        $sql = 'SELECT SUM(cb.amount)AS total, b.branch_name FROM closing_balance AS cb
        INNER JOIN branch AS b ON b.branch_id=cb.branch_id
        WHERE cb.closing_balance_date BETWEEN "'.$start_date.'" AND "'.$end_date.' "
        GROUP BY cb.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
}