<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ppsupplier
 *
 * @author Hendra McHen
 */
class Ppsupplier extends CI_Controller {

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

    public function go($string = '', $encrypt_id = '') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $pp_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_checked'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_checked);
                $data['action_approved'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_approved);
                
                $data['check_link'] = 'ppdetail/action_checked/' . $encrypt_id;
                $data['approve_link'] = 'ppdetail/action_approved/' . $encrypt_id;
                /* end privilege */
                // Get data branch
                $this->load->model('branch_model');
                $branch_opt = array();
                $branch_data = $this->branch_model->get_branch_list();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $val) {
                        $branch_opt[$val->branch_id] = $val->branch_name;
                    }
                }
                // Get data supplier
                $this->load->model('supplier_model');
                $supplier = $this->supplier_model->get_supplier_list();
                $supplier_opt = array();
                if ($supplier->num_rows()!=0){
                    foreach ($supplier->result() as $value) {
                        $supplier_opt[$value->supplier_name] = $value->supplier_name;
                    }
                }
                        
                if ($pp_type == 0){
                    // pp general
                    
                    $title = 'PP General';
                    $halaman = 'ppdetail/ppgeneral_detail.php';
                    $data['pp_detail_id'] = $this->general_model->draw_hidden_field('pp_detail_id', '');
                    $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', $pp_id);
                    $data['act_title'] = $this->general_model->draw_text_field('Act title', 0, 'act_title', '', 'Type title or select Outlet below', '');
                    $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 1, $branch_opt, '', 0);
                    $data['job_order'] = $this->general_model->draw_text_field('Job Order', 0, 'job_order', '', '', '');
                    $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
                    $data['unit'] = $this->general_model->draw_input_number('Unit', 0, 'unit', '');
                    $data['price'] = $this->general_model->draw_input_currency('Price', 0, 'price', '');
                    $data['total'] = $this->general_model->draw_input_currency('Total', 0, 'total', '');
                    $data['ket'] = $this->general_model->draw_caption('Keterangan:', 'ket', 'Total diisi jika Unit & Price tidak dinput');
                    $data['show_modal'] = 'ppdetail/ppgeneral_modal.php';
                }
                
                if ($pp_type == 1){
                    // pp supplier
                    $suppliername = '';
                    $data_pp = $this->ppdetail_model->get_payment_process_by_id($pp_id);
                    if ($data_pp->num_rows()!=0){
                        $row = $data_pp->row();
                        $suppliername= $row->pp_title;
                    }
                    $title = 'PP Supplier';
                    $halaman = 'ppdetail/ppsupplier_detail.php';
                    $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', $pp_id);
                    $data['po_data'] = $this->ppdetail_model->get_purchase_order_list($suppliername);
                    $data['show_modal'] = 'ppdetail/ppsupplier_modal.php';
                }
                
                if ($pp_type == 2){
                    // pp cashier expense
                    
                    $title = 'PP Cashier Expense';
                    $halaman = 'ppdetail/ppexpense_detail.php';
                    $data['pp_detail_id'] = $this->general_model->draw_hidden_field('pp_detail_id', '');
                    $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', $pp_id);
                    //$data['act_title'] = $this->general_model->draw_text_field('Act title', 0, 'act_title', '', 'Type title or select Outlet below', '');
                    //$data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 1, $branch_opt, '', 0);
                    $data['job_order'] = $this->general_model->draw_text_field('Job Order', 0, 'job_order', '', '', '');
                    $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
                    $data['unit'] = $this->general_model->draw_input_number('Unit', 0, 'unit', '');
                    $data['price'] = $this->general_model->draw_input_currency('Price', 0, 'price', '');
                    $data['total'] = $this->general_model->draw_input_currency('Total', 0, 'total', '');
                    $data['ket'] = $this->general_model->draw_caption('Keterangan:', 'ket', 'Total diisi jika Unit & Price tidak dinput');
                    $data['show_modal'] = 'ppdetail/ppexpense_modal.php';
                }
                $data['pp_info'] = $this->ppdetail_model->get_payment_process_by_id($pp_id);
                $data['detail'] = $this->ppdetail_model->get_detail_by_pp_id($pp_id);
                $data['encrypt_id'] = $encrypt_id;
                $data['back_link'] = 'paymentprocess/go/'.$string;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header($title, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = $halaman;
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
    
    public function ppgeneral_add() {
        $this->ppdetail_model->insert_ppgeneral();
        echo json_encode(array("status" => TRUE));
    }
    
    public function ppgeneral_edit() {
        $this->ppdetail_model->update_ppgeneral();
        echo json_encode(array("status" => TRUE));
    }
    
    public function ppsupplier_add() {
        $this->ppdetail_model->insert_ppsupplier();
        echo json_encode(array("status" => TRUE));
    }
    
    public function ppexpense_add() {
        $this->ppdetail_model->insert_ppexpense();
        echo json_encode(array("status" => TRUE));
    }
    
    public function ppexpense_edit() {
        $this->ppdetail_model->update_ppexpense();
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
    
    public function check_po($po_id=0, $supplier_id = 0, $branch_id = 0) {
        $sql  = 'SELECT * FROM purchase_order ';
        $sql .= 'WHERE po_id='.$po_id;
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
            $po_id = 0;
            $in_po = implode(',', $checked);
            foreach ($checked as $value) {
                $po_id = $value;
                $kondisi = $this->check_po($po_id, $supplier_id, $branch_id);
                if ($kondisi){
                    $datastr = array(
                        'pp_id' => $pp_id,
                        'po_id' => $value
                    );
                    $data_query[$i] = $datastr;
                    $i++;
                    /* update purchase order */
                    $dataup = array('po_status'=>2);
                    $this->db->where('po_id', $value);
                    $this->db->update('purchase_order', $dataup);
                }                
            }
            
            if ($kondisi){
                $gettotal = $this->ppdetail_model->get_total_po($in_po);
                $getpo = $this->ppdetail_model->get_purchase_order_by_id($po_id);
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
        $data['list'] = $this->ppdetail_model->get_purchase_order_check_view($branch_id, $supplier_id);
        $data['active_li'] = $this->category_index;
        $header = $this->asik_model->draw_header('Checked Nota', 'View', $this->category_index, $this->asik_model->category_configuration, $this->asik_model->config_01);
        $data['content_header'] = $header;
        $data['halaman'] = 'ppdetail/checklist_po.php';
        $this->load->view('template', $data);
    }
    
    public function action_checked($encrypt_id = '') {
        $pp_id = $this->general_model->decrypt_value($encrypt_id);
        $data = array(
            'checked_by' => $this->session->userdata('user_id'),
            'pp_status' => 2
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$encrypt_id;
        redirect($back);
    }
    
    public function action_approved($encrypt_id = '') {
        $pp_id = $this->general_model->decrypt_value($encrypt_id);
        $data = array(
            'approved_by' => $this->session->userdata('user_id'),
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        $back = '/ppdetail/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_01 . '/'.$encrypt_id;
        redirect($back);
    }

}