<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Closingbalance
 *
 * @author Hendra McHen
 */
class Closingbalance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('trans_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';


    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_transaction) {
            if (($module == $this->asik_model->trans_06) && ($string == $category . $module)) {
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
                /* form field */
                $today = date('Y-m-d');
                $number = $this->general_model->get_generate_number('CB', 'transactions', 'trans_id');
                $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
                $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
                $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
                $this->load->model('account_model');
                $account = $this->account_model->get_account_by_type(2);
                $account_opt = array();
                if ($account->num_rows()!=0){
                    foreach ($account->result() as $value) {
                        $account_opt[$value->account_id] = $value->account_name;
                    }
                }
                $account2 = $this->account_model->get_account_by_type(0);
                $account_opt2 = array();
                if ($account2->num_rows()!=0){
                    foreach ($account2->result() as $value) {
                        $account_opt2[$value->account_id] = $value->account_name;
                    }
                }
                $data['account_opt'] = $account_opt2;
                $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 1, $account_opt, '', 1);
                $data['account_relation'] = $this->general_model->draw_select('Account Relation', 0, 'account_relation', 1, $account_opt2, '', 1);
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
           
                $type = 0;
                /*=============================================================*/
                if ($button == 0){
                    $data['list'] = $this->trans_model->get_transaction_by_type($type, $start_date, $end_date);
                    
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
                    }
                    $data['list'] = $this->trans_model->get_closing_balance($start_date, $end_date);
                    
                }
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Closing Balance';
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
                            .column(6)
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
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $data['period_title'] = $period_title;
                $data['period_active'] = $period_active;
                $data['period_month'] = $period_month;
                $header = $this->asik_model->draw_header('Closing Balance', $period_title, $this->category_index, $category, $module);
                $halaman = 'closingbalance_view';
                $modal = 'closingbalance_modal';
                $data['content_header'] = $header;
                $data['halaman'] = 'closingbalance/'.$halaman.'.php';  
                $data['show_modal'] = 'closingbalance/'.$modal.'.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function trans01_ajax_view($id) {
        $data = $this->trans_model->get_closing_balance($id)->row();
        echo json_encode($data);
    }
   
    public function trans01_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');

        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->insert_closing_balance();
            echo json_encode(array("status" => TRUE));
        }
        
    }
    
    public function trans01_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_closing_balance($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function trans01_delete($id) {
        $this->trans_model->delete_trans_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
}