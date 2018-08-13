<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('paymentvoucher/go/'.$pagecode) ?>">
            <div class="box-header with-border">
                <div class="pull-left">
                    <div class="input-group">
                        <div class="btn-group">
                            <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                            <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                            <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                            <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                            <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                            <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                        </div>
                    </div> 
                </div>
                <div class="pull-right">
                    <a href="<?php echo site_url('paymentvoucher/paypp/'.$pagecode) ?>" class="btn btn-success">Pay PP</a>
                    <a href="<?php echo site_url('paymentvoucher/paycashrequest/'.$pagecode) ?>" class="btn btn-success">Pay Cashrequest</a>
                </div>

            </div>
            <div class="box-header with-border">
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="start_date" class="form-control datepicker" value="<?php echo $start_date ?>" placeholder="Date from">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="end_date" class="form-control datepicker" value="<?php echo $end_date ?>" placeholder="Date to">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            </div>
                
        </form>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable4" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Description</th>
                        <th>Total</th>
                        <th>Mode</th>
                        <th>Admin Fee</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                if ($payment_voucher_list->num_rows()!=0){
                    $no = 1;
                    $total_fee = 0;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($payment_voucher_list->result() as $val) {
                        $enc_id = $this->general_model->encrypt_value($val->pv_id);
                        $detail = 'paymentvoucher/detail/'.$pagecode.'/'.$enc_id;
                        $total_fee += $val->admin_fee;
                        $total_all += $val->total;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$this->general_model->get_string_date($val->pv_date).'</td>';
                        echo '<td>'.$val->pv_number.'</td>';
                        echo '<td>'.$val->description.'</td>';
                        echo '<td class="text-right">'.number_format($val->total, 2).'</td>';
                        echo '<td>'.$mode_opt[$val->payment_mode].'</td>';
                        echo '<td class="text-right">'.number_format($val->admin_fee, 2).'</td>';
                        echo '<td>';
                        if ($val->paid_by != 0){
                            echo '<span class="label label-success">Paid</span>';
                        } else {
                            echo '<span class="label label-warning">To be Paid</span>';
                        }
                        echo '</td>';
                        echo '<td  class="text-right">';
                        //if ($period_active == 1 && $period_month == $transdate){
                            echo '<a href="'. site_url($detail).'"><i class="fa fa-th-list"></i></a>';
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
                        <th class="text-right">'. number_format($total_all, 2).'</th>
                        <th>Mode</th>
                        <th class="text-right">'. number_format($total_fee, 2).'</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <address>
                <strong>Last Edit by : </strong><?= $username ?><br>
                <strong><?= $last_update ?> - ID : <?= $transid ?></strong>
            </address>
        </div>
    </div>
    <!-- /.box -->
</div>