<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Supplierreport
 *
 * @author Hendra McHen
 */
class Supplierreport extends CI_Controller {

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
            if (($module == $this->asik_model->report_07) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0, $startd='', $endd='') {
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
                
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($startd != ''){
                    $start_date = $startd;
                }
                if ($endd != ''){
                    $end_date = $endd;
                }
                $supplier_temp = '';
                
                if ($button == 0){
                    $report_data = $this->supplier_report_model->get_supplier_report($start_date, $end_date);
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
                    $report_data = $this->supplier_report_model->get_supplier_report($start_date, $end_date);
                }
                // update 15 June 2018
                // cek report file by start_date, end_date
                $report_type = 7;
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
                $data['url_module'] = 'supplierreport';
                
                $array = array();
                $branch = $this->get_branch_list();
                if ($report_data->num_rows()!=0){
                    $i = 0;
                    foreach ($report_data->result() as $value) {
                        $col = array();
                        if ($value->supplier_name != $supplier_temp){
                            $supplier_temp = $value->supplier_name;
                            $col[0] = $value->supplier_name;
                            $k = 0;
                            $totalcol = 0;
                            foreach ($branch as $key=>$b) {
                                $col[$k+1] = 0;
                                if ($value->branch_name == $b){
                                    $col[$key+1] = $value->total;
                                }
                                $totalcol = $totalcol + $col[$k+1];
                                $k++;
                            }
                            $col[$k+1] = $totalcol;
                            $col[$k+2] = $value->supplier_id;
                            $array[$i] = $col;
                            $i++;
                        } else {
                            $supplier_temp = $value->supplier_name;
                            $col[0] = $value->supplier_name;
                            $k = 0;
                            foreach ($branch as $key=>$b) {
                                $col[$k+1] = $array[$i-1][$k+1];
                                $k++;
                            }
                            $totalcol = 0;
                            $j = 1;
                            foreach ($branch as $key=>$b) {
                                if ($value->branch_name == $b){
                                    $col[$key+1] = $col[$key+1] + $value->total;
                                }
                                $totalcol = $totalcol + $col[$j];
                                $j++;
                            }
                            $col[$k+1] = $totalcol;
                            $col[$k+2] = $value->supplier_id;
                            $array[$i-1] = $col;
                        }
                    }
                }
                $data['array'] = $array;
                $data['branch'] = $branch;
                
                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Supplier Report', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/supplier_report.php';
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Supplier Report';
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
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function supplier_detail($string = '', $supplier_id=0) {
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
                $supplier_name = $this->supplier_report_model->get_supplier_name($supplier_id);
                $data['supplier_name'] = $supplier_name;
                $data['list'] = $this->supplier_report_model->get_supplier_detail($supplier_id);
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Supplier Detail', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/supplier_detail.php';
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Supplier '.$supplier_name;
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
                    amount = api
                            .column(3, { page: "current"})
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Update footer
                    $( api.column(3).footer() ).html(
                            numeral(amount).format("0,0.00")
                    );
                }';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function outlet($string = '', $branch_id=0, $button=0) {
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
                $data['branch_id'] = $branch_id;
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                
                if ($button == 0){
                    $report_data = $this->get_outlet_report($branch_id, $start_date, $end_date);
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
                    }
                    $report_data = $this->get_outlet_report($branch_id, $start_date, $end_date);
                }
                $data['report_data'] = $report_data;
                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                // get branch name
                $branch_name = $this->get_branch_by_id($branch_id);
                
                $header = $this->asik_model->draw_header('Supplier Outlet: '.$branch_name, 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/supplier_outlet.php';
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Supplier Outlet';
                $data['footer_total'] = '';
                /* ===== end datatable ===== */
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_outlet_report($branch_id=0, $start_date='', $end_date='') {
        $sql  = 'SELECT ci.invoice_date, ci.amount, s.supplier_name, b.branch_name, pp.pp_number, pp.pp_status ';
        $sql .= 'FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS s ON ci.supplier_id=s.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON ci.branch_id=b.branch_id ';
        $sql .= 'INNER JOIN payment_process_detail AS pp_detail ON ci.credit_invoice_id=pp_detail.credit_invoice_id ';
        $sql .= 'INNER JOIN payment_process AS pp ON pp.pp_id=pp_detail.pp_id ';
        $sql .= 'WHERE b.branch_id='.$branch_id.' AND invoice_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_branch_by_id($id=0) {
        $sql  = 'SELECT * FROM branch ';
        $sql .= 'WHERE branch_id = '.$id;
        $query = $this->db->query($sql);
        $branch_name = '-';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $branch_name = $row->branch_name;
        }
        return $branch_name;
    }
    
    public function get_branch_list() {
        $sql  = 'SELECT DISTINCT b.branch_name FROM credit_invoice AS ci
        INNER JOIN branch AS b ON ci.branch_id=b.branch_id
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
    
    public function action_checked($start_date='', $end_date='') {
        $this->general_model->action_checked($start_date, $end_date, 7, '/supplierreport/go/20191341214307/0/');
    }
    
    public function action_approved($report_file_id = 0, $start_date='', $end_date='') {
        $this->general_model->action_approved($report_file_id, $start_date, $end_date, '/supplierreport/go/20191341214307/0/');
    }
    
    public function do_upload() {
        $this->general_model->do_upload('/supplierreport/go/20191341214307/0/');
    }
}