<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Podetail
 *
 * @author mchen
 */
class Podetail extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('podetail_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_transaction) {
            if (($module == $this->asik_model->trans_01) && ($string == $category . $module)) {
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
                $po_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                $mdl = $this->asik_model->category_transaction . $this->asik_model->trans_01;
                $detail_id = $this->general_model->encrypt_value('0');
                $action_module = 'podetail/ac/' . $mdl .'/'. $this->asik_model->action_add.'/'.$detail_id.'/'.$encrypt_id;
                $go_module = 'podetail/go/' . $mdl;
                $data['mdl'] = $mdl;
                $data['action_mo'] = $action_module;
                $data['go_mo'] = $go_module;
                $data['info'] = $this->podetail_model->get_purchase_order($po_id);
                $data['detail'] = $this->podetail_model->get_detail_by_poid($po_id);
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Credit Invoice', 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'podetail/podetail_list.php';
                /* form */
                $data['po_detail_id'] = $this->general_model->draw_hidden_field('po_detail_id', '');
                $data['po_id'] = $this->general_model->draw_hidden_field('po_id', $po_id);
                $data['item_code'] = $this->general_model->draw_text_field('Unit', 1, 'item_code', '', '', '');
                $data['quantity'] = $this->general_model->draw_input_number('Quantity', 1, 'quantity', '');
                $data['item_name'] = $this->general_model->draw_text_field('Item Name', 1, 'item_name', '', '', '');
                $data['price'] = $this->general_model->draw_input_currency('Price', 1, 'price', '');
                //$data['discount'] = $this->general_model->draw_text_field('Discount', 1, 'discount', '', '', '');
                $data['show_modal'] = 'podetail/podetail_modal.php';
        
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_edit($id) {
        $data = $this->podetail_model->get_detail_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function podetail_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('po_id', 'PO ID', 'required');
        $this->form_validation->set_rules('item_code', 'Item Code', 'required');
        $this->form_validation->set_rules('quantity', 'Quantity', 'required');
        $this->form_validation->set_rules('item_name', 'Item name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->podetail_model->insert_detail();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function podetail_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('po_id', 'PO ID', 'required');
        $this->form_validation->set_rules('item_code', 'Item Code', 'required');
        $this->form_validation->set_rules('quantity', 'Quantity', 'required');
        $this->form_validation->set_rules('item_name', 'Item name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->podetail_model->update_detail($this->input->post('po_detail_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function podetail_delete($id) {
        $this->podetail_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
