<?php
if ($info->num_rows()!=0){
    $row = $info->row();
?>
<div class="col-xs-12">
    <div class="box box-default">
        <div class="box-header with-border">
            <div class="pull-left">
                <table>
                    <tr>
                        <th style="padding-right: 21px">Outlet</th>
                        <td><?php echo $row->branch_name ?></td>
                    </tr>
                    <tr>
                        <th style="padding-right: 21px">PO Number</th>
                        <td><?php echo $row->po_number ?></td>
                    </tr>
                    <tr>
                        <th style="padding-right: 21px">PO Date</th>
                        <td><?php echo $this->general_model->get_string_date($row->po_date) ?></td>
                    </tr>
                </table>
            </div>
            <div class="pull-right" style="text-align: right">
                <table>
                    <tr>
                        <th style="padding-right: 21px">Supplier</th>
                        <td><?php echo $row->supplier_name ?></td>
                    </tr>
                    <tr>
                        <th style="padding-right: 21px">Invoice</th>
                        <td><?php echo $row->invoice ?></td>
                    </tr>
                    <tr>
                        <th style="padding-right: 21px">Invoice Date</th>
                        <td><?php echo $this->general_model->get_string_date($row->invoice_date) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="box-body">
            <p>
            <?php
            if ($row->po_status != 2){
                echo '<button class="btn btn-primary btn-sm" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New detail</button>';
            }

            $link = 'purchaseorder/go/' . $this->asik_model->category_transaction;
            $link .= $this->asik_model->trans_01 . '/';
            ?>
            <a href="<?php echo site_url($link) ?>" class="btn btn-default btn-sm">Back</a>
            </p>
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Qty</th>
                        <th>Code</th>
                        <th>Item</th>
                        <th>Unit Cost</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                $subtotal = 0;
                if ($detail->num_rows()!=0){
                    $no = 1;
                    echo '<tbody>';
                    foreach ($detail->result() as $value) {
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->quantity.'</td>';
                        echo '<td>'.$value->item_code.'</td>';
                        echo '<td>'.$value->item_name.'</td>';
                        echo '<td>'.number_format($value->price).'</td>';
                        echo '<td>'.$value->discount.'</td>';
                        echo '<td class="text-right">'.number_format($value->amount).'</td>';
                        echo '<td>';
                            if ($row->po_status != 2){
                            echo '<button class="btn btn-success btn-sm" onclick="edit_data('.$value->po_detail_id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                            echo '<button class="btn btn-danger btn-sm" onclick="delete_data('.$value->po_detail_id.')"><i class="fa fa-trash"></i></button>';
                            }
                        echo '</td>';
                        echo '</tr>';
                        $subtotal = $subtotal + $value->amount;
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>
                    <tr>
                        <th>#</th>
                        <th>Qty</th>
                        <th>Code</th>
                        <th>Item</th>
                        <th>Unit Cost</th>
                        <th>Discount</th>
                        <th class="text-right">'.number_format($subtotal).'</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>';
                }
                ?>
            </table>
        </div>
        <div class="box-footer">
            <p>&nbsp;</p>
        </div>
    </div>
</div>
    
<?php
}
?>