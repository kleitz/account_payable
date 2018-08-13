<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ppdetail
 *
 * @author mchen
 */
class Ppdetail extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('ppdetail_model');
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

    public function go($string = '', $encrypt_id = '', $pp_type=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $pp_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_cross_check'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_cross_check);
                $data['action_checked'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_checked);
                $data['action_approved'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_approved);
                
                $data['cross_check_link'] = 'ppdetail/action_cross_check/' . $encrypt_id.'/'.$pp_type;
                $data['check_link'] = 'ppdetail/action_checked/' . $encrypt_id.'/'.$pp_type;
                $data['approve_link'] = 'ppdetail/action_approved/' . $encrypt_id.'/'.$pp_type;
                $data['approve_supplier_link'] = 'ppdetail/action_approved_supplier/' . $encrypt_id.'/'.$pp_type;
                /* end privilege */
                $this->load->helper('form');
                $this->load->helper('file');
                $data['up_encrypt_id'] = $encrypt_id;
                $data['pp_type'] = $pp_type;
                $data['data_pp_id'] = $pp_id;
                // Get data branch
                $this->load->model('branch_model');
                $branch_opt = array();
                $branch_data = $this->branch_model->get_branch_list();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $val) {
                        $branch_opt[$val->branch_id] = $val->branch_name;
                    }
                }
                
                $data['pp_detail_id'] = $this->general_model->draw_hidden_field('pp_detail_id', '');
                $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', $pp_id);
                $data['act_title'] = $this->general_model->draw_text_field('Dept/Act', 0, 'act_title', '', 'Type title or select Outlet below', '');
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 1, $branch_opt, '', 0);
                $data['job_order'] = $this->general_model->draw_text_field('Reference No.', 0, 'job_order', '', '', '');
                $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
                $data['unit'] = $this->general_model->draw_input_number('Unit', 0, 'unit', 1);
                $data['price'] = $this->general_model->draw_input_currency('Price', 0, 'price', '');
                //$data['total'] = $this->general_model->draw_input_currency('Total', 0, 'total', '');
                //$data['ket'] = $this->general_model->draw_caption('Keterangan:', 'ket', 'Total diisi jika Unit & Price tidak dinput');
                $detail = $this->ppdetail_model->get_detail_by_pp_id($pp_id);
                $data['detail'] = $detail;
                /* ======= PP General ======= */        
                if ($pp_type == 0){
                    $title = 'PP General';
                    $data['pp_info'] = $this->ppdetail_model->get_payment_process($pp_id);
                    $halaman = 'ppdetail/ppgeneral_detail.php';      
                    $data['show_modal'] = 'ppdetail/ppgeneral_modal.php';
                }
                /*  ======= PP Supplier ======= */
                if ($pp_type == 1){
                    // list pp supplier
                    $detail = $this->ppdetail_model->get_detail_join_invoice($pp_id);
                    $data['detail'] = $detail;
                    
                    $supplier_id = 0;
                    $branch_id = 0;
                    $data_pp = $this->ppdetail_model->get_payment_process_by_id($pp_id);
                    if ($data_pp->num_rows()!=0){
                        $row = $data_pp->row();
                        $supplier_id = $row->supplier_id;
                        $branch_id = $row->branch_id;
                    }
                    $title = 'PP Supplier';
                    $data['pp_info'] = $this->ppdetail_model->get_payment_process_by_id($pp_id);
                    $halaman = 'ppdetail/ppsupplier_detail.php';
                    $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', $pp_id);
                    $data['encrypt_id'] = $this->general_model->draw_hidden_field('encrypt_id', $encrypt_id);
                    $data['credit_invoice'] = $this->ppdetail_model->get_invoice_list($supplier_id, $branch_id);
                    $data['show_modal'] = 'ppdetail/ppsupplier_modal.php';
                    if ($detail->num_rows()!=0){
                        $clause_in = '';
                        foreach ($detail->result() as $value) {
                            $clause_in .= $value->credit_invoice_id.',';
                        }
                        $clausein = substr($clause_in, 0, strlen($clause_in)-1);
                        $data['cinvoice_file'] = $this->ppdetail_model->get_credit_invoice_file($clausein);
                    }
                }
                /*  ======= PP Cashier Expense ======= */
                if ($pp_type == 2){
                    $title = 'PP Cashier Expense';
                    $data['pp_info'] = $this->ppdetail_model->get_payment_process($pp_id);
                    $halaman = 'ppdetail/ppexpense_detail.php';
                    $data['show_modal'] = 'ppdetail/ppexpense_modal.php';
                }
                /*  ======= PP Outstanding ======= */
                if ($pp_type == 3){
                    $title = 'PP Outstanding';
                    $data['pp_info'] = $this->ppdetail_model->get_payment_process($pp_id);
                    $halaman = 'ppdetail/ppoutstanding_detail.php';
                    $data['show_modal'] = 'ppdetail/ppoutstanding_modal.php';
                }
                
                /*  ======= PP Project ======= */
                if ($pp_type == 4){
                    $title = 'PP Project';
                    $data['pp_info'] = $this->ppdetail_model->get_payment_process($pp_id);
                    $halaman = 'ppdetail/ppproject_detail.php';
                    $data['show_modal'] = 'ppdetail/ppproject_modal.php';
                }
                
                $data['paymentprocess_file'] = $this->ppdetail_model->get_payment_process_file_by_pp_id($pp_id);
                $data['back_link'] = 'paymentprocess/go/'.$string;
                /* update 2018-02-08 */
                $data['print_link'] = 'printout/pp/'.date('Ymd').$pp_id;
                
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header($title, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = $halaman;
                
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Payment Process Detail';
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
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_get_data($pp_detail_id=0) {
        $data = $this->ppdetail_model->get_detail_by_id($pp_detail_id)->row();
        echo json_encode($data);
    }
    
    public function ppgeneral_action() {
        $pp_detail_id = $this->input->post('pp_detail_id');
        if ($pp_detail_id != 0){
            $this->ppdetail_model->update_ppgeneral();
        } else {
            $this->ppdetail_model->insert_ppgeneral();
        }
        
        $encrypt_id = $this->input->post('pp_encrypt');
        //echo json_encode(array("status" => TRUE));
        $link = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $link .= $this->asik_model->config_01 . '/'.$encrypt_id.'/0';
        redirect($link);
    }
    
    public function ppgeneral_edit() {
        $this->ppdetail_model->update_ppgeneral();
        echo json_encode(array("status" => TRUE));
    }
    
    public function ppsupplier_add() {
        $this->ppdetail_model->insert_ppsupplier();
        $encrypt_id = $this->input->post('encrypt_id');
        $link = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $link .= $this->asik_model->config_01 . '/'.$encrypt_id.'/1';
        redirect($link);
    }
    
    public function ppexpense_action() {
        $pp_detail_id = $this->input->post('pp_detail_id');
        if ($pp_detail_id != 0){
            $this->ppdetail_model->update_ppexpense();
        } else {
            $this->ppdetail_model->insert_ppexpense();
        }
        
        //echo json_encode(array("status" => TRUE));
        $encrypt_id = $this->input->post('pp_encrypt');
        $link = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $link .= $this->asik_model->config_01 . '/'.$encrypt_id.'/2';
        redirect($link);
    }
    
    public function ppexpense_edit() {
        $this->ppdetail_model->update_ppexpense();
        echo json_encode(array("status" => TRUE));
    }
    
    public function ppoutstanding_action() {
        $pp_detail_id = $this->input->post('pp_detail_id');
        if ($pp_detail_id != 0){
            // update
            $this->ppdetail_model->update_ppoutstanding();
        } else {
            $this->ppdetail_model->insert_ppoutstanding();
        }
        
        //echo json_encode(array("status" => TRUE));
        $encrypt_id = $this->input->post('pp_encrypt');
        $link = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $link .= $this->asik_model->config_01 . '/'.$encrypt_id.'/3';
        redirect($link);
    }
    
    public function ppproject_action() {
        $pp_detail_id = $this->input->post('pp_detail_id');
        if ($pp_detail_id != 0){
            // update
            $this->ppdetail_model->update_ppproject();
        } else {
            $this->ppdetail_model->insert_ppproject();
        }
        
        //echo json_encode(array("status" => TRUE));
        $encrypt_id = $this->input->post('pp_encrypt');
        $link = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $link .= $this->asik_model->config_01 . '/'.$encrypt_id.'/4';
        redirect($link);
    }
    
    public function ppoutstanding_edit() {
        $this->ppdetail_model->update_ppoutstanding();
        echo json_encode(array("status" => TRUE));
    }
    
    public function ppdetail_delete($pp_detail_id=0) {
        $this->ppdetail_model->delete_by_id($pp_detail_id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function delitem($encrypt_id = '') {
        $id = $this->general_model->decrypt_value($encrypt_id);
        $this->ppdetail_model->delete_approve($id);
    }
    
    public function check_po($credit_invoice_id=0, $supplier_id = 0, $branch_id = 0) {
        $sql  = 'SELECT * FROM credit_invoice ';
        $sql .= 'WHERE credit_invoice_id='.$credit_invoice_id;
        $query = $this->db->query($sql);

        if ($query->num_rows()!=0){
            $row = $query->row();
            if ($supplier_id !=0 && $branch_id != 0){
                if ($row->supplier_id == $supplier_id && $row->branch_id == $branch_id){
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        }
    }
    
    public function submit_process($encrypt_id='') {
        $pp_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $checked = $this->input->post('checkpo');
        $data_query = array();
        $total = 0;
        $supplier_id = 0;
        $branch_id = 0;
        $kondisi = FALSE;
        /* get data payment process */
        $paymentprocess = $this->ppdetail_model->get_payment_process_by_id($pp_id);
        if ($paymentprocess->num_rows()!=0){
            $row = $paymentprocess->row();
            $supplier_id = $row->supplier_id;
            $branch_id = $row->branch_id;
            $total = $row->total;
        }
        if (isset($checked)){
            $i = 0;
            $credit_invoice_id = 0;
            $in_po = implode(',', $checked);
            foreach ($checked as $value) {
                $credit_invoice_id = $value;
                $kondisi = $this->check_po($credit_invoice_id, $supplier_id, $branch_id);
                if ($kondisi){
                    $datastr = array(
                        'pp_id' => $pp_id,
                        'credit_invoice_id' => $value
                    );
                    $data_query[$i] = $datastr;
                    $i++;
                    /* update purchase order */
                    $dataup = array('po_status'=>1);
                    $this->db->where('credit_invoice_id', $value);
                    $this->db->update('credit_invoice', $dataup);
                }                
            }
            
            if ($kondisi){
                $gettotal = $this->ppdetail_model->get_total_po($in_po);
                $getpo = $this->ppdetail_model->get_credit_invoice_by_id($credit_invoice_id);
                if ($gettotal->num_rows()!=0){
                    $row = $gettotal->row();
                    $total = $total + $row->total;
                }
                if ($getpo->num_rows()!=0){
                    $row = $getpo->row();
                    $supplier_id = $supplier_id == 0 ? $row->supplier_id:$supplier_id;
                    $branch_id = $branch_id == 0 ? $row->branch_id:$branch_id;
                }
                
                $this->db->insert_batch('payment_process_detail', $data_query);
                /* update payment process */
                $data = array(
                    'supplier_id' => $supplier_id,
                    'branch_id' => $branch_id,
                    'total' => $total,
                    'pp_status' => 1
                );

                $this->db->where('pp_id', $pp_id);
                $this->db->update('payment_process', $data);
            }
            
        }
        
        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$encrypt_id;
        redirect($back);
        
    }
    
    public function checklist($encrypt_id='') {
        $this->load->helper('form');
        $pp_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $data['enc_pp_id'] = $encrypt_id;
        $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', $pp_id);
        $pp = $this->ppdetail_model->get_payment_process_by_id($pp_id);
        $supplier_id = 0;
        $branch_id = 0;
        if ($pp->num_rows()!=0){
            $row = $pp->row();
            $supplier_id = $row->supplier_id;
            $branch_id = $row->branch_id;
        }
        $data['list'] = $this->ppdetail_model->get_credit_invoice_check_view($branch_id, $supplier_id);
        $data['active_li'] = $this->category_index;
        $header = $this->asik_model->draw_header('Checked Nota', 'View', $this->category_index, $this->asik_model->category_configuration, $this->asik_model->config_01);
        $data['content_header'] = $header;
        $data['halaman'] = 'ppdetail/checklist_po.php';
        $this->load->view('template', $data);
    }
    
    public function action_cross_check($encrypt_id = '', $pp_type=0) {
        $pp_id = $this->general_model->decrypt_value($encrypt_id);
        $data = array(
            'cross_check_by' => $this->session->userdata('user_id'),
            'pp_status' => 1
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$encrypt_id.'/'.$pp_type;
        redirect($back);
    }
    
    public function action_checked($encrypt_id = '', $pp_type=0) {
        $pp_id = $this->general_model->decrypt_value($encrypt_id);
        $data = array(
            'checked_by' => $this->session->userdata('user_id'),
            'pp_status' => 2
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$encrypt_id.'/'.$pp_type;
        redirect($back);
    }
    
    public function action_approved($encrypt_id = '', $pp_type=0) {
        $pp_id = $this->general_model->decrypt_value($encrypt_id);
        $data = array(
            'approved_by' => $this->session->userdata('user_id'),
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        // insert to balance
        $arr_pp = $this->get_payment_process_by_id($pp_id);
        switch ($pp_type) {
            case 1: // supplier balance
                // insert
                $datainsert = array(
                    'balance_date' => $arr_pp[1],
                    'supplier_id' => $arr_pp[2],
                    'pp_id' => $arr_pp[0],
                    'debit' => $arr_pp[3]
                );
                $this->db->insert('supplier_balance', $datainsert);
                break;

            case 3: // third party balance
                // insert
                $datainsert = array(
                    'balance_date' => $arr_pp[1],
                    'third_party_id' => $arr_pp[5],
                    'pp_id' => $arr_pp[0],
                    'debit' => $arr_pp[3]
                );
                $this->db->insert('third_party_balance', $datainsert);
                break;
            
            case 4: // project balance
                // insert
                $datainsert = array(
                    'balance_date' => $arr_pp[1],
                    'vendor_id' => $arr_pp[4],
                    'pp_id' => $arr_pp[0],
                    'debit' => $arr_pp[3]
                );
                $this->db->insert('project_balance', $datainsert);
                break;
        }
        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$encrypt_id.'/'.$pp_type;
        redirect($back);
    }
    
    public function action_approved_supplier($encrypt_id = '', $pp_type=0) {
        $pp_id = $this->general_model->decrypt_value($encrypt_id);
        $data = array(
            'approved_by' => $this->session->userdata('user_id'),
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        /* insert to transaction n ledger */
        /* code is off (2018-02-21)
        $this->load->model('payment_process_model');
        $this->payment_process_model->insert_account_payable($pp_id);
        */
        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$encrypt_id.'/'.$pp_type;
        redirect($back);
    }
    
    public function do_upload() {
        $id = $this->input->post('pp_id');
        $up_encrypt_id = $this->input->post('up_encrypt_id');
        $pp_type = $this->input->post('pp_type');
        
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'pp'.date('YmdHis');
        
        $config['upload_path'] = './assets/paymentprocess/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 9000;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->ppdetail_model->update_file_name($id, $info['orig_name'], strtolower($info['file_ext']));
        }

        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$up_encrypt_id.'/'.$pp_type;
        redirect($back);
    }
    
    public function print_action() {
        $this->load->view('ppdetail/ppsupplier_print');
    }

    public function delete_file($pp_file_id, $file_name, $encrypt_id, $pp_type) {
        unlink('./assets/paymentprocess/' . $file_name);
        $this->db->where('pp_file_id', $pp_file_id);
        $this->db->delete('payment_process_file');
        
        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$encrypt_id.'/'.$pp_type;
        redirect($back);
    }
    
    public function get_payment_process_by_id($id=0) {
        $sql  = 'SELECT * FROM payment_process ';
        $sql .= 'WHERE pp_id = '.$id;
        $query = $this->db->query($sql);
        $arr = array();
        if ($query->num_rows()!=0){
            $row = $query->row();
            $arr[0] = $row->pp_id;
            $arr[1] = $row->pp_date;
            $arr[2] = $row->supplier_id;
            $arr[3] = $row->total;
            $arr[4] = $row->vendor_id;
            $arr[5] = $row->third_party_id;
        }
        return $arr;
    }
}
