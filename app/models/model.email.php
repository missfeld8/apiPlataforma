<?php

class Email
{

    public static function getEmailCredentials($church_id = null)
    {
        // Simplesmente retornando credenciais padrão sem acessar o banco de dados
        $default_key = getenv("AMAZON_KEY");
        $default_secret = getenv("AMAZON_SECRET");
        $default_region = 'us-west-2';

        $credential = [
            'service' => "aws_ses",
            'key' => $default_key,
            'secret' => $default_secret,
            'region' => $default_region,
        ];

        return $credential;
    }

    public static function send_smtp_mail($to, $subject, $msg, $church_id = null)
    {
        // Criando uma instância de PHPMailer sem realmente enviar e-mails
        $mail = new PHPMailer(true);

        if (!isset($church_id)) {
            $church_id = $GLOBALS['church_id'];
        }

        $credential = Email::getEmailCredentials($church_id);

        // Configurando o PHPMailer
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->IsSMTP();
        $mail->SMTPDebug  = 0;

        // Retornando imediatamente 'success' sem enviar e-mails
        return [
            'status' => 'success',
            'send' => true,
        ];
    }
}
