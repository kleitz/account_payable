<div class="col-xs-12">
    <div class="box box-warning">
        <div class="box-header with-border">
            List of Payment Process
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Branch</th>
                        <th>Supplier</th>
                        <th>Description</th>
                        <th class="text-right">Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                if ($payment_process_list->num_rows()!=0){
                    $no = 1;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($payment_process_list->result() as $val) {
                        $total_all += $val->total;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->pp_number.'</td>';
                        echo '<td>'.$val->pp_date.'</td>';
                        echo '<td>'.$val->pp_due_date.'</td>';
                        echo '<td>'.$val->branch_name.'</td>';
                        echo '<td>'.$val->supplier_name.'</td>';
                        echo '<td>'.$val->description.'</td>';
                        echo '<td class="text-right">'.number_format($val->total, 2).'</td>';
                        echo '<td>';
                        echo '<button class="btn btn-success btn-sm" onclick="add_data('.$val->pp_id.')">Pay</button>';
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>No</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Branch</th>
                        <th>Supplier</th>
                        <th>Description</th>
                        <th class="text-right">'. number_format($total_all, 2).'</th>
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

<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('trans/go/'.$pagecode) ?>">
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
                        <a href="<?php echo site_url('trans/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('trans/go/'.$pagecode.'/2/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('trans/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('trans/go/'.$pagecode.'/4/') ?>" class="btn btn-default">Last Month</a>
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
                        <th>Number</th>
                        <th>Description</th>
                        <th>Admin Fee</th>
                        <th>Mode</th>
                        <th>Total</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                if ($list_pv->num_rows()!=0){
                    $mode = array('Cash', 'Bank');
                    $no = 1;
                    $total_fee = 0;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($list_pv->result() as $val) {
                        $total_fee += $val->admin_fee;
                        $total_all += $val->total;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->pv_date.'</td>';
                        echo '<td>'.$val->pv_number.'</td>';
                        echo '<td>'.$val->description.'</td>';
                        echo '<td class="text-right">'.number_format($val->admin_fee, 2).'</td>';
                        echo '<td>'.$mode[$val->payment_mode].'</td>';
                        echo '<td class="text-right">'.number_format($val->total, 2).'</td>';
                        echo '<td>[0]</td>';
                        echo '<td  class="text-right">';
                        //if ($period_active == 1 && $period_month == $transdate){
                            echo '<button class="btn btn-success btn-sm" onclick="edit_data('. $val->trans_id.')">Edit</button>&nbsp;';
                            echo '<button class="btn btn-danger btn-sm" onclick="delete_data('. $val->trans_id.')">Delete</button>';
                        //}
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
                        <th>Description</th>
                        <th class="text-right">'. number_format($total_fee, 2).'</th>
                        <th>Mode</th>
                        <th class="text-right">'. number_format($total_all, 2).'</th>
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