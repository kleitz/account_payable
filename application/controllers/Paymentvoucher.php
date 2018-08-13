<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Paymentvoucher
 *
 * @author Hendra McHen
 */
class Paymentvoucher extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('payment_voucher_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 2;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_configuration) {
            if (($module == $this->asik_model->config_02) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;    
                /* unset session */
                $post_data = array(
                    'description',
                    'admin_fee',
                    'account_id',
                    'bank_id',
                    'bank_cek_from',
                    'bank_bg_from',
                    'received_name'
                );
                $this->session->unset_userdata($post_data);
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
                
                $this->load->helper('form');
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');

                /*======= start form field =======*/
                $this->load->model('bank_model');
                $bankdata = $this->bank_model->get_bank_account_list();
                $bank_opt = array();
                if ($bankdata->num_rows()!=0){
                    foreach ($bankdata->result() as $value) {
                        $bank_opt[$value->bank_id] = $value->bank_account_no;
                    }
                }
                $data['pv_id'] = $this->general_model->draw_hidden_field('trans_id', '');
                $payment_opt = array('Cash', 'Transfer ATM', 'Online (Token)', 'Cek', 'BG');
                $data['mode_opt'] = $payment_opt;
                $data['payment_mode'] = $this->general_model->draw_select('Payment Mode', 0, 'payment_mode', 0, $payment_opt, '');
                $this->load->model('account_model');
                $account = $this->account_model->get_account_by_keyword(0, "Petty Cash");
                $account_opt = array();
                if ($account->num_rows()!=0){
                    foreach ($account->result() as $value) {
                        $account_opt[$value->account_id] = $value->account_name;
                    }
                }
                
                /* Auto input */
                $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', '');
                /* Manual input */
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['admin_fee'] = $this->general_model->draw_input_currency('Admin fee', 0, 'admin_fee', '');
                $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 1, $account_opt, '', 1);
                $data['bank_account_name_to'] = $this->general_model->draw_text_field('Nama Rekening Tujuan', 1, 'bank_account_name_to', '', '', '');
                $data['bank_account_num_to'] = $this->general_model->draw_text_field('No. Rekening Tujuan', 1, 'bank_account_num_to', '', '00-000', '');
                $data['bank_name_to'] = $this->general_model->draw_text_field('Nama Bank Tujuan', 1, 'bank_name_to', '', 'BCA / BRI', '');
                $data['bank_id'] = $this->general_model->draw_select('Bank QT', 0, 'bank_id', 0, $bank_opt, '', 0);
                $data['bank_cek_from'] = $this->general_model->draw_text_field('Cek Number', 1, 'bank_cek_from', '', '', '');
                $data['bank_bg_from'] = $this->general_model->draw_text_field('BG Number', 1, 'bank_bg_from', '', '', '');
                $data['received_name'] = $this->general_model->draw_text_field('Received', 1, 'received_name', '', 'nama penerima', '');
                
                /* end form */
                
    
                /*=============================================================*/
                if ($button == 0){
                    $data['payment_voucher_list'] = $this->payment_voucher_model->get_payment_voucher_list($start_date, $end_date);
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
                    $data['payment_voucher_list'] = $this->payment_voucher_model->get_payment_voucher_list($start_date, $end_date);
                }
                
                
                $header = $this->asik_model->draw_header('Payment Voucher', $period_title, $this->category_index, $category, $module);
                $data['pagecode'] = $string;
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['active_li'] = $this->category_index;
                $data['period_title'] = $period_title;
                $data['period_active'] = $period_active;
                $data['period_month'] = $period_month;
                $data['content_header'] = $header;
                $data['halaman'] = 'paymentvoucher/paymentvoucher_view.php';  
                
                /* datatable */
                $data['datatable_title'] = 'Payment Voucher';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
                    // Total over all pages
                    total = api
                            .column(4)
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
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
                //////////////////////////////////////
                $data['footer_total'] = $footer_total;
                /* get footer info */
                $datafooter = $this->payment_voucher_model->get_last_record();
                $username = '';
                $lastupdate = '';
                $transid = 0;
                if ($datafooter->num_rows()!=0){
                    $row = $datafooter->row();
                    $username = $row->username;
                    $lastupdate = $row->last_update;
                    $transid = $row->pv_number;
                }
                $data['username'] = $username;
                $data['last_update'] = $lastupdate;
                $data['transid'] = $transid;
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function paypp($string = '', $pp_type=5) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;    
                /* datatable */
                $data['datatable_title'] = 'Payment Process';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
                    // Total over all pages
                    total = api
                            .column(4)
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Total over this page
                    pageTotal = api
                            .column(4, { page: "current"})
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Update footer
                    $( api.column(4).footer() ).html(
                            numeral(pageTotal).format("0,0.00")
                    );
                }';
                $data['footer_total'] = $footer_total;
                $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
                $data['mode_opt'] = $payment_opt;
                $pptype_select = $this->input->post('pptype_select');
                if (isset($pptype_select)){
                    $pp_type = $pptype_select;
                }
                $data['payment_process_list'] = $this->get_payment_process_by_pp_type($pp_type);
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Pay Payment Process', 'Pay', $this->category_index, $category, $module);
                $data['content_header'] = $header;
                $data['halaman'] = 'paymentvoucher/pv_paymentprocess.php';  
                $this->load->view('template', $data);
            } else {
                show_404();     
            }
        } else {
            show_404();
        }
    }
    
    public function paycashrequest($string = '') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;    
                /* datatable */
                $data['datatable_title'] = 'Cash Request';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
                    // Total over all pages
                    total = api
                            .column(5)
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
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
                }';
                $data['footer_total'] = $footer_total;
                $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
                $data['mode_opt'] = $payment_opt;
                $this->load->model('cashrequest_model');
                $data['castrequest_list'] = $this->cashrequest_model->get_cashrequest_by_status($this->cashrequest_model->approved);
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Pay Cash Request', 'Pay', $this->category_index, $category, $module);
                $data['content_header'] = $header;
                $data['halaman'] = 'paymentvoucher/pv_cashrequest.php';  
                $this->load->view('template', $data);
            } else {
                show_404();     
            }
        } else {
            show_404();
        }
    }
    
    public function pay($string = '', $encrypt_id='', $pv_number='0', $tipe = 1, $confirm=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;    
                $this->load->helper('form');
                
                $pp_id = 0;
                $cash_request_id = 0;
                $branch_id = 0;
                $nota_number = '';
                $pv_title = '';
                $description = '';
                $total = 0;
                $payment_mode = 0;
                $caption_number = '';
                $box = '';
                
                $bank_account_name_to = '';
                $bank_account_num_to = 0;
                $bank_name_to = '';
                
                $cash_from_cashrequest = 0;
                $employee_id = 0;
                /* Jika pembayaran PP*/
                if ($tipe == 1){
                    $caption_number = 'PP';
                    $box = 'box-primary';
                    $pp_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                    $this->load->model('payment_process_model');
                    $datapp = $this->payment_process_model->get_payment_process_by_id($pp_id);
                    if ($datapp->num_rows()!=0){
                        $r = $datapp->row();
                        $nota_number = $r->pp_number;
                        $pv_title = $r->pp_title;
                        $description = $r->pp_title;
                        $total = $r->total;
                        $payment_mode = $r->payment_mode;
                        $branch_id = $r->branch_id;
                        if ($r->supplier_id!=0){
                            $this->load->model('supplier_model');
                            $supplierdata = $this->supplier_model->get_supplier_by_id($r->supplier_id);
                            if ($supplierdata->num_rows()!=0){
                                $row = $supplierdata->row();
                                $bank_account_name_to = $row->account_name;
                                $bank_account_num_to = $row->account_number;
                                $bank_name_to = $row->bank_name;
                            }
                        }
                        // jika pp mempunyai cash request id
                        $cash_from_cashrequest = $r->cash_request_id;
                        // update 2018-05-24
                        $employee_id = $r->employee_id;
                    }
                }
                // Jika pembayaran Cash request
                if ($tipe == 2){
                    $caption_number = 'CR';
                    $box = 'box-warning';
                    $cash_request_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                    $this->load->model('cashrequest_model');
                    $crdata = $this->cashrequest_model->get_cashrequest_by_id($cash_request_id);
                    if ($crdata->num_rows()!=0){
                        $cr = $crdata->row();
                        $nota_number = $cr->cash_request_number;
                        $pv_title = $cr->employee_name;
                        $total = $cr->amount;
                        $payment_mode = $cr->payment_mode;
                        $branch_id = $cr->branch_id;
                    }
                }
                $data['datatable_title'] = '';
                $data['footer_total'] = '';
                // get from session 
                //$description = $this->session->userdata('description');
                $admin_fee = $this->session->userdata('admin_fee');
                $account_id = $this->session->userdata('account_id');
                $bank_id = $this->session->userdata('bank_id');
                $bank_cek_from = $this->session->userdata('bank_cek_from');
                $bank_bg_from = $this->session->userdata('bank_bg_from');
                $received_name = $this->session->userdata('received_name');
                $bank_account_name_to = $this->session->userdata('bank_account_name_to');
                $bank_account_num_to = $this->session->userdata('bank_account_num_to');
                $bank_name_to = $this->session->userdata('bank_name_to');
                /*======= start form field =======*/
                $this->load->model('bank_model');
                $bankdata = $this->bank_model->get_bank_account_list();
                $bank_opt = array();
                if ($bankdata->num_rows()!=0){
                    foreach ($bankdata->result() as $value) {
                        $bank_opt[$value->bank_id] = $value->bank_account_name .' | '. $value->bank_account_no . ' - '.$value->branch_name;
                    }
                }
                $data['pv_id'] = $this->general_model->draw_hidden_field('pv_id', '');
                $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
                $data['mode_opt'] = $payment_opt;
                
                $this->load->model('account_model');
                $account = $this->account_model->get_account_by_keyword(0, "Petty Cash");
                $account_opt = array();
                if ($account->num_rows()!=0){
                    foreach ($account->result() as $value) {
                        $account_opt[$value->account_id] = $value->account_name;
                    }
                }
                
                $this->load->model('branch_model');
                $databranch = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($databranch->num_rows()!=0){
                    foreach ($databranch->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                
                $data['branch_opt'] = $branch_opt;
                $data['outlet'] = $branch_id;
                
                /* Auto input */
                $pv_number = $pv_number == '0' ? $this->general_model->get_generate_number('PV', 'payment_voucher', 'pv_id'):$pv_number;
                $data['pvnumber'] = $pv_number;
                $data['pv_number'] = $this->general_model->draw_hidden_field('pv_number', $pv_number);
                $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', $pp_id);
                $data['cash_request_id'] = $this->general_model->draw_hidden_field('cash_request_id', $cash_request_id);
                $data['cash_from_cashrequest'] = $this->general_model->draw_hidden_field('cash_from_cashrequest', $cash_from_cashrequest);
                $data['val_cash_request_id'] = $cash_request_id;
                $data['val_cash_from_cashrequest'] = $cash_from_cashrequest;
                $data['val_employee_id'] = $employee_id;
                $data['nota_number'] = $nota_number;
                $data['pv_title'] = $pv_title;
                $data['total'] = $total;
                $data['payment_mode'] = $payment_mode;
                $data['caption_number'] = $caption_number;
                $data['box'] = $box;
                $data['branch_id'] = $this->general_model->draw_hidden_field('branch_id', $branch_id);
                $data['tipe'] = $this->general_model->draw_hidden_field('tipe', $tipe);
                /* Manual input */
                $data['pv_date'] = $this->general_model->draw_datepicker('Date', 1, 'pv_date', date('Y-m-d'));
                $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', $description);
                $data['admin_fee'] = $this->general_model->draw_input_currency('Admin fee', 0, 'admin_fee', $admin_fee);
                $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 0, $account_opt, $account_id, 0);
                
                $data['bank_account_name_to'] = $this->general_model->draw_text_field('Nama Rekening Tujuan', 1, 'bank_account_name_to', '', '', $bank_account_name_to);
                $data['bank_account_num_to'] = $this->general_model->draw_text_field('No. Rekening Tujuan', 1, 'bank_account_num_to', '', '00-000', $bank_account_num_to);
                $data['bank_name_to'] = $this->general_model->draw_text_field('Nama Bank Tujuan', 1, 'bank_name_to', '', 'BCA / BRI', $bank_name_to);
                
                $data['bank_id'] = $this->general_model->draw_select('Bank QT', 0, 'bank_id', 0, $bank_opt, $bank_id, 0);
                $data['bank_cek_from'] = $this->general_model->draw_text_field('Cek Number', 1, 'bank_cek_from', '', '', $bank_cek_from);
                $data['bank_bg_from'] = $this->general_model->draw_text_field('BG Number', 1, 'bank_bg_from', '', '', $bank_bg_from);
                $data['received_name'] = $this->general_model->draw_text_field('Diterima Oleh (Received by)', 1, 'received_name', '', 'nama penerima', $received_name);
                /* end form */
                
                /* unset session */
                $post_data = array(
                    'description',
                    'admin_fee',
                    'account_id',
                    'bank_id',
                    'bank_cek_from',
                    'bank_bg_from',
                    'received_name',
                    'cash_from_cashrequest',
                    'bank_account_name_to',
                    'bank_account_num_to',
                    'bank_name_to'
                );
                $this->session->unset_userdata($post_data);
                
                /*confirm data*/
                $confirm_text = '';
                if ($confirm == 1){
                    $confirm_text = 'Are you sure to borrow in other Petty Bank ?';
                }
                if ($confirm == 2){
                    $confirm_text = 'Apakah yakin ingin meminjam dari Petty Cash OUTLET lain?';
                }
                $data['confirm_text'] = $confirm_text;
                $data['confirm'] = $confirm;
                $header = $this->asik_model->draw_header('Payment Voucher', 'Form', $this->category_index, $category, $module);
                $data['pagecode'] = $string;
                $data['encrypt_id'] = $encrypt_id;
                $data['active_li'] = $this->category_index;
                $data['content_header'] = $header;
                $data['halaman'] = 'paymentvoucher/paymentvoucher_pays.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_validation($string = '', $encrypt_id='', $pv_number='0') {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('received_name', 'Received', 'required');
        $tipe = $this->input->post('tipe');
        /* tampung ke session */
        $pv_id = $this->input->post('pv_id');
        $branch_id = $this->input->post('branch_id');
        $confirm_go = $this->input->post('confirm_go');
            
        $description = $this->input->post('description');
        $admin_fee = $this->input->post('admin_fee');
        $account_id = $this->input->post('account_id');
        $bank_id = $this->input->post('bank_id');
        $bank_cek_from = $this->input->post('bank_cek_from');
        $bank_bg_from = $this->input->post('bank_bg_from');
        $received_name = $this->input->post('received_name');
        $bank_account_name_to = $this->input->post('bank_account_name_to');
        $bank_account_num_to = $this->input->post('bank_account_num_to');
        $bank_name_to = $this->input->post('bank_name_to');
        ///
        $cash_from_cashrequest = $this->input->post('cash_from_cashrequest');
        /* cek bank id | 2018-03-20 */
        if ($bank_id == ''){
            $bank_id = 0;
        }
        
        $post_data = array(
            'description' => $description,
            'admin_fee' =>$admin_fee,
            'account_id' => $account_id,
            'bank_id' => $bank_id,
            'bank_cek_from' => $bank_cek_from,
            'bank_bg_from' => $bank_bg_from,
            'received_name' => $received_name,
            'cash_from_cashrequest' => $cash_from_cashrequest,
            'bank_account_name_to' => $bank_account_name_to,
            'bank_account_num_to' => $bank_account_num_to,
            'bank_name_to' => $bank_name_to
        );

        $this->session->set_userdata($post_data);
        
        if ($this->form_validation->run() == FALSE) {
            $this->pay($string, $encrypt_id, $pv_number, $tipe);
        } else {
            $this->load->model('bank_model');
            $bank = $this->bank_model->get_bank_account_by_id($bank_id);
            if ($bank->num_rows()!=0){
                $row = $bank->row();
                if ($branch_id != $row->branch_id){
                    if (isset($confirm_go)){
                        if ($pv_id == 0) {
                            $this->payment_voucher_model->insert_payment_voucher();
                        } else {
                            $this->payment_voucher_model->update_payment_voucher($pv_id);
                        }
                    } else {
                        $this->pay($string, $encrypt_id, $pv_number, $tipe, 1);
                    }
                } else {
                    if ($pv_id == 0) {
                        $this->payment_voucher_model->insert_payment_voucher();
                    } else {
                        $this->payment_voucher_model->update_payment_voucher($pv_id);
                    }
                }
            } else {
                // pay by kembali nota
                if ($pv_id == 0) {
                    $this->payment_voucher_model->insert_payment_voucher();
                } else {
                    $this->payment_voucher_model->update_payment_voucher($pv_id);
                }
            }
        }
        /* unset session */
        $post_data = array(
            'description',
            'admin_fee',
            'account_id',
            'bank_id',
            'bank_cek_from',
            'bank_bg_from',
            'received_name',
            'cash_from_cashrequest',
            'bank_account_name_to',
            'bank_account_num_to',
            'bank_name_to'
        );
        $this->session->unset_userdata($post_data);
    }
    
    public function detail($string = '', $encrypt_id='') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;    
                $this->load->helper('form');
                $this->load->helper('file');
                $pv_number = '';
                $nota_from = '';
                $pv_title = '';
                $total = 0;
                $description = '';
                $admin_fee = 0;
                $payment_mode = 0;
                $payment_mode_text = '';
                $bank_id = 0;
                $bank_outlet = '';
                $account_name = '';
                $bank_account_name = '';
                $bank_account_num = '';
                $bank_name_to = '';
                $received_name = '';
                $pv_date = '';
                $paid_by = 0;
                $branch_id = 0;
                /* update pp id dan cash request id | 20180512*/
                $pp_id = 0;
                $cash_request_id = 0;
                $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
                $pv_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                $datapv = $this->payment_voucher_model->get_payment_voucher_by_id($pv_id);
                if ($datapv->num_rows()!=0){
                    $row = $datapv->row();
                    $pv_number = $row->pv_number;
                    $pv_title = $row->pv_title;
                    $pv_date = $row->pv_date;
                    $nota_from = $row->nota_from;
                    $total = $row->total;
                    $branch_id = $row->branch_id;
                    $description = $row->description;
                    $admin_fee = $row->admin_fee;
                    $payment_mode = $row->payment_mode;
                    $payment_mode_text = $payment_opt[$row->payment_mode];
                    $account_id = $row->account_id == ''? 0:$row->account_id;
                    $this->load->model('account_model');
                    $account = $this->account_model->get_account_by_id($account_id);
                    if ($account->num_rows()!=0){
                        $ar = $account->row();
                        $account_name = $ar->account_name;
                    }
                    $bank_id = $row->bank_id == '' ? 0:$row->bank_id;
                    $this->load->model('bank_model');
                    $bankdata = $this->bank_model->get_bank_account_by_id($bank_id);
                    if ($bankdata->num_rows()!=0){
                        $br = $bankdata->row();
                        $bank_outlet = $br->bank_account_name . ' ('.$br->bank_account_no.') - '.$br->branch_name;
                    }
                    $bank_account_name = $row->bank_account_name_to;
                    $bank_account_num = $row->bank_account_num_to;
                    $bank_name_to = $row->bank_name_to;
                    $bank_cek_from = $row->bank_cek_from;
                    $bank_bg_from = $row->bank_bg_from;
                    $received_name = $row->received_name;
                    $paid_by = $row->paid_by;
                    $pp_id = $row->pp_id;
                    $cash_request_id = $row->cash_request_id;
                    if ($row->pp_id != 0){
                        $caption_number = 'PP';
                    } else {
                        $caption_number = 'CR';
                    }
                    $data['caption_number'] = $caption_number;
                    /* ======= update 13 Feb 2018 ======= */  
                    $data['input_pv_id'] = $this->general_model->draw_hidden_field('input_pv_id', '');
                    $data['input_pv_date'] = $this->general_model->draw_datepicker('PV Date', 0, 'input_pv_date', '');
                    $data['input_pv_number'] = $this->general_model->draw_hidden_field('input_pv_number', '');
                    $data['input_description'] = $this->general_model->draw_text_field('Description', 1, 'input_description', '', '', '');
                    $data['input_total'] = $this->general_model->draw_hidden_field('input_total', '');
                    $data['input_admin_fee_org'] = $this->general_model->draw_hidden_field('input_admin_fee_org', '');
                    $data['input_admin_fee'] = $this->general_model->draw_input_currency('Admin Fee', 1, 'input_admin_fee', '');
                    $data['input_bank_name_to'] = $this->general_model->draw_text_field('Bank', 1, 'input_bank_name_to', '', '', '');
                    $data['input_bank_account_name_to'] = $this->general_model->draw_text_field('Nama Rekening', 1, 'input_bank_account_name_to', '', '', '');
                    $data['input_bank_account_num_to'] = $this->general_model->draw_text_field('No. Rekening', 1, 'input_bank_account_num_to', '', '', '');
                    $data['input_received_name'] = $this->general_model->draw_text_field('Received', 1, 'input_received_name', '', '', '');
                    $data['input_trans_id'] = $this->general_model->draw_hidden_field('input_trans_id', '');
                }
                // daa branch
                $this->load->model('branch_model');
                $databranch = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($databranch->num_rows()!=0){
                    foreach ($databranch->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['outlet'] = $branch_opt[$branch_id];
                
                $data['pv_id'] = $pv_id;
                $data['pv_number'] = $pv_number;
                $data['nota_from'] = $nota_from;
                $data['pv_title'] = $pv_title;
                $data['total'] = $total;
                $data['description'] = $description;
                $data['admin_fee'] = $admin_fee;
                $data['payment_mode'] = $payment_mode;
                $data['payment_mode_text'] = $payment_mode_text;
                $data['bank_id'] = $bank_id;
                $data['bank_outlet'] = $bank_outlet;
                $data['account_name'] = $account_name;
                $data['bank_account_name_to'] = $bank_account_name;
                $data['bank_account_num_to'] = $bank_account_num;
                $data['bank_name_to'] = $bank_name_to;
                $data['bank_cek_from'] = $bank_cek_from;
                $data['received_name'] = $received_name;
                $data['pv_date'] = $pv_date;
                $data['paid_by'] = $paid_by;
                /* update 20180512 */
                $data['pp_id'] = $pp_id;
                $data['cash_request_id'] = $cash_request_id;
                $header = $this->asik_model->draw_header('Payment Voucher', 'Detail', $this->category_index, $category, $module);
                $data['action_paid'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_paid);
                $data['pagecode'] = $string;
                $data['encrypt_id'] = $encrypt_id;
                $data['active_li'] = $this->category_index;
                $data['content_header'] = $header;
                $data['paymentvoucher_file'] = $this->payment_voucher_model->get_payment_voucher_file_by_pv_id($pv_id);
                $data['print_link'] = 'printout/pv/'. date('Ymd').$pv_id;
                
                $data['datatable_title'] = 'Payment voucher';
                $footer_total = '';
                $data['footer_total'] = $footer_total;
                
                //$data['show_modal'] = 'paymentvoucher/paymentvoucher_modal.php';
                $data['halaman'] = 'paymentvoucher/paymentvoucher_detail.php';
                $data['show_modal'] = 'paymentvoucher/paymentvoucher_upload.php';
                $this->load->view('template', $data);
            }
        }
    }
    
    public function do_upload() {
        $id = $this->input->post('pv_id');
        $encrypt_id = $this->input->post('encrypt_id');
                
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'pv'.date('YmdHis');
        
        $config['upload_path'] = './assets/paymentvoucher/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 9000;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->payment_voucher_model->update_file_name($id, $info['orig_name'], strtolower($info['file_ext']));
        }

        $back = '/paymentvoucher/detail/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_02 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function check_paid($pv_id = 0, $encrypt_id='') {
        $data = array(
            'paid_by' => $this->session->userdata('user_id')
        );

        $this->db->where('pv_id', $pv_id);
        $this->db->update('payment_voucher', $data);
        $back = '/paymentvoucher/detail/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_02 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function trans06_delete($id, $pp_id) {
        $this->payment_voucher_model->delete_trans_by_id($id);
        $data_pp = array(
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data_pp);
        echo json_encode(array("status" => TRUE));
    }
    
    public function delete_file($pv_file_id, $file_name, $encrypt_id) {
        unlink('./assets/paymentvoucher/' . $file_name);
        $this->db->where('pv_file_id', $pv_file_id);
        $this->db->delete('payment_voucher_file');
        $back = '/paymentvoucher/detail/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_02 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function ajax_edit($pv_id) {
        $pv_data = $this->payment_voucher_model->get_payment_voucher_by_id($pv_id);
        $data = array();
        if ($pv_data->num_rows()!=0){
            $row = $pv_data->row();
            //$trans_id = $this->get_trans_by_pv_number($row->pv_number);
            $data["pv_id"] = $row->pv_id;
            $data["pv_date"] = $row->pv_date;
            $data["pv_number"] = $row->pv_number;
            $data["description"] = $row->description;
            $data["total"] = $row->total;
            $data["admin_fee"] = $row->admin_fee;
            $data["bank_name_to"] = $row->bank_name_to;
            $data["bank_account_name_to"] = $row->bank_account_name_to;
            $data["bank_account_num_to"] = $row->bank_account_num_to;
            $data["received_name"] = $row->received_name;
            $data["trans_id"] = $row->trans_id; /*update : 2018-05-30*/
        }        

        echo json_encode($data);
    }
    
    public function update_pv_edit() {
        $input_pv_id = $this->input->post('input_pv_id'); 
        $input_pv_date = $this->input->post('input_pv_date');
        $input_pv_number = $this->input->post('input_pv_number'); 
        $input_description = $this->input->post('input_description'); 
        $input_total = $this->input->post('input_total');  
        $input_admin_fee_org = $this->input->post('input_admin_fee_org');   
        $input_admin_fee = $this->general_model->change_decimal($this->input->post('input_admin_fee'));
        $input_bank_name_to = $this->input->post('input_bank_name_to');   
        $input_bank_account_name_to = $this->input->post('input_bank_account_name_to');   
        $input_bank_account_num_to = $this->input->post('input_bank_account_num_to');   
        $input_received_name = $this->input->post('input_received_name');   
        $input_trans_id = $this->input->post('input_trans_id');   
        
        $admin_fee = $input_admin_fee_org;
        if ($input_trans_id != 0){
            if (is_numeric($input_admin_fee)){
                if ($input_admin_fee != $input_admin_fee_org){
                    $total = $input_total + $input_admin_fee;
                    $admin_fee = $input_admin_fee;
                    // update to transactions
                    $datatrans = array('amount' => $total);
                    $this->db->where('trans_id', $input_trans_id);
                    $this->db->update('transactions', $datatrans);
                    // update to ledger
                    $this->db->where(array('trans_id' => $input_trans_id, 'debit' => 0));
                    $this->db->update('ledger', array('credit'=>$total));
                    $this->db->where(array('trans_id' => $input_trans_id, 'credit' => 0));
                    $this->db->update('ledger', array('debit'=>$total));
                    // update to expense table
                    $this->db->where('pv_number', $input_pv_number);
                    $this->db->update('expense', $datatrans);
                    // update to outstanding table
                    $this->db->where('pv_number', $input_pv_number);
                    $this->db->update('outstanding', $datatrans);
                }
            }
            
        }
        
        $data = array(
            'pv_date' => $input_pv_date,
            'description' => $input_description,
            'admin_fee' => $admin_fee,
            'bank_name_to' => $input_bank_name_to,
            'bank_account_name_to' => $input_bank_account_name_to,
            'bank_account_num_to' => $input_bank_account_num_to,
            'received_name' => $input_received_name,
            'username' => $this->session->userdata('username')
        );

        $this->db->where('pv_id', $input_pv_id);
        $this->db->update('payment_voucher', $data);
        
        // update to transactions date
        $datatrans_date = array('trans_date' => $input_pv_date);
        $this->db->where('trans_id', $input_trans_id);
        $this->db->update('transactions', $datatrans_date);
        
        $dataex = array('expense_date'=>$input_pv_date);
        // update to expense table
        $this->db->where('trans_id', $input_trans_id);
        $this->db->update('expense', $dataex);
        // update to outstanding table
        $dataos = array('outstanding_date'=>$input_pv_date);
        $this->db->where('trans_id', $input_trans_id);
        $this->db->update('outstanding', $dataos);
        // update cash receive date
        $datacashreceive = array(
            'cash_receive_date' => $input_pv_date
        );
        $this->db->where('trans_id', $input_trans_id);
        $this->db->update('cash_receive', $datacashreceive);
        
        echo json_encode(array("status" => TRUE));
    }
    
    public function get_trans_by_pv_number($pv_number='') {
        $sql  = 'SELECT trans_id FROM transactions WHERE pv_number="'.$pv_number.'"';
        $query = $this->db->query($sql);
        $trans_id = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $trans_id = $row->trans_id;
        }
        return $trans_id;
    }
    
    public function get_payment_process_by_pp_type($type=0) {
        $pp_type = ''.$type;
        if ($type == 5){
            $pp_type = '0,1,2,3,4';
        }
        $this->load->model('payment_process_model');
        $sql  = 'SELECT pp.* FROM payment_process AS pp ';
        $sql .= 'WHERE pp.pp_status='.$this->payment_process_model->approved.' ';
        $sql .= 'AND pp.pp_type IN('.$pp_type.') ';
        $sql .= 'ORDER BY pp_date DESC';
        $query = $this->db->query($sql);
        return $query;
    }
}