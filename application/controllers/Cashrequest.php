<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cashrequest
 *
 * @author Hendra McHen
 */
class Cashrequest extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('cashrequest_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_transaction) {
            if (($module == $this->asik_model->trans_02) && ($string == $category . $module)) {
                $this->category_index = 1;
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
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                $data['action_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);
                $data['action_download'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_download);
                $data['action_checked'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_checked);
                $data['action_approved'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_approved);
                /* end privilege */
                $this->load->helper('form');
                $this->load->helper('file');
                $data['pagecode'] = $string;
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                $field_search = $this->input->post('field_search');
                $keyword = $this->input->post('keyword');
                
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Cash Request', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                if ($button == 0){
                    $data['list'] = $this->cashrequest_model->get_cashrequest_list($start_date, $end_date, $field_search, $keyword);
                    $arr_pv = $this->cashrequest_model->get_arr_pv($start_date, $end_date, $field_search, $keyword);
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
                    $data['list'] = $this->cashrequest_model->get_cashrequest_list($start_date, $end_date, $field_search, $keyword);
                    $arr_pv = $this->cashrequest_model->get_arr_pv($start_date, $end_date, $field_search, $keyword);
                }
                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                
                $fields = array(
                    "cash_request_number"=>"ID", 
                    "employee_name"=>"Employee",
                    "branch_id"=>"Outlet"
                    );
                $data['field_opt'] = $fields;
                $data['halaman'] = 'cashrequest/cashrequest_list.php';
                $data['arr_pv'] = $arr_pv;
                /*===== form input =====*/
                $today = date('Y-m-d');
                $number = $this->general_model->get_generate_number('CR', 'cash_request', 'cash_request_id');
                $data['cash_request_id'] = $this->general_model->draw_hidden_field('cash_request_id', '');
                $data['cash_request_number'] = $this->general_model->draw_hidden_field('cash_request_number', $number);
                $data['cash_request_number_disable'] = $this->general_model->draw_text_disabled('Number', 'cash_request_number_disable', $number);
                $data['cash_request_date'] = $this->general_model->draw_datepicker('Date', 1, 'cash_request_date', $today);
                $this->load->model('employee_model');
                $employee = $this->employee_model->get_employee_list();
                $emp_opt = array();
                $emp_opt[0] = '-None-';
                if ($employee->num_rows()!=0){
                    foreach ($employee->result() as $value) {
                        $emp_opt[$value->employee_id] = $value->full_name;
                    }
                }
                $this->load->model('branch_model');
                $branch = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch->num_rows()!=0){
                    foreach ($branch->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['employee_id'] = $this->general_model->draw_select('Employee', 0, 'employee_id', 1, $emp_opt, '', 1);
                $emp_ket = 'Type employee name, if you want to create new employee';
                $data['employee_name'] = $this->general_model->draw_text_field('New Employee', 1, 'employee_name', '', $emp_ket, '');
                $data['branch_id'] = $this->general_model->draw_select('Outlet of destination', 0, 'branch_id', 0, $branch_opt, '');
                $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
                $data['remark'] = $this->general_model->draw_textarea('Remark', 1, 'remark', '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
                $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
                $data['payment_mode'] = $this->general_model->draw_select('Payment Method', 0, 'payment_mode', 0, $payment_opt, '', 0);
                $this->load->model('account_model');
                $account = $this->account_model->get_account_by_keyword(0, 'Petty');
                $account_opt = array();
                if ($account->num_rows()!=0){
                    foreach ($account->result() as $value) {
                        $account_opt[$value->account_id] = $value->account_name;
                    }
                }
                $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 1, $account_opt, '', 1);
                $data['cash_return'] = $this->general_model->draw_input_currency('Cash Return', 1, 'cash_return', '');
                $data['show_modal'] = 'cashrequest/cashrequest_modal.php';
                /*===== end form input =====*/
                /* ===== start datatable ===== */
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
                /* ===== end datatable ===== */
                /* get footer info */
                $datafooter = $this->cashrequest_model->get_last_record();
                $username = '';
                $lastupdate = '';
                $transid = 0;
                if ($datafooter->num_rows()!=0){
                    $row = $datafooter->row();
                    $username = $row->username;
                    $lastupdate = $row->last_update;
                    $transid = $row->cash_request_number;
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

    public function ajax_edit($id) {
        $data = $this->cashrequest_model->get_cashrequest_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function cashrequest_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('cash_request_number', 'Note number', 'required');
        $this->form_validation->set_rules('cash_request_date', 'Note date', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->cashrequest_model->insert_cashrequest();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function cashrequest_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cash_request_number', 'Note number', 'required');
        $this->form_validation->set_rules('cash_request_date', 'Note date', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->cashrequest_model->update_cashrequest($this->input->post('cash_request_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function cashrequest_delete($id) {
        $this->cashrequest_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function cdetail($string, $encrypt_id = '') {
        $cash_request_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            $this->load->helper('form');
            $this->load->helper('file');
            /* start privilege */
            // value = TRUE or FALSE
            
            $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
            $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
            $data['action_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);

            $data['action_checked'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_checked);
            $data['action_approved'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_approved);
            /* end privilege */
            /*===== form input =====*/
            
            $data['cash_request_id'] = $this->general_model->draw_hidden_field('cash_request_id', '');
            $data['cash_request_number'] = $this->general_model->draw_hidden_field('cash_request_number', '');
            $data['cash_request_number_disable'] = $this->general_model->draw_text_disabled('Number', 'cash_request_number_disable', '');
            
            $data['cash_request_date'] = $this->general_model->draw_datepicker('Date', 1, 'cash_request_date', '');
            $this->load->model('employee_model');
            $employee = $this->employee_model->get_employee_list();
            $emp_opt = array();
            $emp_opt[0] = '-None-';
            if ($employee->num_rows()!=0){
                foreach ($employee->result() as $value) {
                    $emp_opt[$value->employee_id] = $value->full_name;
                }
            }
            $this->load->model('branch_model');
            $branch = $this->branch_model->get_branch_list();
            $branch_opt = array();
            if ($branch->num_rows()!=0){
                foreach ($branch->result() as $value) {
                    $branch_opt[$value->branch_id] = $value->branch_name;
                }
            }
            $data['employee_id'] = $this->general_model->draw_select('Employee', 0, 'employee_id', 1, $emp_opt, '', 1);
            $emp_ket = 'Type employee name, if there is no data in Master Employee';
            $data['employee_name'] = $this->general_model->draw_text_field('Employee name', 1, 'employee_name', '', $emp_ket, '');
            $data['branch_id'] = $this->general_model->draw_select('Branch', 0, 'branch_id', 0, $branch_opt, '');
            $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
            $data['remark'] = $this->general_model->draw_textarea('Remark', 1, 'remark', '');
            $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
            $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
            $data['payment_mode'] = $this->general_model->draw_select('Payment Method', 0, 'payment_mode', 0, $payment_opt, '', 0);
            $this->load->model('account_model');
            $account = $this->account_model->get_account_by_keyword(0, 'Petty');
            $account_opt = array();
            if ($account->num_rows()!=0){
                foreach ($account->result() as $value) {
                    $account_opt[$value->account_id] = $value->account_name;
                }
            }
            $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 1, $account_opt, '', 1);
            $data['cash_return'] = $this->general_model->draw_input_currency('Cash Return', 1, 'cash_return', '');
            $data['show_modal'] = 'cashrequest/cashrequest_modal.php';
            /*===== end form input =====*/
            /* ===== start datatable ===== */
            $data['datatable_title'] = 'Cash Request';
            $data['footer_total'] = '';
            $data['pagecode'] = $string;
            $data['active_li'] = $this->category_index;
            $header = $this->asik_model->draw_header('Cash Request', 'Detail', $this->category_index, $category, $module);
            $data['content_header'] = $header;
            $data['encrypt_id'] = $encrypt_id;
            $data['back_link'] = 'cashrequest/go/'.$string;
            $data['closing_link'] = 'cashrequest/closing_update/'.$string.'/'.$encrypt_id;
            // update print_link | 2018-06-16
            $data['print_link'] = 'printout/cashrequest/'. date('Ymd').$cash_request_id;
            $data['balance_check'] = $this->get_crbalance($cash_request_id);
            $data['crbalance_list'] = $this->get_crbalance_list($cash_request_id);
            $data['cash_request_detail'] = $this->cashrequest_model->get_cashrequest_by_id($cash_request_id);
            $data['cash_request_file'] = $this->cashrequest_model->get_file_by_cashreq_id($cash_request_id);
            $data['pp_cash_request'] = $this->cashrequest_model->get_pp_by_cr_id($cash_request_id);
            $data['remark_list'] = $this->cashrequest_model->get_remark_list($cash_request_id);
            $data['halaman'] = 'cashrequest/cashrequest_detail.php';
            $this->load->view('template', $data);
        } else {
            show_404();
        }
    }
    
    public function do_upload() {
        $id = $this->input->post('cash_request_id');
        $encrypt_id = $this->input->post('encrypt_id');
        
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'cr'.date('YmdHis');
        
        $config['upload_path'] = './assets/cashrequest/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 9000;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->cashrequest_model->update_file_name($id, $info['orig_name'], strtolower($info['file_ext']));
        }
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/cashrequest/cdetail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function do_upload_pr() {
        $id = $this->input->post('cash_request_id');
        $note = $this->cashrequest_model->get_cashrequest_by_id($id);
        if ($note->num_rows()!=0){
            $row = $note->row();
            unlink('./assets/files/' . $row->file_pr);
        }
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'pr-'.$id;
        $config['upload_path'] = './assets/files/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 3540;
        $config['max_width'] = 5000;
        $config['max_height'] = 5000;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->cashrequest_model->update_file_pr($id, $info['orig_name']);
        }
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/cashrequest/go/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/';
        redirect($back);
    }
    
    public function checked($encrypt_id = '') {
        $cash_request_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $data = array(
            'checked_by' => $this->session->userdata('user_id'),
            'cash_request_status' => 1
        );

        $this->db->where('cash_request_id', $cash_request_id);
        $this->db->update('cash_request', $data);
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/cashrequest/cdetail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function approved($encrypt_id = '') {
        $cash_request_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $data = array(
            'approved_by' => $this->session->userdata('user_id'),
            'cash_request_status' => 2
        );

        $this->db->where('cash_request_id', $cash_request_id);
        $this->db->update('cash_request', $data);
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/cashrequest/cdetail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function trans_pay() {
        $cash_request_id = $this->input->post('cash_request_id');
        $account_id = $this->input->post('account_id');
        $data = array(
            'cash_request_status' => 3,
            'account_id' => $account_id
        );

        $this->db->where('cash_request_id', $cash_request_id);
        $this->db->update('cash_request', $data);
        $this->cashrequest_model->insert_trans_cashreq($cash_request_id, $account_id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function trans_cash_return() {
        $cash_request_id = $this->input->post('cash_request_id');
        $cash_return = $this->general_model->change_decimal($this->input->post('cash_return'));
        
        $data = array(
            'cash_return' => $cash_return
        );

        $this->db->where('cash_request_id', $cash_request_id);
        $this->db->update('cash_request', $data);
        
        $this->cashrequest_model->insert_trans_cash_return($cash_request_id, $cash_return);
        echo json_encode(array("status" => TRUE));
    }
    
    public function delete_file($cash_request_file_id, $file_name, $encrypt_id) {
        unlink('./assets/cashrequest/' . $file_name);
        $this->db->where('cash_request_file_id', $cash_request_file_id);
        $this->db->delete('cash_request_file');
        
        $back = '/cashrequest/cdetail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function add_remark() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('remark', 'Remark', 'required');
        $encrypt_id = $this->input->post('encrypt_id');
        if ($this->form_validation->run() == TRUE) {
            $cash_request_id = $this->input->post('cash_request_id');
            $remark = $this->input->post('remark');

            $data = array(
                'cash_request_id' => $cash_request_id,
                'remark' => $remark
            );
            $this->db->insert('cash_request_remark', $data);
        }
        $back = '/cashrequest/cdetail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function add_remark_from_modal() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('remark', 'Remark', 'required');
        if ($this->form_validation->run() == TRUE) {
            $cash_request_id = $this->input->post('cash_request_id');
            $remark = $this->input->post('remark');

            $data = array(
                'cash_request_id' => $cash_request_id,
                'remark' => $remark
            );
            $this->db->insert('cash_request_remark', $data);
        }
        echo json_encode(array("status" => TRUE));
    }
    
    public function closing_update($string='', $encrypt_id='') {
        $cash_request_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $cashrequest = $this->cashrequest_model->get_cashrequest_by_id($cash_request_id);
        $pv_number = '-';
        if ($cashrequest->num_rows()!=0){
            $row = $cashrequest->row();
            $pv_number = $row->pv_number;
        }
        $data = array(
            'cash_request_status' => $this->cashrequest_model->closed
        );

        $this->db->where('cash_request_id', $cash_request_id);
        $this->db->update('cash_request', $data);
        
        $dataos = array(
            'outstanding_status' => 1
        );
        
        $this->db->where('pv_number', $pv_number);
        $this->db->update('outstanding', $dataos);
        
        $back = '/cashrequest/cdetail/' .$string. '/'.$encrypt_id;
        redirect($back);
    }
    
    public function get_crbalance($cash_request_id=0) {
        $sql  = 'SELECT SUM(debit) AS total_debit, SUM(credit) AS total_credit  ';
        $sql .= 'FROM cash_request_balance  AS crb WHERE cash_request_id ='.$cash_request_id;
        $query = $this->db->query($sql);
        $balance = 1;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $balance = $row->total_debit - $row->total_credit;
        }
        return $balance;
    }
    
    public function get_crbalance_list($cash_request_id=0) {
        $sql  = 'SELECT * FROM cash_request_balance WHERE cash_request_id ='.$cash_request_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function os() {
        $category = $this->asik_model->category_transaction;
        $module = $this->asik_model->trans_02;
        $data['cashrequest_outstanding'] = $this->get_outstanding_cash_request();
        /* ===== start datatable ===== */
        $data['pagecode'] = '20191121214302';
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
                    alltotal = api
                                .column(5, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(5).footer() ).html(
                                numeral(alltotal).format("0,0.00")
                        );}';
        $data['footer_total'] = $footer_total;
        $data['active_li'] = $this->category_index;
        $header = $this->asik_model->draw_header('Cash Request', 'Outstanding', $this->category_index, $category, $module);
        $data['content_header'] = $header;
        $data['halaman'] = 'cashrequest/cashrequest_dashboard.php';
        $this->load->view('template', $data);
    }
    
    public function get_outstanding_cash_request() {
        $sql  = 'SELECT cash_request.*, branch.branch_name FROM cash_request ';
        $sql .= 'INNER JOIN branch ON branch.branch_id=cash_request.branch_id ';
        $sql .= 'WHERE  cash_request.cash_request_status=3 ';
        $sql .= 'ORDER BY last_update DESC';
        $query = $this->db->query($sql);
        return $query;
    }
}