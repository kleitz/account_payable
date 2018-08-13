<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Asik_model
 *
 * @author mchen
 */
class Asik_model extends CI_Model {
    
    public $category = array(
        '201910' => 'Dashboard',
        '201911' => 'Transaction',
        '201912' => 'Create New',
        '201913' => 'Report',
        '201914' => 'Master Data',
        '201915' => 'System'
    );
    
    public $category_dashboard = '201910';
    public $category_transaction = '201911';
    public $category_configuration = '201912';
    public $category_report = '201913';
    public $category_masterdata = '201914';
    public $category_system = '201915';


    public $module = array(
        array(
            '11214301' => ['dashboard','Dashboard']
        ),
        array(
            '21214301' => ['creditinvoice','Credit Invoice'],
            '21214302' => ['cashrequest','Cash Request'],
            '21214303' => ['cashreceived','Cash Received'],
            '21214304' => ['cashreturned','Cash Returned'],
            '21214305' => ['openingbalance','Received from Bank']
        ),
        array(
            '31214301' => ['paymentprocess','Payment Process'],
            '31214302' => ['paymentvoucher','Payment Voucher']
        ),
        array(
            '41214301' => ['certificate','Summary Report'],
            '41214302' => ['supplierreport','Supplier Report'],
            '41214303' => ['expensereport','Expenses Report'],
            '41214304' => ['duesreport','Dues Report'],
            '41214305' => ['outstandingreport','Outstanding Report'],

            '41214306' => ['report','Journal'],
            '41214307' => ['report','Trial Balance'],
            '41214308' => ['report','Ledger']
        ),
        array(
            '51214301' => ['account','Account'],
            '51214302' => ['supplier','Supplier'],
            '51214303' => ['branch','Branch'],
            '51214304' => ['users','Users'],
            '51214305' => ['employee','Employee'],
            '51214306' => ['period','Period'],
            '51214307' => ['bank','Bank']
        ),
        array(
            '61214301' => ['logactivity','Log Activity'],
            '61214302' => ['privilege','Privilege']
        )
    );
    
    public $dash_01 = '11214301';
    /*transaction*/
    public $trans_01 = '21214301';
    public $trans_02 = '21214302';
    public $trans_03 = '21214303';
    public $trans_04 = '21214304';
    public $trans_05 = '21214305';
    /*configuration*/            
    public $config_01 = '31214301';
    public $config_02 = '31214302';
    /*report*/
    public $report_01 = '41214301';
    public $report_02 = '41214302';
    public $report_03 = '41214303';
    public $report_04 = '41214304';
    public $report_05 = '41214305';
    public $report_06 = '41214306';
    public $report_07 = '41214307';
    public $report_08 = '41214308';
    /*masterdata*/
    public $master_01 = '51214301';
    public $master_02 = '51214302';
    public $master_03 = '51214303';
    public $master_04 = '51214304';
    public $master_05 = '51214305';
    public $master_06 = '51214306';
    public $master_07 = '51214307';
    /*system*/
    public $system_01 = '61214301';
    public $system_02 = '61214302';

    public $action = array(
        '021901' => 'View Data',
        '021902' => 'Add',
        '021903' => 'Edit',
        '021904' => 'Delete',
        '021905' => 'Upload',
        '021906' => 'Download',
        '021907' => 'Checked',
        '021908' => 'Approved',
        '021909' => 'Paid'
    );
    public $action_view_data = '021901';
    public $action_add = '021902';
    public $action_edit = '021903';
    public $action_delete = '021904';
    public $action_upload = '021905';
    public $action_download = '021906';
    public $action_checked = '021907';
    public $action_approved = '021908';
    public $action_paid = '021909';


    public $icon = array(
        '<i class="fa fa-dashboard"></i>',
        '<i class="fa fa-refresh"></i>',
        '<i class="fa fa-gear"></i>',
        '<i class="fa fa-book"></i>',
        '<i class="fa fa-hdd-o"></i>',
        '<i class="fa fa-desktop"></i>'
    );
    
    public function get_privilege($priv_group_id=0, $action_code=''){
        $sql = 'SELECT 
        ma.module_id, ma.action_id, ca.module_category_code, ca.module_category_name,
        mo.module_code, mo.module_name, a.action_code
        FROM privilege_user AS p
        INNER JOIN module_action AS ma ON ma.module_action_id=p.module_action_id
        INNER JOIN module AS mo ON mo.module_id=ma.module_id
        INNER JOIN module_category AS ca ON ca.module_category_id=mo.module_category_id
        INNER JOIN action AS a ON a.action_id=ma.action_id
        WHERE p.priv_group_id='.$priv_group_id.' AND a.action_code='.$action_code;
        $query = $this->db->query($sql);
        
        if (isset($query)){
            $idx = 0;
            foreach ($query->result() as $val) {
                $category[$idx] = $val->module_category_code;
                $module[$idx] = $val->module_code;
                $action[$idx] = $val->action_code;
                $idx++;
            }
            $data = array ($category, $module, $action);
            return $data;
        }
        return FALSE;
    }
    
    public function is_privilege($category_code='', $module_code='', $priv_group_id = '', $action_code=''){
        $sql = 'SELECT 
        p.priv_group_id, p.privilege_id  
        FROM privilege_user AS p
        INNER JOIN module_action AS ma ON ma.module_action_id=p.module_action_id
        INNER JOIN module AS mo ON mo.module_id=ma.module_id
        INNER JOIN module_category AS ca ON ca.module_category_id=mo.module_category_id
        INNER JOIN action AS a ON a.action_id=ma.action_id
        WHERE ca.module_category_code='.$category_code.' AND mo.module_code='.$module_code.' ';
        $sql .= ' AND a.action_code='.$action_code. ' AND p.priv_group_id='.$priv_group_id;
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            return TRUE;
        }
        return FALSE;
    }

    public function draw_header($title='', $small='', $idx=0, $category_code='', $module_code='') {
        $data = $this->module[$idx];
        $module_name = $this->module_name = $data[$module_code][1];
        $string = '<section class="content-header">
            <h1>' . $title . ' <small>'.$small.'</small></h1>
            <ol class="breadcrumb">
              <li><a href="#">' . $this->icon[$idx] . ' ' . $this->category[$category_code]. '</a></li>
              <li class="active">' . $module_name . '</li>
            </ol>
            </section>';
        return $string;
    }
    
    public function is_login() {
        if (!$this->session->has_userdata('username') && !$this->session->has_userdata('password')) {
            redirect('/auth');
        }
    }
}
