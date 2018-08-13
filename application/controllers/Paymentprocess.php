<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Paymentprocess
 *
 * @author mchen
 */
class Paymentprocess extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('payment_process_model');
    }

    public $category_index = 2;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_configuration) {
            if (($module == $this->asik_model->config_01) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function go($string = '', $button=0, $shdate='', $ehdate='') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $is_cashier = $this->get_priv_name($this->session->userdata('priv_group_id'));
                $data['is_cashier'] = $is_cashier;
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                $data['action_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);
                $data['action_download'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_download);
                $data['action_checked'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_checked);
                $data['action_approved'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_approved);
                $pp_type = 0;
                if ($is_cashier == 1){
                    $pp_type = 2;
                }
                /* end privilege */
                
                $this->load->helper('form');
                $this->load->helper('file');
                $data['pagecode'] = $string;
                $start_date = $this->input->post('start_date')==''?$shdate:$this->input->post('start_date');
                $end_date = $this->input->post('end_date')==''?$ehdate:$this->input->post('end_date');
                $field_search = $this->input->post('field_search');
                $keyword = $this->input->post('keyword');
                $field_status = $this->input->post('field_status');
                if ($button == 0){
                    $data['list'] = $this->payment_process_model->get_payment_process_list($start_date, $end_date, $field_search, $keyword, $field_status, $pp_type);
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
                    $data['list'] = $this->payment_process_model->get_payment_process_list($start_date, $end_date, $field_search, $keyword, $field_status, $pp_type);
                }
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['field_search'] = $field_search;
                $data['keyword'] = $keyword;
                $data['field_status'] = $field_status;
                $fields = array(
                    "pp_number"=>"Number",
                    "pp_title"=>"Title",
                    "branch_name"=>"Outlet"
                    );
                $data['field_opt'] = $fields;
                
                /*====== form ======*/
                
                $gn_number = $this->general_model->get_generate_number('GN', 'payment_process', 'pp_id');
                $sc_number = $this->general_model->get_generate_number('SC', 'payment_process', 'pp_id');
                $ce_number = $this->general_model->get_generate_number('CE', 'payment_process', 'pp_id');
                $os_number = $this->general_model->get_generate_number('OT', 'payment_process', 'pp_id');
                $pr_number = $this->general_model->get_generate_number('PR', 'payment_process', 'pp_id');
                $data['gn_number'] = $gn_number;
                $data['sc_number'] = $sc_number;
                $data['ce_number'] = $ce_number;
                $data['os_number'] = $os_number;
                $data['pr_number'] = $pr_number;
                /* form GN */
                
                /* form SC */
                $this->load->model('supplier_model');
                $supplier = $this->supplier_model->get_supplier_from_invoice();
                $supplier_opt = array();
                if ($supplier->num_rows()!=0){
                    foreach ($supplier->result() as $value) {
                        $supplier_opt[$value->credit_invoice_id] = $value->supplier_name.' - '.$value->branch_name;
                    }
                }
                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $this->load->model('cashrequest_model');
                $cashrequest = $this->cashrequest_model->get_cashrequest_by_status(3);
                $cashrequest_opt = array();
                if ($cashrequest->num_rows()!=0){
                    $cashrequest_opt[0] = 'No Data';
                    foreach ($cashrequest->result() as $value) {
                        $cashrequest_opt[$value->cash_request_id] = $value->cash_request_number.' - '.$value->employee_name.' - '.$value->branch_name .' - '.number_format($value->amount);
                    }
                }
                /* updated : 2018-03-28 */
                $this->load->model('thirdparty_model');
                $thirdparty = $this->thirdparty_model->get_third_party_list();
                $thirdparty_opt = array();
                if ($thirdparty->num_rows()!=0){
                    $thirdparty_opt[0] = 'None';
                    foreach ($thirdparty->result() as $value) {
                        $thirdparty_opt[$value->third_party_id] = $value->third_party_name;
                    }
                }
                /*input global */
                $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', '');
                $data['pp_number_disabled'] = $this->general_model->draw_text_disabled('PP Number', 'pp_number_disabled', '');
                $data['pp_number'] = $this->general_model->draw_hidden_field('pp_number', ''); 
                $data['pp_date'] = $this->general_model->draw_datepicker('PP Date', 1, 'pp_date', date('Y-m-d'));
                $payment_opt = array('Cash', 'Transfer ATM', 'Online (Token)', 'Cek', 'BG');
                $data['payment_mode'] = $this->general_model->draw_select('Payment Method', 0, 'payment_mode', 0, $payment_opt, '', 0);
                /* pp_title : untuk pp general dan pp expense */
                $data['pp_title'] = $this->general_model->draw_text_field('PP Title', 1, 'pp_title', '', '', '');
                /* supplier_id : untuk pp supplier */
                $data['supplier_id'] = $this->general_model->draw_select('Supplier', 0, 'supplier_id', 1, $supplier_opt, '', 1);
                /* branch_id : untuk pp supplier dan pp expense */
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 0, $branch_opt, '', 0);
                /*cash request : for PP general*/
                $data['cash_request_id'] = $this->general_model->draw_select('Cash Request Number (untuk kembali nota)', 0, 'cash_request_id', 1, $cashrequest_opt, '', 1);
                $data['job_order'] = $this->general_model->draw_text_field('Reference No. (Job Order)', 0, 'job_order', '', '', '');
                $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
                $data['unit'] = $this->general_model->draw_input_number('Unit', 0, 'unit', 1);
                $data['price'] = $this->general_model->draw_input_currency('Price', 0, 'price', '');
                /*for pp outstanding */
                $data['third_party_id'] = $this->general_model->draw_select('Select Third Party', 0, 'third_party_id', 1, $thirdparty_opt, '', 1);
                //$thr_ket = 'Create new third party name..';
                //$data['third_party_name'] = $this->general_model->draw_text_field('New Third Party', 0, 'third_party_name', '', $thr_ket, '');
                /* for pp project */
                $vendor_list = $this->get_vendor_list();
                $vendor_opt = array();
                if ($vendor_list->num_rows()!=0){
                    $vendor_opt[0] = 'None';
                    foreach ($vendor_list->result() as $value) {
                        $vendor_opt[$value->vendor_id] = $value->vendor_name;
                    }
                }
                //$ven_ket = 'Create new vendor name..';
                $data['vendor_id'] = $this->general_model->draw_select('Vendor', 0, 'vendor_id', 1, $vendor_opt, '', 1);
                //$data['vendor_name'] = $this->general_model->draw_text_field('New Vendor', 0, 'vendor_name', '', $ven_ket, '');
                
                $data['show_modal'] = 'paymentprocess/pp_modal.php';
                /* ===== start datatable ===== */
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
                /* ===== end datatable ===== */
                /* get footer info */
                $datafooter = $this->payment_process_model->get_last_record();
                $username = '';
                $lastupdate = '';
                $transid = 0;
                if ($datafooter->num_rows()!=0){
                    $row = $datafooter->row();
                    $username = $row->username;
                    $lastupdate = $row->last_update;
                    $transid = $row->pp_number;
                }
                $data['username'] = $username;
                $data['last_update'] = $lastupdate;
                $data['transid'] = $transid;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Payment Process', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'paymentprocess/payment_process_list.php';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_edit($id) {
        $data = $this->payment_process_model->get_payment_process_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function pp_add($pp_type=0) {
        $pp_id = $this->payment_process_model->insert_payment_process($pp_type);
        $encrypt_id = $this->general_model->encrypt_value($pp_id);
        echo json_encode(array("status" => TRUE, "pp_id"=>$encrypt_id));
    }
    
    public function pp_update($pp_type=0) {
        $pp_id = $this->payment_process_model->update_payment_process($pp_type);
        $encrypt_id = $this->general_model->encrypt_value($pp_id);
        echo json_encode(array("status" => TRUE, "pp_id"=>$encrypt_id));
    }
    
    public function pp_sc_update() {
        $pp_id = $this->input->post('pp_id');
        $pp_date = $this->input->post('pp_date');
        $payment_mode = $this->input->post('payment_mode');
        $data = array(
            'pp_date' => $pp_date,
            'payment_mode' => $payment_mode,
            'username' => $this->session->userdata('username')
        );

        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        
        echo json_encode(array("status" => TRUE));
    }

    public function pp_delete($id) {
        $this->payment_process_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    public function do_upload() {
        $id = $this->input->post('up_pp_id');
        $pp = $this->payment_process_model->get_payment_process_by_id($id);
        if ($pp->num_rows()!=0){
            $row = $pp->row();
            unlink('./assets/files/' . $row->file_name);
        }
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'pp_nota-'.$id;
        $config['upload_path'] = './assets/files/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['max_width'] = 5000;
        $config['max_height'] = 5000;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $data = array('file_name'=>$info['orig_name']);
            $this->db->where('pp_id', $id);
            $this->db->update('payment_process', $data);
        }
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/paymentprocess/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/';
        redirect($back);
    }
    
    public function update_payment_mode() {
        $pp_id = $this->input->post('pp_id');
        $payment_mode = $this->input->post('payment_mode');
        $pp_date = $this->input->post('pp_date');
        $branch_id = $this->input->post('branch_id');
        
        $data = array(
            'pp_date' => $pp_date,
            'branch_id' => $branch_id,
            'payment_mode' => $payment_mode,
            'username' => $this->session->userdata('username')
        );

        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        // update pp detail
        $branch_name = $this->get_branch_name($branch_id);
        $datadetail = array(
            'act_title' => $branch_name,
            'branch_id' => $branch_id
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process_detail', $datadetail);
        
        
        echo json_encode(array("status" => TRUE));
    }
    
    public function dash($string = '', $field_status=0, $start_date='', $end_date='') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $data['pagecode'] = $string;

                $data['list'] = $this->payment_process_model->get_payment_process_list_dash($start_date, $end_date, $field_status);
                /* ===== start datatable ===== */
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
                /* ===== end datatable ===== */
                
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Payment Process', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'paymentprocess/payment_process_dash.php';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_branch_name($branch_id=0) {
        $sql  = 'SELECT * FROM branch ';
        $sql .= 'WHERE branch_id = '.$branch_id;
        $query = $this->db->query($sql);
        $branch_name = '-';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $branch_name = $row->branch_name;
        }
        return $branch_name;
    }
    
    public function get_vendor_list() {
        $sql  = 'SELECT * FROM vendor ';
        $sql .= 'ORDER BY vendor_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function ppgeneral($string='', $param_pp_id=0, $param_cr_id=0, $process=0, $detail_id=0, $employee_id=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_cross_check'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_cross_check);
                $data['action_checked'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_checked);
                $data['action_approved'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_approved);
                
                
                //======= process insert =======
                $this->load->model('ppdetail_model');
                $detail = $this->ppdetail_model->get_detail_by_pp_id($param_pp_id);
                if ($process > 0){
                    switch ($process) {
                        case 1:
                            $pp_id = $this->payment_process_model->insert_ppgeneral(0);
                            $param_pp_id = $pp_id;
                            break;

                        case 2:
                            if ($detail_id != 0){
                                $this->ppdetail_model->delete_by_id($detail_id);
                                $pp_id = $param_pp_id;
                            } else {
                                $this->ppdetail_model->insert_ppgeneral();
                                $pp_id = $param_pp_id;
                            }
                            break;
                        case 3:
                            $this->action_cross_check($param_pp_id);
                            $pp_id = $param_pp_id;
                            break;
                        case 4:
                            $this->action_checked($param_pp_id);
                            $pp_id = $param_pp_id;
                            break;
                        case 5:
                            $this->action_approved($param_pp_id);
                            $pp_id = $param_pp_id;
                            break;
                    }

                    if ($pp_id != 0){
                        $pp = $this->payment_process_model->get_payment_process_by_id($pp_id);
                        if ($pp->num_rows()!=0){
                            $row = $pp->row();
                            $pp_id = $row->pp_id;
                            $pp_number = $row->pp_number;
                            $pp_date = $row->pp_date;
                            $payment_mode = $row->payment_mode;
                            $pp_title = $row->pp_title;
                            $branch_id = $row->branch_id;
                            $cash_request_id = $row->cash_request_id;
                            $pp_status = $row->pp_status;
                            $prepare_by = $row->prepare_by;
                            $cross_check_by = $row->cross_check_by;
                            $checked_by = $row->checked_by;
                            $approved_by = $row->approved_by;
                        }
                        $detail = $this->ppdetail_model->get_detail_by_pp_id($pp_id);
                    }
                } else {
                    $pp_id = 0;
                    $pp_number = $this->general_model->get_generate_number('GN', 'payment_process', 'pp_id');
                    $pp_date = date('Y-m-d');
                    $payment_mode = 0;
                    $pp_title = '';
                    $branch_id = 0;
                    $cash_request_id = $param_cr_id;
                    $pp_status = 0;
                    $prepare_by = 0;
                    $cross_check_by = 0;
                    $checked_by = 0;
                    $approved_by = 0;
                }
                $data['cross_check_link'] = 'paymentprocess/ppgeneral/20191231214301/'.$param_pp_id.'/0/3/';
                $data['check_link'] = 'paymentprocess/ppgeneral/20191231214301/'.$param_pp_id.'/0/4/';
                $data['approve_link'] = 'paymentprocess/ppgeneral/20191231214301/'.$param_pp_id.'/0/5/';
                $cash_request_number = $this->get_crnumber_by_id($cash_request_id);
                
                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['param_pp_id'] = $param_pp_id;
                /*input global */
                $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', $pp_id);
                $data['pp_number_disabled'] = $this->general_model->draw_text_disabled('PP Number', 'pp_number_disabled', $pp_number);
                $data['pp_number'] = $this->general_model->draw_hidden_field('pp_number', $pp_number); 
                $data['pp_date'] = $this->general_model->draw_datepicker('PP Date', 1, 'pp_date', $pp_date);
                $payment_opt = array('Cash', 'Transfer ATM', 'Online (Token)', 'Cek', 'BG');
                $data['payment_mode'] = $this->general_model->draw_select('Payment Method', 0, 'payment_mode', 0, $payment_opt, $payment_mode);
                $data['pp_title'] = $this->general_model->draw_text_field('PP Title', 1, 'pp_title', '', '', $pp_title);
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 0, $branch_opt, $branch_id);
                $data['cash_request_disabled'] = $this->general_model->draw_text_disabled('CR Number', 'cash_request_disabled', $cash_request_number);
                $data['cash_request_id'] = $this->general_model->draw_hidden_field('cash_request_id', $cash_request_id);
                $data['employee_oid'] = $employee_id;
                $data['employee_id'] = $this->general_model->draw_hidden_field('employee_id', $employee_id);
                $employee_name = $this->get_employee_name($employee_id);
                $data['employee_disabled'] = $this->general_model->draw_text_disabled('Employee', 'employee_disabled', $employee_name);
                
                $data['pp_status'] = $pp_status;
                $data['prepare_by'] = $prepare_by;
                $data['cross_check_by'] = $cross_check_by;
                $data['checked_by'] = $checked_by;
                $data['approved_by'] = $approved_by;
                $data['detail'] = $detail;
                
                $data['datatable_title'] = '';
                $data['footer_total'] = '';
                /* ===== end datatable ===== */
                $this->load->helper('form');
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('PP General Form', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'paymentprocess/pp_general_form.php';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_crnumber_by_id($id=0) {
        $sql  = 'SELECT cash_request_number FROM cash_request ';
        $sql .= 'WHERE cash_request_id='.$id;
        $query = $this->db->query($sql);
        $cash_request_number = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $cash_request_number = $row->cash_request_number;
        }
        return $cash_request_number;
    }
    
    public function action_cross_check($pp_id = 0) {
        $data = array(
            'cross_check_by' => $this->session->userdata('user_id'),
            'pp_status' => 1
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
    }
    
    public function action_checked($pp_id = 0) {
        $data = array(
            'checked_by' => $this->session->userdata('user_id'),
            'pp_status' => 2
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
    }
    
    public function action_approved($pp_id = 0) {
        $data = array(
            'approved_by' => $this->session->userdata('user_id'),
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
    }
    
    public function get_employee_name($employee_id=0) {
        $sql  = 'SELECT full_name FROM employee ';
        $sql .= 'WHERE employee_id='.$employee_id;
        $query = $this->db->query($sql);
        $employee_name = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $employee_name = $row->full_name;
        }
        return $employee_name;
    }
    
    public function get_priv_name($group_id=0) {
        $sql  = 'SELECT priv_group_name FROM privilege_group ';
        $sql .= 'WHERE priv_group_id='.$group_id;
        $query = $this->db->query($sql);
        $is_cashier = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $priv_group_name = $row->priv_group_name;
            if ($priv_group_name == 'Cashier'){
                $is_cashier = 1;
            }
        }
        return $is_cashier;
    }
    
    public function get_refresh_status($pp_id=0) {        
        $data = array(
            'cross_check_by' => 0,
            'checked_by' => 0,
            'approved_by' => 0,
            'pp_status' => 0,
            'username' => $this->session->userdata('username')
        );

        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);        
        
        echo json_encode(array("status" => TRUE));
    }
    
    public function do_approve() {
        $checked = $this->input->post('chk_pp');
        $shdate = $this->input->post('shdate');
        $ehdate = $this->input->post('ehdate');
        if(isset($checked)){
            foreach ($checked as $value) {
                $data = array(
                    'approved_by' => $this->session->userdata('user_id'),
                    'pp_status' => 3
                );

                $this->db->where('pp_id', $value);
                $this->db->update('payment_process', $data); 
            }
        }

        $link = '/paymentprocess/go/' . $this->asik_model->category_configuration;
        $link .= $this->asik_model->config_01 . '/0/'.$shdate.'/'.$ehdate;
        redirect($link);
    }

}
