//get environment
var redis_port = '127.0.0.1';
var redis_server = '6379';
var nodejs_port = '3000';
var protocol = 'http';
var categories = ['visitor', 'admin'];

//express js
var express = require('express');
var app = express();
app.use(function(req, res, next)
{
	res.header('Access-Control-Allow-Origin', '*.luiquanfu.com');
	res.header('Access-Control-Allow-Credentials', true);
	res.header('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE');
	res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
	next();
});

//health check
app.get('/status', function(req, res)
{
	res.send('ok');
});

//total statistic
app.get('/total', function(req, res)
{
    var html = '';
    for(var channel in websockets)
    {
		html += '<br>' + channel + ' = ';
		var total = 0;
        for(var socket in websockets[channel])
    	{
			for(var random in websockets[channel][socket])
    		{
				total++;
			}
		}
		html += total;
    }
	res.send(html);
});

//setup nodejs server
var websockets = {};
var fs = require('fs');
var options = {
	key: fs.readFileSync('socket.key'),
	cert: fs.readFileSync('socket.crt')
}
var server;
if(protocol == 'http')
{
	server = require('http').Server(app);
}
if(protocol == 'https')
{
	server = require('https').createServer(options, app);
}
var io = require('socket.io')(server);
io.sockets.on('connection', function (socket)
{
	//socket initialize
	socket.on('initialize', function(data)
	{
		var socket_id = data.socket_id;
		var random_id = data.random_id;
		var channel = data.channel;

		if(typeof(websockets[channel]) === 'undefined')
		{
			websockets[channel] = {};
		}
		
		if(typeof(websockets[channel][socket_id]) === 'undefined')
		{
			websockets[channel][socket_id] = {};
		}
		
		if(typeof(websockets[channel][socket_id][random_id]) === 'undefined')
		{
			socket.socket_id = socket_id;
			socket.random_id = random_id;
			socket.channel = channel;
			websockets[channel][socket_id][random_id] = socket;
			console.log(channel + ' ' + socket_id + ' (' + random_id + ') initialize');
		}
	});

	//socket disconnect
	socket.on('disconnect', function()
	{
		var socket_id = socket.socket_id;
		var random_id = socket.random_id;
		var channel = socket.channel;
		
		if(typeof(websockets[channel]) !== 'undefined')
		if(typeof(websockets[channel][socket_id]) !== 'undefined')
		if(typeof(websockets[channel][socket_id][random_id]) !== 'undefined')
		{
			delete websockets[channel][socket_id][random_id];
			console.log(channel + ' ' + socket_id + ' (' + random_id + ') disconnect');
		}
	});
});

//subscribe redis
categories.forEach(function(category)
{
    var ioredis = require('ioredis');
	var redis = new ioredis(redis_port, redis_server);
    redis.subscribe(category);
    redis.on('message', function(channel, data)
    {
        // console.log(data);
		data = JSON.parse(data);
		var socket_id = data.socket_id;
		if(typeof(websockets[channel][socket_id]) !== 'undefined')
		{
			for(var random_id in websockets[channel][socket_id])
			{
				console.log(channel + ' ' + socket_id + ' (' + random_id + ') ' + data.action);
				websockets[channel][socket_id][random_id].emit('message', data);
			}
		}
    });
});

server.listen(nodejs_port, function()
{
	console.log('Server listen ' + protocol + ' ' + nodejs_port);
});