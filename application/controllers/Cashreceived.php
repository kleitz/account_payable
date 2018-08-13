<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cashreceived
 *
 * @author Hendra McHen
 */
class Cashreceived extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('cashreceived_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';


    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_transaction) {
            if (($module == $this->asik_model->trans_03) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0, $startd='', $endd='', $outlet=''){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;             
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                
                
                $this->load->helper('form');
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                $field_search = $this->input->post('field_search');
                $keyword = $this->input->post('keyword');
                if ($startd != ''){
                    $start_date = $startd;
                }
                if ($endd != ''){
                    $end_date = $endd;
                }  
                 
                // Tipe transaksi  $type = 1;
                /*=============================================================*/
                if ($button == 0){
                    $data['list'] = $this->cashreceived_model->get_cash_received_list($start_date, $end_date, $outlet);
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
                    $data['list'] = $this->cashreceived_model->get_cash_received_list($start_date, $end_date, $outlet);
                    
                }
                /* ===== form search ===== */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                
                $fields = array(
                    "cash_receive_number"=>"ID", 
                    "branch_id"=>"Outlet"
                    );
                $data['field_opt'] = $fields;
                $data['halaman'] = 'cashreceived/cashreceived_list.php';
                /*======= form input =======*/
                
                $data['cash_receive_id'] = $this->general_model->draw_hidden_field('cash_receive_id', '');
                $data['cash_receive_date'] = $this->general_model->draw_datepicker('Date', 1, 'cash_receive_date', '');
                $generate_number = $this->general_model->get_generate_number('RC', 'cash_receive', 'cash_receive_id');
                $data['cash_receive_number'] = $this->general_model->draw_hidden_field('cash_receive_number', $generate_number);
                $this->load->model('account_model');
                $account = $this->account_model->get_account_by_keyword(0, 'Petty Bank');
                $account_opt = array();
                if ($account->num_rows()!=0){
                    foreach ($account->result() as $value) {
                        $account_opt[$value->account_id] = $value->account_name;
                    }
                }
                $data['account_opt'] = $account_opt;
                $data['account_from'] = $this->general_model->draw_select('Cash From', 0, 'account_from', 0, $account_opt, '');
                $data['account_to'] = $this->general_model->draw_select('Received by', 0, 'account_to', 0, $account_opt, '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
                $data['remark'] = $this->general_model->draw_textarea('Remark', 0, 'remark', '');
                $data['show_modal'] = 'cashreceived/cashreceived_modal.php';
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'O/S Outlet Received';
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
                $datafooter = $this->cashreceived_model->get_last_record();
                $username = '';
                $lastupdate = '';
                $transid = 0;
                if ($datafooter->num_rows()!=0){
                    $row = $datafooter->row();
                    $username = $row->username;
                    $lastupdate = $row->last_update;
                    $transid = $row->cash_receive_number;
                }
                $data['username'] = $username;
                $data['last_update'] = $lastupdate;
                $data['transid'] = $transid;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('O/S Outlet Received', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function detail($string = '', $encrypt_id=''){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;     
                $cash_receive_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                $this->load->helper('form');
                $this->load->model('account_model');
                $account = $this->account_model->get_account_list();
                $account_opt = array();
                if ($account->num_rows()!=0){
                    foreach ($account->result() as $value) {
                        $account_opt[$value->account_id] = $value->account_name;
                    }
                }
                $data['account_opt'] = $account_opt;
                
                $data['cash_receive'] = $this->cashreceived_model->get_cash_received_by_id($cash_receive_id);  
                $data['cash_receive_file'] = $this->cashreceived_model->get_file_by_cash_receive_id($cash_receive_id);
                $data['show_modal'] = 'cashreceived/cashreceived_file_modal.php';
                $data['encrypt_id'] = $encrypt_id;
                $data['back_link'] = 'cashreceived/go/'.$string;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $data['halaman'] = 'cashreceived/cashreceived_detail.php';
                $header = $this->asik_model->draw_header('Cash Received', 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_view($id) {
        $data = $this->cashreceived_model->get_cash_received_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function add_data() {
        $this->load->library('form_validation');    
        $this->form_validation->set_rules('cash_receive_date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        $account1 = $this->input->post('account_from');
        $account2 = $this->input->post('account_to');
        if ($this->form_validation->run() == TRUE) {
            if ($account1 != $account2){
                $this->cashreceived_model->insert_cashreceived();
                echo json_encode(array("status" => TRUE));
            }
        }
    }
    
    public function update_data() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cash_receive_date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->cashreceived_model->update_cashreceived();
            echo json_encode(array("status" => TRUE));
        }
    }

    public function delete_data($id) {
        $this->cashreceived_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function do_upload() {
        $id = $this->input->post('cash_receive_id');
        $encrypt_id = $this->input->post('encrypt_id');
        $receive = $this->cashreceived_model->get_cash_received_by_id($id);
        $number = '';
        if ($receive->num_rows()!=0){
            $row = $receive->row();
            $number = $row->cash_receive_number;
            unlink('./assets/cashreceive/cashreceive-' . $number);
        }
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'cashreceive-'.$number;
        
        $config['upload_path'] = './assets/cashreceive/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 3540;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->cashreceived_model->update_file_name($id, $info['orig_name'], strtolower($info['file_ext']));
        }

        $back = '/cashreceived/detail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_03 . '/'.$encrypt_id;
        redirect($back);
    }
}
