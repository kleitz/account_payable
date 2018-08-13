<div class="col-xs-12">
    <div class="box box-warning">
        <div class="box-header with-border">
            <strong>List of Cash Received</strong>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-striped table-bordered datatable3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Cash From</th>
                        <th>Received By</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Outlet</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                if ($cashreceived_list->num_rows()!=0){
                    $no = 1;    
                    $total_amount = 0;
                    $total_paid = 0;
                    echo '<tbody>';
                    foreach ($cashreceived_list->result() as $value) {
                        $total_amount += $value->amount;
                        $total_paid += $value->paid_off;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->cash_receive_number.'</td>';
                        echo '<td>'.$this->general_model->get_string_date($value->cash_receive_date).'</td>';
                        echo '<td>'.$account_opt[$value->account_from].'</td>';
                        echo '<td>'.$account_opt[$value->account_to].'</td>';
                        echo '<td class="text-right">'.number_format($value->amount, 2).'</td>';
                        echo '<td class="text-right">'.number_format($value->paid_off, 2).'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td>';
                            echo '<button onclick="add_data('.$value->cash_receive_id.')" class="btn btn-success btn-sm">Return</button>';
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Cash From</th>
                        <th>Received By</th>
                        <th class="text-right">'.number_format($total_amount, 2).'</th>
                        <th class="text-right">'.number_format($total_paid, 2).'</th>
                        <th>Outlet</th>
                        <th>Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('cashreturned/go/'.$pagecode) ?>" class="btn btn-default">Back</a>
        </div>
    </div>
</div>