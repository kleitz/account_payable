<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('duesreport/go/'.$pagecode) ?>">
            <div class="box-header with-border">
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="start_date" class="form-control datepicker" value="<?php echo $start_date ?>" placeholder="Date from">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="end_date" class="form-control datepicker" value="<?php echo $end_date ?>" placeholder="Date to">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('duesreport/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('duesreport/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                        <a href="<?php echo site_url('duesreport/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('duesreport/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('duesreport/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                        <a href="<?php echo site_url('duesreport/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                    </div>
                </div>
            </div>
            
        </form>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Receipt to be Returned</th>
                        <?php
                        foreach ($branch as $b) {
                            echo '<th>'.$b.'</th>';
                        }
                        ?>
                        <th>Total</th>
                    </tr>
                </thead>
                <?php
                if (sizeof($array)!=0){
                    $no = 1;
                    echo '<tbody>';
                    for($i=0; $i<sizeof($array); $i++){
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>FROM '.strtoupper($array[$i][0]).' TO</td>';
                        $k = 0;
                        foreach ($branch as $b) {
                            echo '<td class="text-right">'.number_format($array[$i][$k+1]). '</td>';
                            $k++;
                        }
                        echo '<td class="text-right">'.number_format($array[$i][$k+1]). '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Receipt to be Returned</th>
                        <?php
                        foreach ($branch as $b) {
                            echo '<th class="text-right">'.$b.'</th>';
                        }
                        ?>
                        <th class="text-right">0</th>
                    </tr>
                </tfoot>
            </table>
            
        </div>
    </div>
</div>
