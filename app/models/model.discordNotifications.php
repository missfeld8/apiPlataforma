<?php

use \DiscordWebhooks\Client;

class DiscordNotifications
{

    // Adicione os links dos seus webhooks aqui
    public $all_channels = [
        "default" => "https://discord.com/api/webhooks/SEU_WEBHOOK_AQUI",
        "errors" => "https://discord.com/api/webhooks/SEU_WEBHOOK_DE_ERROS_AQUI",
        // Adicione outros webhooks conforme necessário
    ];

    public $chosen_channel = "";
    public $username = "";
    public $message = "";

    public function __construct(string $channel_key = 'default')
    {
        if (isset($this->all_channels[$channel_key])) {
            $this->chosen_channel = $this->all_channels[$channel_key];
        } else {
            $this->chosen_channel = $this->all_channels['default'];
        }
        return $this;
    }

    public function username(string $username = '')
    {
        $this->username = '[API] ' . $username;
        return $this;
    }

    public function avatar(string $avatar = '')
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function message($message = '')
    {
        $this->message = $message;
        return $this;
    }

    public function send()
    {
        try {
            (new Client($this->chosen_channel))
                ->username($this->username)
                ->message($this->message)
                ->avatar($this->avatar)
                ->send();
        } catch (Exception $e) {
            // Tratar o erro aqui, se necessário
            echo "Erro ao enviar mensagem para o Discord: " . $e->getMessage();
        }
    }

    public static function sendException($exception)
    {
        $discord_message = DiscordNotifications::build_exception_log($exception);
        (new DiscordNotifications('errors'))
            ->username("Error Notifier")
            ->message($discord_message)
            ->send();
    }

    public static function build_exception_log($exception)
    {
        $message = "=-=-=-=-=-=-=-=-=-=-=\n";
        $message .= "**Exception:** {$exception->getMessage()}\n";
        $message .= "**File:** {$exception->getFile()} (Line {$exception->getLine()})\n";
        return $message;
    }

    public static function build_fatal_error_log($error)
    {
        $message = "=-=-=-=-=-=-=-=-=-=-=\nFatal error API\n";
        $message .= "**Error:** {$error['message']}\n";
        $message .= "**File:** {$error['file']} (Line {$error['line']})\n";
        return $message;
    }
}

// Exemplo de uso
try {
    // Código que pode lançar uma exceção
    throw new Exception("Teste de erro");
} catch (Exception $e) {
    DiscordNotifications::sendException($e);
}
