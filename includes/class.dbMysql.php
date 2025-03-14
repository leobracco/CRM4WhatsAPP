<?php
/****Version 1.0.0*****/
class dbMysql {
    var $idConnection = "";
    var $idResult = 0;
    var $isOpen = 0;
    var $Error = "";

    var $Host;
    var $Db;
    var $User;
    var $Password;
    var $Fields;
    var $LastId;

    /* Constructor corregido */
    function __construct($host, $database, $user, $password) {
        $this->Host = $host;
        $this->Db = $database;  // 🔹 Usar siempre `$Db`
        $this->User = $user;
        $this->Password = $password;
    }

    /* Abre una conexión con la base de datos */
    function connect() {
        if (!$this->isOpen) {
            error_log("Intentando conectar con: Host={$this->Host}, Usuario={$this->User}, Password=***** (oculto), Base={$this->Db}");

            if (empty($this->Host) || empty($this->User) || empty($this->Db)) {
                $this->Error = "ERROR>>connect() [Faltan credenciales o base de datos]";
                error_log($this->Error);
                return false;
            }

            $this->idConnection = new mysqli($this->Host, $this->User, $this->Password, $this->Db);

            if ($this->idConnection->connect_error) {
                $this->Error = "ERROR>>connect() [Fallo la conexión: " . $this->idConnection->connect_error . "]";
                error_log($this->Error);
                return false;
            }

            $this->Error = "";
            $this->isOpen = true;
            return true;
        } else {
            $this->Error = "ERROR>>connect() [La conexión ya está abierta]";
            error_log($this->Error);
            return false;
        }
    }

    /* Cierra la conexión */
    function close() {
        if ($this->isOpen) {
            mysqli_close($this->idConnection);
            $this->Error = "";
            $this->isOpen = 0;
            return true;
        } else {
            $this->Error = "ERROR>>close() [La conexión ya está cerrada]";
            error_log($this->Error);
            return false;
        }
    }

    /* Ejecuta una consulta */
    function exec($sql) {
        if (!$this->isOpen || !$this->idConnection) {
            $this->Error = "ERROR>>exec() [La conexión a la base de datos está cerrada o no fue establecida]";
            error_log($this->Error);
            return false;
        }

        if (empty($this->Db)) {
            $this->Error = "ERROR>>exec() [No se ha definido ninguna base de datos]";
            error_log($this->Error);
            return false;
        }

        // Log de la consulta antes de ejecutarla
        error_log("Ejecutando SQL: $sql");

        $this->idResult = $this->idConnection->query($sql);

        if (!$this->idResult) {
            $this->Error = "ERROR>>exec() [Falló la consulta SQL: " . $this->idConnection->error . "]";
            error_log($this->Error);
            return false;
        }

        $this->Error = "";
        return $this->idResult;
    }

    /* Obtiene un registro */
    function getrow() {
        if (!$this->isOpen || !$this->idResult) {
            $this->Error = "ERROR>>getrow() [Debe realizar una consulta primero o la conexión está cerrada]";
            error_log($this->Error);
            return false;
        }

        $this->Fields = mysqli_fetch_assoc($this->idResult);
        return $this->Fields;
    }

    /* Obtiene el valor de un campo */
    function getfield($fieldname) {
        if (!$this->isOpen || !$this->idResult) {
            $this->Error = "ERROR>>getfield() [Debe realizar una consulta primero o la conexión está cerrada]";
            error_log($this->Error);
            return false;
        }

        return isset($this->Fields[$fieldname]) ? $this->Fields[$fieldname] : null;
    }

    /* Obtiene el último ID insertado */
    function lastid() {
        if (!$this->isOpen) {
            $this->Error = "ERROR>>lastid() [La conexión está cerrada]";
            error_log($this->Error);
            return false;
        }

        return mysqli_insert_id($this->idConnection);
    }

    /* Devuelve la cantidad de filas en el resultado */
    function numrows() {
        if (!$this->isOpen || !$this->idResult) {
            $this->Error = "ERROR>>numrows() [Debe realizar una consulta primero o la conexión está cerrada]";
            error_log($this->Error);
            return false;
        }

        return mysqli_num_rows($this->idResult);
    }
}
?>
