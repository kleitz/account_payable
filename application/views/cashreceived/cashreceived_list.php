<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('cashreceived/go/'.$pagecode) ?>">
            <div class="box-header with-border">
                
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('cashreceived/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('cashreceived/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                        <a href="<?php echo site_url('cashreceived/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('cashreceived/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('cashreceived/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                        <a href="<?php echo site_url('cashreceived/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                    </div>
                </div>
                <?php
                if ($action_add_val){
                    echo '<a href="#" class="btn btn-success" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Data</a>';
                }
                ?>
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
                <!--
                <div class="form-group">
                    <label class="sr-only">Fields</label>
                    <select class="form-control" name="field_search">
                        <
                            foreach ($field_opt as $key => $value) {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        >
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" >Filter</label>
                    <input type="text" class="form-control" name="keyword" placeholder="Keyword">
                </div>-->
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            </div> 
        </form>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Account From</th>
                        <th>Account To</th>
                        <th>Amount</th>
                        <th>Outlet</th>
                        <th>Remark</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>

                <?php
                if ($list->num_rows()!=0){
                    $no = 1;    
                    $total_amount = 0;
                    
                    echo '<tbody>';
                    foreach ($list->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->cash_receive_id);
                        $total_amount += $value->amount;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->cash_receive_number.'</td>';
                        echo '<td>'.$this->general_model->get_string_date($value->cash_receive_date).'</td>';
                        echo '<td>'.$account_opt[$value->account_from].'</td>';
                        echo '<td>'.$account_opt[$value->account_to].'</td>';
                        echo '<td class="text-right">'.number_format($value->amount, 2).'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td>'.$value->remark.'</td>';
                        echo '<td class="text-right">';
                            
                            if ($action_edit_val){
                                echo '<a class="btn btn-default btn-sm" href="#" onclick="edit_data('.$value->cash_receive_id.')"><i class="fa fa-edit"></i></a>&nbsp;';
                            }


                            if ($action_delete_val){
                                echo '<a class="btn btn-default btn-sm" href="#" onclick="delete_data('.$value->cash_receive_id.')"><i class="fa fa-trash-o"></i></a>&nbsp;';
                            }
                            
                            echo '<a href="'. site_url('cashreceived/detail/'.$pagecode.'/'.$enc_id).'" class="btn btn-default btn-sm"><i class="fa fa-th-list"></i></a>';
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
                        <th>Account From</th>
                        <th>Account To</th>
                        <th class="text-right">'.number_format($total_amount, 2).'</th>
                        <th>Outlet</th>
                        <th>Remark</th>
                        <th class="text-right">Action</th>
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
