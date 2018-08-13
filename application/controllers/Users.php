<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Users
 *
 * @author mchen
 */
class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_masterdata;
        $module_code = $this->asik_model->master_04;
        if ($category == $category_code){
            if (($module == $module_code) && ($string == $category.$module)){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string=''){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $this->load->helper('form');
                $this->load->helper('file');
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('User List', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['list'] = $this->users_model->get_user_list();
                $data['user_id'] = $this->general_model->draw_hidden_field('user_id', '');
                $data['fullname'] = $this->general_model->draw_text_field('Full Name', 1, 'fullname', '', '', '');
                $gender_option = array('0' => 'Female','1' => 'Male');
                $data['gender'] = $this->general_model->draw_select('Gender', 0, 'gender', 0, $gender_option, '');
                $data['address'] = $this->general_model->draw_text_field('Address', 1, 'address', '', '', '');
                $data['email'] = $this->general_model->draw_email_field('Email', 0, 'email', '', '', '');
                $data['phone'] = $this->general_model->draw_text_field('Phone', 1, 'phone', '', '', '');
                $group = $this->users_model->get_group_list();
                $group_arr = array();
                if ($group->num_rows()!=0){
                    foreach ($group->result() as $value) {
                        $group_arr[$value->priv_group_id] = $value->priv_group_name;
                    }
                }
                $data['priv_group_id'] = $this->general_model->draw_select('Privilege Group', 0, 'priv_group_id', 0, $group_arr, '');
                $status_opt = array('0' => 'non-active','1' => 'active');
                $data['user_status'] = $this->general_model->draw_select('Status', 0, 'user_status', 0, $status_opt, '');
                $data['halaman'] = 'users/user_list.php';
                $data['show_modal'] = 'users/user_modal.php';
                $data['datatable_title'] = 'User';
                $data['footer_total'] = '';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_edit($id) {
        $data = $this->users_model->get_user_by_id($id)->row();
        echo json_encode($data);
    }
 
    public function user_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('fullname', 'Fullname', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $fullname = $this->input->post('fullname');
            $gender = $this->input->post('gender');
            $address = $this->input->post('address');
            $phone = $this->input->post('phone');
            $email = $this->input->post('email');
            $priv_group_id = $this->input->post('priv_group_id');
            $data = array(
                'username' => $username,
                'password' => $this->hash_passwd($password),
                'fullname' => $fullname,
                'gender' => $gender,
                'address' => $address,
                'email' => $email,
                'phone' => $phone,
                'priv_group_id' => $priv_group_id,
                'user_status' => 1
            );
            $insert = $this->users_model->insert_user($data);
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function user_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('fullname', 'Fullname', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $fullname = $this->input->post('fullname');
            $gender = $this->input->post('gender');
            $address = $this->input->post('address');
            $phone = $this->input->post('phone');
            $email = $this->input->post('email');
            $priv_group_id = $this->input->post('priv_group_id');
            $user_status = $this->input->post('user_status');
            $data = array(
                'fullname' => $fullname,
                'gender' => $gender,
                'address' => $address,
                'email' => $email,
                'phone' => $phone,
                'priv_group_id' => $priv_group_id,
                'user_status' => $user_status
            );

            $this->users_model->update_user(array('user_id' => $this->input->post('user_id')), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function user_delete($id) {
        $this->users_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function do_upload() {
        $id = $this->input->post('uid');
        $user = $this->users_model->get_user_by_id($id);
        if ($user->num_rows()!=0){
            $row = $user->row();
            unlink('./assets/img_profile/' . $row->photo);
        }
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'profile-'.$id;
        $config['upload_path'] = './assets/img_profile/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 2048;
        $config['max_width'] = 3024;
        $config['max_height'] = 2768;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->users_model->update_file_name($id, $info['orig_name']);
        }
    
        $back = '/users/go/' . $this->asik_model->category_masterdata;
        $back .= $this->asik_model->master_04 . '/';
        redirect($back);
    }
    
    public function update_password() {
        $id = $this->input->post('puid');
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');
        
        $user = $this->users_model->get_user_by_id($id);
        if ($user->num_rows()!=0){
            $row = $user->row();
            if ($this->check_passwd($row->password, $old_password)) {
                if ($new_password == $confirm_password){
                    $data = array('password'=>$this->hash_passwd($new_password));
                    $this->db->where('user_id', $id);
                    $this->db->update('users', $data);
                }
            }
        }
        $back = '/users/go/' . $this->asik_model->category_masterdata;
        $back .= $this->asik_model->master_04 . '/';
        redirect($back);
    }
    
    public function vdetail($encrypt_id='') {
        $id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */ 
        $data['active_li'] = $this->category_index;
        $header = $this->asik_model->draw_header('User Detail', 'View', $this->category_index, $this->asik_model->category_masterdata, $this->asik_model->master_04);
        $data['content_header'] = $header;
        $group = $this->users_model->get_group_list();
        $group_arr = array();
        if ($group->num_rows()!=0){
            foreach ($group->result() as $value) {
                $group_arr[$value->priv_group_id] = $value->priv_group_name;
            }
        }
        $data['group_arr'] = $group_arr;
            
        $data['detail'] = $this->users_model->get_user_by_id($id);
        $data['halaman'] = 'users/user_detail.php';
        $this->load->view('template', $data);
    }
    
    public function profile($username='') { 
        $data['active_li'] = $this->category_index;
        $header = $this->asik_model->draw_header('User Profile', 'View', $this->category_index, $this->asik_model->category_masterdata, $this->asik_model->master_04);
        $data['content_header'] = $header;
        $group = $this->users_model->get_group_list();
        $group_arr = array();
        if ($group->num_rows()!=0){
            foreach ($group->result() as $value) {
                $group_arr[$value->priv_group_id] = $value->priv_group_name;
            }
        }
        $data['group_arr'] = $group_arr;
        $data['detail'] = $this->users_model->get_user_login($username);
        $data['halaman'] = 'users/user_profile.php';
        $this->load->view('template', $data);
    }
    
    public function hash_passwd($password, $random_salt = '') {
        // If no salt provided for older PHP versions, make one
        if (!is_php('5.5') && empty($random_salt))
            $random_salt = $this->random_salt();

        // PHP 5.5+ uses new password hashing function
        if (is_php('5.5')) {
            return password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
        }

        // PHP < 5.5 uses crypt
        else {
            return crypt($password, '$2y$10$' . $random_salt);
        }
    }
    
    public function check_passwd($hash, $password) {
        if (is_php('5.5') && password_verify($password, $hash)) {
            return TRUE;
        } else if ($hash === crypt($password, $hash)) {
            return TRUE;
        }
        return FALSE;
    }

}
