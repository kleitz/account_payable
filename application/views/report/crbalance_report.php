<div class="col-xs-12">
    <div class="box">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
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
                        echo '<td><a href="'. site_url('crbalance/detail/20191341214311/'.$value->employee_id).'">'.$value->full_name.'</a></td>';
                        echo '<td class="text-right">'.number_format($value->debit_total).'</td>';
                        echo '<td class="text-right">'.number_format($value->credit_total).'</td>';
                        echo '<td class="text-right">'.number_format($value->debit_total - $value->credit_total).'</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Debit (Total)</th>
                        <th>Credit (Total)</th>
                        <th>Balance</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

