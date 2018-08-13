<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>PP Number</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <?php
                if ($detail->num_rows()!=0){
                    $pp_status_arr = array(
                    '<span class="label label-danger">Draft</span>', 
                    '<span class="label label-warning">Cross Check</span>', 
                    '<span class="label label-info">Checked</span>', 
                    '<span class="label label-primary">Approved</span>',
                    '<span class="label label-success">Closed</span>'
                    );
                    echo '<tbody>';
                    $no = 1;
                    $ppcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                    foreach ($detail->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->pp_id);
                        $linkdetail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/4/'; 
                        
                        echo '<tr>
                            <td>'.$no.'</td>
                            <td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>
                            <td><a href="'. site_url($linkdetail).'" target="_blank">'.$value->pp_number.'</a></td>
                            <td>'.$value->branch_name.'</td>
                            <td>'.$pp_status_arr[$value->pp_status].'</td>
                            <td class="text-right">'. number_format($value->debit).'</td>
                            <td class="text-right">'. number_format($value->credit).'</td>
                            <td class="text-right">'. number_format($value->debit - $value->credit).'</td>
                        </tr>';
                        
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>PP Number</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>