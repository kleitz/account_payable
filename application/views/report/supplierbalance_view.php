<div class="col-xs-12">
    <div class="box">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier</th>
                        <th>Debit (Total)</th>
                        <th>Credit (Total)</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    echo '<tbody>';
                    foreach ($list->result() as $value) {                     
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td><a href="'. site_url('supplierbalance/detail/20191341214312/'.$value->supplier_id).'">'.$value->supplier_name.'</a></td>';
                        echo '<td class="text-right">'.number_format($value->sum_debit).'</td>';
                        echo '<td class="text-right">'.number_format($value->sum_credit).'</td>';
                        echo '<td class="text-right">'.number_format($value->sum_debit - $value->sum_credit).'</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Supplier</th>
                        <th>Debit (Total)</th>
                        <th>Credit (Total)</th>
                        <th>Balance</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

