<?php

class Auth
{

    public static function check()
    {
        $token = $GLOBALS['authorization'];
        $token = explode(" ", $token);

        $type  = $token[0];
        $token = $token[1];

        if (!$type || $type != "Bearer") {
            return [
                'status'  => 401,
                'message' => 'Authentication type not authorized.',
                'created_at' => date("Y-m-d H:i:s"),
                'rControl' => rand(1, 999)
            ];
        }

        // Verifica se há um token presente
        if (!$token) {
            return [
                'status'  => 401,
                'message' => 'Access not authorized.',
                'created_at' => date("Y-m-d H:i:s"),
                'rControl' => rand(1, 999)
            ];
        }

        // Adiciona lógica adicional de verificação de token conforme necessário
        // Adiciona a verificação do token "a1c2b3"
        if ($token !== "a1c2b3") {
            return [
                'status'  => 401,
                'message' => 'Invalid token.',
                'created_at' => date("Y-m-d H:i:s"),
                'rControl' => rand(1, 999)
            ];
        }

        return 1;
    }
}

// Adiciona o token "a1c2b3" para autorizar
$GLOBALS['authorization'] = "Bearer a1c2b3";
