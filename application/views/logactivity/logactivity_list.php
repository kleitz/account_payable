<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('logactivity/go/'.$pagecode) ?>">
                <div class="form-group">

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="start_date" class="form-control datepicker" value="" placeholder="Date from">
                    </div>

                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="end_date" class="form-control datepicker" value="" placeholder="Date to">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                <div class="form-group">
                    <div class="input-group">
                        <div class="btn-group">
                            <a href="<?php echo site_url('logactivity/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                            <a href="<?php echo site_url('logactivity/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                            <a href="<?php echo site_url('logactivity/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                            <a href="<?php echo site_url('logactivity/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                            <a href="<?php echo site_url('logactivity/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                        </div>
                    </div>  
                </div>
                
            </form>
            
            
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    echo '<tbody>';
                    $i = 1;
                    foreach ($list->result() as $val) {
                        echo '<tr>';
                        echo '<td>'.$i.'</td>';
                        echo '<td>'.$val->log_date.'</td>';
                        echo '<td>'.$val->fullname.'</td>';
                        echo '<td>'.$val->module_name.'</td>';
                        echo '<td>'.$val->action_name.'</td>';
                        echo '<td><button name="detail" onclick="show_detail('.$val->log_activity_id.')" class="btn btn-default btn-sm">description</button></td>';
                        echo '</tr>';
                        $i++;
                    }
                    echo '</tbody>';
                }
                ?>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>