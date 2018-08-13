<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('cashrequest/go/'.$pagecode) ?>">
            <div class="box-header with-border">
                <div class="pull-left">
                    <div class="input-group">
                        <div class="btn-group">
                            <a href="<?php echo site_url('cashrequest/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                            <a href="<?php echo site_url('cashrequest/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                            <a href="<?php echo site_url('cashrequest/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                            <a href="<?php echo site_url('cashrequest/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                            <a href="<?php echo site_url('cashrequest/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                            <a href="<?php echo site_url('cashrequest/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                        </div>
                    </div>
                </div>
                <div class="pull-right">
                <?php
                if ($action_add_val){
                    echo '<a href="#" class="btn btn-success" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Data</a>';
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
                <div class="form-group">
                    <label class="sr-only">Fields</label>
                    <select class="form-control" name="field_search">
                        <?php
                            foreach ($field_opt as $key => $value) {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" >Filter</label>
                    <input type="text" class="form-control" name="keyword" placeholder="Keyword">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                
            </div> 
        </form>
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
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                if ($list->num_rows()!=0){
                    $no = 1;    
                    $total_amount = 0;
                    
                    echo '<tbody>';
                    foreach ($list->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->cash_request_id);
                        $total_amount = $total_amount + $value->amount;
                        $cash_request_number = '';
                        if (isset($arr_pv[$value->cash_request_id])){
                            $pv_enc = $this->general_model->encrypt_value($arr_pv[$value->cash_request_id]);
                            $cash_request_number = '<a href="'. site_url('paymentvoucher/detail/20191231214302/'.$pv_enc).'" target="_blank">'.$value->cash_request_number.'</a>';
                        } else {
                            $cash_request_number = $value->cash_request_number;
                        }
                        
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$this->general_model->get_string_date($value->cash_request_date).'</td>';
                        echo '<td>'.$cash_request_number.'</td>';
                        echo '<td>'.$value->employee_name.'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td class="text-right">'.number_format($value->amount, 2).'</td>';
                        echo '<td>'.$this->cashrequest_model->cashrequest_status[$value->cash_request_status].'</td>';
                        echo '<td>';
                            echo '<a href="'. site_url('cashrequest/cdetail/'.$pagecode.'/'.$enc_id).'" class="btn btn-default btn-sm">Detail</a>&nbsp;';
                            echo '<a href="#" onclick="add_remark('.$value->cash_request_id.')" class="btn btn-primary btn-sm">Remark</a>';
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
                        <th>Status</th>
                        <th>Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
        <div class="box-footer">
            <address>
                <strong>Last Edit by : </strong><?= $username ?><br>
                <strong><?= $last_update ?> - ID : <?= $transid ?></strong>
            </address>
        </div>
    </div>
</div>
