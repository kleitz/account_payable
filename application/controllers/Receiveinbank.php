<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Receiveinbank
 *
 * @author Hendra McHen
 */
class Receiveinbank extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('trans_model');
        $this->load->model('receiveinbank_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';


    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_transaction) {
            if (($module == $this->asik_model->trans_05) && ($string == $category . $module)) {
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
                if ($startd != ''){
                    $start_date = $startd;
                }
                if ($endd != ''){
                    $end_date = $endd;
                }     
                /* form field */
                $today = date('Y-m-d');
                $number = $this->general_model->get_generate_number('RB', 'receive_bank', 'receive_bank_id');
                
                $data['receive_bank_id'] = $this->general_model->draw_hidden_field('receive_bank_id', '');
                $data['receive_bank_number'] = $this->general_model->draw_hidden_field('receive_bank_number', $number);
                $data['number_disable'] = $this->general_model->draw_text_disabled('Number', 'number_disable', $number);
                $data['receive_bank_date'] = $this->general_model->draw_datepicker('Date', 1, 'receive_bank_date', $today);
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
                /* add field 2018-07-18 */
                $receive_type = array('Receive in Bank', 'Others');
                $data['receive_type'] = $this->general_model->draw_select('Type', 0, 'receive_type', 0, $receive_type, '', 0);
                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 0, $branch_opt, '', 0);
                
                /* updated : 2018-04-04 */
                $outstanding = $this->get_outstanding();
                $outstanding_opt = array();
                if ($outstanding->num_rows()!=0){
                    $outstanding_opt[0] = 'None';
                    foreach ($outstanding->result() as $value) {
                        $outstanding_opt[$value->outstanding_id] = $value->outstanding_date . ' - '.$value->outstanding_description.' - '.number_format($value->amount) . ' - '.$value->branch_name;
                    }
                }
                $data['outstanding_id'] = $this->general_model->draw_select('Outstanding Third Party', 0, 'outstanding_id', 1, $outstanding_opt, '', 1);
           
                $type = 0;
                /*=============================================================*/
                if ($button == 0){
                    $data['list'] = $this->receiveinbank_model->get_receive_bank($start_date, $end_date, $outlet);
                    
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
                    $data['list'] = $this->receiveinbank_model->get_receive_bank($start_date, $end_date, $outlet);
                    
                }
                
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'O/S Receive';
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
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $data['period_title'] = $period_title;
                $data['period_active'] = $period_active;
                $data['period_month'] = $period_month;
                $header = $this->asik_model->draw_header('O/S Receive', $period_title, $this->category_index, $category, $module);
                $halaman = 'receiveinbank_view';
                $modal = 'receiveinbank_modal';
                $data['content_header'] = $header;
                $data['halaman'] = 'receiveinbank/'.$halaman.'.php';  
                $data['show_modal'] = 'receiveinbank/'.$modal.'.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_view($id) {
        $data = $this->receiveinbank_model->get_receive_bank_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function receive_bank_add() {
        
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('receive_bank_date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');

        if ($this->form_validation->run() == TRUE) {
            $this->receiveinbank_model->insert_receive_bank();
            echo json_encode(array("status" => TRUE));
        }
        
    }
    
    public function receive_bank_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('receive_bank_date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->receiveinbank_model->update_receive_bank();
            echo json_encode(array("status" => TRUE));
        }
    }

    public function receive_bank_delete($id) {
        $this->receiveinbank_model->delete_receive_bank($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function get_outstanding() {
        $sql  = 'SELECT os.outstanding_id, os.outstanding_number, os.outstanding_date, os.outstanding_description, ';
        $sql .= 'b.branch_name, os.amount, os.outstanding_status FROM outstanding AS os ';
        $sql .= 'INNER JOIN branch AS b ON os.branch_id=b.branch_id ';
        $sql .= 'WHERE os.outstanding_status = 0 ';
        $sql .= 'AND os.outstanding_type=3';
        
        $query = $this->db->query($sql);
        return $query;
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
                $receive_bank_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                $this->load->helper('form');
                $this->load->helper('file');
                $data['receivebank_file'] = $this->receiveinbank_model->get_receive_bank_by_rb_id($receive_bank_id);
                $data['receivebank'] = $this->receiveinbank_model->get_receive_bank_by_id($receive_bank_id);  
                //$data['cash_receive_file'] = $this->receiveinbank_model->get_file_by_cash_receive_id($cash_receive_id);
                $data['show_modal'] = 'receiveinbank/receiveinbank_modal.php';
                $data['encrypt_id'] = $encrypt_id;
                $data['back_link'] = 'receiveinbank/go/'.$string;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $data['halaman'] = 'receiveinbank/receiveinbank_detail.php';
                $header = $this->asik_model->draw_header('Receive in Bank', 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function do_upload() {
        $id = $this->input->post('receive_bank_id');
        $encrypt_id = $this->input->post('encrypt_id');
        
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'op'.date('YmdHis');
        
        $config['upload_path'] = './assets/receivebank/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 9000;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->insert_file_name($id, $info['orig_name'], strtolower($info['file_ext']));
        }

        $back = '/receiveinbank/detail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_05 . '/'.$encrypt_id.'/';
        redirect($back);
    }
    
    public function insert_file_name($id=0, $file='', $type='') {
        $data = array(
            'receive_bank_id' => $id,
            'file_name'=> $file,
            'file_type'=> $type
        );
        $this->db->insert('receive_bank_file', $data);
    }
    
    public function delete_file($rb_file_id, $file_name, $encrypt_id) {
        unlink('./assets/receivebank/' . $file_name);
        $this->db->where('receive_bank_file_id', $rb_file_id);
        $this->db->delete('receive_bank_file');
        
        $back = '/receiveinbank/detail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_05 . '/'.$encrypt_id.'/';
        redirect($back);
    }
    
    public function return_by_emp($string = '', $cash_request_id=0, $amount=0){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;     
                
                $this->load->helper('form');
                $cashrequest = $this->get_cashrequest_by_id($cash_request_id);
                if ($cashrequest->num_rows()!=0){
                    $rowcashr = $cashrequest->row();
                    $branch_id = $rowcashr->branch_id;
                    $cr_number = $rowcashr->cash_request_number;
                }
                /* form field */
                $today = date('Y-m-d');
                $number = $this->general_model->get_generate_number('RB', 'receive_bank', 'receive_bank_id');
                $data['receive_bank_id'] = $this->general_model->draw_hidden_field('receive_bank_id', '');
                $data['receive_bank_number'] = $this->general_model->draw_hidden_field('receive_bank_number', $number);
                $data['number_disable'] = $this->general_model->draw_text_disabled('Number', 'number_disable', $number);
                $data['receive_bank_date'] = $this->general_model->draw_datepicker('Date', 1, 'receive_bank_date', $today);
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                
                /*update 3 Juli 2018 change amount*/
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', $amount);
                //$data['amount'] = $this->general_model->draw_hidden_field('amount', $amount);
                //$data['amount_disable'] = $this->general_model->draw_text_disabled('Amount', 'amount_disable', number_format($amount));
                $branch_name = $this->get_branch_name_by_id($branch_id);
                $data['branch_id'] = $this->general_model->draw_hidden_field('branch_id', $branch_id);
                $data['branch_disable'] = $this->general_model->draw_text_disabled('Branch', 'branch_disable', $branch_name);
                $data['cash_request_id'] = $this->general_model->draw_hidden_field('cash_request_id', $cash_request_id);
                $data['cash_request_disable'] = $this->general_model->draw_text_disabled('CR Number', 'cash_request_disable', $cr_number);
                
                $data['back_link'] = 'receiveinbank/go/'.$string;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $data['halaman'] = 'receiveinbank/receiveinbank_form.php';
                $header = $this->asik_model->draw_header('Receive in Bank', 'Form', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['datatable_title'] = 'Receive in Bank';
                $data['footer_total'] = '';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_cashrequest_by_id($id=0) {
        $sql  = 'SELECT * FROM cash_request ';
        $sql .= 'WHERE cash_request_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_branch_name_by_id($id=0) {
        $sql  = 'SELECT * FROM branch ';
        $sql .= 'WHERE branch_id = '.$id;
        $query = $this->db->query($sql);
        $branch_name = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $branch_name = $row->branch_name;
        }
        return $branch_name;
    }
    
    public function insert_return_cash_emp() {
        $id = $this->receiveinbank_model->insert_receive_bank_v2();
        $enc_id = $this->general_model->encrypt_value($id);
        $back = '/receiveinbank/detail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_05 . '/'.$enc_id.'/';
        redirect($back);
    }
}