<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Period_model
 *
 * @author mchen
 */
class Period_model extends CI_Model {
    
    public function get_period_list() {
        $sql = 'SELECT * FROM period';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_period_by_id($id=0) {
        $sql = 'SELECT * FROM period WHERE period_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_period_active() {
        $sql = 'SELECT * FROM period WHERE active_status=1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_period_date($period=''){
        $date = 0;
        if ($period != ''){
            $sql  = 'SELECT trans_date FROM transactions ';
            $sql .= 'WHERE trans_date LIKE "'.$period.'%" ';
            $sql .= 'ORDER BY trans_date DESC';
            $query = $this->db->query($sql);
            $date = $period.'-01';
            if ($query->num_rows()!=0){
                $row = $query->row();
                $date = $row->trans_date;
            }
        }
        return $date;
    }
    
    public function get_period_and_detail() {
        $sql  = 'SELECT p.period, p.year, pd.date_day, pd.date_event  FROM period_detail AS pd ';
        $sql .= 'INNER JOIN period AS p ON pd.period_id=p.period_id ';
        $sql .= 'WHERE pd.date_type=1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_period() {
        $year = $this->input->post('year');
        $sorting = $this->input->post('sorting');
        $period = $this->input->post('period');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $closed = $this->input->post('closed');
        $active_status = $this->input->post('active_status');
        $data = array(
            'year' => $year,
            'sorting' => $sorting,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'closed' => $closed,
            'active_status' => $active_status
        );

        $this->db->insert('period', $data);
    }
    
    public function update_period($id=0) {
        $year = $this->input->post('year');
        $sorting = $this->input->post('sorting');
        $period = $this->input->post('period');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $closed = $this->input->post('closed');
        $active_status = $this->input->post('active_status');
        $data = array(
            'year' => $year,
            'sorting' => $sorting,
            'period' => $period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'closed' => $closed,
            'active_status' => $active_status
        );

        $this->db->where('period_id', $id);
        $this->db->update('period', $data);
    }
    
    public function delete_by_id($id) {
        $this->db->where('period_id', $id);
        $this->db->delete('period');
    }
    
    public function is_active() {
        $sql  = 'SELECT * FROM period ';
        $sql .= 'WHERE active_status=1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function set_active($id=0) {
        $is_active = $this->is_active();
        if ($is_active->num_rows()==0){
            $data = array(
                'closed' => 0,
                'active_status' => 1
            );

            $this->db->where('period_id', $id);
            $this->db->update('period', $data);
            /**
            $back = '/period/go/' . $this->asik_model->category_masterdata;
            $back .= $this->asik_model->master_06 . '/';
            redirect($back);*/
        }
    }
    
    public function set_non_active($id = 0) {
        $data = array(
            'active_status' => 0
        );

        $this->db->where('period_id', $id);
        $this->db->update('period', $data);
        /**
        $back = '/period/go/' . $this->asik_model->category_masterdata;
        $back .= $this->asik_model->master_06 . '/';
        redirect($back);*/
    }

    public function set_closing($id=0) {
        $data = array(
            'closed' => 1,
            'active_status' => 0
        );

        $this->db->where('period_id', $id);
        $this->db->update('period', $data);
        
        /*== redirect ==
        $back = '/period/go/' . $this->asik_model->category_masterdata;
        $back .= $this->asik_model->master_06 . '/';
        redirect($back);*/
    }
    
    public function get_cash_by_period($period_month='') {
        $sql  = 'SELECT c.trans_id, t.trans_date, c.debit, c.credit
        FROM tb_cash AS c 
        INNER JOIN tb_transaction AS t ON t.trans_id=c.trans_id 
        WHERE t.trans_date LIKE "'.$period_month.'%"';
        $query = $this->db->query($sql);
        return $query;
    }

}
