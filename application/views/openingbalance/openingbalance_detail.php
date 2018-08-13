<?php
if ($openingbalance->num_rows()!=0){
    $row = $openingbalance->row();
?>
<div class="col-md-8">
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="<?php echo site_url($back_link) ?>" class="btn btn-default btn-sm">Back</a>
        </div>
        <div class="box-body">
            <?php
                echo '<table class="table">
                    <tr>
                        <th>Number</th>
                        <td>'.$row->opening_balance_number.'</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>'.$this->general_model->get_string_date($row->opening_balance_date).'</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>'.$row->description.'</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>'.number_format($row->amount).'</td>
                    </tr>
                    <tr>
                        <th>Outlet (Branch)</th>
                        <td>'.$row->branch_name.'</td>
                    </tr>
                </table>';
            ?>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="box box-default">
        <div class="box-header with-border">
            <button class="btn btn-default btn-sm" onclick="upload_file(<?php echo $row->opening_balance_id ?>)">Upload file</button>
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
            
            if ($openingbalance_file->num_rows()!=0){
                echo '<table class="table">';
                foreach ($openingbalance_file->result() as $val) {
                    echo '<tr>
                        <td><i class="fa '.$iconfile[$val->file_type].'"></i> | <span style="font-size:11px;">'.$val->file_name.'</span></td>
                        <td class="text-right">
                            <a href="'. base_url().'assets/openingbalance/'.$val->file_name.'" target="_blank">
                            <i class="fa fa-download"></i></a>
                            &nbsp;|&nbsp;
                            <a href="'. site_url('openingbalance/delete_file/'.$val->opening_balance_file_id.'/'.$val->file_name.'/'.$encrypt_id.'/') .'">
                            <i class="fa fa-trash-o"></i></a>
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