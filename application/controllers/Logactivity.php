<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logactivity
 *
 * @author Hendra McHen
 */
class Logactivity extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('log_activity_model');
    }

    public $category_index = 5;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_system) {
            if (($module == $this->asik_model->system_01) && ($string == $category . $module)) {
                $this->category_index = 5;
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
                $this->category = $category;
                $this->module = $module;
                
                $data['pagecode'] = $string;
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Log Activity', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                if ($button == 0){
                    $data['list'] = $this->log_activity_model->get_activity_list($start_date, $end_date);
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
                            $last_month = $month - 1;
                            $start_date = $year.'-'.$last_month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                    }
                    $data['list'] = $this->log_activity_model->get_activity_list($start_date, $end_date);
                }
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Log Activity';
                $data['footer_total'] = '';
                /* ===== end datatable ===== */
                $data['halaman'] = 'logactivity/logactivity_list.php'; 
                $data['show_modal'] = 'logactivity/logactivity_modal.php';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function ajax_view($id) {
        $data = $this->log_activity_model->get_activity_by_id($id)->row();
        echo json_encode($data);
    }
    
   
}
