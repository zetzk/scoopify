<?php

use src\support\Sessions;

/**
 * Its responsible for get a session from index 'old'
 *
 * @param string $key
 * @return mixed
 */
function old(string $key)
{
    if (isset($_SESSION['old']) and isset($_SESSION['old'][$key]))
        return $_SESSION['old'][$key];

    return null;
}



/**
 * Its responsible for return if an index exist in 'isWrong' from $_SESSION
 *
 * @param string $key
 * @return mixed
 */
function isWrong(string $key)
{
    $sessionIsWrong = isset($_SESSION['isWrong']);
    if (!$sessionIsWrong)
        return null;
    else
        return isset($_SESSION['isWrong'][$key]);
}

/**
 * Its responsible for return a text from 'isWrong' in $_SESSION
 *
 * @param string $key
 * @return mixed
 */
function isWrongText(string $key)
{
    $sessionIsWrong = isset($_SESSION['isWrong']);
    if (!$sessionIsWrong)
        return null;
    else
        return isset($_SESSION['isWrong'][$key]) ? $_SESSION['isWrong'][$key] : null;
}


/**
 * Its responsible for forget multiples sessions
 *
 * @param array<int, string> $sessions
 * @return void
 */
function forgetSessions($sessions = []): void
{
    foreach ($sessions as $index => $session) {
        if (isset($_SESSION[$session]))
            unset($_SESSION[$session]);
    }
}

/**
 * Its responsible for return 'is-invalid' or 'is-valid' when isWrong contais a key
 *
 * @param string $key
 * @return mixed
 */
function applyWrong(string $key)
{
    if (Sessions::get('isWrong')) {
        $keyWrong = isWrong($key) ? 'is-invalid' : 'is-valid';
        return $keyWrong;
    }
    return null;
}

/**
 * Its responsible for return the text from isWrongText
 *
 * @param string $key
 * @return mixed
 */
function applyWrongText(string $key)
{
    if (Sessions::get('isWrong'))
        return isWrongText($key);
    return null;
}

/**
 * Its verify if session 'old' exists
 *
 * @return boolean
 */
function hasOld()
{
    return Sessions::get('old');
}

/**
 * Its apply old in checkbox simple
 *
 * @param string $key
 * @return string
 */
function applyOldCheck(string $key): string
{
    $isChecked = '';
    if (hasOld())
        $isChecked = old($key) ? 'checked' : '';
    else
        $isChecked = 'checked';
    return $isChecked;
}


/**
 * Its apply old in simple select
 *
 * @param mixed $value
 * @param string $key
 * @return mixed
 */
function applyOldSelectSimple($value, $key)
{
    if (!hasOld())
        return null;
    else
        return ($value === old($key)) ? 'selected' : '';
}

/**
 * Its responsible for apply old in input
 *
 * @param string $key
 * @return mixed
 */
function applyOldInput(string $key)
{
    if (hasOld())
        return old($key);
    else
        return null;
}


function fromGet(string $k)
{
    $r = $_GET[$k] ?? null;

    if (empty($r))
        return null;

    return $_GET[$k] ?? null;
}


function getStatus(string $statusCode)
{
    $status = [
        "A" => "Em andamento",
        "C" => "Cancelado",
        "R" => "Rejeitado",
        "P" => "Finalizado",
    ];
    return $status[$statusCode] ?? null;
}





/**
 * Returns the first and last name from a full name string.
 *
 * @param string $fullName The full name of the person.
 * @return string The first and last name concatenated.
 */
function getFirstAndLastName(string $fullName): string
{
    // Clean and normalize whitespace
    $cleanName = trim(preg_replace('/\s+/', ' ', $fullName));

    // Split the name into parts
    $nameParts = explode(' ', $cleanName);

    // If there's only one name, return it
    if (count($nameParts) === 1) {
        return ucfirst(strtolower($nameParts[0]));
    }

    $firstName = ucfirst(strtolower($nameParts[0]));
    $lastName = ucfirst(strtolower(end($nameParts)));

    return "{$firstName} {$lastName}";
}


function user()
{
    if (isset($_SESSION["user"]))
        return (object) $_SESSION["user"];
    return null;
}



function dbEnv(string $file): array
{
    return is_remote() ? db_remote($file) : db_local();
}


/**
 * obtem os parâmetros para conexao remota com os servidores de teste e produção
 * @param string $file
 * @return array
 */
function db_remote(string $file): array
{
    $company = path()->get_company();
    $map = [
        "DB_FILE_INTRANET" => "/var/www/htdocs/{$company}/includes/configuracoes.php",
        "DB_FILE_WORKFLOW" => "/var/www/htdocs/{$company}/includes/sqlserver_wf_prod.php",
        "DB_FILE_INFORMIX" => "/var/www/htdocs/{$company}/includes/informix.php",
        "DB_FILE_SENIOR" => "/var/www/htdocs/{$company}/includes/sqlserver_senior.php",
        "DB_FILE_SENIOR_GBMX" => "/var/www/htdocs/{$company}/includes/senior_gbmx.php"
    ];
    return require $map[$file];
}

/**
 * obtém os parâmetros para conexão local (exemplo está usando o docker)
 * @return array{DB_HOST: string, DB_NAME: string, DB_PASSWORD: string, DB_TYPE: string, DB_USER: string}
 */
function db_local(): array
{
    return [
        "DB_TYPE" => "mysql",
        "DB_HOST" => "mysql-db",
        "DB_USER" => "root",
        "DB_PASSWORD" => "root",
        "DB_NAME" => "ammx",
    ];
}


/**
 * verifica se a execução do app está em um servidor remoto
 * @return bool
 */
function is_remote(): bool
{
    return in_array(
        path()->get_host(),
        ["http://172.30.0.59", "http://172.30.0.94"]
    );
}