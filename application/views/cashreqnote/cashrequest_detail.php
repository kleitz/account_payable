<?php
if ($cash_request_detail->num_rows()!=0){
    $row = $cash_request_detail->row();
?>
<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="<?php echo site_url($back_link) ?>" class="btn btn-default btn-sm">Back</a>
            
            <?php
            if ($row->cash_request_status < 2){
                echo '<a href="#" class="btn btn-default btn-sm" onclick="edit_data('.$row->cash_request_id.')">Edit</a>&nbsp;';
                echo '<a href="#" class="btn btn-default btn-sm" onclick="delete_data('.$row->cash_request_id.')">Delete</a>&nbsp;';
            } 
            ?>
        </div>
        <div class="box-body">
            <?php
            
                echo '<table class="table">
                    <tr>
                        <th>Number</th>
                        <td>'.$row->cash_request_number.'</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>'.$this->general_model->get_string_date($row->cash_request_date).'</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>'.$row->employee_name.'</td>
                    </tr>
                    <tr>
                        <th>Outlet (Branch)</th>
                        <td>'.$row->branch_name.'</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>'.$row->description.'</td>
                    </tr>
                    <tr>
                        <th>Remark</th>
                        <td>'.$row->remark.'</td>
                    </tr>
                    <tr>
                        <th>Payment mode</th>
                        <td>'.$this->cashrequest_model->payment_mode_opt[$row->payment_mode].'</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>'.number_format($row->amount).'</td>
                    </tr>
                    <tr>
                        <th>Cash return</th>
                        <td>'.number_format($row->cash_return).'</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>'.number_format($row->amount - $row->cash_return).'</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>'.$this->cashrequest_model->cashrequest_status[$row->cash_request_status].'</td>
                    </tr>
                </table>';
            ?>
        </div>
        <div class="box-footer">
            <div class="col-sm-4">
                <h4>Prepared by</h4>
                <strong><?= $this->cashrequest_model->get_fullname_by_user_id($row->prepared_by) ?></strong>
            </div>
            <div class="col-sm-4">
                <h4>Checked by</h4>
                <?php
               
                
                if ($row->checked_by == 0 && $action_checked){
                    echo '<a href="'. site_url('cashrequest/checked/'.$encrypt_id).'" class="btn btn-default btn-sm">Checked</a>';
                } 
                if ($row->checked_by != 0){
                    echo '<strong>'.$this->cashrequest_model->get_fullname_by_user_id($row->checked_by).'</strong>';
                }
                ?>
            </div>
            <div class="col-sm-4">
                <h4>Approved by</h4>
                <?php
                if ($row->approved_by == 0 && $action_approved){
                    echo '<a href="'. site_url('cashrequest/approved/'.$encrypt_id).'" class="btn btn-default btn-sm">Approve</a>';
                } 
                if ($row->approved_by != 0){
                    echo '<strong>'.$this->cashrequest_model->get_fullname_by_user_id($row->approved_by).'</strong>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="box box-default">
        <div class="box-header with-border">
            <button class="btn btn-default btn-sm" onclick="upload_file(<?php echo $row->cash_request_id ?>)">Upload file</button>
        </div>
        <div class="box-body">
            <?php
            
            $iconfile = array(
                '.jpg' => 'fa-file-image-o',
                '.png' => 'fa-file-image-o',
                '.jpeg' => 'fa-file-image-o',
                '.pdf' => 'fa-file-pdf-o',
                '.doc' => 'fa-file-word-o',
                '.docx' => 'fa-file-word-o',
                '.xls' => 'fa-file-excel-o',
                '.xlsx' => 'fa-file-excel-o'
            );
            
            if ($cash_request_file->num_rows()!=0){
                echo '<table class="table">';
                foreach ($cash_request_file->result() as $val) {
                    echo '<tr>
                        <td><i class="fa '.$iconfile[$val->file_type].'"></i> | '.$val->file_name.'</td>
                        <td class="text-right">
                        <a href="'. base_url().'assets/cashrequest/'.$val->file_name.'" target="_blank">
                            <i class="fa fa-download"></i></a>
                        </td>
                    </tr>';
                }
                echo '</table>';
            } else {
                echo 'Belum ada file.';
            }
            ?>
        </div>
    </div>
</div>
<?php
}
?>