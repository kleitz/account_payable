<?php if ($os_cash_request != 0){ ?>
<div class="col-md-12">
    <div class="alert alert-danger" role="alert">
        <h4>
            <?= $os_cash_request ?> Cash Request is Outstanding & <?= $total_remark ?> no remark! 
            <a href="<?= site_url('cashrequest/os/') ?>">Show</a>
        </h4>
    </div>
</div>
<?php } ?>
<div class="col-md-4">
    <div class="box box-success">
        <div class="box-header with-border">
            <h4>Bank Balance</h4>
        </div>
        <div class="box-header with-border">
            <select class="form-control" onchange="show_balance(this.value)">
                <?php
                foreach ($arr_period as $value) {
                    echo $value;
                }
                ?>
            </select>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="table_balance">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Outlet</th>
                            <th class="text-right">Balance</th>
                            <th>C</th>
                            <th>A</th>
                            <th>U</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total1 = 0;

                    if ($bank_balance->num_rows()!=0){
                        $start_month = date('Y-m') . '-01';
                        $end_month = date("Y-m-t", strtotime($start_month));
                        $paramdate = $start_month.'/'.$end_month;
                        foreach ($bank_balance->result() as $value) {
                            $total = $value->total_debit - $value->total_credit;
                            $link = '<a href="'. site_url('report/ledger_detail/'.$value->account_id).'/0/'.$paramdate.'">'.number_format($total, 2).'</a>';
                            echo '<tr>';
                            echo '<th>'.$value->branch_name.'</th>';
                            echo '<td class="text-right">'.$link.'</td>';
                            
                            echo '<td>'.$report_bank_balance[$value->account_id][0].'</td>
                            <td>'.$report_bank_balance[$value->account_id][1].'</td>
                            <td>'.$report_bank_balance[$value->account_id][2].'</td>';
                            echo '</tr>';
                            $total1 = $total1 + $total;
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-right"><?php echo number_format($total1, 2) ?></th>
                            <th>C</th>
                            <th>A</th>
                            <th>U</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <!-------------->
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4>O/S Cash Request</h4>
        </div>
        <div class="box-header with-border">
            <select class="form-control" onchange="show_outstanding(this.value, 1)">
                <?php
                foreach ($arr_period as $value) {
                    echo $value;
                }
                ?>
            </select>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="table_outstandingcr">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Outlet</th>
                            <th class="text-right">Today</th>
                            <th class="text-right">This Month</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php                
                    $total1 = 0;
                    $total2 = 0;

                    if (sizeof($outstanding_cr)!=0){
                        $exurl = $this->asik_model->category_report.$this->asik_model->report_04;
                        $datenow = date('Y-m-d').'/'.date('Y-m-d');
                        $start_month = date('Y-m') . '-01';
                        $end_month = date("Y-m-t", strtotime($start_month));
                        $paramdate = $start_month.'/'.$end_month;
                        for($i=0; $i<sizeof($outstanding_cr); $i++){
                            $aweek = '0';
                            $amonth = '0';
                            if ($outstanding_cr[$i][1] != 0){
                                $aweek = '<a href="'. site_url('outstandingreport/go/'.$exurl.'/0/'.$datenow).'">'. number_format($outstanding_cr[$i][1], 2) .'</a>';
                            }
                            if ($outstanding_cr[$i][2] != 0){
                                $amonth = '<a href="'. site_url('outstandingreport/go/'.$exurl.'/0/'.$paramdate).'">'. number_format($outstanding_cr[$i][2], 2) .'</a>';
                            }
                            echo '<tr>';
                            echo '<th>'.$outstanding_cr[$i][0].'</th>';
                            echo '<td class="text-right">'.$aweek.'</td>';
                            echo '<td class="text-right">'.$amonth.'</td>';
                            echo '</tr>';
                            $total1 += $outstanding_cr[$i][1];
                            $total2 += $outstanding_cr[$i][2];
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-right"><?php echo number_format($total1, 2) ?></th>
                            <th class="text-right"><?php echo number_format($total2, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="col-md-4">
    <div class="box box-danger">
        <div class="box-header with-border">
            <h4>Expenses by Outlet</h4>
        </div>
        <div class="box-header with-border">
            <select class="form-control" onchange="show_expense(this.value)">
                <?php
                foreach ($arr_period as $value) {
                    echo $value;
                }
                ?>
            </select>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="table_expense">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Outlet</th>
                            <th class="text-right">Today</th>
                            <th class="text-right">This Month</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php                
                    $total1 = 0;
                    $total2 = 0;

                    if (sizeof($expense)!=0){
                        $exurl = $this->asik_model->category_report.$this->asik_model->report_03;
                        $datenow = date('Y-m-d').'/'.date('Y-m-d');
                        $start_month = date('Y-m') . '-01';
                        $end_month = date("Y-m-t", strtotime($start_month));
                        $paramdate = $start_month.'/'.$end_month;
                        for($i=0; $i<sizeof($expense); $i++){
                            $aweek = '0';
                            $amonth = '0';
                            if ($expense[$i][1] != 0){
                                $aweek = '<a href="'. site_url('expensereport/go/'.$exurl.'/0/'.$datenow).'">'. number_format($expense[$i][1], 2) .'</a>';
                            }
                            if ($expense[$i][2] != 0){
                                $amonth = '<a href="'. site_url('expensereport/go/'.$exurl.'/0/'.$paramdate).'">'. number_format($expense[$i][2], 2) .'</a>';
                            }
                            echo '<tr>';
                            echo '<th>'.$expense[$i][0].'</th>';
                            echo '<td class="text-right">'.$aweek.'</td>';
                            echo '<td class="text-right">'.$amonth.'</td>';
                            echo '</tr>';
                            $total1 += $expense[$i][1];
                            $total2 += $expense[$i][2];
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-right"><?php echo number_format($total1, 2) ?></th>
                            <th class="text-right"><?php echo number_format($total2, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
                
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Checked</th>
                            <th>Approved</th>
                            <th>Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $report_expense[0] ?></td>
                            <td><?= $report_expense[1] ?></td>
                            <td><?= $report_expense[2] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-------------->
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4>O/S Outlet</h4>
        </div>
        <div class="box-header with-border">
            <select class="form-control" onchange="show_outstanding(this.value, 2)">
                <?php
                foreach ($arr_period as $value) {
                    echo $value;
                }
                ?>
            </select>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="table_outstandingot">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Outlet</th>
                            <th class="text-right">Today</th>
                            <th class="text-right">This Month</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php                
                    $total1 = 0;
                    $total2 = 0;

                    if (sizeof($outstanding_ot)!=0){
                        $exurl = $this->asik_model->category_report.$this->asik_model->report_05;
                        $datenow = date('Y-m-d').'/'.date('Y-m-d');
                        $start_month = date('Y-m') . '-01';
                        $end_month = date("Y-m-t", strtotime($start_month));
                        $paramdate = $start_month.'/'.$end_month;
                        for($i=0; $i<sizeof($outstanding_ot); $i++){
                            $aweek = '0';
                            $amonth = '0';
                            if ($outstanding_ot[$i][1] != 0){
                                $aweek = '<a href="'. site_url('outstandingreport/go/'.$exurl.'/0/'.$datenow).'">'. number_format($outstanding_ot[$i][1], 2) .'</a>';
                            }
                            if ($outstanding_ot[$i][2] != 0){
                                $amonth = '<a href="'. site_url('outstandingreport/go/'.$exurl.'/0/'.$paramdate).'">'. number_format($outstanding_ot[$i][2], 2) .'</a>';
                            }
                            echo '<tr>';
                            echo '<th>'.$outstanding_ot[$i][0].'</th>';
                            echo '<td class="text-right">'.$aweek.'</td>';
                            echo '<td class="text-right">'.$amonth.'</td>';
                            echo '</tr>';
                            $total1 += $outstanding_ot[$i][1];
                            $total2 += $outstanding_ot[$i][2];
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-right"><?php echo number_format($total1, 2) ?></th>
                            <th class="text-right"><?php echo number_format($total2, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4>Payment Process Status</h4>
        </div>
        <div class="box-header with-border">
            <select class="form-control" onchange="show_ppstatus(this.value)">
                <?php
                foreach ($arr_period as $value) {
                    echo $value;
                }
                ?>
            </select>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="table_ppstatus">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-right">Today</th>
                            <th class="text-right">Up Today</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $strcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                    if (sizeof($ppstatus)!=0){
                        $today = '';
                        $balance = '';
                        $datenow = date('Y-m-d').'/'.date('Y-m-d');
                        $start_month = date('Y-m') . '-01';
                        $end_month = date("Y-m-t", strtotime($start_month));
                        $paramdate = '2018-01-01'.'/'.$end_month;
                        for($i=0; $i<(sizeof($ppstatus)-1); $i++){
                            if ($ppstatus[$i][1] != 0){
                                $today = '<a href="'. site_url('paymentprocess/dash/'.$strcode.'/'.$i.'/'.$datenow).'">'.$ppstatus[$i][1].'</a>';
                            } else {
                                $today = $ppstatus[$i][1];
                            }
                            
                            if ($ppstatus[$i][2] != 0){
                                $balance = '<a href="'. site_url('paymentprocess/dash/'.$strcode.'/'.$i.'/'.$paramdate).'">'.$ppstatus[$i][2].'</a>';
                            } else {
                                $balance = $ppstatus[$i][2];
                            }
                            echo '<tr>';
                            echo '<td>'.$ppstatus[$i][0].'</td>';
                            echo '<td class="text-right">'.$today.'</td>';
                            echo '<td class="text-right">'.$balance.'</td>';
                            echo '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-------------->
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4>O/S Third Party</h4>
        </div>
        <div class="box-header with-border">
            <select class="form-control" onchange="show_outstanding(this.value, 3)">
                <?php
                foreach ($arr_period as $value) {
                    echo $value;
                }
                ?>
            </select>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="table_outstandingth">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Outlet</th>
                            <th class="text-right">Today</th>
                            <th class="text-right">This Month</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php                
                    $total1 = 0;
                    $total2 = 0;

                    if (sizeof($outstanding_th)!=0){
                        $exurl = $this->asik_model->category_report.$this->asik_model->report_06;
                        $datenow = date('Y-m-d').'/'.date('Y-m-d');
                        $start_month = date('Y-m') . '-01';
                        $end_month = date("Y-m-t", strtotime($start_month));
                        $paramdate = $start_month.'/'.$end_month;
                        for($i=0; $i<sizeof($outstanding_th); $i++){
                            $aweek = '0';
                            $amonth = '0';
                            if ($outstanding_th[$i][1] != 0){
                                $aweek = '<a href="'. site_url('outstandingreport/go/'.$exurl.'/0/'.$datenow).'">'. number_format($outstanding_th[$i][1], 2) .'</a>';
                            }
                            if ($outstanding_th[$i][2] != 0){
                                $amonth = '<a href="'. site_url('outstandingreport/go/'.$exurl.'/0/'.$paramdate).'">'. number_format($outstanding_th[$i][2], 2) .'</a>';
                            }
                            echo '<tr>';
                            echo '<th>'.$outstanding_th[$i][0].'</th>';
                            echo '<td class="text-right">'.$aweek.'</td>';
                            echo '<td class="text-right">'.$amonth.'</td>';
                            echo '</tr>';
                            $total1 += $outstanding_th[$i][1];
                            $total2 += $outstanding_th[$i][2];
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-right"><?php echo number_format($total1, 2) ?></th>
                            <th class="text-right"><?php echo number_format($total2, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">


    function show_balance(month)
    {

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('dashboard/show_bank_balance/') ?>"+month,
            type: "GET",
            dataType: "html",
            success: function (data)
            {                        
                $('.table_balance').html(data); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function show_expense(month)
    {

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('dashboard/show_expense/') ?>"+month,
            type: "GET",
            dataType: "html",
            success: function (data)
            {                        
                $('.table_expense').html(data); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function show_ppstatus(month)
    {

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('dashboard/show_ppstatus/') ?>"+month,
            type: "GET",
            dataType: "html",
            success: function (data)
            {                        
                $('.table_ppstatus').html(data); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function show_outstanding(month, tipe)
    {

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('dashboard/show_outstanding/') ?>"+month+"/"+tipe,
            type: "GET",
            dataType: "html",
            success: function (data)
            {             
                switch (tipe) {
                    case 1:
                        $('.table_outstandingcr').html(data);
                        break;
                    case 2:
                        $('.table_outstandingot').html(data);
                        break;
                    case 3:
                        $('.table_outstandingth').html(data);
                        break;
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    
</script>