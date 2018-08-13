<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('cashreqnote/go/'.$pagecode) ?>">
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
                        <a href="<?php echo site_url('cashreqnote/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('cashreqnote/go/'.$pagecode.'/2/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('cashreqnote/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('cashreqnote/go/'.$pagecode.'/4/') ?>" class="btn btn-default">Last Month</a>
                    </div>
                </div>  
            </div>
            <?php
            if ($action_add_val){
                echo '<button class="btn btn-primary btn-sm" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Data</button>&nbsp;';
            }
            ?>
        </div>
        <div class="box-body table-responsive">
            <table id="datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Employee</th>
                        <th>Branch</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>CR</th>
                        <th>PR</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                if ($list->num_rows()!=0){
                    $no = 1;    
                    $total_amount = 0;
                    $status = array('<span class="label label-danger">Draft</span>', '<span class="label label-primary">Checked</span>', '<span class="label label-success">Paid</span>');
                    echo '<tbody>';
                    foreach ($list->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->note_id);
                        $strdownload = '[0]';
                        $strpr = '[0]';
                        if (strlen($value->file_name)>0){
                            $strdownload = '<a href="'. base_url().'assets/files/'.$value->file_name.'" target="_blank"><i class="fa fa-download"></i></a>';
                        }
                        if (strlen($value->file_pr)>0){
                            $strpr = '<a href="'. base_url().'assets/files/'.$value->file_pr.'" target="_blank"><i class="fa fa-download"></i></a>';
                        }
                        $total_amount = $total_amount + $value->amount;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->note_date.'</td>';
                        echo '<td>'.$value->note_number.'</td>';
                        echo '<td>'.$value->full_name.'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td>'.$value->description.'</td>';
                        echo '<td class="text-right">'.number_format($value->amount, 2).'</td>';
                        echo '<td>'.$strdownload.'</td>';
                        echo '<td>'.$strpr.'</td>';
                        echo '<td>'.$status[$value->note_status].'</td>';
                        echo '<td>';
                            if ($action_upload || $action_edit_val || $action_delete_val){
                            echo '<div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">';
                                    if ($action_upload){
                                        echo '<li><a href="#" onclick="upload_file('.$value->note_id.')"><i class="fa fa-upload"></i> Upload CR</a></li>';
                                        echo '<li><a href="#" onclick="upload_pr('.$value->note_id.')"><i class="fa fa-upload"></i> Upload PR</a></li>';
                                    }
                                    if ($value->note_status == 0){
                                        if ($action_edit_val){
                                        echo '<li><a href="#" onclick="edit_data('.$value->note_id.')"><i class="fa fa-edit"></i> Edit</a></li>';
                                        }
                                        if ($action_delete_val){
                                        echo '<li><a href="#" onclick="delete_data('.$value->note_id.')"><i class="fa fa-trash-o"></i> Delete</a></li>';
                                        }
                                    }
                                    if ($value->checked_by == 0 && $action_checked){
                                        echo '<li><a href="'. site_url('cashreqnote/checked/'.$enc_id).'"><i class="fa fa-check"></i> Check</a></li>';
                                    }
                                    if ($value->checked_by != 0 && $value->note_status == 1){
                                        echo '<li><a href="#" onclick="pay('.$value->note_id.')"><i class="fa fa-calculator"></i> Pay</a></li>';
                                    }
                                    if ($value->note_status == 2){
                                        echo '<li><a href="#" onclick="cash_return('.$value->note_id.')"><i class="fa fa-money"></i> Cash Return</a></li>';
                                    }
                                    
                            echo '</ul>
                                </div>';
                            }
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>';
                    echo '<th>#</th><th>Date</th><th>Number</th>';
                    echo '<th>Employee</th><th>Branch</th><th>Description</th>';
                    echo '<th class="text-right">'. number_format($total_amount, 2).'</th>';
                    echo '<th>File</th><th>Status</th><th>Action</th>';
                    echo '</tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
        <div class="box-footer">
            <?php
            if ($action_excel_val){
                echo '<a href="#" class="btn btn-success btn-sm">Excel</a>&nbsp;';
            }
            if ($action_pdf_val){
                echo '<a href="#" class="btn btn-danger btn-sm">PDF</a>';
            }
            ?>
        </div>
    </div>
</div>
