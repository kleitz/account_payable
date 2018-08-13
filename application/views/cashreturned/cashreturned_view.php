<div class="col-xs-12">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4>List of Cash Received</h4>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Received From</th>
                        <th>Received By</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                if ($list_received->num_rows()!=0){
                    $no = 1;
                    $total_debit = 0;
                    $total_credit = 0;
                    $total_balance = 0;
                    echo '<tbody>';
                    foreach ($list_received->result() as $val) {
                        $transdate = substr($val->trans_date, 0, 7);
                        $total_debit += $val->debit;
                        $total_credit += $val->credit;
                        $total_balance += $val->balance;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->trans_date.'</td>';
                        echo '<td>'.$val->trans_code.'</td>';
                        echo '<td>'.$val->description.'</td>';
                        echo '<td>'.$account_opt[$val->received_from].'</td>';
                        echo '<td>'.$account_opt[$val->received_by].'</td>';
                        echo '<td class="text-right">'.number_format($val->debit, 2).'</td>';
                        echo '<td class="text-right">'.number_format($val->credit, 2).'</td>';
                        echo '<td class="text-right">'.number_format($val->balance, 2).'</td>';
                        echo '<td  class="text-right">';
                        if ($period_active == 1 && $period_month == $transdate){
                            echo '<button class="btn btn-success btn-sm" onclick="add_data('. $val->trans_id.')">Return</button>&nbsp;';
                        }
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Received From</th>
                        <th>Received By</th>
                        <th class="text-right">'. number_format($total_debit, 2).'</th>
                        <th class="text-right">'. number_format($total_credit, 2).'</th>
                        <th class="text-right">'. number_format($total_balance, 2).'</th>
                        <th>Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
                
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>
<!------------------- list return ------------------->
<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('cashreturned/go/'.$pagecode) ?>">
                <div class="form-group">

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="start_date" class="form-control datepicker" value="" placeholder="Date from">
                    </div>

                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="end_date" class="form-control datepicker" value="" placeholder="Date to">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            </form>
            
            <div class="form-group">
                <label style="color:#575757">&nbsp;</label>
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('cashreturned/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('cashreturned/go/'.$pagecode.'/2/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('cashreturned/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('cashreturned/go/'.$pagecode.'/4/') ?>" class="btn btn-default">Last Month</a>
                    </div>
                </div>  
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Returned By</th>
                        <th>Returned To</th>
                        <th>Amount</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    $total_amount = 0;
                    echo '<tbody>';
                    foreach ($list->result() as $val) {
                        $total_amount += $val->amount;
                        $transdate = substr($val->trans_date, 0, 7);
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->trans_date.'</td>';
                        echo '<td>'.$val->trans_code.'</td>';
                        echo '<td>'.$val->description.'</td>';
                        echo '<td>'.$val->account_name.'</td>';
                        echo '<td>'.$account_opt[$val->account_relation].'</td>';
                        echo '<td class="text-right">'.number_format($val->amount, 2).'</td>';
                        echo '<td>[0]</td>';
                        echo '<td  class="text-right">';
                        if ($period_active == 1 && $period_month == $transdate){
                            echo '<button class="btn btn-success btn-sm" onclick="edit_data('. $val->trans_id.')">Edit</button>&nbsp;';
                            echo '<button class="btn btn-danger btn-sm" onclick="delete_data('. $val->trans_id.')">Delete</button>';
                        }
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Returned By</th>
                        <th>Returned To</th>
                        <th class="text-right">'. number_format($total_amount, 2).'</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
                
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="#" class="btn btn-success btn-sm">Excel</a>
            <a href="#" class="btn btn-danger btn-sm">PDF</a>
        </div>
    </div>
    <!-- /.box -->
</div>