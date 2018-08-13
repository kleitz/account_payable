<div class="col-xs-12">
    <div class="box box-default">
        

        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Total</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($list->result() as $val) {
                        $enc_id = $this->general_model->encrypt_value($val->pp_id);
                        $detail = 'ppdetail/go/' . $pagecode.'/'.$enc_id.'/'.$val->pp_type;
                        $total_all = $total_all + $val->total;
                        $ppnumber = $val->pp_number;
                        if ($val->pp_status == 4){ // status = closed
                            $pv_enc = $this->payment_process_model->get_pv_id($val->pv_number);
                            $ppnumber = '<a href="'. site_url('paymentvoucher/detail/20191231214302/').$pv_enc.'">'.$val->pp_number.'</a>';
                        }
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$ppnumber.'</td>';
                        echo '<td>'.$this->general_model->get_string_date_ver2($val->pp_date).'</td>';
                        echo '<td>'.$val->pp_title.'</td>';
                        echo '<td class="text-right">'.number_format($val->total, 2).'</td>';
                        echo '<td>'.$val->branch_name.'</td>';
                        echo '<td><span class="label '.$this->payment_process_model->pp_status_style[$val->pp_status].'">'.$this->payment_process_model->pp_status_opt[$val->pp_status].'</span></td>';
                        echo '<td class="text-right">';
                            /*
                                if ($val->pp_status == 3){
                                    echo '<a class="btn btn-success btn-sm" href="#" onclick="edit_data('.$val->pp_id.')">Pay</a>&nbsp;';
                                }
                             */  
                                echo '<a  href="'. site_url($detail).'"><i class="fa fa-th-list"></i></a>';
                                
                            
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
                        <th>Outlet</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
        
    </div>
</div>