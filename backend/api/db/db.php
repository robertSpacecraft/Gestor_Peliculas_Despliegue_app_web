<?php

//Guardo los datos de conexión de la base de datos en un fichero YML
function getDBConfig()
{
    $dbFileConfig = dirname(__FILE__) . "/../../dbconfiguration.yml";

    // Valores por defecto o respaldo del YML
    $config = [
        "dbname" => getenv('DB_NAME'),
        "host" => getenv('DB_HOST'),
        "user" => getenv('DB_USER'),
        "pass" => getenv('DB_PASS')
    ];

    // Si las variables de entorno no están configuradas, intentar cargar desde YML (Compatibilidad hacia atrás/Configuración local genérica)
    if (!$config["dbname"] && file_exists($dbFileConfig) && function_exists('yaml_parse_file')) {
        $configYML = yaml_parse_file($dbFileConfig);
        if ($configYML) {
            $config["dbname"] = $configYML["dbname"];
            $config["host"] = $configYML["ip"]; // Mapeando 'ip' del YML a 'host'
            $config["user"] = $configYML["user"];
            $config["pass"] = $configYML["pass"];
        }
    }

    $cad = sprintf("mysql:dbname=%s;host=%s;charset=UTF8", $config["dbname"], $config["host"]);

    $result = array(
        "cad" => $cad,
        "user" => $config["user"],
        "pass" => $config["pass"]
    );

    return $result;
}

function getDBConnection()
{
    try {
        $res = getDBConfig();

        $bd = new PDO($res["cad"], $res["user"], $res["pass"]);

        return $bd;
    } catch (PDOException $e) {
        return null;
    }
}

/* ------------ LOGIN --------------- */
function checkLogin($email, $password)
{
    try {
        $bd = getDBConnection();

        if (!is_null($bd)) {
            $sqlPrepared = $bd->prepare("SELECT email from user WHERE email = :email AND password = :password ");
            $params = array(
                ':email' => $email,
                ':password' => $password
            );
            $sqlPrepared->execute($params);

            return $sqlPrepared->rowCount() > 0 ? true : false;
        } else
            return $bd;

    } catch (PDOException $e) {
        return null;
    }
}


/* ------------ PELÍCULAS  --------------- */
function getFilmsDB()
{
    try {
        $bd = getDBConnection();

        if (!is_null($bd)) {
            $sqlPrepared = $bd->prepare("SELECT id,name, director, classification from film");
            $sqlPrepared->execute();

            return $sqlPrepared->fetchAll(PDO::FETCH_ASSOC);
        } else
            return $bd;

    } catch (PDOException $e) {
        return null;
    }
}

function getFilmDB($id)
{
    try {
        $bd = getDBConnection();

        if (!is_null($bd)) {
            $sqlPrepared = $bd->prepare("SELECT * from film WHERE id = :id");
            $params = array(
                ':id' => $id,
            );
            $sqlPrepared->execute($params);

            return $sqlPrepared->fetchAll(PDO::FETCH_ASSOC);
        } else
            return $bd;

    } catch (PDOException $e) {
        return null;
    }
}

function addFilmDB($data)
{
    try {
        $bd = getDBConnection();

        if (!is_null($bd)) {

            $sqlPrepared = $bd->prepare("
                INSERT INTO film (name,director,classification,img,plot)
                VALUES (:name,:director,:classification,:img,:plot)
            ");

            $params = array(
                ':name' => $data["name"],
                ':director' => $data["director"],
                ':classification' => $data["classification"],
                ':img' => $data["img"],
                ':plot' => $data["plot"]
            );

            return $sqlPrepared->execute($params);

            return $sqlPrepared->rowCount();// verificar filas afectadas usando rowCount

        } else
            return $bd;

    } catch (PDOException $e) {
        return null;
    }
}


function updateFilmDB($data)
{
    try {
        $bd = getDBConnection();

        if (!is_null($bd)) {

            $sqlPrepared = $bd->prepare("
                UPDATE film
                SET name=:name,director=:director,classification=:classification,img=:img,plot=:plot
                WHERE id=:id
            ");

            $params = array(
                ':name' => $data["name"],
                ':director' => $data["director"],
                ':classification' => $data["classification"],
                ':img' => $data["img"],
                ':plot' => $data["plot"],
                ':id' => $data["id"]
            );

            return $sqlPrepared->execute($params);

            return $sqlPrepared->rowCount();// verificar filas afectadas usando rowCount

        } else
            return $bd;

    } catch (PDOException $e) {
        return null;
    }
}

function deleteFilmDB($id)
{
    try {
        $bd = getDBConnection();

        if (!is_null($bd)) {

            $sqlPrepared = $bd->prepare("
                DELETE FROM film
                WHERE id=:id
            ");

            $params = array(
                ':id' => $id
            );

            return $sqlPrepared->execute($params);

            return $sqlPrepared->rowCount();// verificar filas afectadas usando rowCount

        } else
            return $bd;

    } catch (PDOException $e) {
        return null;
    }
}