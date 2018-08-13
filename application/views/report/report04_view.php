<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('report/go/'.$pagecode) ?>">
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
            </form>
            
            <div class="form-group">
                <label style="color:#575757">&nbsp;</label>
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('report/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('report/go/'.$pagecode.'/2/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('report/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('report/go/'.$pagecode.'/4/') ?>" class="btn btn-default">Last Month</a>
                    </div>
                </div>  
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Account</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                    </tr>
                </thead>
                
            </table>
        </div>
        <div class="box-footer">
            <a href="#" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Export Excel</a>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>