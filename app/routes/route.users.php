<?php


function post_create_user(array $vars) {
    if (!isset($_POST['first_name']) || 
        !isset($_POST['last_name']) || 
        !isset($_POST['email']) || 
        !isset($_POST['password']) || 
        !isset($_POST['city']) || 
        !isset($_POST['phone']) || 
        !isset($_POST['address']) || 
        !isset($_POST['account'])) {
        return [
            'status' => 400,
            'message' => 'Uma ou mais variáveis POST estão faltando.',
            'created_at' => date("Y-m-d H:i:s"),
            'rControl' => rand(1, 999)
        ];
    }

    $auth = Auth::check(1);
    if ($auth != 1) return $auth;

    $user_created = Users::created_user(
        $_POST['first_name'], 
        $_POST['last_name'], 
        $_POST['email'], 
        $_POST['password'], 
        $_POST['city'], 
        $_POST['phone'], 
        $_POST['address'], 
        $_POST['account']
    );

    if (!$user_created['success']) {
        return [
            'status' => 500,
            'message' => 'Erro ao criar usuário: ' . $user_created['message'],
            'created_at' => date("Y-m-d H:i:s"),
            'rControl' => rand(1, 999)
        ];
    }

    return [
        'status' => 200,
        'message' => 'Usuário criado com sucesso!',
        'created_at' => date("Y-m-d H:i:s"),
        'rControl' => rand(1, 999)
    ];
}


function get_list_users() {
    $auth = Auth::check(1);
    if ($auth != 1) return $auth;

    // Aqui você chamaria a função que realmente lista os usuários
    // Por exemplo, $data = Client::list_users();

    return [
        'status' => 200,
        'rows' => count($data),
        'result' => $data,
        'created_at' => date("Y-m-d H:i:s"),
        'rControl' => rand(1, 999)
    ];
}

function post_update_user(array $vars) {
    $auth = Auth::check(1);
    if ($auth != 1) return $auth;

    if (is_object($_POST)) {
        $_POST = (array) $_POST;
    }

    // Valida as variáveis de entrada
    if (!isset($vars['id']) || !is_numeric($vars['id'])) {
        return [
            'status' => 400,
            'message' => 'Invalid user ID.',
            'created_at' => date("Y-m-d H:i:s"),
            'rControl' => rand(1, 999)
        ];
    }

    if (!$_POST['name'] || !$_POST['phone'] || !$_POST['address'] || !$_POST['account']) {
        return [
            'status' => 400,
            'message' => 'One or more POST vars are missing.',
            'created_at' => date("Y-m-d H:i:s"),
            'rControl' => rand(1, 999)
        ];
    }

    // Aqui você chamaria a função que realmente atualiza o usuário
    // Por exemplo, Client::update_user($vars['id'], $_POST);

    return [
        'status' => 200,
        'message' => 'User updated successfully.',
        'created_at' => date("Y-m-d H:i:s"),
        'rControl' => rand(1, 999)
    ];
}

function post_delete_user(array $vars) {
    $auth = Auth::check(1);
    if ($auth != 1) return $auth;

    // Valida as variáveis de entrada
    if (!isset($vars['id']) || !is_numeric($vars['id'])) {
        return [
            'status' => 400,
            'message' => 'Invalid user ID.',
            'created_at' => date("Y-m-d H:i:s"),
            'rControl' => rand(1, 999)
        ];
    }

    // Aqui você chamaria a função que realmente exclui o usuário
    // Por exemplo, Client::delete_user($vars['id']);

    return [
        'status' => 200,
        'message' => 'User deleted successfully.',
        'created_at' => date("Y-m-d H:i:s"),
        'rControl' => rand(1, 999)
    ];
}
?>
