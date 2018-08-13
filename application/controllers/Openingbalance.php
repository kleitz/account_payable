<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Openingbalance
 *
 * @author Hendra McHen
 */
class Openingbalance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('openingbalance_model');
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
                $number = $this->general_model->get_generate_number('OP', 'opening_balance', 'opening_balance_id');
                
                $data['opening_balance_id'] = $this->general_model->draw_hidden_field('opening_balance_id', '');
                $data['opening_balance_number'] = $this->general_model->draw_hidden_field('opening_balance_number', $number);
                $data['number_disable'] = $this->general_model->draw_text_disabled('Number', 'number_disable', $number);
                $data['opening_balance_date'] = $this->general_model->draw_datepicker('Date', 1, 'opening_balance_date', $today);
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 0, $branch_opt, '', 0);
                
                
                /*=============================================================*/
                if ($button == 0){
                    $data['list'] = $this->openingbalance_model->get_opening_balance($start_date, $end_date);
                    
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
                    $data['list'] = $this->openingbalance_model->get_opening_balance($start_date, $end_date);
                    
                }
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Opening Balance';
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
                $header = $this->asik_model->draw_header('Opening Balance', $period_title, $this->category_index, $category, $module);
                $halaman = 'openingbalance_view';
                $modal = 'openingbalance_modal';
                $data['content_header'] = $header;
                $data['halaman'] = 'openingbalance/'.$halaman.'.php';  
                $data['show_modal'] = 'openingbalance/'.$modal.'.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_view($id) {
        $data = $this->openingbalance_model->get_opening_balance_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function opening_balance_add() {
        
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('opening_balance_date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');

        if ($this->form_validation->run() == TRUE) {
            $this->openingbalance_model->insert_opening_balance();
            echo json_encode(array("status" => TRUE));
        }
        
    }
    
    public function opening_balance_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('opening_balance_date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->openingbalance_model->update_opening_balance();
            echo json_encode(array("status" => TRUE));
        }
    }

    public function opening_balance_delete($id) {
        $this->openingbalance_model->delete_opening_balance($id);
        echo json_encode(array("status" => TRUE));
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
                $opening_balance_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                $this->load->helper('form');
                $this->load->helper('file');
                $data['openingbalance_file'] = $this->openingbalance_model->get_openingbalance_file_by_op_id($opening_balance_id);
                $data['openingbalance'] = $this->openingbalance_model->get_opening_balance_by_id($opening_balance_id);
                //$data['cash_receive_file'] = $this->receiveinbank_model->get_file_by_cash_receive_id($cash_receive_id);
                $data['show_modal'] = 'openingbalance/openingbalance_modal.php';
                $data['encrypt_id'] = $encrypt_id;
                $data['back_link'] = 'openingbalance/go/'.$string;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $data['halaman'] = 'openingbalance/openingbalance_detail.php';
                $header = $this->asik_model->draw_header('Opening Balance', 'Detail', $this->category_index, $this->category, $this->module);
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
        $id = $this->input->post('opening_balance_id');
        $encrypt_id = $this->input->post('encrypt_id');
        
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'op'.date('YmdHis');
        
        $config['upload_path'] = './assets/openingbalance/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 9000;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->insert_file_name($id, $info['orig_name'], strtolower($info['file_ext']));
        }

        $back = '/openingbalance/detail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_06 . '/'.$encrypt_id.'/';
        redirect($back);
    }
    
    public function insert_file_name($id=0, $file='', $type='') {
        $data = array(
            'opening_balance_id' => $id,
            'file_name'=> $file,
            'file_type'=> $type
        );
        $this->db->insert('opening_balance_file', $data);
    }
    
    public function delete_file($op_file_id, $file_name, $encrypt_id) {
        unlink('./assets/openingbalance/' . $file_name);
        $this->db->where('opening_balance_file_id', $op_file_id);
        $this->db->delete('opening_balance_file');
        
        $back = '/openingbalance/detail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_06 . '/'.$encrypt_id.'/';
        redirect($back);
    }
}