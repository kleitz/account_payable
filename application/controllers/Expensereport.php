<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Expensereport
 *
 * @author Hendra McHen
 */
class Expensereport extends CI_Controller {

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
            if (($module == $this->asik_model->report_03) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0, $startd='', $endd='', $ex_type=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                /*========== start privilege ==========*/
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                /* end privilege */
                $this->load->helper('form');
                $data['pagecode'] = $string;
                
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($startd != ''){
                    $start_date = $startd;
                }
                if ($endd != ''){
                    $end_date = $endd;
                }                
                
                
                if ($button == 0){
                    //$report_data = $this->supplier_report_model->get_supplier_report($start_date, $end_date);
                    $expense = $this->get_expense($start_date, $end_date, $ex_type);
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
                            $start_date = $year.'-'.$last_month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 6:
                            $start_date = '2018-01-01';
                            $end_date = date('Y-m-d');
                            break;
                    }
                    $expense = $this->get_expense($start_date, $end_date, $ex_type);
                }
                
                // update 14 June 2018
                // cek report file by start_date, end_date
                $report_type = 3;
                $reporthistory = $this->general_model->get_report_by_date($start_date, $end_date, $report_type);
                $report_id = 0;
                $checked_name = '0';
                $approved_name = '0';
                $report_file = '0';
                if ($reporthistory->num_rows()!=0){
                    $row = $reporthistory->row();
                    $report_id = $row->report_file_id;
                    $checked_name = $this->general_model->get_user_by_id($row->checked_by);
                    if ($row->approved_by != 0){
                        $approved_name = $this->general_model->get_user_by_id($row->approved_by);
                    }
                    $report_file = $row->file_name;
                }
                $data['report_id'] = $report_id;
                $data['checked_name'] = $checked_name;
                $data['approved_name'] = $approved_name;
                $data['report_file'] = $report_file;
                $data['report_type'] = $report_type;
                $data['url_module'] = 'expensereport';
                               
                $array = array();
                $title_temp = '';
                $branch = $this->get_branch_list();
                if ($expense->num_rows()!=0){
                    $i = 0;
                    foreach ($expense->result() as $value) {
                        $col = array();
                        if ($value->expense_title != $title_temp){
                            $title_temp = $value->expense_title;
                            $pv_enc = $this->get_pv_id($value->pv_number);
                            $col[0] = '<a href="'. site_url('paymentvoucher/detail/20191231214302/'.$pv_enc).'" target="_blank">'.$value->expense_title.'</a>';

                            $k = 0;
                            $totalcol = 0;
                            foreach ($branch as $key=>$b) {
                                $col[$k+1] = 0;
                                if ($value->branch_name == $b){
                                    $col[$key+1] = $value->amount;
                                }
                                $totalcol = $totalcol + $col[$k+1];
                                $k++;
                            }
                            $col[$k+1] = $totalcol;
                            $array[$i] = $col;
                            $i++;
                        } else {
                            $title_temp = $value->expense_title;
                            $pv_enc = $this->get_pv_id($value->pv_number);
                            $col[0] = '<a href="'. site_url('paymentvoucher/detail/20191231214302/'.$pv_enc).'" target="_blank">'.$value->expense_title.'</a>';
                            $k = 0;
                            foreach ($branch as $key=>$b) {
                                $col[$k+1] = $array[$i-1][$k+1];
                                $k++;
                            }
                            $totalcol = 0;
                            $j = 1;
                            foreach ($branch as $key=>$b) {
                                if ($value->branch_name == $b){
                                    $col[$key+1] = $col[$key+1] + $value->amount;
                                }
                                $totalcol = $totalcol + $col[$j];
                                $j++;
                            }
                            $col[$k+1] = $totalcol;
                            $array[$i-1] = $col;
                        }
                    }
                }
                $data['array'] = $array; 
                $data['branch'] = $branch;
                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Expense Report';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };';
                    $c = 2;
                    $strtotal = '';
                    foreach ($branch as $key=>$b) {
                        $strtotal .= 'total'.$c.' = api
                                .column('.$c.', { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column('.$c.').footer() ).html(
                                numeral(total'.$c.').format("0,0.00")
                        );';
                        $c++;
                    }
                    $strtotal .= 'alltotal = api
                                .column('.$c.', { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column('.$c.').footer() ).html(
                                numeral(alltotal).format("0,0.00")
                        );';
                    
                $footer_total .= $strtotal . '}';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Expenses Report', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/expense_report.php';
                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_expense($start_date='', $end_date='', $type=0) {
        $sql  = 'SELECT ex.expense_date, ex.expense_title, ';
        $sql .= 'b.branch_name, ex.amount, ex.expense_status, ex.pv_number FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_status = 0 ';
        //if ($start_date != '' && $end_date != ''){
        if ($type == 0){
            $where = 'expense_type IN(0,1)';
        } else {
            $where = 'expense_type=1';
        }
            $sql .= 'AND ex.expense_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND '.$where;
        //}
        
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_branch_list() {
        $sql  = 'SELECT DISTINCT b.branch_name FROM expense AS ex
        INNER JOIN branch AS b ON ex.branch_id=b.branch_id
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
    
    public function action_checked($start_date='', $end_date='') {
        $this->general_model->action_checked($start_date, $end_date, 3, '/expensereport/go/20191341214303/0/');
    }
    
    public function action_approved($report_file_id = 0, $start_date='', $end_date='') {
        $this->general_model->action_approved($report_file_id, $start_date, $end_date, '/expensereport/go/20191341214303/0/');
    }
    
    public function do_upload() {
        $this->general_model->do_upload('/expensereport/go/20191341214303/0/');
    }
}