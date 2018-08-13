<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Auth
 *
 * @author mchen
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->library('form_validation');
    }

    public function index() {
        if ($this->session->has_userdata('username') && $this->session->has_userdata('password')) {
            $sess_username = $this->session->userdata('username');
            //$sess_password = $this->session->userdata('password');
            $data = $this->users_model->get_user_login($sess_username);
            $row = $data->row();
            if (isset($row)) {
                redirect('/dashboard/');
            } else {
                $data['valid_message'] = 0;
                $this->load->view('login', $data);
            }
        } else {
            $this->validasi();
        }
    }

    public function validasi() {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required', array('required' => 'You must provide a %s.')
        );
        if ($this->form_validation->run() == FALSE) {
            $data['valid_message'] = 0;
            $this->load->view('login', $data);
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            /* update 2018 02 25 */
            $ap_lang = $this->input->post('ap_lang');
            $row = $this->users_model->get_user_login($username)->row();
            if (isset($row) && ($this->check_passwd($row->password, $password))) {
                $login_data = array(
                    'user_id' => $row->user_id,
                    'username' => $row->username,
                    'password' => $row->password,
                    'fullname' => $row->fullname,
                    'priv_group_id' => $row->priv_group_id,
                    'photo' => $row->photo,
                    'ap_lang' => $ap_lang
                );

                $this->session->set_userdata($login_data);
                redirect('/dashboard/');
            } else {
                $data['valid_message'] = 1;
                $this->load->view('login', $data);
            }
        }
    }

    public function logout() {
        $login_data = array(
            'user_id', 'username', 'password', 'fullname', 'priv_group_id', 'photo', 'ap_lang'
        );

        $this->session->unset_userdata($login_data);
        $this->load->view('logout');
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
