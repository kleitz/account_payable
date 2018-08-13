<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('#username').show();
        $('#password').show();
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Add User'); // Set Title to Bootstrap modal title
    }
    
    function upload_file(id)
    {
        save_method = 'upload';
        $('#form_up')[0].reset(); // reset form on modals
        $('#modal_form_up').modal('show'); // show bootstrap modal
        $('[name="uid"]').val(id);
        $('.modal-title-up').text('Photo Upload'); // Set Title to Bootstrap modal title
    }
    
    function change_password(id)
    {
        save_method = 'upload';
        $('#form_password')[0].reset(); // reset form on modals
        $('#modal_password_form').modal('show'); // show bootstrap modal
        $('[name="puid"]').val(id);
        $('.modal-title-up').text('Change Password'); // Set Title to Bootstrap modal title
    }
    
    function detail_data(id)
    {
        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('users/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                var gender = '';
                if (data.gender == 0){
                    gender = 'Female';
                } else {
                    gender = 'Male';
                }
                
                $('#modal_detail').modal('show'); // show bootstrap modal when complete loaded
                $('#cfullname').text(data.fullname);
                $('#cgender').text(gender);
                $('#caddress').text(data.address);
                $('#cphone').text(data.phone);
                $('#cemail').text(data.email);
                $('#cpriv_group_id').text(data.priv_group_id);
                $('#cuser_status').text(data.user_status);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function edit_data(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('users/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('#username').hide();
                $('#password').hide();
                $('[name="user_id"]').val(data.user_id);
                $('[name="fullname"]').val(data.fullname);
                $('[name="gender"]').val(data.gender);
                $('[name="address"]').val(data.address);
                $('[name="phone"]').val(data.phone);
                $('[name="email"]').val(data.email);
                $('[name="priv_group_id"]').val(data.priv_group_id);
                $('[name="user_status"]').val(data.user_status);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit User'); // Set title to Bootstrap modal title

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
            url = "<?php echo site_url('users/user_add') ?>";
        }
        else
        {
            url = "<?php echo site_url('users/user_update') ?>";
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

    function delete_data(id)
    {
        if (confirm('Are you sure delete this data?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('users/user_delete') ?>/" + id,
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
                <h4 class="modal-title">User Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form">
                    <div class="form-body">
                        <div class="form-group" id="username">
                            <div class="row">
                            <div class="col-md-12">
                                <label style="color:#575757">Username <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="username" placeholder="" value="">
                            </div>
                            </div>
                        </div>
                        <div class="form-group" id="password">
                            <div class="row">
                            <div class="col-md-12">
                                <label style="color:#575757">Password <span style="color: red;">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="" value="">
                            </div>
                            </div>
                        </div>
                        <?php
                        echo $user_id;
                        echo $fullname;
                        echo $gender;
                        echo $address;
                        echo $email;
                        echo $phone;
                        echo $priv_group_id;
                        echo $user_status;
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
<!-- End Bootstrap modal -->

<div class="modal fade" id="modal_form_up" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_up', 'class'=>'form-horizontal');
        echo form_open_multipart('users/do_upload', $attribut); 
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
                <input type="hidden" name="uid" value="">
                
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
</div>

<div class="modal fade" id="modal_detail" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">User Detail</h4>
            </div>
            <div class="modal-body form">
                <table class="table">
                    <tr>
                        <td>Full name</td>
                        <td><span id="cfullname"></span></td>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <td><span id="cgender"></span></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><span id="caddress"></span></td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td><span id="cphone"></span></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><span id="cemail"></span></td>
                    </tr>
                    <tr>
                        <td>Level</td>
                        <td><span id="cpriv_group_id"></span></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><span id="cuser_status"></span></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal_password_form" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_password', 'class'=>'form-horizontal');
        echo form_open('users/update_password', $attribut); 
        ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Change Password Form</h4>
            </div>
            <div class="modal-body form">
                <input type="hidden" name="puid" value="" />
                <div class="form-body">
                    <div class="form-group" id="password">
                        <div class="col-md-12">
                            <label style="color:#575757">Old Password <span style="color: red;">*</span></label>
                            <input type="password" class="form-control" name="old_password" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group" id="password">
                        <div class="col-md-12">
                            <label style="color:#575757">New Password <span style="color: red;">*</span></label>
                            <input type="password" class="form-control" name="new_password" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group" id="password">
                        <div class="col-md-12">
                            <label style="color:#575757">Confirm Password <span style="color: red;">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="" value="">
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <input class="btn btn-success" type="submit" value="Update" />
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div>