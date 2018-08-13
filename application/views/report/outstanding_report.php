<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('outstandingreport/go/'.$pagecode) ?>">
            
            <div class="box-header with-border">
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('outstandingreport/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('outstandingreport/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                        <a href="<?php echo site_url('outstandingreport/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('outstandingreport/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('outstandingreport/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                        <a href="<?php echo site_url('outstandingreport/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                    </div>
                </div>
                <!--<button class="btn btn-success">Go to Summary Report Approved</button>-->
                <?php
                    // for cash request
                    if ($link_code == '20191341214304'){
                        echo '<a href="'. site_url('crbalance/go/20191341214311').'" class="btn btn-success">Cash Request Summary</a>';
                    }
                ?>
            </div> 
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
                <div class="form-group">
                    <div class="input-group">
                        <?php $arrstatus = array('Outstanding', 'Close', 'All Status'); ?>
                        <select name="outstanding_status" class="form-control">
                            <?php
                            foreach ($arrstatus as $key => $value) {
                                if ($outstanding_status == $key){
                                    echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                } else {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                                
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                    // for outstanding outlet
                    if ($link_code == '20191341214305'){
                        echo '<div class="form-group">
                            <div class="input-group">';
                                echo '<select name="branch_filter" class="form-control">';
                                    
                                    foreach ($outlet as $key => $value) {
                                        if ($branch_filter == $key){
                                            echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                        } else {
                                            echo '<option value="'.$key.'">'.$value.'</option>';
                                        }

                                    }
                                    
                                echo '</select>
                            </div>
                        </div>';
                    }
                    // for o/s third party
                    if ($link_code == '20191341214306'){
                        echo '<div class="form-group">
                            <div class="input-group">';
                                echo '<select name="thirdparty_filter" class="form-control">';
                                    
                                    foreach ($thirdparty as $key => $value) {
                                        if ($thirdparty_filter == $key){
                                            echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                        } else {
                                            echo '<option value="'.$key.'">'.$value.'</option>';
                                        }

                                    }
                                    
                                echo '</select>
                            </div>
                        </div>';
                    }
                ?>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            </div>
        </form>
        <div class="box-body table-responsive">
            <table id="datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Description</th>
                        <?php
                        foreach ($branch as $b) {
                            echo '<th>'.$b.'</th>';
                        }
                        ?>
                        <th>Total</th>
                    </tr>
                </thead>
                 <?php
                if (sizeof($array_tbl)!=0){
                    $no = 1;
                    echo '<tbody style="font-size:12px;">';
                    for($i=0; $i<=sizeof($array_tbl); $i++){
                        if (isset($array_tbl[$i][0])){
                            echo '<tr>';
                            echo '<td>'.$no.'</td>';
                            echo '<td>'.$array_tbl[$i][0].'</td>';
                            echo '<td>'.$array_tbl[$i][1].'</td>';
                            $k = 2;
                            foreach ($branch as $b) {
                                echo '<td class="text-right">'.number_format($array_tbl[$i][$k]). '</td>';
                                $k++;
                            }
                            echo '<td class="text-right">'.number_format($array_tbl[$i][$k]). '</td>';
                            echo '</tr>';
                        }                        
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Description</th>
                        <?php
                        foreach ($branch as $b) {
                            echo '<th class="text-right">'.$b.'</th>';
                        }
                        ?>
                        <th class="text-right">Total</th>
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
                        echo '<div><a href="'. site_url('outstandingreport/action_checked/'.$start_date.'/'.$end_date.'/'.$report_type.'/'.$link_code).'" class="btn btn-sm btn-warning">Check</a></div>';
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
                            echo '<div><a href="'. site_url('outstandingreport/action_approved/'.$report_id.'/'.$start_date.'/'.$end_date.'/'.$link_code).'" class="btn btn-sm btn-primary">Approve</a></div>';
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
        echo form_open_multipart('outstandingreport/do_upload/'.$link_code, $attribut); 
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