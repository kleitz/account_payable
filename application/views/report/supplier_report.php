<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('supplierreport/go/'.$pagecode) ?>">
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
                        <a href="<?php echo site_url('supplierreport/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('supplierreport/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                        <a href="<?php echo site_url('supplierreport/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('supplierreport/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('supplierreport/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                        <a href="<?php echo site_url('supplierreport/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                    </div>
                </div>
            </div>
            <!--- --->
            <!--
            <div class="box-header with-border">
                <div class="input-group">
                    <div class="btn-group">
                        <a href="< echo site_url('supplierreport/outlet/'.$pagecode.'/1/') ?>" class="btn btn-default">QTR</a>
                        <a href="< echo site_url('supplierreport/outlet/'.$pagecode.'/2/') ?>" class="btn btn-default">QOI</a>
                        <a href="< echo site_url('supplierreport/outlet/'.$pagecode.'/3/') ?>" class="btn btn-default">QND</a>
                        <a href="< echo site_url('supplierreport/outlet/'.$pagecode.'/4/') ?>" class="btn btn-default">QUB</a>
                        <a href="< echo site_url('supplierreport/outlet/'.$pagecode.'/5/') ?>" class="btn btn-default">Mess</a>
                    </div>
                </div>
            </div> -->
        </form>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier</th>
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
                        echo '<td><a href="'. site_url('supplierreport/supplier_detail/'.$pagecode.'/'.$array[$i][sizeof($branch)+2]). '">'.$array[$i][0].'</a></td>';
                        $k = 0;
                        foreach ($branch as $b) {
                            echo '<td class="text-right">'.number_format($array[$i][$k+1], 2). '</td>';
                            $k++;
                        }
                        echo '<td class="text-right">'.number_format($array[$i][$k+1], 2). '</td>';
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
        <div class="box-footer">
            <div class="col-xs-4">
                <p><strong>Checked By</strong></p>
                <?php
                if ($checked_name == '0'){
                    if ($start_date == ''){
                        echo '<div><a href="#" class="btn btn-sm btn-default">Check</a></div>';
                    } else {
                        echo '<div><a href="'. site_url($url_module.'/action_checked/'.$start_date.'/'.$end_date.'/'.$report_type).'" class="btn btn-sm btn-warning">Check</a></div>';
                    }
                    
                } else {
                    echo '<div>'.$checked_name.'</div>';
                }
                ?>                            
            </div>
            <div class="col-xs-4">
                <p><strong>Approved By</strong></p>
                <?php
                if ($approved_name == '0'){
                    if ($start_date == ''){
                        echo '<div><a href="#" class="btn btn-sm btn-default">Approve</a></div>';
                    } else {
                        if ($checked_name == '0'){
                            echo '<div><a href="#" class="btn btn-sm btn-default">Approve</a></div>';
                        } else {
                            echo '<div><a href="'. site_url($url_module.'/action_approved/'.$report_id.'/'.$start_date.'/'.$end_date).'" class="btn btn-sm btn-primary">Approve</a></div>';
                        }
                    }
                } else {
                    echo '<div>'.$approved_name.'</div>';
                }
                ?>
            </div>
            <div class="col-xs-4">
                <p><strong>File upload</strong></p>
                <?php
                if ($report_id != 0){
                    echo '<div>';
                    echo '<a href="#" onclick="upload_file('.$report_id.')" class="btn btn-sm btn-default">Upload</a> ';
                    if ($report_file != ''){
                        echo '<a href="'. base_url().'assets/reportfile/'.$report_file.'" class="btn btn-sm btn-default" target="_blank">'. $report_file . '</a>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
            <div class="col-xs-12">&nbsp;</div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    
    function upload_file(id)
    {
        save_method = 'upload';
        $('#form_up')[0].reset(); // reset form on modals
        $('#modal_form_up').modal('show'); // show bootstrap modal
        $('[name="report_file_id"]').val(id);
        $('.modal-title-up').text('File Upload'); // Set Title to Bootstrap modal title
    }

</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_up" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_up', 'class'=>'form-horizontal');
        echo form_open_multipart($url_module.'/do_upload/', $attribut); 
        ?>
        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title-up">Upload Formzz</h4>
            </div>
            <div class="modal-body form">
                <div class="form-body">
                <input type="hidden" name="report_file_id" value="">
                
                <div class="form-group">
                    <div class="col-md-12">
                    <label class="control-label">File image</label>
                    <input type="file" name="userfile" class="filestyle" data-icon="false" data-buttonname="btn-default">
                    </div>
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <input class="btn btn-success" type="submit" value="Upload" />
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
            
        
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->