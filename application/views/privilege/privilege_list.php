<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            List of Privilege Group
        </div>
        <div class="box-header with-border">
            <button class="btn btn-primary" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Group</button>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Privilege Group</th>
                        <th class="text-right">Detail</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    echo '<tbody>';
                    $no = 1;
                    foreach ($list->result() as $val) {
                        $encrypt_id = $this->general_model->encrypt_value($val->priv_group_id);
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->priv_group_name.'</td>';
                        echo '<td class="text-right">';
                        echo '<a class="btn btn-default btn-sm" href="'. site_url('privilege/godetail/'.$pagecode.'/'.$encrypt_id).'"><i class="fa fa-list"></i></a>&nbsp;';
                        echo '<a class="btn btn-default btn-sm" onclick="edit_data('.$val->priv_group_id.')"><i class="fa fa-edit"></i></a>&nbsp;';
                        echo '<a class="btn btn-default btn-sm" onclick="delete_data('.$val->priv_group_id.')"><i class="fa fa-trash"></i></a>';
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
