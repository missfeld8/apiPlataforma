<?php
use FastRoute\RouteCollector;

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // Hello
    $r->addRoute('GET', '/hello', 'get_hello');
    $r->addRoute('GET', '/ping', 'get_ping');

    //users
    $r->addRoute('POST', '/create_user', 'post_create_user');
});

function handleRequest($dispatcher, string $request_method, string $request_uri)
{
    list($code, $handler, $vars) = $dispatcher->dispatch($request_method, $request_uri);

    switch ($code) {
        case FastRoute\Dispatcher::NOT_FOUND:
            $result = [
                'status' => 404,
                'message' => 'Not Found',
                'errors' => [
                    sprintf('The URI "%s" was not found', $request_uri)
                ]
            ];
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $handler;
            $result = [
                'status' => 405,
                'message' => 'Method Not Allowed',
                'errors' => [
                    sprintf('Method "%s" is not allowed', $request_method)
                ]
            ];
            break;
        case FastRoute\Dispatcher::FOUND:
            $result = call_user_func($handler, $vars);
            break;
    }

    return $result;
}
