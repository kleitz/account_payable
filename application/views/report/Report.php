<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Report
 *
 * @author JUNA
 */
class Report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('report_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_report;
        $module_06 = $this->asik_model->report_06;
        $module_07 = $this->asik_model->report_07;
        $module_08 = $this->asik_model->report_08;
        if ($category == $category_code){
            if (($module == $module_06) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_07) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_08) && ($string == $category.$module)){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button = 0){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;     
                /* ===== get active period ===== */
                $period_active = 0;
                $period_title = 'Belum Ada Periode Aktif';
                $period_month = '';
                $this->load->model('period_model');
                $period = $this->period_model->get_period_active();
                if ($period->num_rows()!=0){
                    $row = $period->row();
                    $period_title = $row->period.' '.$row->year;
                    $period_active = 1;
                    $period_month = substr($row->start_date, 0, 7);
                }
                /* ===== end get active period ===== */
                if ($module == $this->asik_model->report_06){
                    $header = $this->asik_model->draw_header('Journal Report', $period_title, $this->category_index, $category, $module);
                    $halaman = 'report/report01_view.php';
                }
                if ($module == $this->asik_model->report_07){
                    $header = $this->asik_model->draw_header('Trial Balance Report', $period_title, $this->category_index, $category, $module);
                    $halaman = 'report/report02_view.php';
                }
                if ($module == $this->asik_model->report_08){
                    $header = $this->asik_model->draw_header('Ledger Report', $period_title, $this->category_index, $category, $module);
                    $halaman = 'report/report03_view.php';
                }
                $datatable_title = '';
                $str_footer = '';
                ////////////////////////////////////////////////////////////////
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($button == 0){
                    if ($module == $this->asik_model->report_06){
                        $data['list'] = $this->report01_list($start_date, $end_date);
                    }
                    if ($module == $this->asik_model->report_07){
                        $data['list'] = $this->report02_list($start_date, $end_date);
                    }
                    if ($module == $this->asik_model->report_08){
                        $data['account_list'] = $this->report03_list();
                    }
                } else {
                    $year = date('Y');
                    $month = date('m');
                    $day = date('d');
                    //////////////////                    
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
                    if ($module == $this->asik_model->report_06){
                        $datatable_title = 'Journal Report';
                        $data['list'] = $this->report01_list($start_date, $end_date);
                        $str_footer = '// Total over this page

                        pageTotal2 = api
                                .column(4, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(4).footer() ).html(
                                numeral(pageTotal2).format("0,0.00")
                        );';
                    }
                    if ($module == $this->asik_model->report_07){
                        $datatable_title = 'Trial Balance Report';
                        $data['list'] = $this->report02_list($start_date, $end_date);
                        $str_footer = '// Total over this page
                        pageTotal1 = api
                                .column(2, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(2).footer() ).html(
                                numeral(pageTotal1).format("0,0.00")
                        );
                        pageTotal2 = api
                                .column(3, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(3).footer() ).html(
                                numeral(pageTotal2).format("0,0.00")
                        );
                        pageTotal3 = api
                                .column(4, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(4).footer() ).html(
                                numeral(pageTotal3).format("0,0.00")
                        );
                        pageTotal4 = api
                                .column(5, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(5).footer() ).html(
                                numeral(pageTotal4).format("0,0.00")
                        );
                        ';
                    }
                    if ($module == $this->asik_model->report_08){
                        $datatable_title = 'Ledger Report';
                        $data['account_list'] = $this->report03_list();
                    }
                }
                /* ===== start datatable ===== */
                $data['datatable_title'] = $datatable_title;
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };'.$str_footer.'
                }';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['pagecode'] = $string;
                $data['content_header'] = $header;
                $data['active_li'] = $this->category_index;
                $data['halaman'] = $halaman;
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function report01_list($start_date='', $end_date='') {        
        $this->load->model('ledger_model');
        $list = $this->ledger_model->get_mutation($start_date, $end_date);
        return $list;
    }
    
    public function report02_list($start_date='', $end_date='') {
        $this->load->model('ledger_model');
        $list = $this->ledger_model->get_trial_balance($start_date, $end_date);
        return $list;
    }
    
    public function report03_list() {
        $this->load->model('ledger_model');
        $list = $this->ledger_model->account_ledger();
        return $list;
    }
    
    public function report04_list($start_date='', $end_date='') {
        $this->load->model('ledger_model');
        $list = $this->ledger_model->get_trial_balance($start_date, $end_date);
        return $list;
    }
    
    public function ledger_detail($account_id=0, $button=0) {
        $this->load->helper('form');
        $this->load->model('ledger_model');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        if ($button == 0) {
            $data['list'] = $this->ledger_model->get_ledger_by_account($account_id, $start_date, $end_date);
        } else {
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            //////////////////                    
            switch ($button) {
                case 1:
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
                case 2:
                    $start_date = date('Y-m-d', strtotime("-1 days"));
                    $end_date = date('Y-m-d', strtotime("-1 days"));
                    break;
                case 3:
                    $signupdate = $year . '-' . $month . '-' . $day;
                    $signupweek = date("W", strtotime($signupdate));

                    $dto = new DateTime();
                    $start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                    $end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                    break;
                case 4:
                    $start_date = $year . '-' . $month . '-01';
                    $end_date = $end = date("Y-m-t", strtotime($start_date));
                    break;
                case 5:
                    if ($month == 1) {
                        $last_month = '12';
                        $year = $year - 1;
                    } else {
                        $last_month = $month - 1;
                    }
                    $start_date = $year . '-' . $last_month . '-01';
                    $end_date = $end = date("Y-m-t", strtotime($start_date));
                    break;
                case 6:
                    $start_date = '2018-01-01';
                    $end_date = date('Y-m-d');
                    break;
            }
            $data['list'] = $this->ledger_model->get_ledger_by_account($account_id, $start_date, $end_date);
        }

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
            
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_id($account_id);
        $account_name = '';
        if ($account->num_rows()!=0){
            $row = $account->row();
            $account_name = $row->account_name;
        }
        $data['account_name'] = $account_name;
        $account_opt = array();
        $account_list = $this->account_model->get_account_list();
        if ($account_list->num_rows()!=0){
            foreach ($account_list->result() as $value) {
                $account_opt[$value->account_id] = $value->account_name;
            }
        }
        $data['account_id'] = $account_id;
        $data['account_opt'] = $account_opt;
        /* ===== start datatable ===== */
        $data['datatable_title'] = 'Ledger Detail';
        $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                    return typeof i === "string" ?
                            i.replace(/[\$,]/g, "")*1 :
                            typeof i === "number" ?
                                    i : 0;
            };
            // Total over this page
            pageTotal1 = api
                    .column(3, { page: "current"})
                    .data()
                    .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                    }, 0 );
            // Update footer
            $( api.column(3).footer() ).html(
                    numeral(pageTotal1).format("0,0.00")
            );
            pageTotal2 = api
                    .column(4, { page: "current"})
                    .data()
                    .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                    }, 0 );
            // Update footer
            $( api.column(4).footer() ).html(
                    numeral(pageTotal2).format("0,0.00")
            );
            pageTotal3 = api
                    .column(5, { page: "current"})
                    .data()
                    .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                    }, 0 );
            // Update footer
            $( api.column(5).footer() ).html(
                    numeral(pageTotal3).format("0,0.00")
            );
            
        }';
        $data['footer_total'] = $footer_total;
        /* ===== end datatable ===== */
        $header = $this->asik_model->draw_header('Ledger', $account_name, $this->category_index, $this->asik_model->category_report, $this->asik_model->report_07);
        $data['pagecode'] = $this->asik_model->category_report . $this->asik_model->report_07;
        $data['content_header'] = $header;
        $data['active_li'] = $this->category_index;
        $data['halaman'] = 'report/ledger_detail.php';  
        $this->load->view('template', $data);
    }
    
}
