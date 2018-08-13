<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('creditinvoice/go/'.$pagecode) ?>">
        <div class="box-header with-border">
            <div class="pull-left">
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('creditinvoice/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('creditinvoice/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                        <a href="<?php echo site_url('creditinvoice/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('creditinvoice/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('creditinvoice/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                        <a href="<?php echo site_url('creditinvoice/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to today</a>
                    </div>
                </div>  
            </div>
            <div class="pull-right">
            <?php
            if ($action_add_val){
                echo '<a href="'. site_url('creditinvoice/goform/'.$pagecode).'" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> New Data</a>';
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
                    <label class="sr-only">Date Type</label>
                    <select class="form-control" name="date_search">
                        <?php
                            foreach ($type_date as $key => $value) {
                                if ($key == $date_search){
                                    echo '<option value="'.$key.'" selected="selected">'.$value.'</option>';
                                } else {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                            }
                        ?>
                    </select>
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
            
            <table id="datatable4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Supplier</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <!--<th>Receive No.</th>-->
                        <th>Receive Date</th>
                        <th>Amount</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($list->result() as $value) {
                        $credit_invoice_number = '';
                        if (isset($arr_pp[$value->credit_invoice_id])){
                            $ppcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                            $enc_pp_id = $this->general_model->encrypt_value($arr_pp[$value->credit_invoice_id]);
                            $detail = 'ppdetail/go/' . $ppcode.'/'.$enc_pp_id.'/1';
                            $credit_invoice_number = '<a target="_blank"  href="'. site_url($detail).'" target="_blank">'.$value->credit_invoice_number.'</a>';
                        } else {
                            $credit_invoice_number = $value->credit_invoice_number;
                        }
                        
                        
                        
                        
                        
                        $encryp_credit_invoice_id = $this->general_model->encrypt_value($value->credit_invoice_id);

                        $total_all = $total_all + $value->amount;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$credit_invoice_number.'</td>';
                        echo '<td>'.$value->supplier_name.'</td>';
                        echo '<td>'.$value->po_number.'</td>';
                        echo '<td>'.$value->po_date.'</td>';
                        echo '<td>'.$value->invoice.'</td>';
                        echo '<td>'.$value->invoice_date.'</td>';
                        //echo '<td>'.$value->receive_no.'</td>';
                        echo '<td>'.$value->receive_date.'</td>';
                        echo '<td style="text-align:right">'.number_format($value->amount, 2).'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td>'.$this->credit_invoice_model->status_style[$value->po_status].'</td>';
                        echo '<td>';
                                
                                if ($value->po_status !=2){
                                    if ($action_edit_val){  
                                    echo '<a class="btn btn-default btn-sm" href="'. site_url('creditinvoice/goform/'.$pagecode.'/'.$value->credit_invoice_id).'"><i class="fa fa-edit"></i></a>&nbsp;';
                                    }
                                    if ($action_delete_val){
                                    echo '<a class="btn btn-default btn-sm" href="#" onclick="delete_data('.$value->credit_invoice_id.')"><i class="fa fa-trash-o"></i></a>&nbsp;';
                                    }
                                }
                                echo '<a class="btn btn-default btn-sm" href="'. site_url('creditinvoice/godetail/'.$pagecode.'/'.$value->credit_invoice_id).'"><i class="fa fa-list"></i></a>';
                  
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Supplier</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th>Receive Date</th>
                        <th class="text-right">'. number_format($total_all, 2).'</th>
                        <th>Outlet</th>
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