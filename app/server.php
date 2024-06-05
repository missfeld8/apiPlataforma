<?php

require_once __DIR__ . '/vendor/autoload.php';

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use \Firebase\JWT\JWT;
use GuzzleHttp\Client;

ini_set('display_errors', 'on');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// date_default_timezone_set("Brazil/East");
// Alterar timezone pra não ler horário de verão
date_default_timezone_set("America/Recife");

// Fatal Handler
register_shutdown_function("fatal_handler");

function fatal_handler()
{
    $error = error_get_last();
    $extra['title'] = 'Fatal handler API';

    $discord_message = DiscordNotifications::build_fatal_error_log($error, $extra);
    send_discord_notification($discord_message, 'errors');
}

function send_discord_notification($message, $type)
{
    $webhook_urls = [
        'errors' => 'https://discord.com/api/webhooks/1245882814935728178/yx_Xpi1jDTlM5skpg1apUSdVq317z4bAuCtjQUNk7LqggWXdAi1pUhqJIHWJWHws0vCp',
        'fatal_errors' => 'https://discord.com/api/webhooks/1246159223079833601/ZhTvIwByuypr_HZL_tl46MgU6Y4ZkkF_JdkAqwCmO1MaSzPBOzQGX1vEagCPZh6S1WBE'
    ];

    $webhook_url = $webhook_urls[$type];

    $client = new Client();
    try {
        $response = $client->post($webhook_url, [
            'json' => [
                'username' => 'Fatal bot',
                'avatar_url' => 'https://cdn.discordapp.com/attachments/1245882616142499912/1246163598364115066/637dc9daee9e5.jpg?ex=665b63ca&is=665a124a&hm=cade30f223c51533f7e8386cae922289fdc850e6ffbb3068f52c49113f285e61&',
                'content' => $message
            ]
        ]);

        if ($response->getStatusCode() !== 204) {
            throw new Exception("Erro ao enviar mensagem para o Discord: " . $response->getStatusCode() . ":" . $response->getBody());
        }
    } catch (Exception $e) {
        echo "Erro ao enviar mensagem para o Discord: " . $e->getMessage();
    }
}

require_once "config.php";
require_once "controllers/init.php";

$server = new Server('0.0.0.0', 8080);

$server->on('start', function (Server $server) use ($hostname, $port) {
    echo sprintf('Swoole http server is started at http://%s:%s' . PHP_EOL, $hostname, $port);
});

$server->on('request', static function (Request $request, Response $response) {
    global $dispatcher;
    global $secret;

    $request_method = $request->server['request_method'];
    $request_uri    = $request->server['request_uri'];

    // Log de debug para verificar as solicitações recebidas
    var_dump("Recebida solicitação $request_method para $request_uri" . PHP_EOL); 

    // populate the global state with the request info
    $_SERVER['REQUEST_URI']    = $request_uri;
    $_SERVER['REQUEST_METHOD'] = $request_method;
    $_SERVER['REMOTE_ADDR']    = $request->server['remote_addr'];

    $GLOBALS['authorization'] = $request->header["authorization"] ?? null;

    $_GET = $request->get ?? [];
    $_FILES = $request->files ?? [];
    $_POST = $request->post ?? [];

    // Log de debug para verificar os dados da solicitação
    var_dump("Dados da solicitação POST: ", $_POST);

    // form-data and x-www-form-urlencoded work out of the box so we handle JSON POST here
    if ($request_method === 'POST' && $request->header['content-type'] === 'application/json') {
        if (!$_POST || count($_POST) == 0) {
            $body = $request->rawContent();
            $_POST = empty($body) ? [] : json_decode($body, true);
        }
    } elseif ($request->header["authorization"]) {
        try {
            $auth = explode(" ", $request->header["authorization"], 4);
            $token = $auth[1];
            $tokenDecoded = JWT::decode($token, $secret, ['HS256']);
            if ($request->post['client_id'] == $tokenDecoded->client_id && $tokenDecoded->d === date("d") && $request->post['event_id'] === $tokenDecoded->event_id) {
                $_POST = $request->post;
            } else {
                $_POST = [];
            }
        } catch (Exception $e) {
            // log error or handle it accordingly
        }
    } else {
        $_POST = $request->post ?? [];
    }

    // global content type for our responses
    $response->header('Content-Type', 'application/json');
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, PATCH, DELETE');
    $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    $response->header('Access-Control-Allow-Credentials', 'true');

    // Handle preflight request
    if ($request_method === 'OPTIONS') {
        $response->status(204);
        $response->end();
        return;
    }

    $result = handleRequest($dispatcher, $request_method, $request_uri);

    // write the JSON string out
    $response->end(json_encode($result));
});

$server->start();
