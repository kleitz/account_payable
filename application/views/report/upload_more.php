<div class="col-xs-12">
    <div class="box">
        <div class="box-body table-responsive">
            <?php echo form_open_multipart('report/upload_more/'.$start_date.'/'.$end_date.'/'.$account_id); ?>
            <input type="file" name="userfile">
            <div style="padding:5px 0px;"><input class="btn btn-success" type="submit" value="Upload" /></div>
            <?php echo form_close(); ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>file name</th>
                        <th>action</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    echo '<tbody>';
                    foreach ($list->result() as $value) {                     
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$value->file_name.'</td>';
                        echo '<td><a href="'. base_url().'assets/uploadmore/'.$value->file_name.'" target="_blank" class="btn btn-sm btn-success">download</a></td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>file name</th>
                        <th>action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>