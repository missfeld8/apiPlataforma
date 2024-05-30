<?php

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, PATCH, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Accept, X-Auth-Token, Origin, Authorization');

// Alterar timezone pra não ler horário de verão
// date_default_timezone_set('America/Sao_Paulo');
date_default_timezone_set("America/Recife");

# ---------
# Load all models
$directories = dir(realpath(dirname(__FILE__) . "/../models/"));
while ($file = $directories->read()) {

    if (strstr($file, "model.") && substr($file, -4, 4) === ".php") {

        require_once($directories->path . "/" . $file);
    }
}

$directories->close();

//Load all routes
$dir = dir(realpath(dirname(__FILE__) . "/../routes/"));
while ($fileName = $dir->read()) {
    if (strstr($fileName, "route") && substr($fileName, -4, 4) == ".php") {
        require_once($dir->path . "/" . $fileName);
    }
}

$dir->close();

function mes($n, $full = 1)
{
    if ($full == 1) {
        switch ($n) {
            case 1:
                return "Janeiro";
                break;
            case 2:
                return "Fevereiro";
                break;
            case 3:
                return "Março";
                break;
            case 4:
                return "Abril";
                break;
            case 5:
                return "Maio";
                break;
            case 6:
                return "Junho";
                break;
            case 7:
                return "Julho";
                break;
            case 8:
                return "Agosto";
                break;
            case 9:
                return "Setembro";
                break;
            case 10:
                return "Outubro";
                break;
            case 11:
                return "Novembro";
                break;
            case 12:
                return "Dezembro";
                break;
        }
    } else {
        switch ($n) {
            case 1:
                return "Jan";
                break;
            case 2:
                return "Fev";
                break;
            case 3:
                return "Mar";
                break;
            case 4:
                return "Abr";
                break;
            case 5:
                return "Mai";
                break;
            case 6:
                return "Jun";
                break;
            case 7:
                return "Jul";
                break;
            case 8:
                return "Ago";
                break;
            case 9:
                return "Set";
                break;
            case 10:
                return "Out";
                break;
            case 11:
                return "Nov";
                break;
            case 12:
                return "Dez";
                break;
        }
    }
}
