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
        $module_09 = $this->asik_model->report_08;
        $module_10 = $this->asik_model->report_09;
        $module_11 = $this->asik_model->report_10;
        if ($category == $category_code){
            if (($module == $module_09) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_10) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_11) && ($string == $category.$module)){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button = 0, $startd='', $endd=''){
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
                $data['trans_notif'] = $this->get_trans_notif();
                if ($period->num_rows()!=0){
                    $row = $period->row();
                    $period_title = $row->period.' '.$row->year;
                    $period_active = 1;
                    $period_month = substr($row->start_date, 0, 7);
                }
                /* ===== end get active period ===== */
                $this->load->helper('form');
                $report_type = 0;
                $link_code = '';
                if ($module == $this->asik_model->report_08){
                    $header = $this->asik_model->draw_header('Journal Report', $period_title, $this->category_index, $category, $module);
                    $halaman = 'report/report01_view.php';
                    $report_type = 8;
                    $link_code = '20191341214308';
                }
                
                if ($module == $this->asik_model->report_09){
                    $header = $this->asik_model->draw_header('Ledger Report', $period_title, $this->category_index, $category, $module);
                    $halaman = 'report/report03_view.php';
                    $report_type = 9;
                    $link_code = '20191341214309';
                }
                
                if ($module == $this->asik_model->report_10){
                    $header = $this->asik_model->draw_header('Trial Balance Report', $period_title, $this->category_index, $category, $module);
                    $halaman = 'report/report02_view.php';
                    $report_type = 10;
                    $link_code = '20191341214310';
                }
                $datatable_title = '';
                $str_footer = '';
                ////////////////////////////////////////////////////////////////
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                
                if ($startd != ''){
                    $start_date = $startd;
                }
                if ($endd != ''){
                    $end_date = $endd;
                }
                if ($button == 0){
                    if ($module == $this->asik_model->report_08){
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
                        
                        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
                        $data['remark'] = $this->general_model->draw_textarea('Remark', 1, 'remark', '');
                        $data['show_modal'] = 'report/delete_trans_confirm.php';
                    }
                    if ($module == $this->asik_model->report_09){
                        $datatable_title = 'Ledger Report';
                        $data['account_list'] = $this->report03_list();
                    }
                    if ($module == $this->asik_model->report_10){
                        $data['list'] = $this->report02_list($start_date, $end_date);
                        $datatable_title = 'Trial Balance Report';
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
                    
                    if ($module == $this->asik_model->report_08){
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
                        
                        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
                        $data['remark'] = $this->general_model->draw_textarea('Remark', 1, 'remark', '');
                        $data['show_modal'] = 'report/delete_trans_confirm.php';
                    }
                    if ($module == $this->asik_model->report_09){
                        $datatable_title = 'Ledger Report';
                        $data['account_list'] = $this->report03_list();
                    }
                    if ($module == $this->asik_model->report_10){  
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
                }
                // update 15 June 2018
                // cek report file by start_date, end_date
                
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
                $data['url_module'] = 'report';
                $data['link_code'] = $link_code;
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
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
    
    public function ledger_detail($account_id=0, $button=0, $startd='', $endd='') {
        $this->load->helper('form');
        $this->load->model('ledger_model');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $pp_type = $this->input->post('pp_type');
        if ($startd != ''){
            $start_date = $startd;
        }
        if ($endd != ''){
            $end_date = $endd;
        } 
        if ($pp_type == ''){
            $pp_type = 'All';
        }
        if ($button == 0) {
            $data['list'] = $this->ledger_model->get_ledger_by_account($account_id, $start_date, $end_date);
            $data['pp_numbers'] = $this->ledger_model->get_pp_number_by_account($account_id, $start_date, $end_date);
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
                        $last_month = '0'.$last_month;
                    }
                    $start_date = $year . '-' . $last_month . '-01';
                    $end_date = date("Y-m-t", strtotime($start_date));
                    break;
                case 6:
                    $start_date = '2018-01-01';
                    $end_date = date('Y-m-d');
                    break; // up to today
            }
            $data['list'] = $this->ledger_model->get_ledger_by_account($account_id, $start_date, $end_date);
            $data['pp_numbers'] = $this->ledger_model->get_pp_number_by_account($account_id, $start_date, $end_date);
        }
        // update 15 June 2018
        // cek report file by start_date, end_date
        $report_type = 9;
        $reporthistory = $this->general_model->get_report_by_date($start_date, $end_date, $report_type, $account_id);
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
        $data['url_module'] = 'report';
                
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        /* update code : 2018-03-20 */
        
        $first_date = substr($start_date, 0, 8).'01';
        if ($start_date == $first_date){
            $previous_balance = 0;
            $data['previous_date'] = $start_date;
        } else {
            $previous_date = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
            $data['previous_date'] = $previous_date;
            $previous_balance = $this->ledger_model->get_previous_balance($account_id, $first_date, $previous_date);
        }
        $data['previous_balance'] = $previous_balance;
        $data['next_balance'] = $this->ledger_model->get_previous_balance($account_id, $start_date, $end_date);
        
        ///////////////////////////////////
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
        $branch_name = $this->get_branch_name($account_id);
        /* update 2018-07-18 */
        $pp_type_arr = array(
            'GN'=> 'GN', 
            'SC'=>'SC', 
            'CE'=>'CE', 
            'OS'=>'OS', 
            'PR'=>'PR', 
            'All'=> 'All PP Type'
        );
        $data['pp_type_arr'] = $pp_type_arr;
        $data['pp_type'] = $pp_type;
        /* ===== start datatable ===== */
        $data['datatable_title'] = 'Ledger of '.$branch_name;
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
                    pageTotal = api
                            .column(5, { page: "current"})
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Update footer
                    $( api.column(5).footer() ).html(
                            numeral(pageTotal).format("0,0.00")
                    );
                    // Total over this page
                    pageTotal = api
                            .column(6, { page: "current"})
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Update footer
                    $( api.column(6).footer() ).html(
                            numeral(pageTotal).format("0,0.00")
                    );
                }';
        $data['footer_total'] = $footer_total;
        /* ===== end datatable ================================================= */
        $header = $this->asik_model->draw_header('Ledger', $account_name, $this->category_index, $this->asik_model->category_report, $this->asik_model->report_10);
        $data['pagecode'] = $this->asik_model->category_report . $this->asik_model->report_10;
        $data['content_header'] = $header;
        $data['active_li'] = $this->category_index;
        $data['halaman'] = 'report/ledger_detail.php';  
        $this->load->view('template', $data);
    }
    
    public function get_branch_name($account_id=0) {
        $sql  = 'SELECT b.branch_name FROM account AS a ';
        $sql .= 'INNER JOIN branch AS b ON a.branch_id=b.branch_id ';
        $sql .= 'WHERE a.account_id='.$account_id;
        $query = $this->db->query($sql);
        $branch_name = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $branch_name = $row->branch_name;
        }
        return $branch_name;
    }
    
    // update code : 2018-03-29
    public function delete_confirm() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('remark', 'Remark', 'required');
        if ($this->form_validation->run() == TRUE) {
            $trans_id = $this->input->post('trans_id');
            $remark = $this->input->post('remark');
            $datenow = date('Y-m-d');
            $data = array(
                'trans_id' => $trans_id,
                'request_date' => $datenow,
                'user_id' => $this->session->userdata('user_id'),
                'remark' => $remark
            );
            $this->db->insert('trans_notif', $data);
        }
        echo json_encode(array("status" => TRUE));
    }
    
    public function get_trans_notif() {
        $sql  = 'SELECT tn.*, t.trans_code, t.pv_number, u.username FROM trans_notif AS tn ';
        $sql .= 'INNER JOIN transactions AS t ON tn.trans_id=t.trans_id ';
        $sql .= 'INNER JOIN users AS u ON tn.user_id=u.user_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function delete_cancel($id=0) {
        $this->db->where('trans_notif_id', $id);
        $this->db->delete('trans_notif');
        $back = '/report/go/' . $this->asik_model->category_report;
        $back .= $this->asik_model->report_08;
        redirect($back);
    }
    
    public function delete_approve($id=0, $transid=0) {
        // change status for PP or Cash Request
        $pv_data = $this->get_pv_by_trans_id($transid);
        if ($pv_data->num_rows()!=0){
            $row = $pv_data->row();
            $pp_id = $row->pp_id;
            $cash_request_id = $row->cash_request_id;
            if ($pp_id != 0){
                $this->update_pp_status($pp_id);
            }
            if ($cash_request_id != 0){
                $this->update_cashrequest_status($cash_request_id);
            }
        }

        // change status for Cash receive
        $cashreturn = $this->get_cashreturn_by_trans_id($transid);
        if ($cashreturn->num_rows()!=0){
            $row = $cashreturn->row();
            $cash_receive_id = $row->cash_receive_id;
            $this->update_cashreceive_status($cash_receive_id);
        }
        
        $this->db->where('trans_notif_id', $id);
        $this->db->delete('trans_notif');
        $this->db->where('trans_id', $transid);
        $this->db->delete('transactions');
        $back = '/report/go/' . $this->asik_model->category_report;
        $back .= $this->asik_model->report_08;
        redirect($back);
    }
    
    public function get_pv_by_trans_id($trans_id=0) {
        $sql  = 'SELECT * FROM payment_voucher ';
        $sql .= 'WHERE trans_id='.$trans_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function update_pp_status($pp_id=0) {
        /*update status pp */
        $data = array(
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
    }
    
    public function update_cashrequest_status($cash_request_id=0) {
        /* cash request update */
        $data = array(
            'cash_request_status' => 2
        );
        $this->db->where('cash_request_id', $cash_request_id);
        $this->db->update('cash_request', $data);
    }
    
    public function get_cashreturn_by_trans_id($trans_id) {
        $sql  = 'SELECT * FROM cash_return ';
        $sql .= 'WHERE trans_id='.$trans_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function update_cashreceive_status($cash_receive_id=0) {
        $data = array(
            'cash_receive_status' => 0,
            'paid_off' => 0
        );
        $this->db->where('cash_receive_id', $cash_receive_id);
        $this->db->update('cash_receive', $data);
    }
    
    public function action_checked($start_date='', $end_date='', $report_type=0, $link_code='', $account_id=0) {
        $url = $account_id == 0 ? '/report/go/'.$link_code.'/0/': '/report/ledger_detail/'.$account_id.'/0/';
        $this->general_model->action_checked($start_date, $end_date, $report_type, $url, $account_id);
    }
    
    public function action_approved($report_file_id = 0, $start_date='', $end_date='', $link_code='', $account_id=0) {
        $url = $account_id == 0 ? '/report/go/'.$link_code.'/0/': '/report/ledger_detail/'.$account_id.'/0/';
        $this->general_model->action_approved($report_file_id, $start_date, $end_date, $url);
    }
    
    public function do_upload($link_code='', $account_id=0) {
        $url = $account_id == 0 ? '/report/go/'.$link_code.'/0/': '/report/ledger_detail/'.$account_id.'/0/';
        $this->general_model->do_upload($url);
    }
    
    public function upload_more($start_date='', $end_date='', $account_id=0) {        
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'up'.date('YmdHis');
        
        $config['upload_path'] = './assets/uploadmore/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 9000;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $dataup = array(
                'file_name' => $info['orig_name'],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'account_id' => $account_id
            );
            $this->db->insert('upload_more', $dataup);
        }
        
        // get data upload more
        $sql  = 'SELECT * FROM upload_more WHERE start_date="'.$start_date.'" AND ';
        $sql .= 'end_date="'.$end_date.'" AND account_id='.$account_id;
        $list = $this->db->query($sql);
        $data['list'] = $list;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['account_id'] = $account_id;
        /* ===== start datatable ===== */
        $data['datatable_title'] = '';
        $data['footer_total'] = '';
        /* ===== end datatable ================================================= */
        $header = $this->asik_model->draw_header('Upload more', '', $this->category_index, $this->asik_model->category_report, $this->asik_model->report_10);
        $data['pagecode'] = $this->asik_model->category_report . $this->asik_model->report_10;
        $data['content_header'] = $header;
        $data['active_li'] = $this->category_index;
        $data['halaman'] = 'report/upload_more.php';  
        $this->load->view('template', $data);
    }
    
}
