<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Welcome to AP System</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
<!-- Favicons -->
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url(); ?>assets/ico/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url(); ?>assets/ico/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url(); ?>assets/ico/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url(); ?>assets/ico/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url(); ?>assets/ico/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url(); ?>assets/ico/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url(); ?>assets/ico/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url(); ?>assets/ico/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url(); ?>assets/ico/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo base_url(); ?>assets/ico/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/ico/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url(); ?>assets/ico/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/ico/favicon-16x16.png">
<link rel="manifest" href="<?php echo base_url(); ?>assets/ico/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>
<body class="hold-transition" style="background-color: #d6ebf7;">
<div class="login-box">

  <!-- /.login-logo -->
  <div class="login-box-body" style="border-radius: 5px">
    <div class="login-logo">
      <img src="<?php echo base_url(); ?>assets/dist/img/queens-head.png" alt="logo">
    </div>
    
    <?php if (validation_errors() != ""){ ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Warning!</h4>
        <?php echo validation_errors(); ?>
    </div>
    <?php } ?>
    <?php if ($valid_message == 1){ ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Warning!</h4>
        Username atau Password salah
    </div>
    <?php } ?>
    <?php echo form_open('auth/validasi'); ?>
      <div class="form-group has-feedback">
        <input type="text" name="username" class="form-control" autocomplete="off" placeholder="Username">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
            <div class="radio">
                <label>
                    <input type="radio" name="ap_lang" id="optionsRadios1" value="1" checked>
                    <strong>English</strong>
                </label>
                <label>
                    <input type="radio" name="ap_lang" id="optionsRadios1" value="2">
                    <strong>Indonesia</strong>
                </label>
            </div>
        </div>
        <!-- /.col -->
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <button type="submit" class="btn btn-primary">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    <?php echo form_close(); ?>
  </div>
  <!-- /.login-box-body -->
  <footer align="center" style="margin-top:21px;">
      <div><img src="<?php echo base_url(); ?>assets/dist/img/mchensolutions.png" alt="logo"></div>
      <h3 style="color:#005384;">Account Payable</h3>
      <strong style="color:#005384;">Copyright &copy; 2017 
          <a href="https://www.mchensolutions.com">Mchensolutions</a>.
      </strong> 
      All rights reserved.
  </footer>
</div>
<!-- /.login-box -->
<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>

</body>
</html>