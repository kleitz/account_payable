<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('purchaseorder/go/'.$pagecode) ?>">
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
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('purchaseorder/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('purchaseorder/go/'.$pagecode.'/2/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('purchaseorder/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('purchaseorder/go/'.$pagecode.'/4/') ?>" class="btn btn-default">Last Month</a>
                    </div>
                </div>  
                <?php
                if ($action_add_val){
                    echo '<button class="btn btn-primary" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Data</button>';
                }
                ?>
            </form>
            
        </div>
        <div class="box-body table-responsive">
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th>Receive No.</th>
                        <th>Receive Date</th>
                        <th>Outlet</th>
                        <th>File</th>
                        <th>Total</th>
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
                        $encryp_po_id = $this->general_model->encrypt_value($value->po_id);
                        $status = array('<span class="label label-danger">Draft</span>', '<span class="label label-warning">In Progress</span>', '<span class="label label-success">Closed</span>');
                        $strdownload = '[0]';
                        if (strlen($value->file_name)>0){
                            $strdownload = '<a href="'. base_url().'assets/files/'.$value->file_name.'" target="_blank"><i class="fa fa-download"></i></a>';
                        }
                        $total_all = $total_all + $value->total;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->supplier_name.'</td>';
                        echo '<td>'.$value->po_number.'</td>';
                        echo '<td>'.$value->po_date.'</td>';
                        echo '<td>'.$value->invoice.'</td>';
                        echo '<td>'.$value->invoice_date.'</td>';
                        echo '<td>'.$value->receive_no.'</td>';
                        echo '<td>'.$value->receive_date.'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        
                        echo '<td>'.$strdownload.'</td>';
                        echo '<td style="text-align:right">'.number_format($value->total, 2).'</td>';
                        echo '<td>'.$status[$value->po_status].'</td>';
                        echo '<td>';
        
                                echo '<a class="btn btn-default btn-sm" href="'. site_url('podetail/go/'.$mdl.'/'.$encryp_po_id).'"><i class="fa fa-th-list"></i></a>&nbsp;';
                                echo '<a class="btn btn-default btn-sm" href="#" onclick="upload_file('.$value->po_id.')"><i class="fa fa-upload"></i></a>&nbsp;';
                                if ($value->po_status !=2){
                                    if ($action_edit_val){  
                                    echo '<a class="btn btn-default btn-sm" href="#" onclick="edit_data('.$value->po_id.')"><i class="fa fa-edit"></i></a>&nbsp;';
                                    }
                                    if ($action_delete_val){
                                    echo '<a class="btn btn-default btn-sm" href="#" onclick="delete_data('.$value->po_id.')"><i class="fa fa-trash-o"></i></a>';
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
                        <th>Supplier</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th>Receive No.</th>
                        <th>Receive Date</th>
                        <th>Outlet</th>
                        <th>File</th>
                        <th class="text-right">'. number_format($total_all, 2).'</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>';
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
