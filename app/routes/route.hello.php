<?php

function get_hello(array $vars)
{

    $auth = Auth::check(0);
    if ($auth != 1) return $auth;

    return [
        'status'     => 200,
        'total_rows' => 1,
        'rows'       => ["hello" => "Hello world - o servidor ta funcionando :)"],
        'request'    => $vars,
        'created_at' => date("Y-m-d H:i:s"),
        'rControl'   => rand(1, 999)
    ];
}

function get_ping(array $vars)
{

    return [
        'status'     => 200,
        'total_rows' => 1,
        'result'     => "pong",
        'request'    => $vars,
        'created_at' => date("Y-m-d H:i:s"),
        'rControl'   => rand(1, 999)
    ];
}


    

