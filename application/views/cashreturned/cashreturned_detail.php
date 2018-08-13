<?php
if ($cash_return->num_rows()!=0){
    $row = $cash_return->row();
?>
<div class="col-md-8">
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="<?php echo site_url($back_link) ?>" class="btn btn-default btn-sm">Back</a>
            <a target="_blank" href="<?php echo site_url($print_link) ?>" class="btn btn-default btn-sm">Print</a>
        </div>
        <div class="box-body">
            <?php
            
                echo '<table class="table">
                    <tr>
                        <th>ID Cash Return</th>
                        <td>'.$row->cash_return_number.'</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>'.$this->general_model->get_string_date($row->cash_return_date).'</td>
                    </tr>
                    <tr>
                        <th>Cash From</th>
                        <td>'.$account_opt[$row->account_from].'</td>
                    </tr>
                    <tr>
                        <th>Received By</th>
                        <td>'.$account_opt[$row->account_to].'</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>'.number_format($row->amount).'</td>
                    </tr>
                    <tr>
                        <th>Return mode</th>
                        <td>'.$payment_opt[$row->return_mode].'</td>
                    </tr>
                    <tr>
                        <th>Outlet (Branch)</th>
                        <td>'.$row->branch_name.'</td>
                    </tr>
                    <tr>
                        <th>Remark</th>
                        <td>'.$row->remark.'</td>
                    </tr>
                    
                </table>';
            ?>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="box box-default">
        <div class="box-header with-border">
            <button class="btn btn-default btn-sm" onclick="upload_file(<?php echo $row->cash_return_id ?>)">Upload file</button>
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
            
            if ($cash_return_file->num_rows()!=0){
                echo '<table class="table">';
                foreach ($cash_return_file->result() as $val) {
                    echo '<tr>
                        <td><i class="fa '.$iconfile[$val->file_type].'"></i> | '.$val->file_name.'</td>
                        <td class="text-right">
                        <a href="'. base_url().'assets/cashreturn/'.$val->file_name.'" target="_blank">
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