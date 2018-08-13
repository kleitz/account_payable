<div class="col-md-5">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h4>Form Credit Invoice</h4>
        </div>
        <?php echo form_open('creditinvoice/purchaseorder_action/');  ?>
        <div class="box-body">
            
            <?php
            echo $credit_invoice_id;
            echo $credit_invoice_number;
            echo $credit_invoice_number_disabled;
            echo $supplier_id;
            ?>
           
           <div class="form-group">
               <div class="row">
                <div class="col-md-6">
                    <label for="po_number" style="color:#575757">PO Number <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" name="po_number" id="po_number" placeholder="0000" value="<?php echo $po_number ?>">
                </div>
                <div class="col-md-6">
                    <label for="po_date" style="color:#575757">PO Date <span style="color: red;">*</span></label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="po_date" class="form-control pull-right datepicker" value="<?php echo $po_date ?>">
                    </div>
                </div>
               </div>
            </div>
            <div class="form-group">
                <div class="row">
                <div class="col-md-6">
                    <label for="po_number" style="color:#575757">Supplier Invoice <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" name="invoice" id="invoice" placeholder="0000" value="<?php echo $invoice ?>">
                </div>
                <div class="col-md-6">
                    <label for="po_date" style="color:#575757">Invoice Date <span style="color: red;">*</span></label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="invoice_date" class="form-control pull-right datepicker" value="<?php echo $invoice_date ?>">
                    </div>
                </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                <div class="col-md-6">
                    <label for="po_number" style="color:#575757">Receive No.</label>
                    <input type="text" class="form-control" name="receive_no" id="receive_no" placeholder="0000" value="<?php echo $receive_no ?>">
                </div>
                <div class="col-md-6">
                    <label for="po_date" style="color:#575757">Receive Date</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="receive_date" class="form-control pull-right datepicker" value="<?php echo $receive_date ?>">
                    </div>
                </div>
                </div>
            </div>
           <?php
            echo $branch_id;
            echo $description;
            echo $amount;
            ?>
            
        </div>
        <div class="box-footer text-right">
            <input type="submit" class="btn btn-primary" name="submit" value="Save" />
            <a href="<?php echo site_url('creditinvoice/go/'.$pagecode) ?>" class="btn btn-default">Back</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<div class="col-md-7">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h4>List of Credit Invoice</h4>
        </div>
        <div class="box-body">
            <table class="table table-striped table-bordered datatable3" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Supplier</th>
                        <th>Amount</th>
                        <th class="text-right">Action</th>
                    </tr>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($list->result() as $value) {
                        $encryp_credit_invoice_id = $this->general_model->encrypt_value($value->credit_invoice_id);
                        $total_all = $total_all + $value->amount;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->credit_invoice_number.'</td>';
                        echo '<td>'.$value->supplier_name.'</td>';
                        echo '<td style="text-align:right">'.number_format($value->amount, 2).'</td>';
                        echo '<td class="text-right">';
                                
                                if ($value->po_status !=2){
                                    if ($action_edit_val){  
                                    echo '<a class="btn btn-default btn-sm" href="'. site_url('creditinvoice/goform/'.$pagecode.'/'.$value->credit_invoice_id) .'"><i class="fa fa-edit"></i></a>&nbsp;';
                                    }
                                    if ($action_delete_val){
                                    echo '<a class="btn btn-default btn-sm" href="#" onclick="delete_data('.$value->credit_invoice_id.')"><i class="fa fa-trash-o"></i></a>&nbsp;';
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
                        <th>ID</th>
                        <th>Supplier</th>
                        <th class="text-right">'. number_format($total_all, 2).'</th>
                        <th class="text-right">Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
                </thead>

               
            </table>
        </div>
    </div>
</div>