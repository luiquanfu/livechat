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
<link rel="stylesheet" href="assets/admin/adminlte/adminlte.min.css">
<link rel="stylesheet" href="assets/admin/adminlte/skin-blue.min.css">
<link rel="stylesheet" href="assets/admin/admin.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div id="app"></div>
<script>
var app_url = "{{ $app_url }}";
var api_token = "{{ $api_token }}";
var device_id = "{{ $device_id }}";
var app_data = {};

var hours = [];
hours.push('12:00 AM');
hours.push('12:30 AM');
hours.push('1:00 AM');
hours.push('1:30 AM');
hours.push('2:00 AM');
hours.push('2:30 AM');
hours.push('3:00 AM');
hours.push('3:30 AM');
hours.push('4:00 AM');
hours.push('4:30 AM');
hours.push('5:00 AM');
hours.push('5:30 AM');
hours.push('6:00 AM');
hours.push('6:30 AM');
hours.push('7:00 AM');
hours.push('7:30 AM');
hours.push('8:00 AM');
hours.push('8:30 AM');
hours.push('9:00 AM');
hours.push('9:30 AM');
hours.push('10:00 AM');
hours.push('10:30 AM');
hours.push('11:00 AM');
hours.push('11:30 AM');
hours.push('12:00 PM');
hours.push('12:30 PM');
hours.push('1:00 PM');
hours.push('1:30 PM');
hours.push('2:00 PM');
hours.push('2:30 PM');
hours.push('3:00 PM');
hours.push('3:30 PM');
hours.push('4:00 PM');
hours.push('4:30 PM');
hours.push('5:00 PM');
hours.push('5:30 PM');
hours.push('6:00 PM');
hours.push('6:30 PM');
hours.push('7:00 PM');
hours.push('7:30 PM');
hours.push('8:00 PM');
hours.push('8:30 PM');
hours.push('9:00 PM');
hours.push('9:30 PM');
hours.push('10:00 PM');
hours.push('10:30 PM');
hours.push('11:00 PM');
hours.push('11:30 PM');

var days = [];
days.push('Day');
days.push('Monday');
days.push('Tuesday');
days.push('Wednesday');
days.push('Thursday');
days.push('Friday');
days.push('Saturday');
days.push('Sunday');
days.push('Public Holiday');
days.push('Public Holiday Eve');
</script>
<script src="assets/admin/jquery/jquery.min.js"></script>
<script src="assets/admin/bootstrap/bootstrap.min.js"></script>
<script src="assets/admin/select2/select2.full.min.js"></script>
<script src="assets/admin/adminlte/adminlte.min.js"></script>
<script src="assets/admin/admin.js"></script>
</body>
</html>