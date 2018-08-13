<!-- /.panel-heading -->
<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3><?php echo $period_name ?></h3>
            <small>Untuk menambah data, silahkan klik tanggal pada kalendar di bawah.</small>
        </div>
        <div class="box-body">
            <?php

            $a = 0;
            $b = 0;
            ?>
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sun</th>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                </tr>
                <?php
                for($i = 0; $i<$r; $i++){
                    echo '<tr>';
                    for($j=0; $j<7; $j++){
                        if (sizeof($arr_tt)!=0 && $tgl[$i][$j] == $arr_tt[$a]){
                            echo '<td><a class="btn btn-warning btn-xs" href="#" onclick="add_data('.$tgl[$i][$j].')">'.$tgl[$i][$j].'</a></td>';
                            if ($a < (sizeof($arr_tt)-1)){
                                $a++;
                            }
                        } else if (sizeof($arr_tp)!=0 && $tgl[$i][$j] == $arr_tp[$b]){
                            echo '<td><a class="btn btn-success btn-xs" href="#" onclick="add_data('.$tgl[$i][$j].')">'.$tgl[$i][$j].'</a></td>';
                            if ($b < (sizeof($arr_tp)-1)){
                                $b++;
                            }
                            
                        } else {
                            echo '<td><a href="#" onclick="add_data('.$tgl[$i][$j].')">'.$tgl[$i][$j].'</a></td>';
                        }
                        
                    }
                    echo '</tr>';
                }
                ?>
                
            </table>
        </div>
        <div class="box-footer">
            <h4>Keterangan:</h4>
            <span class="label label-warning">Tanggal Tukar</span>
            <span class="label label-success">Tanggal Pembayaran</span>
            <p>&nbsp;</p>
            <a href="<?php echo site_url($back_link) ?>" class="btn btn-default btn-sm">Back</a>
            
        </div>
    </div>
</div>
<!-- /.panel-body -->