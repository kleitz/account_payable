<div class="col-xs-12">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4><a href="<?php echo site_url('paymentvoucher/go/'.$pagecode) ?>" class="btn btn-default">Back</a> List of Cash Request</h4>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-striped table-bordered datatable3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Employee</th>
                        <th>Branch</th>
                        <th>Amount</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>

                <?php
                if ($castrequest_list->num_rows()!=0){
                    $no = 1;    
                    $total_amount = 0;
                    
                    echo '<tbody>';
                    foreach ($castrequest_list->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->cash_request_id);
                        $total_amount = $total_amount + $value->amount;
                        
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->cash_request_date.'</td>';
                        echo '<td>'.$value->cash_request_number.'</td>';
                        echo '<td>'.$value->employee_name.'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td class="text-right">'.number_format($value->amount, 2).'</td>';
                        echo '<td class="text-right">';
                            echo '<a class="btn btn-success btn-sm" href="'. site_url('paymentvoucher/pay/'.$pagecode.'/'.$enc_id.'/0/2').'">Pay</a>';
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
                        <th class="text-right">Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
    </div>
</div>