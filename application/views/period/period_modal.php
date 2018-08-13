<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Add Period'); // Set Title to Bootstrap modal title
    }
    
    function detail_data(id)
    {
        save_method = 'detail';
        $('#form_detail')[0].reset(); // reset form on modals
        $('[name="period_id"]').val(id);
        $('#modal_form_detail').modal('show'); // show bootstrap modal
    }

    function edit_data(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('period/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {                
                $('[name="period_id"]').val(data.period_id);
                $('[name="year"]').val(data.year);
                $('[name="sorting"]').val(data.sorting);
                $('[name="period"]').val(data.period);
                $('[name="start_date"]').val(data.start_date);
                $('[name="end_date"]').val(data.end_date);
                $('[name="closed"]').val(data.closed);
                $('[name="active_status"]').val(data.active_status);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Period'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    function save()
    {
        var url;
        if (save_method == 'add')
        {
            url = "<?php echo site_url('period/period_add') ?>";
        }
        else
        {
            url = "<?php echo site_url('period/period_update') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form').modal('hide');
                location.reload();// for reload a page
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }
    
    function save_detail()
    {
        var url;
        if (save_method == 'add')
        {
            url = "<?php echo site_url('period/period_add') ?>";
        }
        else
        {
            url = "<?php echo site_url('period/period_update') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_detail').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form').modal('hide');
                location.reload();// for reload a page
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }

    function delete_data(id)
    {
        if (confirm('Are you sure delete this data?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('period/period_delete') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });

        }
    }
    
    function active(id)
    {
        if (confirm('Are you sure to activate this period?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('period/set_active') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });

        }
    }
    
    function non_active(id)
    {
        if (confirm('Are you sure to non-active this period?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('period/set_non_active') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });

        }
    }
    
    function closing(id)
    {
        if (confirm('Are you sure to closing this period?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('period/closing_confirm') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });

        }
    }

</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Period Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form">
                    <div class="form-body">

                        <?php
                        echo $period_id;
                        echo $year;
                        echo $sorting;
                        echo $period;
                        echo $start_date;
                        echo $end_date;
                        echo $closed;
                        echo $active_status;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal_form_detail" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_detail">Calendar Supplier</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_detail" class="form-horizontal">
                    <div class="form-body">

                        <?php
                        echo $period_id;
                        echo $period_tt;
                        echo $period_tp;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_detail()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- End Bootstrap modal -->