var socket;
var cropper;
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

var spectrum_options = {};

initialize();

function popup_show(html)
{
    $('#popup_content').html(html);
    $('#popup_overlay').show();
    $('#popup_content').show();
}

function popup_hide()
{
    $('#popup_overlay').hide();
    $('#popup_content').hide();
    $('#popup_content').html('');
}

function loading_show()
{
    $('#loading_overlay').show();
    $('#loading_content').show();
}

function loading_hide()
{
    $('#loading_overlay').hide();
    $('#loading_content').hide();
}

function cropper_show(html_container, html_footer)
{
    $('#cropper_container').html(html_container);
    $('#cropper_footer').html(html_footer);
    $('#cropper_box').show();
    $('#cropper_footer').show();
}

function cropper_hide()
{
    $('#cropper_box').hide();
    $('#cropper_footer').hide();
}

function socket_io()
{
    var admin_id = app_data.admin.id;
    var random_id = Math.floor((Math.random() * 999999));
    var data = {};
    data.socket_id = admin_id;
    data.random_id = random_id;
    data.channel = 'admin';
    
    socket = io(nodejs_url);
    socket.emit('initialize', data);

    //socket reconnect
    socket.on("reconnect", function(message)
    {
        var data = {};
        data.socket_id = admin_id;
        data.random_id = random_id;
        data.channel = 'admin';
        socket.emit('initialize', data);
    });

    //socket message
    socket.on('message', function(task)
    {
        var action = task.action;
        console.log('action = ' + action);
        
        if(action == 'chat_room')
        {
            socket_chat_room(task);
        }
        if(action == 'chat_message')
        {
            socket_chat_message(task);
        }
    });
}

function socket_chat_room(task)
{
    var chat_room = task.chat_room;
    var chat_room_exist = false;
    var chat_room_admins = app_data.chat_room_admins;

    for(i in chat_room_admins)
    {
        var chat_room_admin = chat_room_admins[i];
        if(chat_room_admin.chat_room_id == chat_room.id)
        {
            chat_room_exist = true;
        }
    }

    if(chat_room_exist == false)
    {
        var html = '';
        html += '<div id="conversation_chat_room_' + chat_room.id + '" class="conversation_chat_room" onclick="chat_room_show(\'' + chat_room.id + '\')">';
        html += '<i class="fa fa-commenting"></i> <div class="width5"></div>New Chat';
        $('#conversation').prepend(html);

        var chat_room_admin = {};
        chat_room_admin.chat_room_id = chat_room.id;
        app_data.chat_room_admins.push(chat_room_admin);
    }
}

function socket_chat_message(task)
{
    console.log(task);
    var html = '';
    html += '<div style="padding: 12px 5px 12px 18px; background-color: #f39c12; color:#ffffff">';
    html += '<i class="fa fa-commenting"></i> <div class="width5"></div>New Chat';
    $('#conversation').prepend(html);
}

function initialize()
{
    var data = {};
    data.api_token = api_token;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/initialize';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
		var error = response.error;
        
        if(error != 0)
        {
            login_display();
            return;
        }

        var admin = response.admin;
        app_data.admin = admin;
        app_data.chat_room_admins = response.chat_room_admins;
        
        ui_display();
        socket_io();

        var last_visit = admin.last_visit;
        if(last_visit == '')
        {
            dashboard_index();
            return;
        }

        last_visit = JSON.parse(last_visit);
        if(last_visit.page == 'dashboard_listing')
        {
            dashboard_index();
            return;
        }
        if(last_visit.page == 'website_listing')
        {
            website_index();
            return;
        }
        if(last_visit.page == 'chat_display_listing')
        {
            chat_display_index();
            return;
        }
        if(last_visit.page == 'admin_listing')
        {
            admin_index();
            return;
        }
	}
    $.ajax(ajax);
}

function ui_display()
{
    var html = '';

    // generic div
    html += '<div id="popup_overlay" class="popup_overlay" onclick="popup_hide()"></div>';
    html += '<div id="popup_content" class="popup_content"></div>';
    html += '<div id="cropper_box" class="cropper_box">';
    html += '<div id="cropper_container" class="cropper_container"></div>';
    html += '</div>';
    html += '<div id="cropper_footer" class="cropper_footer"></div>';
    html += '<div id="loading_overlay" class="loading_overlay"></div>';
    html += '<div id="loading_content" class="loading_content">';
    html += '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>';
    html += '</div>';

    html += '<div class="wrapper">';

    // top header
    html += '<header id="header" class="main-header">';
    html += '<a href="' + app_url + '/admin" class="logo">';
    html += '<span class="logo-mini"><b>L</b>C</span>';
    html += '<span class="logo-lg"><b>Live</b>Chat</span>';
    html += '</a>';
    html += '<nav class="navbar navbar-static-top">';
    html += '<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">';
    html += '<span class="sr-only">Toggle navigation</span>';
    html += '</a>';
    html += '</nav>';
    html += '</header>';

    // sidebar
    html += '<aside class="main-sidebar">';
    html += '<section class="sidebar">';
    html += '<ul class="sidebar-menu" data-widget="tree">';
    html += '<li><a href="#" onclick="dashboard_index()"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>';
    html += '<li><a href="#" onclick="website_index()"><i class="fa fa-object-group"></i> <span>Website</span></a></li>';
    html += '<li><a href="#" onclick="chat_display_index()"><i class="fa fa-camera"></i> <span>Chat Display</span></a></li>';
    html += '<li><a href="#" onclick="admin_index()"><i class="fa fa-user"></i> <span>Admin</span></a></li>';
    html += '<li><a href="#" onclick="testing()"><i class="fa fa-fire"></i> <span>Testing</span></a></li>';
    html += '<li><a href="#" onclick="logout()"><i class="fa fa-power-off"></i> <span>Logout</span></a></li>';
    html += '</ul>';
    html += '<div id="conversation"></div>';
    html += '</section>';
    html += '</aside>';

    // content
    html += '<div id="content" class="content-wrapper"></div>';

    // footer
    html += '<footer id="footer" class="main-footer">';
    html += '<div class="pull-right hidden-xs"><b>Version</b> 1.0</div>';
    html += '<strong>2019 <a href="' + app_url + '/admin">Live Chat</a></strong>';
    html += ' Engage Your Customer';
    html += '</footer>';
    html += '</div>';

    $('#app').html(html);
    $('body').layout('fix');

    // spectrum
    spectrum_options.showInput = true;
    spectrum_options.preferredFormat = 'hex';
    spectrum_options.chooseText = 'Select';
    spectrum_options.cancelText = 'Cancel';
    spectrum_options.color = 'blanchedalmond';
}

function testing()
{
    socket_new_chat();
    return;
    
    $('#content').html('<div class="text-blue">Please wait...</div>');
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.device_id = device_id;
    data.device_type = 'website';
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/test';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;

		if(error == 1)
		{
			$('#content').html('<div class="text-red">' + message + '</div>');
			return;
		}
        
        // $('#content').html('<div class="text-green">' + message + '</div>');
	}
    $.ajax(ajax);
}

function login_display()
{
    var html = '';
    html += '<div id="div_login" class="login-page" style="padding: 1px;">';
    html += '<div class="login-box">';
    html += '<div class="login-logo">';
    html += '<a href=""><b>Live</b>Chat</a>';
    html += '</div>';
    html += '<div class="login-box-body">';
    html += '<p class="login-box-msg">Admin Login</p>';
    html += '<div class="form-group has-feedback">';
    html += '<input id="email" type="email" class="form-control" placeholder="Email" onkeyup="login_onkeyup(event, \'email\')">';
    html += '<span class="glyphicon glyphicon-envelope form-control-feedback"></span>';
    html += '</div>';
    html += '<div class="form-group has-feedback">';
    html += '<input id="password" type="password" class="form-control" placeholder="Password" onkeyup="login_onkeyup(event, \'password\')">';
    html += '<span class="glyphicon glyphicon-lock form-control-feedback"></span>';
    html += '</div>';
    html += '<div class="row">';
    html += '<div class="col-xs-8">';
    html += '</div>';
    html += '<div class="col-xs-4">';
    html += '<div class="btn btn-primary btn-block btn-flat" onclick="login_submit()">Sign In</div>';
    html += '</div>';
    html += '</div>';
    html += '<div id="result"></div>';
    html += '<div class="social-auth-links text-center">';
    html += '<p>- OR -</p>';
    html += '<div class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</div>';
    html += '</div>';
    html += '<a href="#">Forgot your password?</a><br>';
    html += '<a href="#" class="text-center">Sign up for a free account</a>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    $('#app').html(html);

    var document_height = $(document).height() - 2;
    $('#div_login').height(document_height);
}

function login_onkeyup(event, position)
{
    if(event.keyCode == 13)
    {
        if(position == 'email')
        {
            $('#password').focus();
            return;
        }

        login_submit();
    }
}

function login_submit()
{
    $('#result').html('<div class="text-light-blue">Please wait...</div>');
    loading_show();

    var data = {};
    data.email = $('#email').val();
    data.password = $('#password').val();
    data.device_id = device_id;
    data.device_type = 'website';
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/login';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;

		if(error == 1)
		{
			$('#result').html('<div class="text-red">' + message + '</div>');
			return;
		}
        
        var admin = response.admin;
        api_token = admin.api_token;
        
        $('#result').html('<div class="text-green">' + message + '</div>');
        initialize();
	}
    $.ajax(ajax);
}

function logout()
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/logout';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
        login_display();
        socket.disconnect();
	}
    $.ajax(ajax);
}

function chat_room_show(chat_room_id)
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.chat_room_id = chat_room_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/chat_room/show';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }

        if(error != 0)
        {
            $('#result').html('<span class="text-red">' + message + '</span>');
            return;
        }

        // start
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Add Chat Display</h3>';
        html += '</div>';
        html += '<div class="box-body">';

        // name
        html += '<div class="form-group">';
        html += '<label>Chat Display Name</label>';
        html += '<input id="name" type="text" class="form-control">';
        html += '</div>';

        // end
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-success" onclick="chat_display_add()">Add Chat Display</button>';
        html += '</div>';
        html += '<div id="result"></div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        $('#content').html(html);
	}
    $.ajax(ajax);
}

function dashboard_index()
{
    website_index();
    return;

    app_data.filter_bank_id = '';
    app_data.filter_package_id = '';
    dashboard_list();
}

function dashboard_filter()
{
    app_data.filter_bank_id = $('#filter_bank_id').val();
    app_data.filter_package_id = $('#filter_package_id').val();
    dashboard_list();
}

function dashboard_list()
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.filter_bank_id = app_data.filter_bank_id;
    data.filter_package_id = app_data.filter_package_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/dashboard/listing';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        
        if(error == 99)
        {
            login_display();
            return;
        }

        var dashboards = response.dashboards;
        var packages = response.packages;
        var banks = response.banks;
        var html = '';

        // header
        html += '<section class="content-header">';
        html += '<h1>';
        html += 'Dashboard';
        html += '<small>Listing of all Bank Rates</small>';
        html += '</h1>';
        html += '</section>';

        // filter start
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Filters</h3>';
        html += '</div>';
        html += '<div class="box-body">';

        // filter bank_id
        html += '<div class="form-group">';
        html += '<label>Bank</label>';
        html += '<select id="filter_bank_id" class="form-control select2" style="width: 100%;">';
        html += '<option value="">All Banks</option>';
        for(i in banks)
        {
            var bank = banks[i];
            var html_selected = '';
            if(bank.id == app_data.filter_bank_id)
            {
                html_selected = 'selected';
            }
            html += '<option value="' + bank.id + '" ' + html_selected + '>' + bank.name + '</option>';
        }
        html += '</select>';
        html += '</div>';

        // filter package_id
        html += '<div class="form-group">';
        html += '<label>Package</label>';
        html += '<select id="filter_package_id" class="form-control select2" style="width: 100%;">';
        html += '<option value="">All Packages</option>';
        for(i in packages)
        {
            var package = packages[i];
            var html_selected = '';
            if(package.id == app_data.filter_package_id)
            {
                html_selected = 'selected';
            }
            html += '<option value="' + package.id + '" ' + html_selected + '>' + package.name + '</option>';
        }
        html += '</select>';
        html += '</div>';

        // filter end
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-primary" onclick="dashboard_filter()">Filter</button>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        // list dashboard
        for(i in dashboards)
        {
            var dashboard = dashboards[i];
            var websites = dashboard.websites;

            if(websites.length == 0)
            {
                continue;
            }

            html += '<section class="content">';
            html += '<div class="row">';
            html += '<div class="col-xs-12">';
            html += '<div class="box box-primary">';
            html += '<div class="box-header">';
            html += '<h3 class="box-title">' + dashboard.name + '</h3>';
            html += '</div>';
            html += '<div class="box-body table-responsive no-padding">';
            html += '<table class="table table-hover">';
            html += '<tr>';
            html += '<th>Bank</th>';
            html += '<th>Minimum Loan</th>';
            html += '<th>Loan</th>';
            html += '<th>Lock Period</th>';
            html += '<th>Year</th>';
            html += '<th>Rate</th>';
            html += '<th>Interest Rate</th>';
            html += '</tr>';

            for(i in websites)
            {
                var website = websites[i];

                html += '<tr>';
                html += '<td>' + website.bank_name + '</td>';
                html += '<td>' + website.minimum_loan + '</td>';
                html += '<td>' + website.name + '</td>';
                html += '<td>' + website.lock_period + '</td>';

                var operating_hours = website.operating_hours;
                for(j in operating_hours)
                {
                    var operating_hour = operating_hours[j];

                    if(j != 0)
                    {
                        html += '<tr>';
                        html += '<td colspan="4"></td>';
                    }
                    html += '<td>' + operating_hour.year + '</td>';
                    html += '<td>' + operating_hour.formula + '</td>';
                    html += '<td>' + operating_hour.interest_rate + '</td>';
                    html += '</tr>';
                }

                if(operating_hours.length == 0)
                {
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '</tr>';
                }
            }
            html += '</table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</section>';
        }
        $('#content').html(html);
        
        var options = {};
        options.minimumResultsForSearch = -1;
        $('.select2').select2(options);
	}
    $.ajax(ajax);
}

function admin_index()
{
    app_data.page = 1;
    app_data.sort = 'firstname';
    app_data.direction = 'asc';
    app_data.filter_firstname = '';
    admin_list();
}

function admin_list()
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.page = app_data.page;
    data.sort = app_data.sort;
    data.direction = app_data.direction;
    data.filter_firstname = app_data.filter_firstname;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/admin/listing';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        
        if(error == 99)
        {
            login_display();
            return;
        }

        var admins = response.admins;
        var total_pages = response.total_pages;
        var current_page = response.current_page;
        var html = '';

        // header
        html += '<section class="content-header">';
        html += '<h1>';
        html += 'Admin Management';
        html += '<small>Listing of all Admins</small>';
        html += '</h1>';
        html += '</section>';

        // filter admins
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Filters</h3>';
        html += '</div>';
        html += '<div class="box-body">';
        html += '<div class="form-group">';
        html += '<label>Admin Name</label>';
        html += '<input id="filter_firstname" type="text" class="form-control" value="' + app_data.filter_firstname + '">';
        html += '</div>';
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-primary" onclick="admin_filter()">Filter</button>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        // create admins
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="width15"></div>';
        html += '<div class="btn btn-success" onclick="admin_create()">Create Admin</div>';
        html += '</div>';
        html += '</div>';

        // list admins
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-xs-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header">';
        html += '<h3 class="box-title">Admin List</h3>';
        html += '</div>';
        html += '<div class="box-body table-responsive no-padding">';
        html += '<table class="table table-hover">';
        html += '<tr>';
        html += '<th role="button" onclick="admin_sorting(\'firstname\')">Name</th>';
        html += '<th role="button" onclick="admin_sorting(\'email\')">Email</th>';
        html += '<th role="button" onclick="admin_sorting(\'mobile_number\')">Mobile</th>';
        html += '<th>Actions</th>';
        html += '</tr>';
        for(i in admins)
        {
            var admin = admins[i];

            html += '<tr>';
            html += '<td>' + admin.firstname + ' ' + admin.lastname + '</td>';
            html += '<td>' + admin.email + '</td>';
            html += '<td>' + admin.mobile_country + ' ' + admin.mobile_number + '</td>';
            html += '<td>';
            html += '<div class="btn btn-primary" onclick="admin_edit(\'' + admin.id + '\')"><i class="fa fa-edit"></i></div>';
            html += '<div class="width5"></div>';
            html += '<div class="btn btn-danger" onclick="admin_remove(\'' + admin.id + '\')"><i class="fa fa-trash"></i></div>';
            html += '</td>';
            html += '</tr>';
        }
        html += '</table>';
        html += '</div>';
        html += '<div class="box-footer clearfix">';
        html += '<ul class="pagination pagination-sm no-margin pull-right">';
        for(var i = 1; i <= total_pages; i++)
        {
            var html_page = '<a href="#" onclick="admin_paging(' + i + ')">' + i + '</a>';
            if(i == current_page)
            {
                html_page = '<li><span>' + i + '</span></li>';
            }
            html += '<li>' + html_page + '</li>';
        }
        html += '</ul>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';
        $('#content').html(html);
	}
    $.ajax(ajax);
}

function admin_filter()
{
    app_data.filter_firstname = $('#filter_firstname').val();
    app_data.page = 1;
    admin_list();
}

function admin_paging(page)
{
    app_data.page = page;
    admin_list();
}

function admin_sorting(sort)
{
    if(sort == app_data.sort)
    {
        if(app_data.direction == 'asc')
        {
            app_data.direction = 'desc';
        }
        else
        {
            app_data.direction = 'asc';
        }
    }
    if(sort != app_data.sort)
    {
        app_data.sort = sort;
        app_data.direction = 'asc';
    }
    admin_list();
}

function admin_create()
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/admin/create';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }
		
		if(error != 0)
		{
			$('#content').html(message);
			return;
		}

        var websites = response.websites;
        var html = '';
        
        // header
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Add Admin</h3>';
        html += '</div>';
        html += '<div class="box-body">';

        // image
        html += '<div class="form-group">';
        html += '<label class="col-sm-3">Image</label>';
        html += '<div class="col-sm-9">';
        html += '<div id="div_image">';
        html += '<img src="' + app_url + '/assets/default/admin.jpg" height="200" width="200">';
        html += '</div>';
        html += '<input id="image" type="hidden" value="">';
        html += '<input id="change_image" type="file" accept="image/*" onchange="admin_load_image(event)">';
        html += '<div class="height10"></div>';
        html += '<div style="display: inline-block; width: 200px; text-align: center;">';
        html += '<label class="btn btn-success" for="change_image">Upload Logo</label>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        // firstname
        html += '<div class="form-group">';
        html += '<label>First Name</label>';
        html += '<input id="firstname" type="text" class="form-control">';
        html += '</div>';

        // lastname
        html += '<div class="form-group">';
        html += '<label>Last Name</label>';
        html += '<input id="lastname" type="text" class="form-control">';
        html += '</div>';

        // website_admins
        html += '<div class="form-group">';
        html += '<label>Manage Websites</label>';
        html += '<select id="website_ids" class="form-control select2" multiple="multiple" data-placeholder="Select Website" style="width: 100%;">';
        for(i in websites)
        {
            var website = websites[i];
            html += '<option value="' + website.id + '">' + website.name + '</option>';
        }
        html += '</select>';
        html += '</div>';

        // email
        html += '<div class="form-group">';
        html += '<label>Email</label>';
        html += '<input id="email" type="text" class="form-control">';
        html += '</div>';

        // mobile_country
        html += '<div class="form-group">';
        html += '<label>Mobile Country</label>';
        html += '<input id="mobile_country" type="text" class="form-control">';
        html += '</div>';

        // mobile_number
        html += '<div class="form-group">';
        html += '<label>Mobile Number</label>';
        html += '<input id="mobile_number" type="text" class="form-control">';
        html += '</div>';

        // password
        html += '<div class="form-group">';
        html += '<label>Password</label>';
        html += '<input id="password" type="text" class="form-control">';
        html += '</div>';

        // footer
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-success" onclick="admin_add()">Add Admin</button>';
        html += '</div>';
        html += '<div id="result"></div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        $('#content').html(html);
        $('#change_image').hide();
        var options = {};
        options.minimumResultsForSearch = -1;
        $('.select2').select2(options);
	}
    $.ajax(ajax);
}

function admin_load_image()
{
    var local_url = URL.createObjectURL(event.target.files[0]);
    var html_container = '';
    var html_footer = '';

    // prepare cropper
    $('#change_image').prop('value', '');
    html_container += '<img id="crop_image" src="' + local_url + '"></img>';
    html_footer += '<div class="btn btn-default" onclick="cropper_hide()">Cancel</div>';
    html_footer += '<div class="width5"></div>';
    html_footer += '<div class="btn btn-success" onclick="admin_crop_image()">Upload Image</div>';
    cropper_show(html_container, html_footer);

    // load cropper
    var image = document.getElementById('crop_image');
    var options = {};
    options.guides = false;
    options.aspectRatio = 500 / 500;
    options.zoomOnWheel = false;
	cropper = new Cropper(image, options);
}

function admin_crop_image()
{
    loading_show();

    // crop image
    var options = {};
    options.width = 500;
    options.height = 500;
    var logo = cropper.getCroppedCanvas(options).toDataURL('image/jpeg');

    // display image
    var html = '';
    html += '<img src="' + logo + '" height="200" width="200">';
    $('#div_image').html(html);
    $('#image').val(logo);

    loading_hide();
    cropper_hide();
}

function admin_add()
{
    $('#result').html('<span class="text-light-blue">Please wait...</span>');
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.image = $('#image').val();
    data.firstname = $('#firstname').val();
    data.lastname = $('#lastname').val();
    data.email = $('#email').val();
    data.mobile_country = $('#mobile_country').val();
    data.mobile_number = $('#mobile_number').val();
    data.password = $('#password').val();
    var website_ids = [];
	$.each($('#website_ids option:selected'), function()
	{
		website_ids.push($(this).val());
	});
    data.website_ids = website_ids;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/admin/add';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }

        if(error != 0)
        {
            $('#result').html('<span class="text-red">' + message + '</span>');
            return;
        }

        $('#result').html('<span class="text-green">' + message + '</span>');
        admin_list();
	}
    $.ajax(ajax);
}

function admin_edit(admin_id)
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.admin_id = admin_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/admin/edit';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }
		
		if(error != 0)
		{
			$('#content').html(message);
			return;
		}
		
        var admin = response.admin;
        var websites = response.websites;
        var html = '';

        // start
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Edit Admin</h3>';
        html += '</div>';
        html += '<div class="box-body">';

        // id
        html += '<input id="admin_id" type="hidden" value="' + admin.id + '">';

        // image
        html += '<div class="form-group">';
        html += '<label class="col-sm-3">Image</label>';
        html += '<div class="col-sm-9">';
        html += '<div id="div_image">';
        html += '<img src="' + admin.image + '" height="200" width="200">';
        html += '</div>';
        html += '<input id="image" type="hidden" value="">';
        html += '<input id="change_image" type="file" accept="image/*" onchange="admin_load_image(event)">';
        html += '<div class="height10"></div>';
        html += '<div style="display: inline-block; width: 200px; text-align: center;">';
        html += '<label class="btn btn-success" for="change_image">Upload Logo</label>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        // firstname
        html += '<div class="form-group">';
        html += '<label>First Name</label>';
        html += '<input id="firstname" type="text" class="form-control" value="' + admin.firstname + '">';
        html += '</div>';

        // lastname
        html += '<div class="form-group">';
        html += '<label>Last Name</label>';
        html += '<input id="lastname" type="text" class="form-control" value="' + admin.lastname + '">';
        html += '</div>';

        // website_admins
        html += '<div class="form-group">';
        html += '<label>Manage Websites</label>';
        html += '<select id="website_ids" class="form-control select2" multiple="multiple" data-placeholder="Select Website" style="width: 100%;">';
        for(i in websites)
        {
            var website = websites[i];
            var selected = '';
            if(website.selected == 1)
            {
                selected = 'selected';
            }
            html += '<option value="' + website.id + '" ' + selected + '>' + website.name + '</option>';
        }
        html += '</select>';
        html += '</div>';

        // email
        html += '<div class="form-group">';
        html += '<label>Email</label>';
        html += '<input id="email" type="text" class="form-control" value="' + admin.email + '">';
        html += '</div>';

        // mobile_country
        html += '<div class="form-group">';
        html += '<label>Country Code</label>';
        html += '<input id="mobile_country" type="text" class="form-control" value="' + admin.mobile_country + '">';
        html += '</div>';

        // mobile_number
        html += '<div class="form-group">';
        html += '<label>Mobile Number</label>';
        html += '<input id="mobile_number" type="text" class="form-control" value="' + admin.mobile_number + '">';
        html += '</div>';

        // password
        html += '<div class="form-group">';
        html += '<label>Change Password</label>';
        html += '<input id="password" type="text" class="form-control">';
        html += '</div>';

        // end
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-primary" onclick="admin_update()">Update Admin</button>';
        html += '</div>';
        html += '<div id="result"></div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        $('#content').html(html);
        $('#change_image').hide();
        var options = {};
        options.minimumResultsForSearch = -1;
        $('.select2').select2(options);
	}
    $.ajax(ajax);
}

function admin_update()
{
    $('#result').html('<span class="text-light-blue">Please wait...</span>');
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.admin_id = $('#admin_id').val();
    data.image = $('#image').val();
    data.firstname = $('#firstname').val();
    data.lastname = $('#lastname').val();
    data.email = $('#email').val();
    data.mobile_country = $('#mobile_country').val();
    data.mobile_number = $('#mobile_number').val();
    data.password = $('#password').val();
    var website_ids = [];
	$.each($('#website_ids option:selected'), function()
	{
		website_ids.push($(this).val());
	});
    data.website_ids = website_ids;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/admin/update';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }
		
		if(error == 1)
		{
			$('#result').html('<span class="text-red">' + message + '</span>');
			return;
		}
		
        $('#result').html('<span class="text-green">' + message + '</span>');
        admin_list();
	}
    $.ajax(ajax);
}

function admin_remove(admin_id)
{
    var html = '';
    html += '<div class="box box-danger">';
    html += '<div class="box-header with-border">';
    html += '<h3 class="box-title">Click Confirm to Delete</h3>';
    html += '</div>';
    html += '<div class="box-body">';
    html += '<div class="btn btn-secondary" onclick="popup_hide()">Cancel</div>';
    html += '<div class="width5"></div>';
    html += '<div class="btn btn-danger" onclick="admin_destroy(\'' + admin_id + '\')">Confirm</div>';
    html += '<div id="result"></div>';
    html += '</div>';
    html += '</div>';
    popup_show(html);
}

function admin_destroy(admin_id)
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.admin_id = admin_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/admin/destroy';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            window.location.href = login_url;
            return;
        }
		
		if(error == 1)
		{
			$('#result').html('<span class="text-red">' + message + '</span>');
			return;
		}
		
        $('#result').html('<span class="text-green">' + message + '</span>');

        popup_hide();
        admin_list();
	}
    $.ajax(ajax);
}

function chat_display_index()
{
    app_data.page = 1;
    app_data.sort = 'name';
    app_data.direction = 'asc';
    app_data.filter_name = '';
    chat_display_list();
}

function chat_display_list()
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.page = app_data.page;
    data.sort = app_data.sort;
    data.direction = app_data.direction;
    data.filter_name = app_data.filter_name;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/chat_display/listing';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        
        if(error == 99)
        {
            login_display();
            return;
        }

        var chat_displays = response.chat_displays;
        var total_pages = response.total_pages;
        var current_page = response.current_page;
        var html = '';

        // header
        html += '<section class="content-header">';
        html += '<h1>';
        html += 'Chat Display Management';
        html += '<small>Listing of all Chat Displays</small>';
        html += '</h1>';
        html += '</section>';

        // filter chat_displays
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Filters</h3>';
        html += '</div>';
        html += '<div class="box-body">';
        html += '<div class="form-group">';
        html += '<label>Chat Display Name</label>';
        html += '<input id="filter_name" type="text" class="form-control" value="' + app_data.filter_name + '">';
        html += '</div>';
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-primary" onclick="chat_display_filter()">Filter</button>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        // create chat_displays
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="width15"></div>';
        html += '<div class="btn btn-success" onclick="chat_display_create()">Create Chat Display</div>';
        html += '</div>';
        html += '</div>';

        // list chat_displays
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-xs-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header">';
        html += '<h3 class="box-title">Chat Display List</h3>';
        html += '</div>';
        html += '<div class="box-body table-responsive no-padding">';
        html += '<table class="table table-hover">';
        html += '<tr>';
        html += '<th role="button" onclick="chat_display_sorting(\'name\')">Name</th>';
        html += '<th>Actions</th>';
        html += '</tr>';
        for(i in chat_displays)
        {
            var chat_display = chat_displays[i];

            html += '<tr>';
            html += '<td>' + chat_display.name + '</td>';
            html += '<td>';
            html += '<div class="btn btn-primary" onclick="chat_display_edit(\'' + chat_display.id + '\')"><i class="fa fa-edit"></i></div>';
            html += '<div class="width5"></div>';
            html += '<div class="btn btn-danger" onclick="chat_display_remove(\'' + chat_display.id + '\')"><i class="fa fa-trash"></i></div>';
            html += '</td>';
            html += '</tr>';
        }
        html += '</table>';
        html += '</div>';
        html += '<div class="box-footer clearfix">';
        html += '<ul class="pagination pagination-sm no-margin pull-right">';
        for(var i = 1; i <= total_pages; i++)
        {
            var html_page = '<a href="#" onclick="chat_display_paging(' + i + ')">' + i + '</a>';
            if(i == current_page)
            {
                html_page = '<li><span>' + i + '</span></li>';
            }
            html += '<li>' + html_page + '</li>';
        }
        html += '</ul>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';
        $('#content').html(html);
	}
    $.ajax(ajax);
}

function chat_display_filter()
{
    app_data.filter_name = $('#filter_name').val();
    app_data.page = 1;
    chat_display_list();
}

function chat_display_paging(page)
{
    app_data.page = page;
    chat_display_list();
}

function chat_display_sorting(sort)
{
    if(sort == app_data.sort)
    {
        if(app_data.direction == 'asc')
        {
            app_data.direction = 'desc';
        }
        else
        {
            app_data.direction = 'asc';
        }
    }
    if(sort != app_data.sort)
    {
        app_data.sort = sort;
        app_data.direction = 'asc';
    }
    chat_display_list();
}

function chat_display_create()
{
    var html = '';

    // start
    html += '<section class="content">';
    html += '<div class="row">';
    html += '<div class="col-md-12">';
    html += '<div class="box box-primary">';
    html += '<div class="box-header with-border">';
    html += '<h3 class="box-title">Add Chat Display</h3>';
    html += '</div>';
    html += '<div class="box-body">';

    // name
    html += '<div class="form-group">';
    html += '<label>Chat Display Name</label>';
    html += '<input id="name" type="text" class="form-control">';
    html += '</div>';

    // end
    html += '</div>';
    html += '<div class="box-footer">';
    html += '<div class="btn btn-success" onclick="chat_display_add()">Add Chat Display</button>';
    html += '</div>';
    html += '<div id="result"></div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    html += '</section>';

    $('#content').html(html);
}

function chat_display_add()
{
    $('#result').html('<span class="text-light-blue">Please wait...</span>');
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.name = $('#name').val();
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/chat_display/add';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }

        if(error != 0)
        {
            $('#result').html('<span class="text-red">' + message + '</span>');
            return;
        }

        $('#result').html('<span class="text-green">' + message + '</span>');
        chat_display_list();
	}
    $.ajax(ajax);
}

function chat_display_edit(chat_display_id)
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.chat_display_id = chat_display_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/chat_display/edit';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }
		
		if(error != 0)
		{
			$('#content').html(message);
			return;
		}
		
        var chat_display = response.chat_display;
        var html = '';

        // start
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Edit Chat Display</h3>';
        html += '</div>';
        html += '<div class="box-body">';

        // id
        html += '<input id="chat_display_id" type="hidden" value="' + chat_display.id + '">';

        // name
        html += '<div class="form-group">';
        html += '<label>Chat Display Name</label>';
        html += '<input id="name" type="text" class="form-control" value="' + chat_display.name + '">';
        html += '</div>';

        // body_border_color
        html += '<div class="form-group">';
        html += '<label>Chat Border Color</label>';
        html += '<br><input id="body_border_color" type="text" class="form-control spectrum" value="' + chat_display.body_border_color + '">';
        html += '</div>';

        // body_height
        html += '<div class="form-group">';
        html += '<label>Chat Display Height</label>';
        html += '<input id="body_height" type="text" class="form-control" value="' + chat_display.body_height + '">';
        html += '</div>';

        // body_width
        html += '<div class="form-group">';
        html += '<label>Chat Display Width</label>';
        html += '<input id="body_width" type="text" class="form-control" value="' + chat_display.body_width + '">';
        html += '</div>';

        // body_bottom
        html += '<div class="form-group">';
        html += '<label>Chat Bottom Position</label>';
        html += '<input id="body_bottom" type="text" class="form-control" value="' + chat_display.body_bottom + '">';
        html += '</div>';

        // body_right
        html += '<div class="form-group">';
        html += '<label>Chat Right Position</label>';
        html += '<input id="body_right" type="text" class="form-control" value="' + chat_display.body_right + '">';
        html += '</div>';

        // header_height
        html += '<div class="form-group">';
        html += '<label>Header Height</label>';
        html += '<input id="header_height" type="text" class="form-control" value="' + chat_display.header_height + '">';
        html += '</div>';

        // header_text_color
        html += '<div class="form-group">';
        html += '<label>Header Text Color</label>';
        html += '<br><input id="header_text_color" type="text" class="form-control spectrum" value="' + chat_display.header_text_color + '">';
        html += '</div>';

        // header_background_color
        html += '<div class="form-group">';
        html += '<label>Header Background Color</label>';
        html += '<br><input id="header_background_color" type="text" class="form-control spectrum" value="' + chat_display.header_background_color + '">';
        html += '</div>';

        // header_text
        html += '<div class="form-group">';
        html += '<label>Header Text</label>';
        html += '<input id="header_text" type="text" class="form-control" value="' + chat_display.header_text + '">';
        html += '</div>';

        // header_font_size
        html += '<div class="form-group">';
        html += '<label>Header Font Size</label>';
        html += '<input id="header_font_size" type="text" class="form-control" value="' + chat_display.header_font_size + '">';
        html += '</div>';

        // footer_line_color
        html += '<div class="form-group">';
        html += '<label>Footer Line Color</label>';
        html += '<br><input id="footer_line_color" type="text" class="form-control spectrum" value="' + chat_display.footer_line_color + '">';
        html += '</div>';

        // footer_border
        html += '<div class="form-group">';
        html += '<label>Footer Border</label>';
        html += '<input id="footer_border" type="text" class="form-control" value="' + chat_display.footer_border + '">';
        html += '</div>';

        // footer_height
        html += '<div class="form-group">';
        html += '<label>Footer Height</label>';
        html += '<input id="footer_height" type="text" class="form-control" value="' + chat_display.footer_height + '">';
        html += '</div>';

        // content_background_color
        html += '<div class="form-group">';
        html += '<label>Content Background Color</label>';
        html += '<br><input id="content_background_color" type="text" class="form-control spectrum" value="' + chat_display.content_background_color + '">';
        html += '</div>';

        // textbox_text_color
        html += '<div class="form-group">';
        html += '<label>Textbox Text Color</label>';
        html += '<br><input id="textbox_text_color" type="text" class="form-control spectrum" value="' + chat_display.textbox_text_color + '">';
        html += '</div>';

        // textbox_background_color
        html += '<div class="form-group">';
        html += '<label>Textbox Background Color</label>';
        html += '<br><input id="textbox_background_color" type="text" class="form-control spectrum" value="' + chat_display.textbox_background_color + '">';
        html += '</div>';

        // textbox_font_size
        html += '<div class="form-group">';
        html += '<label>Textbox Font Size</label>';
        html += '<input id="textbox_font_size" type="text" class="form-control" value="' + chat_display.textbox_font_size + '">';
        html += '</div>';

        // textbox_text
        html += '<div class="form-group">';
        html += '<label>Textbox Placeholder Text</label>';
        html += '<input id="textbox_text" type="text" class="form-control" value="' + chat_display.textbox_text + '">';
        html += '</div>';

        // placeholder_color
        html += '<div class="form-group">';
        html += '<label>Placeholder Color</label>';
        html += '<br><input id="placeholder_color" type="text" class="form-control spectrum" value="' + chat_display.placeholder_color + '">';
        html += '</div>';

        // visitor_text_color
        html += '<div class="form-group">';
        html += '<label>Visitor Text Color</label>';
        html += '<br><input id="visitor_text_color" type="text" class="form-control spectrum" value="' + chat_display.visitor_text_color + '">';
        html += '</div>';

        // visitor_background_color
        html += '<div class="form-group">';
        html += '<label>Visitor Background Color</label>';
        html += '<br><input id="visitor_background_color" type="text" class="form-control spectrum" value="' + chat_display.visitor_background_color + '">';
        html += '</div>';

        // visitor_font_size
        html += '<div class="form-group">';
        html += '<label>Visitor Font Size</label>';
        html += '<input id="visitor_font_size" type="text" class="form-control" value="' + chat_display.visitor_font_size + '">';
        html += '</div>';

        // admin_text_color
        html += '<div class="form-group">';
        html += '<label>Admin Text Color</label>';
        html += '<br><input id="admin_text_color" type="text" class="form-control spectrum" value="' + chat_display.admin_text_color + '">';
        html += '</div>';

        // admin_background_color
        html += '<div class="form-group">';
        html += '<label>Admin Background Color</label>';
        html += '<br><input id="admin_background_color" type="text" class="form-control spectrum" value="' + chat_display.admin_background_color + '">';
        html += '</div>';

        // admin_font_size
        html += '<div class="form-group">';
        html += '<label>Admin Font Size</label>';
        html += '<input id="admin_font_size" type="text" class="form-control" value="' + chat_display.admin_font_size + '">';
        html += '</div>';

        // end
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-primary" onclick="chat_display_update()">Update Chat Display</button>';
        html += '</div>';
        html += '<div id="result"></div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        $('#content').html(html);

        spectrum_options.color = chat_display.body_border_color;
        $('#body_border_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.header_text_color;
        $('#header_text_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.header_background_color;
        $('#header_background_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.footer_line_color;
        $('#footer_line_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.content_background_color;
        $('#content_background_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.textbox_text_color;
        $('#textbox_text_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.textbox_background_color;
        $('#textbox_background_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.placeholder_color;
        $('#placeholder_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.visitor_text_color;
        $('#visitor_text_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.visitor_background_color;
        $('#visitor_background_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.admin_text_color;
        $('#admin_text_color').spectrum(spectrum_options);

        spectrum_options.color = chat_display.admin_background_color;
        $('#admin_background_color').spectrum(spectrum_options);
	}
    $.ajax(ajax);
}

function chat_display_update()
{
    $('#result').html('<span class="text-light-blue">Please wait...</span>');
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.chat_display_id = $('#chat_display_id').val();
    data.name = $('#name').val();
    data.body_border_color = $('#body_border_color').val();
    data.body_height = $('#body_height').val();
    data.body_width = $('#body_width').val();
    data.body_bottom = $('#body_bottom').val();
    data.body_right = $('#body_right').val();
    data.header_height = $('#header_height').val();
    data.header_text_color = $('#header_text_color').val();
    data.header_background_color = $('#header_background_color').val();
    data.header_text = $('#header_text').val();
    data.header_font_size = $('#header_font_size').val();
    data.footer_line_color = $('#footer_line_color').val();
    data.footer_border = $('#footer_border').val();
    data.footer_height = $('#footer_height').val();
    data.content_background_color = $('#content_background_color').val();
    data.content_height = $('#content_height').val();
    data.textbox_text_color = $('#textbox_text_color').val();
    data.textbox_background_color = $('#textbox_background_color').val();
    data.textbox_font_size = $('#textbox_font_size').val();
    data.textbox_text = $('#textbox_text').val();
    data.placeholder_color = $('#placeholder_color').val();
    data.visitor_text_color = $('#visitor_text_color').val();
    data.visitor_background_color = $('#visitor_background_color').val();
    data.visitor_font_size = $('#visitor_font_size').val();
    data.admin_text_color = $('#admin_text_color').val();
    data.admin_background_color = $('#admin_background_color').val();
    data.admin_font_size = $('#admin_font_size').val();
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/chat_display/update';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }
		
		if(error == 1)
		{
			$('#result').html('<span class="text-red">' + message + '</span>');
			return;
		}
		
        $('#result').html('<span class="text-green">' + message + '</span>');
        chat_display_list();
	}
    $.ajax(ajax);
}

function chat_display_remove(chat_display_id)
{
    var html = '';
    html += '<div class="box box-danger">';
    html += '<div class="box-header with-border">';
    html += '<h3 class="box-title">Click Confirm to Delete</h3>';
    html += '</div>';
    html += '<div class="box-body">';
    html += '<div class="btn btn-secondary" onclick="popup_hide()">Cancel</div>';
    html += '<div class="width5"></div>';
    html += '<div class="btn btn-danger" onclick="chat_display_destroy(\'' + chat_display_id + '\')">Confirm</div>';
    html += '<div id="result"></div>';
    html += '</div>';
    html += '</div>';
    popup_show(html);
}

function chat_display_destroy(chat_display_id)
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.chat_display_id = chat_display_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/chat_display/destroy';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            window.location.href = login_url;
            return;
        }
		
		if(error == 1)
		{
			$('#result').html('<span class="text-red">' + message + '</span>');
			return;
		}
		
        $('#result').html('<span class="text-green">' + message + '</span>');

        popup_hide();
        chat_display_list();
	}
    $.ajax(ajax);
}

function bank_index()
{
    app_data.page = 1;
    app_data.sort = 'name';
    app_data.direction = 'asc';
    app_data.filter_name = '';
    bank_list();
}

function website_index()
{
    app_data.page = 1;
    app_data.sort = 'name';
    app_data.direction = 'asc';
    app_data.filter_name = '';
    var calculates = [];
    calculates.push('add');
    calculates.push('subtract');
    app_data.calculates = calculates;
    website_list();
}

function website_list()
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.page = app_data.page;
    data.sort = app_data.sort;
    data.direction = app_data.direction;
    data.filter_name = app_data.filter_name;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/website/listing';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        
        if(error == 99)
        {
            login_display();
            return;
        }

        var websites = response.websites;
        var total_pages = response.total_pages;
        var current_page = response.current_page;
        var html = '';

        // header
        html += '<section class="content-header">';
        html += '<h1>';
        html += 'Website Management';
        html += '<small>Listing of all websites</small>';
        html += '</h1>';
        html += '</section>';

        // filter websites
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Filters</h3>';
        html += '</div>';
        html += '<div class="box-body">';
        html += '<div class="form-group">';
        html += '<label>Website Name</label>';
        html += '<input id="filter_name" type="text" class="form-control" value="' + app_data.filter_name + '">';
        html += '</div>';
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-primary" onclick="website_filter()">Filter</button>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        // create websites
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="width15"></div>';
        html += '<div class="btn btn-success" onclick="website_create()">Create Website</div>';
        html += '</div>';
        html += '</div>';

        // list websites
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-xs-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header">';
        html += '<h3 class="box-title">Website List</h3>';
        html += '</div>';
        html += '<div class="box-body table-responsive no-padding">';
        html += '<table class="table table-hover">';
        html += '<tr>';
        html += '<th role="button" onclick="website_sorting(\'name\')">Name</th>';
        html += '<th role="button" onclick="website_sorting(\'url\')">URL</th>';
        html += '<th>Actions</th>';
        html += '</tr>';
        for(i in websites)
        {
            var website = websites[i];

            html += '<tr>';
            html += '<td>' + website.name + '</td>';
            html += '<td><a href="' + website.url + '" target="_BLANK">' + website.url + '</a></td>';
            html += '<td>';
            html += '<div class="btn btn-primary" onclick="website_edit(\'' + website.id + '\')"><i class="fa fa-edit"></i></div>';
            html += '<div class="width5"></div>';
            html += '<div class="btn btn-danger" onclick="website_remove(\'' + website.id + '\')"><i class="fa fa-trash"></i></div>';
            html += '</td>';
            html += '</tr>';
        }
        html += '</table>';
        html += '</div>';
        html += '<div class="box-footer clearfix">';
        html += '<ul class="pagination pagination-sm no-margin pull-right">';
        for(var i = 1; i <= total_pages; i++)
        {
            var html_page = '<a href="#" onclick="website_paging(' + i + ')">' + i + '</a>';
            if(i == current_page)
            {
                html_page = '<li><span>' + i + '</span></li>';
            }
            html += '<li>' + html_page + '</li>';
        }
        html += '</ul>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';
        $('#content').html(html);
	}
    $.ajax(ajax);
}

function website_filter()
{
    app_data.filter_name = $('#filter_name').val();
    app_data.page = 1;
    website_list();
}

function website_paging(page)
{
    app_data.page = page;
    website_list();
}

function website_sorting(sort)
{
    if(sort == app_data.sort)
    {
        if(app_data.direction == 'asc')
        {
            app_data.direction = 'desc';
        }
        else
        {
            app_data.direction = 'asc';
        }
    }
    if(sort != app_data.sort)
    {
        app_data.sort = sort;
        app_data.direction = 'asc';
    }
    website_list();
}

function website_create()
{
    var html = '';

    // start
    html += '<section class="content">';
    html += '<div class="row">';
    html += '<div class="col-md-12">';
    html += '<div class="box box-primary">';
    html += '<div class="box-header with-border">';
    html += '<h3 class="box-title">Add Website</h3>';
    html += '</div>';
    html += '<div class="box-body">';

    // name
    html += '<div class="form-group">';
    html += '<label>Website Name</label>';
    html += '<input id="name" type="text" class="form-control">';
    html += '</div>';

    // url
    html += '<div class="form-group">';
    html += '<label>Website URL</label>';
    html += '<input id="url" type="text" class="form-control">';
    html += '</div>';

    // operating_hours
    app_data.new_operating_hour_ids = [];
    app_data.edit_operating_hour_ids = [];
    html += '<div class="box box-success">';
    html += '<div class="box-header with-border">';
    html += '<h3 class="box-title">Operating Hours</h3>';
    html += '</div>';
    html += '<div class="box-body table-responsive no-padding">';
    html += '<table id="table_operating_hours" class="table table-bordered">';
    html += '<tr>';
    html += '<th>Day</th>';
    html += '<th>Start Time</th>';
    html += '<th>End Time</th>';
    html += '<th>Action</th>';
    html += '</tr>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    html += '<div class="btn btn-success pull-right" onclick="operating_hour_create()">Add Operating Hours</div>';

    // end
    html += '</div>';
    html += '<div class="box-footer">';
    html += '<div class="btn btn-success" onclick="website_add()">Add Website</button>';
    html += '</div>';
    html += '<div id="result"></div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    html += '</section>';

    $('#content').html(html);
}

function website_add()
{
    $('#result').html('<span class="text-light-blue">Please wait...</span>');
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.name = $('#name').val();
    data.url = $('#url').val();
    var new_operating_hours = [];
    for(i in app_data.new_operating_hour_ids)
    {
        var id = app_data.new_operating_hour_ids[i];
        var operating_hour = {};
        operating_hour.id = id;
        operating_hour.day = $('#new_operating_hour_day_' + id).val();
        operating_hour.open_time = $('#new_operating_hour_open_time_' + id).val();
        operating_hour.close_time = $('#new_operating_hour_close_time_' + id).val();
        new_operating_hours.push(operating_hour);
    }
    data.new_operating_hours = new_operating_hours;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/website/add';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }

        if(error != 0)
        {
            $('#result').html('<span class="text-red">' + message + '</span>');
            return;
        }

        $('#result').html('<span class="text-green">' + message + '</span>');
        website_list();
	}
    $.ajax(ajax);
}

function website_edit(website_id)
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.website_id = website_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/website/edit';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }
		
		if(error != 0)
		{
			$('#content').html(message);
			return;
		}
		
        var website = response.website;
        var chat_displays = response.chat_displays;
        var operating_hours = response.operating_hours;
        var html = '';

        // start
        html += '<section class="content">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<div class="box box-primary">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Edit Website</h3>';
        html += '</div>';
        html += '<div class="box-body">';

        // id
        html += '<input id="website_id" type="hidden" value="' + website.id + '">';

        // name
        html += '<div class="form-group">';
        html += '<label>Website Name</label>';
        html += '<input id="name" type="text" class="form-control" value="' + website.name + '">';
        html += '</div>';

        // url
        html += '<div class="form-group">';
        html += '<label>Website URL</label>';
        html += '<input id="url" type="text" class="form-control" value="' + website.url + '">';
        html += '</div>';

        // api_token
        html += '<div class="form-group">';
        html += '<label>Copy this javascript to your website</label>';
        html += '<div class="input-group" onclick="website_copy()">';
        html += '<div class="input-group-btn">';
        html += '<div class="btn btn-info"><i class="fa fa-copy"></i> Copy</div>';
        html += '</div>';
        html += '<input id="api_token" type="text" class="form-control" value=\'<script src="' + website.javascript_url + '"></script>\' spellcheck="false">';
        html += '</div>';
        html += '<div class="height10"></div>';
        html += '<p>If necessary, you can re-generate a new javascript URL by clicking the red button below</p>';
        html += '<div class="btn btn-danger" onclick="website_token()">Re-generate Javascript</div>';
        html += '</div>';

        // chat_display_id
        html += '<div class="form-group">';
        html += '<label>Chat Display</label>';
        html += '<select id="chat_display_id" class="form-control select2" style="width: 100%;">';
        html += '<option value="0">Default</option>';
        for(i in chat_displays)
        {
            var chat_display = chat_displays[i];
            var html_selected = '';
            if(chat_display.id == website.chat_display_id)
            {
                html_selected = 'selected';
            }
            html += '<option value="' + chat_display.id + '" ' + html_selected + '>' + chat_display.name + '</option>';
        }
        html += '</select>';
        html += '</div>';

        // operating_hours
        app_data.new_operating_hour_ids = [];
        app_data.edit_operating_hour_ids = [];
        html += '<div class="box box-success">';
        html += '<div class="box-header with-border">';
        html += '<h3 class="box-title">Operating Hours</h3>';
        html += '</div>';
        html += '<div class="box-body table-responsive">';
        html += '<table id="table_operating_hours" class="table table-bordered no-padding">';
        html += '<tr>';
        html += '<th>Day</th>';
        html += '<th>Open Time</th>';
        html += '<th>Close Time</th>';
        html += '<th>Action</th>';
        html += '</tr>';
        for(i in operating_hours)
        {
            var operating_hour = operating_hours[i];
            app_data.edit_operating_hour_ids.push(operating_hour.id);

            html += '<tr id="edit_operating_hour_tr_' + operating_hour.id + '">';

            // days
            html += '<td>';
            html += '<select id="edit_operating_hour_day_' + operating_hour.id + '" class="form-control select2" style="width: 100%;">';
            for(j in days)
            {
                var day = days[j];
                var html_selected = '';
                if(j == operating_hour.day)
                {
                    html_selected = 'selected';
                }
                html += '<option value="' + j + '" ' + html_selected + '>' + day + '</option>';
            }
            html += '</select>';
            html += '</td>';

            // open_times
            html += '<td>';
            html += '<select id="edit_operating_hour_open_time_' + operating_hour.id + '" class="form-control select2" style="width: 100%;">';
            for(j in hours)
            {
                var hour = hours[j];
                var html_selected = '';
                if(hour == operating_hour.open_time)
                {
                    html_selected = 'selected';
                }
                html += '<option value="' + hour + '" ' + html_selected + '>' + hour + '</option>';
            }
            html += '</select>';
            html += '</td>';

            // close_times
            html += '<td>';
            html += '<select id="edit_operating_hour_close_time_' + operating_hour.id + '" class="form-control select2" style="width: 100%;">';
            for(j in hours)
            {
                var hour = hours[j];
                var html_selected = '';
                if(hour == operating_hour.close_time)
                {
                    html_selected = 'selected';
                }
                html += '<option value="' + hour + '" ' + html_selected + '>' + hour + '</option>';
            }
            html += '</select>';
            html += '</td>';
            
            // actions
            html += '<td>';
            html += '<div class="btn btn-danger" onclick="operating_hour_destroy(\'' + operating_hour.id + '\', \'edit\')">';
            html += '<i class="fa fa-trash-o"></i>';
            html += '</div>';
            html += '</td>';
            html += '</tr>';
        }
        html += '</table>';
        html += '</div>';
        html += '</div>';
        html += '<div class="btn btn-success pull-right" onclick="operating_hour_create()">Add Operating Hour</div>';

        // end
        html += '</div>';
        html += '<div class="box-footer">';
        html += '<div class="btn btn-primary" onclick="website_update()">Update Website</button>';
        html += '</div>';
        html += '<div id="result"></div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</section>';

        $('#content').html(html);

        var options = {};
        options.minimumResultsForSearch = -1;
        $('.select2').select2(options);
	}
    $.ajax(ajax);
}

function website_token()
{
    $('#result').html('<span class="text-light-blue">Please wait...</span>');
    loading_show();

    var website_id = $('#website_id').val();

    var data = {};
    data.api_token = api_token;
    data.website_id = website_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/website/token';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }
		
		if(error == 1)
		{
			$('#result').html('<span class="text-red">' + message + '</span>');
			return;
		}
		
        $('#result').html('<span class="text-green">' + message + '</span>');
        website_edit(website_id);
	}
    $.ajax(ajax);
}

function website_copy()
{
    $('#api_token').select();
    document.execCommand('copy');
}

function website_update()
{
    $('#result').html('<span class="text-light-blue">Please wait...</span>');
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.website_id = $('#website_id').val();
    data.name = $('#name').val();
    data.url = $('#url').val();
    data.chat_display_id = $('#chat_display_id').val();
    var new_operating_hours = [];
    for(i in app_data.new_operating_hour_ids)
    {
        var id = app_data.new_operating_hour_ids[i];
        var operating_hour = {};
        operating_hour.id = id;
        operating_hour.day = $('#new_operating_hour_day_' + id).val();
        operating_hour.open_time = $('#new_operating_hour_open_time_' + id).val();
        operating_hour.close_time = $('#new_operating_hour_close_time_' + id).val();
        new_operating_hours.push(operating_hour);
    }
    data.new_operating_hours = new_operating_hours;
    var edit_operating_hours = [];
    for(i in app_data.edit_operating_hour_ids)
    {
        var id = app_data.edit_operating_hour_ids[i];
        var operating_hour = {};
        operating_hour.id = id;
        operating_hour.day = $('#edit_operating_hour_day_' + id).val();
        operating_hour.open_time = $('#edit_operating_hour_open_time_' + id).val();
        operating_hour.close_time = $('#edit_operating_hour_close_time_' + id).val();
        edit_operating_hours.push(operating_hour);
    }
    data.edit_operating_hours = edit_operating_hours;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/website/update';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            login_display();
            return;
        }
		
		if(error == 1)
		{
			$('#result').html('<span class="text-red">' + message + '</span>');
			return;
		}
		
        $('#result').html('<span class="text-green">' + message + '</span>');
        website_list();
	}
    $.ajax(ajax);
}

function website_remove(website_id)
{
    var html = '';
    html += '<div class="box box-danger">';
    html += '<div class="box-header with-border">';
    html += '<h3 class="box-title">Click Confirm to Delete</h3>';
    html += '</div>';
    html += '<div class="box-body">';
    html += '<div class="btn btn-secondary" onclick="popup_hide()">Cancel</div>';
    html += '<div class="width5"></div>';
    html += '<div class="btn btn-danger" onclick="website_destroy(\'' + website_id + '\')">Confirm</div>';
    html += '<div id="result"></div>';
    html += '</div>';
    html += '</div>';
    popup_show(html);
}

function website_destroy(website_id)
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data.website_id = website_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/website/destroy';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
		var error = response.error;
        var message = response.message;
        
        if(error == 99)
        {
            window.location.href = login_url;
            return;
        }
		
		if(error == 1)
		{
			$('#result').html('<span class="text-red">' + message + '</span>');
			return;
		}
		
        $('#result').html('<span class="text-green">' + message + '</span>');

        popup_hide();
        website_list();
	}
    $.ajax(ajax);
}

function operating_hour_create()
{
    // select last_day
    var last_day = 0;
    for(i in app_data.edit_operating_hour_ids)
    {
        var id = app_data.edit_operating_hour_ids[i];
        var day = $('#edit_operating_hour_day_' + id).val();
        last_day = day;
    }
    for(i in app_data.new_operating_hour_ids)
    {
        var id = app_data.new_operating_hour_ids[i];
        var day = $('#new_operating_hour_day_' + id).val();
        last_day = day;
    }
    last_day++;
    if(last_day > 9)
    {
        last_day = 9;
    }

    // create new open hour
    var operating_hour_id = 0;
    for(i in app_data.new_operating_hour_ids)
    {
        operating_hour_id = app_data.new_operating_hour_ids[i];
    }
    operating_hour_id++;
    app_data.new_operating_hour_ids.push(operating_hour_id);

    var html = '';
    html += '<tr id="new_operating_hour_tr_' + operating_hour_id + '">';

    // days
    html += '<td>';
    html += '<select id="new_operating_hour_day_' + operating_hour_id + '" class="form-control select2" style="width: 100%;">';
    for(i in days)
    {
        var day = days[i];
        var html_selected = '';
        if(i == last_day)
        {
            html_selected = 'selected';
        }
        html += '<option value="' + i + '" ' + html_selected + '>' + day + '</option>';
    }
    html += '</select>';
    html += '</td>';

    // open_times
    html += '<td>';
    html += '<select id="new_operating_hour_open_time_' + operating_hour_id + '" class="form-control select2" style="width: 100%;">';
    for(i in hours)
    {
        var hour = hours[i];
        var html_selected = '';
        if(hour == '9:00 AM')
        {
            html_selected = 'selected';
        }
        html += '<option value="' + hour + '" ' + html_selected + '>' + hour + '</option>';
    }
    html += '</select>';
    html += '</td>';

    // close_times
    html += '<td>';
    html += '<select id="new_operating_hour_close_time_' + operating_hour_id + '" class="form-control select2" style="width: 100%;">';
    for(i in hours)
    {
        var hour = hours[i];
        var html_selected = '';
        if(hour == '6:00 PM')
        {
            html_selected = 'selected';
        }
        html += '<option value="' + hour + '" ' + html_selected + '>' + hour + '</option>';
    }
    html += '</select>';
    html += '</td>';

    // actions
    html += '<td>';
    html += '<div class="btn btn-danger" onclick="operating_hour_destroy(\'' + operating_hour_id + '\', \'new\')"><i class="fa fa-trash"></i></div>';
    html += '</td>';
    html += '</tr>';

	$('#table_operating_hours').append(html);

    var options = {};
    options.minimumResultsForSearch = -1;
    $('.select2').select2(options);
}

function operating_hour_destroy(operating_hour_id, mode)
{
    $('#' + mode + '_operating_hour_tr_' + operating_hour_id).remove();

    if(mode == 'new')
    {
        for(i in app_data.new_operating_hour_ids)
        {
            var new_operating_hour_id = app_data.new_operating_hour_ids[i];
            if(new_operating_hour_id == operating_hour_id)
            {
                app_data.new_operating_hour_ids.splice(i, 1);
            }
        }
    }

    if(mode == 'edit')
    {
        for(i in app_data.edit_operating_hour_ids)
        {
            var edit_operating_hour_id = app_data.edit_operating_hour_ids[i];
            if(edit_operating_hour_id == operating_hour_id)
            {
                app_data.edit_operating_hour_ids.splice(i, 1);
            }
        }
    }

    if(mode == 'new')
    {
        return;
    }

    loading_show();

    var data = {};
    data.api_token = api_token;
    data.operating_hour_id = operating_hour_id;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/operating_hour/destroy';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
	}
    $.ajax(ajax);
}