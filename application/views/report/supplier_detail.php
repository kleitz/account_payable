<div class="col-xs-12">
    <div class="box box-primary">
            <div class="box-header with-border">
                <h3><?php echo $supplier_name; ?></h3>
            </div>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Branch (Outlet)</th>
                        <th>Amount</th>
                        <th>PP. No.</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <?php
                $total = 0;
                if ($list->num_rows()!=0){
                    $no = 1;                    
                    echo '<tbody>';
                    foreach ($list->result() as $value) {
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$this->general_model->get_string_date($value->invoice_date).'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td class="text-right">'.number_format($value->amount).'</td>';
                        echo '<td>'.$value->pp_number.'</td>';
                        echo '<td>'.$this->supplier_report_model->status_style[$value->po_status].'</td>';
                        echo '</tr>';
                        $total = $total + $value->amount;
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Branch (Outlet)</th>
                        <th class="text-right"><?php echo number_format($total); ?></th>
                        <th>PP. No.</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
            </table>
            
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('supplierreport/go/'.$pagecode); ?>" class="btn btn-default">Back</a>
        </div>
    </div>
</div>
