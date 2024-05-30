<?php

function get_hello(array $vars)
{

    $auth = Auth::check(0);
    if ($auth != 1) return $auth;

    return [
        'status'     => 200,
        'total_rows' => 1,
        'rows'       => ["hello" => "Hi there, worked :)"],
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

function post_lat_lng(array $vars)
{

    $auth = Auth::check(0);
    if ($auth != 1) return $auth;

    if ($GLOBALS[client_id] == null) {
        return [
            'status'  => 401,
            'message' => 'This endpoint cant be accessed by admin token.',
            'created_at' => date("Y-m-d H:i:s"),
            'rControl' => rand(1, 999)
        ];
    }

    $chave_api = "AIzaSyC_DMlD9x9MP-jRUvu6EVRg3skI2fEI9CY";

    $endereco_codificado = urlencode($_POST[address]);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$endereco_codificado}&key={$chave_api}";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data["status"] === "OK") {
        // Extrair a latitude e a longitude
        $latitude = $data["results"][0]["geometry"]["location"]["lat"];
        $longitude = $data["results"][0]["geometry"]["location"]["lng"];

        return array(
            "latitude" => $latitude,
            "longitude" => $longitude,
            "address" => $_POST[address],
            "address_codified" => $endereco_codificado,
            "data" => $data
        );
    } else {
        echo "Não foi possível obter a latitude e longitude para o endereço informado.";

        return array(
            "address" => $_POST[address],
            "address_codified" => $endereco_codificado,
            "latitude" => null,
            "longitude" => null,
            "error" => $data
        );
    }
}
