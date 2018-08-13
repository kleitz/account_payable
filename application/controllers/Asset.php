<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Asset
 *
 * @author mchen
 */
class Asset extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }

    private $go = FALSE;
    public $category_index = 2;
    public $category = '';
    public $module = '';

    public function set_go($go = FALSE) {
        $this->go = $go;
    }

    public function get_go() {
        return $this->go;
    }

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_configuration) {
            if (($module == $this->asik_model->config_01) && ($string == $category . $module)) {
                $this->category_index = 2;
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function go($string = '', $part = 0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if ($this->asik_model->is_privilege($category, $module, $this->session->userdata('user_level'))) {
                $this->set_go(TRUE);
                $this->view_page($part);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function view_page($part = 0) {
        if ($this->get_go()) {
            $encrypt_id = $this->general_model->encrypt_value('0');
            $mdl = $this->asik_model->category_configuration . $this->asik_model->config_01;
            $action_module = 'asset/ac/' . $mdl;
            $go_module = 'asset/go/' . $mdl;
            $data['action_mo'] = $action_module;
            $data['go_mo'] = $go_module;
            $data['encrypt_id'] = $encrypt_id;
            $header = $this->asik_model->draw_header('Judul', '', $this->category_index, $this->category, $this->module);
            $data['active_li'] = $this->category_index;
            $data['content_header'] = $header;
            $data['halaman'] = 'savingsconfig/savings_home.php';
            $this->load->view('template', $data);
        } else {
            show_404();
        }
    }

    public function ac($string = '', $part = 0, $action = '', $encrypt_id = '') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if ($this->asik_model->is_privilege($category, $module, $this->session->userdata('user_level'))) {
                $id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                switch ($action) {
                    case $this->asik_model->action_add:
                        $this->action_add($part, $id);
                        break;
                    case $this->asik_model->action_edit:
                        $this->action_edit($part, $id);
                        break;
                    case $this->asik_model->action_delete_confirm:
                        $this->set_confirm($id, $part);
                        break;
                    case $this->asik_model->action_delete_approve:
                        $this->asset_model->delete_approve($id, $part);
                        break;
                    default : show_404();
                }
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

}
