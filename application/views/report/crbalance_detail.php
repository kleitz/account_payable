<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>CR Number</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <?php
                if ($detail->num_rows()!=0){
                    $cashrequest_status = array(
                    '<span class="label label-warning">To Be Check</span>', 
                    '<span class="label label-info">To Be Approve</span>', 
                    '<span class="label label-primary">Approved</span>', 
                    '<span class="label label-danger">Outstanding</span>',
                    '<span class="label label-success">Closed</span>'
                    );
                    echo '<tbody>';
                    $no = 1;
                    $ppcode = $this->asik_model->category_transaction.$this->asik_model->trans_02;
                    foreach ($detail->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->cash_request_id);
                        $linkdetail = 'cashrequest/cdetail/' . $ppcode.'/'.$enc_id.'/';    
                        $nomor = $no;
                        if ($value->credit_total != 0){
                            $nomor = '<a href="'. site_url('crbalance/mdetail/20191341214311/'.$value->cash_request_id.'/'.$value->employee_id).'" target="_blank"><span class="label label-info">'.$no.'</span></a>';
                        }
                        echo '<tr>
                            <td>'.$nomor.'</td>
                            <td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>
                            <td><a href="'. site_url($linkdetail).'" target="_blank">'.$value->cash_request_number.'</a></td>
                            <td>'.$value->branch_name.'</td>
                            <td>'.$cashrequest_status[$value->cash_request_status].'</td>
                            <td class="text-right">'. number_format($value->debit_total).'</td>
                            <td class="text-right">'. number_format($value->credit_total).'</td>
                            <td class="text-right">'. number_format($value->debit_total - $value->credit_total).'</td>
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
                        <th>CR Number</th>
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