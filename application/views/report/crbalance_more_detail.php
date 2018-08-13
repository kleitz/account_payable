<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <?php
                if ($detail->num_rows()!=0){
                    echo '<tbody>';
                    $no = 1;                    
                    $balance = 0;
                    foreach ($detail->result() as $value) {                        
                        if ($no == 1){
                            $balance = $value->debit;
                        } else {
                            if ($value->debit == 0){
                                $balance = $balance - $value->credit;
                            } else {
                                $balance = $balance + $value->debit;
                            }
                        }
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>';
                        echo '<td class="text-right">'.number_format($value->debit).'</td>';
                        echo '<td class="text-right">'.number_format($value->credit).'</td>';
                        echo '<td class="text-right">'.number_format($balance).'</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>