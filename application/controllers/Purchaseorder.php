<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Purchaseorder
 *
 * @author mchen
 */
class Purchaseorder extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('purchase_order_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';


    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_transaction) {
            if (($module == $this->asik_model->trans_01) && ($string == $category . $module)) {
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
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                $data['action_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);
                $data['action_excel_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_exp_excel);
                $data['action_pdf_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_exp_pdf);
                
                /* end privilege */
                $this->load->helper('form');
                $this->load->helper('file');
                $data['pagecode'] = $string;
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($button == 0){
                    $data['list'] = $this->purchase_order_model->get_purchase_order_list($start_date, $end_date);
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
                            $signupdate = $year.'-'.$month.'-'.$day;
                            $signupweek = date("W",strtotime($signupdate));

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
                    $data['list'] = $this->purchase_order_model->get_purchase_order_list($start_date, $end_date);
                }
                $mdl = $this->asik_model->category_transaction . $this->asik_model->trans_01;
                $data['mdl'] = $mdl;
                $data['total'] = $this->purchase_order_model->get_total_detail();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Credit Invoice', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'purchaseorder/purchase_order_list.php';
                /* form */
                
                $data['po_id'] = $this->general_model->draw_hidden_field('po_id', '');
                $data['po_number'] = $this->general_model->draw_text_field('PO No.', 1, 'po_number', '', '', '');
                $data['po_date'] = $this->general_model->draw_datepicker('PO Date', 1, 'po_date', '');
                $data['invoice'] = $this->general_model->draw_text_field('Supplier Invoice No.', 1, 'invoice', '', '', '');
                $data['invoice_date'] = $this->general_model->draw_datepicker('Supplier Invoice Date', 1, 'invoice_date', '');
                $data['receive_no'] = $this->general_model->draw_text_field('Receive No.', 1, 'receive_no', '', '', '');
                $data['receive_date'] = $this->general_model->draw_datepicker('Receive Date', 1, 'receive_date', '');
                
                $this->load->model('supplier_model');
                $supplier = $this->supplier_model->get_supplier_list();
                $supplier_opt = array();
                if ($supplier->num_rows()!=0){
                    foreach ($supplier->result() as $value) {
                        $supplier_opt[$value->supplier_id] = $value->supplier_name;
                    }
                }
                
                $data['supplier_id'] = $this->general_model->draw_select('Supplier', 0, 'supplier_id', 1, $supplier_opt, '', 1);
                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 0, $branch_opt, '', 0);
                $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
                $data['show_modal'] = 'purchaseorder/purchaseorder_modal.php';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function ajax_edit($id) {
        $data = $this->purchase_order_model->get_purchase_order_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function purchaseorder_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('po_date', 'PO date', 'required');
        $this->form_validation->set_rules('po_number', 'PO number', 'required');
        $this->form_validation->set_rules('invoice', 'Invoice number', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->purchase_order_model->insert_purchase_order();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function purchaseorder_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('po_date', 'PO date', 'required');
        $this->form_validation->set_rules('po_number', 'PO number', 'required');
        $this->form_validation->set_rules('invoice', 'Invoice number', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->purchase_order_model->update_purchase_order($this->input->post('po_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function purchaseorder_delete($id) {
        $this->purchase_order_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function do_upload() {
        $id = $this->input->post('po_id');
        $po = $this->purchase_order_model->get_purchase_order_by_id($id);
        if ($po->num_rows()!=0){
            $row = $po->row();
            unlink('./assets/files/' . $row->file_name);
        }
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'buktitrans-'.$id;
        $config['upload_path'] = './assets/files/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 3540;
        $config['max_width'] = 5000;
        $config['max_height'] = 5000;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->purchase_order_model->update_file_name($id, $info['orig_name']);
        }
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/purchaseorder/go/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_01 . '/';
        redirect($back);
    }
    
}
