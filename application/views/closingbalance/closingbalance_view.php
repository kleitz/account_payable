<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('openingbalance/go/'.$pagecode) ?>">
            <div class="box-header with-border">
                <div class="pull-left">
                    <div class="input-group">
                        <div class="btn-group">
                            <a href="<?php echo site_url('closingbalance/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                            <a href="<?php echo site_url('closingbalance/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                            <a href="<?php echo site_url('closingbalance/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                            <a href="<?php echo site_url('closingbalance/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                            <a href="<?php echo site_url('closingbalance/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                        </div>
                    </div>  
                </div>
                <div class="pull-right">
                <?php
                if ($period_active == 1){
                ?>
                <a href="#" class="btn btn-success" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Data</a>
                <?php
                } else {
                ?>
                <a href="#" class="btn btn-default disabled"><i class="glyphicon glyphicon-plus"></i> New Data</a>
                <?php
                }
                ?>
                </div>
            </div>
            <div class="box-header with-border">
                <div class="form-group">

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="start_date" class="form-control datepicker" value="<?= $start_date ?>" placeholder="Date from">
                    </div>

                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="end_date" class="form-control datepicker" value="<?= $end_date ?>" placeholder="Date to">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                
                

            </div>
        </form>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Outlet</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    $total_amount = 0;
                    echo '<tbody>';
                    foreach ($list->result() as $val) {
                        $transdate = substr($val->closing_balance_date, 0, 7);
                        $total_amount = $total_amount + $val->amount;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->closing_balance_number.'</td>';
                        echo '<td>'.$this->general_model->get_string_date($val->closing_balance_date).'</td>';
                        echo '<td>'.$val->description.'</td>';
                        echo '<td class="text-right">'.number_format($val->amount, 2).'</td>';
                        echo '<td>'.$val->branch_name.'</td>';
                        echo '<td class="text-right">';
                        if ($period_active == 1 && $period_month == $transdate){
                            echo '<button class="btn btn-default btn-sm" onclick="edit_data('. $val->trans_id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                            echo '<button class="btn btn-default btn-sm" onclick="delete_data('. $val->trans_id.')"><i class="fa fa-trash-o"></i></button>';
                        }
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
                        <th>Description</th>
                        <th class="text-right">'. number_format($total_amount, 2).'</th>
                        <th>Outlet</th>    
                        <th class="text-right">Action</th>
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