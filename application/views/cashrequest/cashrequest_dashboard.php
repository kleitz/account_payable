<div class="col-xs-12">
    <div class="box box-primary">
       
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Employee</th>
                        <th>Branch</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                if ($cashrequest_outstanding->num_rows()!=0){
                    $no = 1;    
                    $total_amount = 0;
                    
                    echo '<tbody>';
                    foreach ($cashrequest_outstanding->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->cash_request_id);
                        $total_amount = $total_amount + $value->amount;
                        
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$this->general_model->get_string_date($value->cash_request_date).'</td>';
                        echo '<td>'.$value->cash_request_number.'</td>';
                        echo '<td>'.$value->employee_name.'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td class="text-right">'.number_format($value->amount, 2).'</td>';
                        echo '<td>';
                            echo '<a target="_blank" href="'. site_url('cashrequest/cdetail/'.$pagecode.'/'.$enc_id).'" class="btn btn-default btn-sm">Detail</a>&nbsp;';
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Employee</th>
                        <th>Branch</th>
                        <th class="text-right">'. number_format($total_amount, 2).'</th>
                        <th>Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
    </div>
</div>
