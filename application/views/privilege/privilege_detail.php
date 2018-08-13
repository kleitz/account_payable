<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <a class="btn btn-primary" href="<?php echo site_url('privilege/priv_add/'.$pagecode.'/'.$encrypt_id) ?>"><i class="glyphicon glyphicon-plus"></i> Add Privilege</a>
            <a href="<?php echo site_url('privilege/go/'.$pagecode) ?>" class="btn btn-default">Back</a>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-striped table-bordered datatable3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Module</th>
                        <th>Feature</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    echo '<tbody>';
                    $no = 1;
                    foreach ($list->result() as $val) {
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->module_name.'</td>';
                        echo '<td>'.$val->action_name.'</td>';
                        echo '<td>';
                        echo '<a class="btn btn-danger btn-sm" onclick="delete_data('.$val->privilege_id.')"><i class="fa fa-close"></i></a>';
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    function delete_data(id)
    {
        if (confirm('Are you sure delete this data?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('privilege/delete_detail') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    window.location = "<?php echo site_url('privilege/godetail/'.$pagecode.'/'.$encrypt_id) ?>";
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });

        }
    }
</script>