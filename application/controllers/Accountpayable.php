<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Accountpayable
 *
 * @author Hendra McHen
 */
class Accountpayable extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('trans_model');
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
                
                ////if ($module == $this->asik_model->trans_05){
                    $data = $this->trans05_form();
                    $header = $this->asik_model->draw_header('Account Payable', $period_title, $this->category_index, $category, $module);
                    $halaman = 'accountpayable_view';
                    $modal = 'accountpayable_modal';
                    $type = 4;
                ////}
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
                $data['halaman'] = 'accountpayable/'.$halaman.'.php';  
                $data['show_modal'] = 'accountpayable/'.$modal.'.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function trans05_form() {
        /* form field */
        $today = date('Y-m-d');
        $number = $this->general_model->get_generate_number('TR');
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
        $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
        $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', '');
        return $data;
    }
    
    public function trans05_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function trans05_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->insert_trans05();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans05_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans05($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans05_delete($id, $pp_id) {
        $this->trans_model->delete_trans_by_id($id);
        $data_pp = array(
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data_pp);
        echo json_encode(array("status" => TRUE));
    }
    
}