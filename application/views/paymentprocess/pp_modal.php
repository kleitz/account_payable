<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data(type)
    {
        save_method = 'add';
        $('#btnSave').text('Generate'); 
        switch (type) {
            case 0:
                $('#form_gn')[0].reset();
                $('[name="pp_number"]').val("<?php echo $gn_number ?>");
                $('[name="pp_number_disabled"]').val("<?php echo $gn_number ?>");
                
                $('#modal_form_gn').modal('show');
                $('.modal-title_gn').text('Generate PP General'); 
                break;
            case 1:
                $('#form_sc')[0].reset();  
                $('[name="pp_number"]').val("<?php echo $sc_number ?>");
                $('[name="pp_number_disabled"]').val("<?php echo $sc_number ?>");
                $('#modal_form_sc').modal('show');
                $('.modal-title_sc').text('Generate PP Supplier'); 
                break;
            case 2:
                $('#form_ce')[0].reset();
                $('[name="pp_number"]').val("<?php echo $ce_number ?>");
                $('[name="pp_number_disabled"]').val("<?php echo $ce_number ?>");
                
                $('#modal_form_ce').modal('show');
                $('.modal-title_ce').text('Generate PP Cashier Expense'); 
                break;
            case 3:
                $('#form_os')[0].reset();
                $('[name="pp_number"]').val("<?php echo $os_number ?>");
                $('[name="pp_number_disabled"]').val("<?php echo $os_number ?>");
                
                $('#modal_form_os').modal('show');
                $('.modal-title_os').text('Generate PP Outstanding'); 
                break;
            case 4:
                $('#form_pr')[0].reset();
                $('[name="pp_number"]').val("<?php echo $pr_number ?>");
                $('[name="pp_number_disabled"]').val("<?php echo $pr_number ?>");
                
                $('#modal_form_pr').modal('show');
                $('.modal-title_pr').text('Generate PP Project'); 
                break;
        }
    }
    
    function edit_data(id)
    {
        save_method = 'update';
        $('#btnSave').text('Save'); 
        $('#form_gn')[0].reset();
        $('#form_sc')[0].reset();
        $('#form_ce')[0].reset();

        //Ajax Load data from ajax

        $.ajax({
            url: "<?php echo site_url('paymentprocess/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {                
                $('[name="pp_id"]').val(data.pp_id);
                $('[name="pp_number_disabled"]').val(data.pp_number);
                $('[name="pp_number"]').val(data.pp_number);
                $('[name="pp_date"]').val(data.pp_date);
                $('[name="payment_mode"]').val(data.payment_mode);
                
                switch (data.pp_type) {
                    case "0":
                        $('[name="pp_title"]').val(data.pp_title);
                        $('[name="branch_id"]').val(data.branch_id);
                        $('#modal_form_gn').modal('show');
                        $('.modal-title_gn').text('Edit Generate PP General'); 
                        break;
                    case "1":
                        $('[name="supplier_id"]').val(data.supplier_id);
                        $('#modal_form_sc').modal('show');
                        $('.modal-title_sc').text('Edit Generate PP Supplier'); 
                        break;
                    case "2":
                        $('[name="pp_title"]').val(data.pp_title);
                        $('[name="branch_id"]').val(data.branch_id);
                        $('#modal_form_ce').modal('show');
                        $('.modal-title_ce').text('Edit Generate PP Expense'); 
                        break;
                    case "3":
                        $('[name="pp_title"]').val(data.pp_title);
                        $('[name="branch_id"]').val(data.branch_id);
                        $('[name="third_party_id"]').val(data.third_party_id);
                        //$('[name="third_party_name"]').val(data.third_party_name);
                        $('#modal_form_os').modal('show');
                        $('.modal-title_os').text('Edit Generate PP Outstanding'); 
                        break;
                    case "4":
                        $('[name="pp_title"]').val(data.pp_title);
                        $('[name="branch_id"]').val(data.branch_id);
                        $('[name="third_party_id"]').val(data.third_party_id);
                        //$('[name="third_party_name"]').val(data.third_party_name);
                        $('#modal_form_pr').modal('show');
                        $('.modal-title_pr').text('Edit Generate PP Project'); 
                        break;
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function edit_pmode(id)
    {
        save_method = 'update';
        $('#btnSave').text('Save'); 
        $('#form_pm')[0].reset();
        //Ajax Load data from ajax

        $.ajax({
            url: "<?php echo site_url('paymentprocess/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {                
                $('[name="pp_id"]').val(data.pp_id);
                $('[name="payment_mode"]').val(data.payment_mode);
                $('[name="pp_date"]').val(data.pp_date);
                $('[name="branch_id"]').val(data.branch_id);
                
                $('#modal_form_pm').modal('show');
                $('.modal-title_pm').text('Edit Payment Process'); 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function upload_file(id)
    {
        save_method = 'upload';
        $('#form_up')[0].reset(); // reset form on modals
        $('#modal_form_up').modal('show'); // show bootstrap modal
        $('[name="up_pp_id"]').val(id);
        $('.modal-title-up').text('File Upload'); // Set Title to Bootstrap modal title
    }
    
    function info()
    {
        $('#modal_form_info').modal('show');
    }
    
    function save_gn()
    {
        var url;
        
        if (save_method == 'add')
        {
            url = "<?php echo site_url('paymentprocess/pp_add/0') ?>";
        }
        if (save_method == 'update')
        {
            url = "<?php echo site_url('paymentprocess/pp_update/0') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_gn').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form_gn').modal('hide');
                if (save_method == 'add')
                {
                    window.location = "<?php echo site_url('ppdetail/go/'.$pagecode.'/') ?>"+data.pp_id+"/0";
                }
                if (save_method == 'update')
                {
                    location.reload();// for reload a page
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }
    
    function save_sc()
    {
        var url;
        
        if (save_method == 'add')
        {
            url = "<?php echo site_url('paymentprocess/pp_add/1') ?>";
        }
        if (save_method == 'update')
        {
            url = "<?php echo site_url('paymentprocess/pp_sc_update/') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_sc').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                // 'ppdetail/go/' . $pagecode.'/'.$enc_id.'/1'
                $('#modal_form_sc').modal('hide');
                if (save_method == 'add')
                {
                    window.location = "<?php echo site_url('ppdetail/go/'.$pagecode.'/') ?>"+data.pp_id+"/1";
                }
                if (save_method == 'update')
                {
                    location.reload();// for reload a page
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }
    
    function save_ce()
    {
        var url;
        
        if (save_method == 'add')
        {
            url = "<?php echo site_url('paymentprocess/pp_add/2') ?>";
        }
        if (save_method == 'update')
        {
            url = "<?php echo site_url('paymentprocess/pp_update/2') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_ce').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form_ce').modal('hide');
                if (save_method == 'add')
                {
                    window.location = "<?php echo site_url('ppdetail/go/'.$pagecode.'/') ?>"+data.pp_id+"/2";
                }
                if (save_method == 'update')
                {
                    location.reload();// for reload a page
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }
    
    function save_os()
    {
        var url;
        
        if (save_method == 'add')
        {
            url = "<?php echo site_url('paymentprocess/pp_add/3') ?>";
        }
        if (save_method == 'update')
        {
            url = "<?php echo site_url('paymentprocess/pp_update/3') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_os').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form_os').modal('hide');
                if (save_method == 'add')
                {
                    window.location = "<?php echo site_url('ppdetail/go/'.$pagecode.'/') ?>"+data.pp_id+"/3";
                }
                if (save_method == 'update')
                {
                    location.reload();// for reload a page
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }
    
    function save_pr()
    {
        var url;
        
        if (save_method == 'add')
        {
            url = "<?php echo site_url('paymentprocess/pp_add/4') ?>";
        }
        if (save_method == 'update')
        {
            url = "<?php echo site_url('paymentprocess/pp_update/4') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_pr').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form_pr').modal('hide');
                if (save_method == 'add')
                {
                    window.location = "<?php echo site_url('ppdetail/go/'.$pagecode.'/') ?>"+data.pp_id+"/4";
                }
                if (save_method == 'update')
                {
                    location.reload();// for reload a page
                }
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
                url: "<?php echo site_url('paymentprocess/pp_delete') ?>/" + id,
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
    
    function save_pm()
    {
        var url;
        url = "<?php echo site_url('paymentprocess/update_payment_mode/') ?>";
        
        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_pm').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form_pm').modal('hide');
                location.reload();// for reload a page
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }
    
    function refresh_status(id)
    {
        var url;
        url = "<?php echo site_url('paymentprocess/get_refresh_status/') ?>"+id;
        
        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_pm').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                location.reload();// for reload a page
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error update!');
            }
        });
    }

</script>
<!-- PP General modal -->
<div class="modal fade" id="modal_form_gn" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_gn text-green">Payment Process Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_gn">
                    <div class="form-body">

                        <?php
                        echo $pp_id;
                        echo $pp_number_disabled;
                        echo $pp_number;
                        echo $pp_date;
                        echo $pp_title;
                        echo $branch_id;
                        echo $payment_mode;
                        echo $cash_request_id;
                        echo $job_order;
                        echo $description;
                        echo $unit;
                        echo $price;
                        ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_gn()" class="btn btn-primary">Generate</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- PP Supplier modal -->
<div class="modal fade" id="modal_form_sc" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_sc text-light-blue">Payment Process Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_sc">
                    <div class="form-body">
                        <!--------->
                        <?php
                        echo $pp_id;
                        echo $pp_number_disabled;
                        echo $pp_number;
                        echo $pp_date;
                        echo $supplier_id;
                        echo $payment_mode;
                        //echo $branch_id;
                        ?>
                        <!--------->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_sc()" class="btn btn-primary">Generate</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- PP Expense modal -->
<div class="modal fade" id="modal_form_ce" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_ce text-yellow">Payment Process Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_ce">
                    <div class="form-body">

                        <?php
                        echo $pp_id;
                        echo $pp_number_disabled;
                        echo $pp_number;
                        echo $pp_date;
                        echo $pp_title;
                        echo $branch_id;
                        echo $payment_mode;
                        echo $job_order;
                        echo $description;
                        echo $unit;
                        echo $price;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_ce()" class="btn btn-primary">Generate</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- PP Outstanding modal -->
<div class="modal fade" id="modal_form_os" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_os text-red">Payment Process Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_os">
                    <div class="form-body">

                        <?php
                        echo $pp_id;
                        echo $pp_number_disabled;
                        echo $pp_number;
                        echo $pp_date;
                        echo $pp_title;
                        echo $third_party_id;
                        //echo $third_party_name;
                        echo $branch_id;
                        echo $payment_mode;
                        echo $job_order;
                        echo $description;
                        echo $unit;
                        echo $price;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_os()" class="btn btn-primary">Generate</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- PP Project modal -->
<div class="modal fade" id="modal_form_pr" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_pr">Payment Process Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_pr">
                    <div class="form-body">

                        <?php
                        echo $pp_id;
                        echo $pp_number_disabled;
                        echo $pp_number;
                        echo $pp_date;
                        echo $vendor_id;
                        //echo $vendor_name;
                        echo $pp_title;
                        echo $branch_id;
                        echo $payment_mode;
                        echo $job_order;
                        echo $description;
                        echo $unit;
                        echo $price;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_pr()" class="btn btn-primary">Generate</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- End Bootstrap modal -->
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_up" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_up', 'class'=>'form-horizontal');
        echo form_open_multipart('paymentprocess/do_upload', $attribut); 
        ?>
        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title-up">Upload Form</h4>
            </div>
            <div class="modal-body form">
                <div class="form-body">
                <input type="hidden" name="up_pp_id" value="">
                
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
<div class="modal fade" id="modal_form_info" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title-info">Information</h4>
            </div>
            <div class="modal-body">
                <h4>Bagamana cara menambah data detail dari PP?</h4>
                <p>
                Secara default ketika "Generate PP" akan otomatis di arah kan ke halaman detail sesuai tipe PP,
                Atau bisa dengan klik tombol <i class="fa fa-list"></i>
                </p>
                <h4>Bagaimana cara edit PP?</h4>
                <p>Ketika buat PP baru, jangan tambahkan detail. Klik tombol back dan pilih tombol <i class="fa fa-edit"></i>.
                    Edit bisa dilakukan jika status PP masih dalam keadaan Draft.
                </p>
                <h4>Bagaimana cara hapus PP?</h4>
                <p>Klik tombol action <i class="fa fa-trash-o"></i>.
                    PP hanya bisa dihapus dalam keadaan status PP Draft atau To be Check.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End Bootstrap modal -->
<div class="modal fade" id="modal_form_pm" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_pm">Payment Process Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_pm">
                    <div class="form-body">

                        <?php
                        echo $pp_id;
                        echo $pp_date;
                        echo $branch_id;
                        echo $payment_mode;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_pm()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>