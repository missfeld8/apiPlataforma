<?php

include_once 'config.php';

class Base {
    public static $client_connection_pool = [];

    public static function connect($mustBePai = false) {
        global $db;

        $conected = false;
        $try = 0;
        $maxTries = 5;

        do {
            try {
                if (!isset($db['host'])) $db['host'] = "localhost";
                if (!isset($db['base'])) $db['base'] = "church_1";
                if (!isset($db['user'])) $db['user'] = "root";
                if (!isset($db['port'])) $db['port'] = "3306";

                Swoole\Runtime::enableCoroutine();

                $dsn = "mysql:host={$db['host']};dbname={$db['base']};port={$db['port']}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false,
                ];
                
                $base = new PDO($dsn, $db['user'], $db['pass'], $options);

                if ($base) {
                    $base->exec('SET NAMES utf8');
                    $base->exec('SET CHARACTER SET utf8');
                    $base->exec('SET LC_TIME_NAMES = "pt_BR"');
                    $base->exec("SET session time_zone = '-3:00'");
                    $base->exec("SET time_zone = '-3:00'");
                    $conected = true;
                    $tryAgain = false;
                } else {
                    if ($try < $maxTries) {
                        sleep(1);
                        $tryAgain = true;
                    } else {
                        $tryAgain = false;
                    }
                    $try++;
                }
            } catch (PDOException $e) {
                echo "OCORREU ERRO:\n";
                echo $e->getMessage();

                if ($try < $maxTries) {
                    sleep(1);
                    $tryAgain = true;
                } else {
                    $tryAgain = false;
                }
                $try++;
            }
        } while ($tryAgain);

        if (!$conected) {
            echo "(tentou conectar e não deu)";
            exit;
        } else {
            return $base;
        }
    }
}

