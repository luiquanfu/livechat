<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Live Chat Visitor</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<link rel="stylesheet" href="{{ $app_url }}/assets/visitor/visitor.css">
</head>
<body>
<div id="app"></div>
<script>
var app_url = "{{ $app_url }}";
var nodejs_url = "{{ $nodejs_url }}";
var visitor_id = "{{ $visitor->id }}";
var app_data = {};
</script>
<script src="{{ $app_url }}/assets/visitor/jquery-3.4.1.min.js"></script>
<script src="{{ $app_url }}/assets/socket.io.slim.js"></script>
<script src="{{ $app_url }}/assets/visitor/visitor.js"></script>
</body>
</html>