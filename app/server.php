<?php

require_once __DIR__ . '/vendor/autoload.php';

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use \Firebase\JWT\JWT;

ini_set('display_errors', 'on');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// Alterar timezone pra não ler horário de verão
date_default_timezone_set("America/Recife");

// Fatal Handler
register_shutdown_function("fatal_handler");

function fatal_handler()
{
    $error = error_get_last();
    $extra['title'] = 'Fatal handler API';
    Email::notify_fatal_error($error, $extra);
}

require_once "config.php";
require_once "controllers/init.php";

if ($ENV == "production") {
    $server = new Swoole\HTTP\Server("0.0.0.0", 3000, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

    $server->set([
        'worker_num' => 8,
        'reactor_num' => 2,
        'open_tcp_keepalive' => 1, // enable TCP Keep-Alive check
        'tcp_keepidle' => 3, // check if there is no data for 4s.
        'tcp_keepinterval' => 1, // check if there is data every 1s
        'tcp_keepcount' => 2, // close the connection if there is no data for 5 cycles.
        'max_conn' => 10000,
        'max_request' => 0,
        'enable_reuse_port' => true,
        'buffer_output_size' => 32 * 1024 * 10240,
        'backlog' => 128,
        'package_max_length' => 9999999,
        'heartbeat_idle_time' => 30,
        'heartbeat_check_interval' => 5,
        'log_level' => 0,
        'open_http2_protocol' => true, // Enable HTTP2 protocol
    ]);
} else {
    $server = new Server('0.0.0.0', 8080);
}

$server->on('start', function (Server $server) use ($hostname, $port) {
    echo sprintf('Swoole http server is started at http://%s:%s' . PHP_EOL, $hostname, $port);
});

$server->on('request', static function (Request $request, Response $response) {
    global $dispatcher;
    global $secret;

    $request_method = $request->server['request_method'];
    $request_uri    = $request->server['request_uri'];

    // populate the global state with the request info
    $_SERVER['REQUEST_URI']    = $request_uri;
    $_SERVER['REQUEST_METHOD'] = $request_method;
    $_SERVER['REMOTE_ADDR']    = $request->server['remote_addr'];

    $GLOBALS['authorization'] = $request->header["authorization"];

    $_GET = $request->get ?? [];
    $_FILES = $request->files ?? [];
    $_POST = $request->post ?? [];

    if ($request_method === 'POST' && $request->header['content-type'] === 'application/json') {
        if (!$_POST || count($_POST) == 0) {
            $body = $request->rawContent();
            $_POST = empty($body) ? [] : json_decode($body, true);
        }
    } else {
        $_POST = $request->post ?? [];
    }

    // global content type for our responses
    $response->header('Content-Type', 'application/json');
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, PATCH, DELETE');
    $response->header('Access-Control-Allow-Headers', '*');
    $response->header('Access-Control-Allow-Credentials', 'true');

    $result = handleRequest($dispatcher, $request_method, $request_uri);

    // write the JSON string out
    $response->end(json_encode($result));
});

$server->start();
