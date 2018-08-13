<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('paymentprocess/go/'.$pagecode) ?>">
            <div class="box-header with-border">
                <div class="row">
                <div class="col-md-6">
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('paymentprocess/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('paymentprocess/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                        <a href="<?php echo site_url('paymentprocess/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('paymentprocess/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('paymentprocess/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                        <a href="<?php echo site_url('paymentprocess/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                    </div>
                </div>  
                </div>
                <div class="col-md-6 text-right">
                <?php
                if ($action_add_val){
                    if ($is_cashier == 0){
                        echo '<a href="'. site_url('paymentprocess/ppgeneral/20191231214301/').'" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> PP GN</a>&nbsp;';
                        echo '<a href="#" class="btn btn-primary" onclick="add_data(1)"><i class="glyphicon glyphicon-plus"></i> PP SC</a>&nbsp;';
                        echo '<a href="#" class="btn btn-warning" onclick="add_data(2)"><i class="glyphicon glyphicon-plus"></i> PP CE</a>&nbsp;';
                        echo '<a href="#" class="btn btn-danger" onclick="add_data(3)"><i class="glyphicon glyphicon-plus"></i> PP OS</a>&nbsp;';
                        echo '<a href="#" class="btn btn-default" onclick="add_data(4)"><i class="glyphicon glyphicon-plus"></i> PP PR</a>&nbsp;';
                    } else {
                        echo '<a href="#" class="btn btn-warning" onclick="add_data(2)"><i class="glyphicon glyphicon-plus"></i> PP CE</a>&nbsp;';
                    }
                }
                ?>
                </div>
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
                    <label class="sr-only">Status</label>
                    <select class="form-control" name="field_status">
                        <?php
                            echo '<option value="0">All Status</option>';
                            foreach ($this->payment_process_model->pp_status_opt as $key => $value) {
                                
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                               
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only">Fields</label>
                    <select class="form-control" name="field_search">
                        <?php
                            foreach ($field_opt as $key => $value) {
                                if ($field_search == $key){
                                    echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                } else {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                                
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" >Filter</label>
                    <input type="text" class="form-control" name="keyword" value="<?= $keyword ?>" placeholder="Keyword">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            </div>
        </form>

        <div class="box-body table-responsive">
            <form name="formsupplier" method="post" action="<?php echo site_url('paymentprocess/do_approve/') ?>">
                <input type="hidden" name="shdate" value="<?php echo $start_date; ?>" />
                <input type="hidden" name="ehdate" value="<?php echo $end_date; ?>" />
            <table id="datatable4" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Total</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($list->result() as $val) {
                        $enc_id = $this->general_model->encrypt_value($val->pp_id);
                        $detail = 'ppdetail/go/' . $pagecode.'/'.$enc_id.'/'.$val->pp_type;
                        $total_all = $total_all + $val->total;
                        $ppnumber = $val->pp_number;
                        if ($val->pp_status == 4){ // status = closed
                            $pv_enc = $this->payment_process_model->get_pv_id($val->pv_number);
                            $ppnumber = '<a href="'. site_url('paymentvoucher/detail/20191231214302/').$pv_enc.'">'.$val->pp_number.'</a>';
                        }
                        $checkboxelm = '&nbsp;';
                        if ($val->pp_status == 2){
                            $checkboxelm = '<input type="checkbox" class="checkbox" name="chk_pp[]" value="'.$val->pp_id.'" />';
                        }
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';                        
                        echo '<td>'.$ppnumber.'</td>';
                        echo '<td>'.$this->general_model->get_string_date_ver2($val->pp_date).'</td>';
                        echo '<td>'.$val->pp_title.'</td>';
                        echo '<td class="text-right">'.number_format($val->total, 2).'</td>';
                        echo '<td>'.$val->branch_name.'</td>';
                        echo '<td><span class="label '.$this->payment_process_model->pp_status_style[$val->pp_status].'">'.$this->payment_process_model->pp_status_opt[$val->pp_status].'</span> '.$checkboxelm.'</td>';
                        echo '<td class="text-right">';
                            /*
                                if ($val->pp_status == 3){
                                    echo '<a class="btn btn-success btn-sm" href="#" onclick="edit_data('.$val->pp_id.')">Pay</a>&nbsp;';
                                }
                             */  
                                
                                echo '<a  href="'. site_url($detail).'"><i class="fa fa-th-list"></i></a>&nbsp;';
                                if ($val->pp_status == 3){
                                    if ($action_edit_val){
                                        echo '<a  href="#" onclick="edit_pmode('.$val->pp_id.')"><i class="fa fa-edit"></i></a>&nbsp;';
                                    }
                                }
                                
                                if ($val->pp_status <= 1){
                                    if ($action_edit_val){
                                        echo '<a  href="#" onclick="edit_data('.$val->pp_id.')"><i class="fa fa-edit"></i></a>&nbsp;';
                                    }
                                }
                                if ($val->pp_status < 4){
                                    if ($action_delete_val){
                                        echo '<a  href="#" onclick="delete_data('.$val->pp_id.')"><i class="fa fa-trash-o"></i></a>&nbsp;';
                                        echo '<a  href="#" onclick="refresh_status('.$val->pp_id.')"><i class="fa fa-refresh"></i></a>&nbsp;';
                                    }
                                }
                            
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Title</th>
                        <th class="text-right">'. number_format($total_all, 2).'</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
            <input class="btn btn-primary btn-sm" type="submit" name="submit" value="Do Approve" />
            </form>
        </div>
        <div class="box-footer">
            <address>
                <strong>Last Edit by : </strong><?= $username ?><br>
                <strong><?= $last_update ?> - ID : <?= $transid ?></strong>
            </address>
        </div>
    </div>
</div>