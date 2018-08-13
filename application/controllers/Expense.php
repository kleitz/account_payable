<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Expense
 *
 * @author Hendra McHen
 */
class Expense extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('trans_model');
        $this->load->model('general_model');
        $this->load->helper('date');
    }
    
    public $category_index = 2;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_configuration) {
            if (($module == $this->asik_model->config_04) && ($string == $category . $module)) {
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
                
                //if ($module == $this->asik_model->trans_07){
                    $data = $this->trans07_form();
                    $header = $this->asik_model->draw_header('Expenses', $period_title, $this->category_index, $category, $module);
                    $halaman = 'expense_view';
                    $modal = 'expense_modal';
                    $type = 6;
                //}
                /*=============================================================*/
                if ($button == 0){
                    $data['list'] = $this->trans_model->get_transaction_by_type($type, $start_date, $end_date);
                    if ($type == 4){
                        $this->load->model('payment_process_model');
                        $data['payment_process_list'] = $this->payment_process_model->get_payment_process_by_status(3);
                        $data['list'] = $this->trans_model->get_transaction_by_pp($type, $start_date, $end_date);
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
                            $signupdate=$year.'-'.$month.'-'.$day;
                            $signupweek=date("W",strtotime($signupdate));
                            $dto = new DateTime();
                            $start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                            $end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                            break;
                        case 3:
                            $start_date = $year.'-'.$month.'-01';
                            $end_date = $year.'-'.$month.'-31';
                            break;
                        case 4:
                            $last_month = $month - 1;
                            $start_date = $year.'-'.$last_month.'-01';
                            $end_date = $year.'-'.$last_month.'-31';
                            break;
                    }
                    $data['list'] = $this->trans_model->get_transaction_by_type($type, $start_date, $end_date);
                    if ($type == 4){
                        $this->load->model('payment_process_model');
                        $data['payment_process_list'] = $this->payment_process_model->get_payment_process_by_status(3);
                        $data['list'] = $this->trans_model->get_transaction_by_pp($type, $start_date, $end_date);
                    }
                }
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $data['period_title'] = $period_title;
                $data['period_active'] = $period_active;
                $data['period_month'] = $period_month;
                $data['content_header'] = $header;
                $data['halaman'] = 'expense/'.$halaman.'.php';  
                $data['show_modal'] = 'expense/'.$modal.'.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function trans07_form() {
        /* form field */
        $today = date('Y-m-d');
        $number = $this->general_model->get_generate_number('TR');
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
        $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_keyword(4, 'Expense');
        $account_opt = array();
        if ($account->num_rows()!=0){
            foreach ($account->result() as $value) {
                $account_opt[$value->account_id] = $value->account_name;
            }
        }
        $data['account_opt'] = $account_opt;
        $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 1, $account_opt, '', 1);
        $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
        $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
        return $data;
    }
    
    public function trans07_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function trans07_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        $trans_date = $this->input->post('trans_date');
        $substr_trans_date = substr($trans_date, 0, 7);
        $substr_start_date = '';
        $this->load->model('period_model');
        $period = $this->period_model->get_period_active();
        if ($period->num_rows()!=0){
            $row = $period->row();
            $substr_start_date = substr($row->start_date, 0, 7);
        }
        if ($substr_trans_date == $substr_start_date){
            if ($this->form_validation->run() == TRUE) {
                $this->trans_model->insert_trans07();
                echo json_encode(array("status" => TRUE));
            }
        }    
    }
    
    public function trans07_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans07($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function trans07_delete($id) {
        $this->trans_model->delete_trans_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
}