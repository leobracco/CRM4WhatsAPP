<?php
session_start();

if (!isset($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"])) {
    error_log("⚠️ ERROR: APP_INCLUDEPATH no está definido en SESSION.");
    die("ERROR: APP_INCLUDEPATH no está definido.");
}

require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"] . "/class.objdb.php");

/**
 * Clase Usuarios
 */
class usuarios extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'usuarios';
        $this->name = $this->table;
        $this->fields = ['idusuario', 'username', 'password', 'email', 'nombre', 'apellido', 'telefono', 'celular', 'direccion', 'dni'];
        $this->join('grupos', new MultipleJoin('usuarios_grupos', 'idusuario'));
        $this->join('usuarios', new MultipleJoin('usuarios_cuentas', 'idusuario'));
        $this->join('usuarios', new MultipleJoin('usuarios_cuenta', 'idusuario'));
        $this->initialize();
    }

    function ID_field() {
        return 'idusuario';
    }

    function validateUser($email, $password) {
        $sql = "SELECT " . $this->ID_field() . " FROM usuarios 
                WHERE (email='$email' OR username='$email') 
                AND password='$password'";

        $this->db->exec($sql);
        if ($this->debug) error_log("Ejecutando SQL: $sql (Error=" . $this->db->Error . ")");

        $row = $this->db->getrow();
        if ($row && isset($row[$this->ID_field()])) {
            $this->fetch($row[$this->ID_field()]);
            return true;
        } else {
            error_log("ERROR>> validateUser(): Usuario no encontrado con email=$email");
            return false;
        }
    }
}

/**
 * Clase Permisos
 */
class permisos extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'permisos';
        $this->name = $this->table;
        $this->fields = ['idpermiso', 'nombre', 'descripcion'];
        $this->join('grupos', new MultipleJoin('grupos_permisos', 'idpermiso'));
        $this->initialize();
    }

    function ID_field() {
        return 'idpermiso';
    }
}

/**
 * Clase Grupos
 */
class grupos extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'grupos';
        $this->name = $this->table;
        $this->fields = ['idgrupo', 'nombre'];
        $this->join('permisos', new MultipleJoin('grupos_permisos', 'idgrupo'));
        $this->join('usuarios', new MultipleJoin('usuarios_grupos', 'idgrupo'));
        $this->initialize();
    }

    function ID_field() {
        return 'idgrupo';
    }
}

/**
 * Clase Usuarios_Grupos
 */
class usuarios_grupos extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'usuarios_grupos';
        $this->name = $this->table;
        $this->fields = ['id', 'idusuario', 'idgrupo'];
        $this->join('grupos', new ForeignKeys('grupos', 'idgrupo'));
        $this->join('usuarios', new ForeignKeys('usuarios', 'idusuario'));
        $this->initialize();
    }
}

/**
 * Clase Grupos_Permisos
 */
class grupos_permisos extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'grupos_permisos';
        $this->name = $this->table;
        $this->fields = ['id', 'idpermiso', 'idgrupo'];
        $this->join('grupos', new ForeignKeys('grupos', 'idgrupo'));
        $this->join('permisos', new ForeignKeys('permisos', 'idpermiso'));
        $this->initialize();
    }
}

/**
 * Clase Eventlog
 */
class eventlog extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'eventlog';
        $this->name = $this->table;
        $this->fields = ['id', 'timestamp', 'evento', 'texto'];
        $this->initialize();
    }

    public function &fetch($ID = null) {
        $values = parent::fetch($ID);
        if ($values) {
            $values['timestamp.nice'] = date("d-m-Y", strtotime($values['timestamp']));
        }
        return $values;
    }
}

/**
 * Clase Clientes
 */
class clientes extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'clientes';
        $this->name = $this->table;
        $this->fields = ['idcliente', 'nombre', 'apellido', 'email', 'telefono', 'direccion'];
        $this->initialize();
    }

    function ID_field() {
        return 'idcliente';
    }
}

/**
 * Clase Chats
 */
class chats extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'chats';
        $this->name = $this->table;
        $this->fields = ['idchat', 'idcliente', 'mensaje', 'original_idchat', 'timestamp', 'visto', 'sender'];
        $this->initialize();
    }

    function ID_field() {
        return 'idchat';
    }
}

/**
 * Clase Productos
 */
class productos extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'productos';
        $this->name = $this->table;
        $this->fields = ['idproducto', 'nombre', 'idtipo', 'stock', 'costo', 'descripcion', 'imagenes', 'peso', 'ancho', 'alto'];
        $this->initialize();
    }

    function ID_field() {
        return 'idproducto';
    }
}

/**
 * Clase Insumos
 */
class insumos extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'insumos';
        $this->name = $this->table;
        $this->fields = ['idinsumo', 'nombre', 'stock', 'costo', 'usa_para_precio_mayorista'];
        $this->initialize();
    }

    function ID_field() {
        return 'idinsumo';
    }
}

/**
 * Clase Producto_Insumo
 */
class producto_insumo extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'producto_insumo';
        $this->name = $this->table;
        $this->fields = ['id', 'idproducto', 'idinsumo', 'cantidad'];
        $this->initialize();
    }

    function ID_field() {
        return 'id';
    }
}

/**
 * Clase Lista_Precios
 */
class lista_precios extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'lista_precios';
        $this->name = $this->table;
        $this->fields = ['idlista', 'nombre', 'descripcion'];
        $this->initialize();
    }

    function ID_field() {
        return 'idlista';
    }
}

/**
 * Función para agregar eventos al log
 */
function addLog($evento, $texto) {
    $dbCMS = new objdb();
    $dbCMS->connect();
    
    $tmp = new eventlog();
    $tmp->db = $dbCMS;
    
    $tmp->field("timestamp", date("Y-m-d H:i:s"));
    $tmp->field('evento', $evento);
    $tmp->field('texto', $texto);
    $tmp->store();
}
?>
