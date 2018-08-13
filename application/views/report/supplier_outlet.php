<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('supplierreport/outlet/'.$pagecode.'/'.$branch_id) ?>">
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
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('supplierreport/outlet/'.$pagecode.'/'.$branch_id.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('supplierreport/outlet/'.$pagecode.'/'.$branch_id.'/2/') ?>" class="btn btn-default">Yesterday</a>
                        <a href="<?php echo site_url('supplierreport/outlet/'.$pagecode.'/'.$branch_id.'/3/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('supplierreport/outlet/'.$pagecode.'/'.$branch_id.'/4/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('supplierreport/outlet/'.$pagecode.'/'.$branch_id.'/5/') ?>" class="btn btn-default">Last Month</a>
                    </div>
                </div>
            </div> 
        </form>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>PP. No.</th>
                        <th>PP. Status</th>
                    </tr>
                </thead>
                <?php
                if ($report_data->num_rows()!=0){
                    echo '<tbody>';
                    $no = 1;
                    /* status diambil dari Payment_process_model */
                    $pp_status_opt = array('Draft', 'To Be Check', 'To Be Approve', 'Approved', 'Closed');
                    $pp_status_style = array('label-danger', 'label-warning', 'label-info', 'label-primary', 'label-success');
                    foreach ($report_data->result() as $value) {
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->supplier_name.'</td>';
                        echo '<td>'.$this->general_model->get_string_date_ver2($value->invoice_date).'</td>';
                        echo '<td class="text-right">'.number_format($value->amount).'</td>';
                        echo '<td>'.$value->pp_number.'</td>';
                        echo '<td><span class="label '.$pp_status_style[$value->pp_status].'">'.$pp_status_opt[$value->pp_status].'</span></td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>PP. No.</th>
                        <th>PP. Status</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!--
    <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Supplier</th>
                <th>Opening</th>
                <th>5/Oct/17</th>
                <th>6/Oct/17</th>
                <th>7/Oct/17</th>
                <th>8/Oct/17</th>
                <th>9/Oct/17</th>
                <th>10/Oct/17</th>
                <th>Total</th>
                <th>Closing</th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <th>#</th>
                <th>Supplier</th>
                <th>Opening</th>
                <th>5/Oct/17</th>
                <th>6/Oct/17</th>
                <th>7/Oct/17</th>
                <th>8/Oct/17</th>
                <th>9/Oct/17</th>
                <th>10/Oct/17</th>
                <th>Total</th>
                <th>Closing</th>
            </tr>
        </tfoot>
    </table>
-->