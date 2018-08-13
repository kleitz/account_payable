<!-- /.panel-heading -->

<div class="col-md-12">
    <div class="alert alert-info">
        <h4><i class="icon fa fa-check"></i> Yakin melakukan closing?</h4>
        <a href="<?php echo site_url($do_closing_link); ?>" class="btn btn-info btn-sm">Ya</a>
        <a href="<?php echo site_url($back_link); ?>" class="btn btn-info btn-sm">Tidak</a>
    </div>
    <div class="box box-default">
        <div class="box-header with-border">
            <h2>Daftar Transaksi Tabungan Periode (<?php echo $periode_title ?>)</h2>
        </div>
            <?php
            if ($savings_data->num_rows()!=0){
            ?>
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Transaksi</th>
                        <th>Nasabah</th>
                        <th>Saldo</th>
                        <th>Bunga</th>
                        <th>Pajak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $inc = 1;
                        $temp_cust = 0;
                        $bunga = 0;
                        $pajak = 0;
                        $total_bunga = 0;
                        $total_pajak = 0;
                        foreach ($savings_data->result() as $value) {
                            if ($value->savings_customer_id != $temp_cust){
                                $temp_cust = $value->savings_customer_id;
                                if ($value->balance > $value->balance_interest){
                                    $bunga = $value->balance * ($percentage[$value->interest_rates_id]/100);
                                }
                                $tr_class = ' class="success"';
                            } else {
                                if ($value->balance > $value->balance_interest){
                                    $bunga = $value->balance * ($percentage[$value->interest_rates_id]/100);
                                }
                                $pajak = 0;
                                $tr_class = '';
                            }
                            if ($bunga > $value->tax_resource){
                                $pajak = $bunga * ($percentage[$value->tax_rates_id]/100);
                                $bunga = $bunga - $pajak;
                            }
                            $total_bunga = $total_bunga + $bunga;
                            $total_pajak = $total_pajak + $pajak;
                          echo '<tr '.$tr_class.'>';
                          echo '<td>'.$inc.'</td>';
                          echo '<td>'.$value->savings_date.'</td>';
                          echo '<td>'.$value->savings_code.' - '.$value->full_name.'</td>';
                          echo '<td>'.number_format($value->balance, 2).'</td>';
                          echo '<td>'.number_format($bunga, 2).'</td>';
                          echo '<td>'.number_format($pajak,2).'</td>';
                          echo '</tr>';
                          $inc++;
                        }
                        echo '<tr>';
                        echo '<th colspan="4">Total</th>';
                        echo '<th>'. number_format($total_bunga, 2).'</th>';
                        echo '<th>'. number_format($total_pajak, 2).'</th>';
                        echo '</tr>';
                    ?>
                </tbody>
            </table>
            <?php
            }
            ?>
    </div>
    
    <div class="box box-default">
        <div class="box-header with-border">
            <h3>Daftar Nasabah yang tidak melakukan transaksi pada Periode (<?php echo $periode_title ?>)</h3>
        </div>
            <?php
            if ($savings_distinct->num_rows()!=0){
            ?>
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Transaksi</th>
                        <th>Nasabah</th>
                        <th>Saldo</th>
                        <th>Bunga</th>
                        <th>Pajak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $inc = 1;
                        $temp_balance = 0;
                        $temp_cust = 0;
                        $temp_date = '';
                        $bunga = 0;
                        $pajak = 0;
                        $total_bunga = 0;
                        $total_pajak = 0;
                        $tr_class = '';
                        foreach ($savings_distinct->result() as $value) {
                            if ($value->savings_customer_id != $temp_cust){
                                $temp_cust = $value->savings_customer_id;
                                $bunga = $value->balance * ($percentage[$value->interest_rates_id]/100);
                                $temp_date = $value->savings_date;
                                $tr_class = ' class="success"';
                            } else {
                                $pajak = 0;
                                $tr_class = '';
                                $bunga = $value->balance * ($percentage[$value->interest_rates_id]/100);
                                
                            }
                            if ($bunga > $value->tax_resource){
                                $pajak = $bunga * ($percentage[$value->tax_rates_id]/100);
                                $bunga = $bunga - $pajak;
                            }
                            $total_bunga = $total_bunga + $bunga;
                            $total_pajak = $total_pajak + $pajak;
                            
                            
                            
                          echo '<tr '.$tr_class.'>';
                          echo '<td>'.$inc.'</td>';
                          echo '<td>'.$value->savings_date.'</td>';
                          echo '<td>'.$value->savings_code.' - '.$value->full_name.'</td>';
                          echo '<td>'.number_format($value->balance, 2).'</td>';
                          echo '<td>'.number_format($bunga, 2).'</td>';
                          echo '<td>'.number_format($pajak,2).'</td>';
                          echo '</tr>';
                          $inc++;
                        }
                        echo '<tr>';
                        echo '<th colspan="4">'.$temp_balance.'</th>';
                        echo '<th>'. number_format($total_bunga, 2).'</th>';
                        echo '<th>'. number_format($total_pajak, 2).'</th>';
                        echo '</tr>';
                    ?>
                </tbody>
            </table>
            <?php
            }
            ?>
    </div>
    
</div>