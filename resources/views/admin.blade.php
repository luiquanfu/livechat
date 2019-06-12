<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Live Chat Admin</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- <link rel="shortcut icon" href="assets/admin/logo.ico" type="image/x-icon"> -->
<link rel="stylesheet" href="assets/admin/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="assets/admin/font-awesome/font-awesome.min.css">
<link rel="stylesheet" href="assets/admin/select2/select2.min.css">
<link rel="stylesheet" href="assets/admin/cropperjs/cropper.min.css">
<link rel="stylesheet" href="assets/admin/spectrum/spectrum.css">
<link rel="stylesheet" href="assets/admin/adminlte/adminlte.min.css">
<link rel="stylesheet" href="assets/admin/adminlte/skin-blue.min.css">
<link rel="stylesheet" href="assets/admin/admin.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div id="app"></div>
<script>
var app_url = "{{ $app_url }}";
var nodejs_url = "{{ $nodejs_url }}";
var api_token = "{{ $api_token }}";
var device_id = "{{ $device_id }}";
</script>
<script src="assets/admin/jquery/jquery.min.js"></script>
<script src="assets/admin/bootstrap/bootstrap.min.js"></script>
<script src="assets/admin/select2/select2.full.min.js"></script>
<script src="assets/admin/cropperjs/cropper.min.js"></script>
<script src="assets/admin/spectrum/spectrum.js"></script>
<script src="assets/admin/adminlte/adminlte.min.js"></script>
<script src="assets/socket.io.slim.js"></script>
<script src="assets/admin/admin.js"></script>
</body>
</html>