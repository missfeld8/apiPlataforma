<?php

class Users {
  public static function created_user($first_name, $last_name, $email, $password, $city, $phone, $address, $account) {
    $db = Base::connect();

    $first_name  = strip_tags(trim($first_name));
    $last_name   = strip_tags(trim($last_name));
    $email       = strip_tags(trim($email));
    $password    = strip_tags(trim($password));
    $city        = strip_tags(trim($city));
    $phone       = strip_tags(trim($phone));
    $address     = strip_tags(trim($address));
    $account     = strip_tags(trim($account));

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if (!$first_name || !$last_name || !$email) {
        return [
            'success' => false,
            'email_in_use' => false,
            'message' => 'Falha ao criar usuário, faltando campos obrigatórios'
        ];
    }

    try {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) >= 1) {
            return [
                'success' => false,
                'email_in_use' => true,
                'message' => 'Email em uso'
            ];
        }

        $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password, city, phone, address, account) VALUES (:first_name, :last_name, :email, :password, :city, :phone, :address, :account)");
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':city' => $city,
            ':phone' => $phone,
            ':address' => $address,
            ':account' => $account
        ]);
    } catch (PDOException $e) {
        return [
            'success' => false,
            'email_in_use' => false,
            'message' => 'Erro ao inserir usuário no banco de dados: ' . $e->getMessage()
        ];
    }

    $db = null;

    return [
        'success' => true
    ];
}
}
