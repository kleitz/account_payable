<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h4>List of Payment Process</h4>
        </div>
        <div class="box-header with-border">
            
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('paymentvoucher/paypp/'.$pagecode) ?>">
                <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode) ?>" class="btn btn-default">Back</a>
                <div class="form-group">
                    <select class="form-control" name="pptype_select">
                        <option value="5">All</option>
                        <option value="0">GN</option>
                        <option value="1">SC</option>
                        <option value="2">CE</option>
                        <option value="3">OT</option>
                        <option value="4">PR</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            </form>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-striped table-bordered datatable3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Total</th>
                        <th>Method</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <?php
                if ($payment_process_list->num_rows()!=0){
                    $no = 1;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($payment_process_list->result() as $val) {
                        $enc_id = $this->general_model->encrypt_value($val->pp_id);
                        $detail = 'ppdetail/go/' . $pagecode.'/'.$enc_id.'/'.$val->pp_type;
                        $total_all = $total_all + $val->total;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->pp_number.'</td>';
                        echo '<td>'.$val->pp_date.'</td>';
                        echo '<td>'.$val->pp_title.'</td>';
                        echo '<td class="text-right">'.number_format($val->total, 2).'</td>';
                        echo '<td>'.$mode_opt[$val->payment_mode].'</td>';
                        echo '<td class="text-right">';
                            echo '<a class="btn btn-success btn-sm" href="'. site_url('paymentvoucher/pay/'.$pagecode.'/'.$enc_id.'/0/1').'">Pay</a>';
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Title</th>
                        <th class="text-right">'. number_format($total_all, 2).'</th>
                        <th>Method</th>
                        <th class="text-right">Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
    </div>
</div>