<?php

if (!isset($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"])) {
    error_log("⚠️ ERROR: APP_INCLUDEPATH no está definido en SESSION.");
    die("ERROR: APP_INCLUDEPATH no está definido.");
}

require_once $_SESSION["TEMPLATE"]["APP_INCLUDEPATH"] . "/class.objdb.php";

if (
    !file_exists($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"] . "/class.objdb.php")
) {
    die(
        "ERROR>> class.objdb.php no se encuentra en la ruta: " .
            $_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]
    );
} else {
    error_log("DEBUG>> class.objdb.php se incluyó correctamente.");
    if (!class_exists("MultipleJoin")) {
        error_log("ERROR>> La clase MultipleJoin no se ha encontrado.");
    } else {
        error_log("DEBUG>> La clase MultipleJoin está correctamente definida.");
    }
}

/**
 * Clase Usuarios
 */
class usuarios extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "usuarios";
        $this->name = $this->table;
        $this->fields = [
            "idusuario",
            "username",
            "password",
            "email",
            "nombre",
            "apellido",
            "telefono",
            "celular",
            "direccion",
            "dni",
        ];
        $this->join("grupos", new MultipleJoin("usuarios_grupos", "idusuario"));
        $this->join(
            "usuarios",
            new MultipleJoin("usuarios_cuentas", "idusuario")
        );
        $this->join(
            "usuarios",
            new MultipleJoin("usuarios_cuenta", "idusuario")
        );
        $this->initialize();
    }

    function ID_field()
    {
        return "idusuario";
    }

    function validateUser($email, $password)
    {
        $sql =
            "SELECT " .
            $this->ID_field() .
            " FROM usuarios 
                WHERE (email='$email' OR username='$email') 
                AND password='$password'";

        $this->db->exec($sql);
        if ($this->debug) {
            error_log("Ejecutando SQL: $sql (Error=" . $this->db->Error . ")");
        }

        $row = $this->db->getrow();
        if ($row && isset($row[$this->ID_field()])) {
            $this->fetch($row[$this->ID_field()]);
            return true;
        } else {
            error_log(
                "ERROR>> validateUser(): Usuario no encontrado con email=$email"
            );
            return false;
        }
    }
    function checkPermission($nombre = "")
    {
        $sql =
            "SELECT count(*) as permisook FROM usuarios as u, usuarios_grupos as ug, grupos_permisos as gp, permisos as p WHERE u.idusuario=ug.idusuario AND ug.idgrupo=gp.idgrupo AND gp.idpermiso=p.idpermiso AND u.idusuario='" .
            $this->ID() .
            "' AND p.nombre='$nombre'";
        $this->db->exec($sql);
        if ($this->debug) {
            print "$sql (Error=" . $this->db->Error . ")";
        }

        if ($this->db->getrow() && $this->db->Fields["permisook"] > 0) {
            return 1;
        } else {
            return 0;
        }
    }
}

/**
 * Clase Permisos
 */
class permisos extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "permisos";
        $this->name = $this->table;
        $this->fields = ["idpermiso", "nombre", "descripcion"];
        $this->join("grupos", new MultipleJoin("grupos_permisos", "idpermiso"));
        $this->initialize();
    }

    function ID_field()
    {
        return "idpermiso";
    }
}

/**
 * Clase Grupos
 */
class grupos extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "grupos";
        $this->name = $this->table;
        $this->fields = ["idgrupo", "nombre"];
        $this->join("permisos", new MultipleJoin("grupos_permisos", "idgrupo"));
        $this->join("usuarios", new MultipleJoin("usuarios_grupos", "idgrupo"));
        $this->initialize();
    }

    function ID_field()
    {
        return "idgrupo";
    }
}

/**
 * Clase Usuarios_Grupos
 */
class usuarios_grupos extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "usuarios_grupos";
        $this->name = $this->table;
        $this->fields = ["id", "idusuario", "idgrupo"];
        $this->join("grupos", new ForeignKeys("grupos", "idgrupo"));
        $this->join("usuarios", new ForeignKeys("usuarios", "idusuario"));
        $this->initialize();
    }
}

/**
 * Clase Grupos_Permisos
 */
class grupos_permisos extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "grupos_permisos";
        $this->name = $this->table;
        $this->fields = ["id", "idpermiso", "idgrupo"];
        $this->join("grupos", new ForeignKeys("grupos", "idgrupo"));
        $this->join("permisos", new ForeignKeys("permisos", "idpermiso"));
        $this->initialize();
    }
}

/**
 * Clase Eventlog
 */
class eventlog extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "eventlog";
        $this->name = $this->table;
        $this->fields = ["id", "timestamp", "evento", "texto"];
        $this->initialize();
    }

    // ✅ Método para asignar la base de datos correctamente
    public function setDB($db)
    {
        $this->db = $db;
    }

    public function &fetch($ID = null)
    {
        $values = parent::fetch($ID);
        $this->values["timestamp.nice"] = date(
            "d-m-Y",
            strtotime($this->values["timestamp"])
        );
        return $this->values;
    }
}

/**
 * Clase Clientes
 */
class clientes extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "clientes";
        $this->name = $this->table;
        $this->fields = [
            "idcliente",
            "nombre",
            "apellido",
            "email",
            "telefono",
            "direccion",
        ];
        $this->initialize();
    }

    function ID_field()
    {
        return "idcliente";
    }
    /**
     * Obtener la lista de clientes con su último mensaje en el chat, ordenados del más reciente al más antiguo.
     */
    function obtenerClientesConMensajesRecientes()
    {
        $sql = "
            SELECT c.idcliente, cl.nombre, cl.apellido, cl.telefono, cl.email, cl.direccion, 
                   c.mensaje, c.timestamp
            FROM clientes cl
            LEFT JOIN (
                SELECT idcliente, mensaje, timestamp
                FROM chats
                WHERE timestamp = (SELECT MAX(timestamp) FROM chats WHERE chats.idcliente = c.idcliente)
            ) c ON cl.idcliente = c.idcliente
            ORDER BY c.timestamp DESC;
        ";

        $this->db->exec($sql);
        return $this->db->fetchall();
    }
}

/**
 * Clase Chats
 */
class chats extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "chats";
        $this->name = $this->table;
        $this->fields = [
            "idchat",
            "idcliente",
            "mensaje",
            "original_idchat",
            "timestamp",
            "visto",
            "sender",
            "tipo",
            "archivo"
        ];
        $this->initialize();
    }

    function ID_field()
    {
        return "idchat";
    }
}
/**
 * Clase Chats Clientes
 */

class chat_clientes extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "vista_clientes_ordenados"; // Usamos la vista creada
        $this->name = $this->table;
        $this->fields = ["idcliente", "nombre", "apellido", "telefono"];
        $this->initialize();
    }

    function ID_field()
    {
        return "idcliente";
    }

    function listarClientes()
    {
        return $this->fetchall();
    }
}

/**
 * Clase Productos
 */
class productos extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "productos";
        $this->name = $this->table;
        $this->fields = [
            "idproducto",
            "nombre",
            "idtipo",
            "stock",
            "descripcion",
            "imagenes",
            "peso",
            "ancho",
            "alto",
        ];
        $this->join(
            "producto_insumo",
            new MultipleJoin("producto_insumo", "idproducto")
        );
        $this->initialize();
    }

    function ID_field()
    {
        return "idproducto";
    }

    function getInsumos()
    {
        $dataArr = ["insumos" => []];

        $objproducto_insumo = new producto_insumo();
        $objproducto_insumo->setDB($this->db); // Usa la misma conexión de productos
        $objproducto_insumo->enableTables(["productos", "insumos"]);
        $objproducto_insumo->where = "idproducto=" . $this->ID(); // Usar el ID del producto actual
        
        $total_venta_minorista = 0;
        $total_venta_mayorista = 0;

        foreach ($objproducto_insumo->fetchall() as $row) {
            $r = [];

            $costo = (float) $row["insumos"]["costo"];
            $cantidad = (float) $row["cantidad"];
            $markupMinorista = (float) $this->values["markup"];
            $markupMayorista = (float) $this->values["markupMayorista"];
            $esMayorista = (bool) $row["mayorista"]; // Si es true, se incluye en mayorista

            // Multiplicar costo por cantidad
            $costo_total = $costo * $cantidad;

            // Evitar división por cero en el cálculo del precio
            $precio_minorista = ($markupMinorista < 100) ? ($costo_total / (100 - $markupMinorista)) * 100 : 0;
            $precio_mayorista = ($markupMayorista < 100) ? ($costo_total / (100 - $markupMayorista)) * 100 : 0;

            // Calcular el total de venta acumulando precios
            $total_venta_minorista += $precio_minorista;
            if ($esMayorista) {
                $total_venta_mayorista += $precio_mayorista;
            }

            // Asignar valores al array
            $r["id"] = $row["id"];
            $r["nombre"] = $row["insumos"]["nombre"];
            $r["cantidad"] = $cantidad;
            $r["markup"] = $markupMinorista;
            $r["markupMayorista"] = $markupMayorista;
            $r["costo_unitario"] = $costo;
            $r["costo_total"] = round($costo_total, 2);
            $r["mayorista"] = $esMayorista;
            //$r["precio_minorista"] = round($precio_minorista, 2);
            //$r["precio_mayorista"] = round($precio_mayorista, 2);

            $dataArr["insumos"][] = $r;
        }

        // Agregar los totales de venta al array
        $dataArr["precio_minorista"] = round($total_venta_minorista, 2);
        $dataArr["precio_mayorista"] = round($total_venta_mayorista, 2);

        return $dataArr;
    }
}
/**
 * Clase Tipo
 */
class tipos extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "tipos";
        $this->name = $this->table;
        $this->fields = [
            "idtipo",
            "nombre"
        ];
        $this->initialize();
    }

    function ID_field()
    {
        return "idtipo";
    }
}
/**
 * Clase Insumos
 */
class insumos extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "insumos";
        $this->name = $this->table;
        $this->fields = [
            "idinsumo",
            "nombre",
            "idunidad",
            "stock",
            "costo",
           
            "created_at",
            "updated_at",
        ];
        $this->initialize();
    }

    function ID_field()
    {
        return "idinsumo";
    }
}

/**
 * Clase Producto_Insumo
 *
 */
class producto_insumo extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "producto_insumo";
        $this->name = $this->table;
        $this->fields = [
            "id",
            "idproducto",
            "idinsumo",
            "cantidad",
            "mayorista"
        ];
        $this->join("productos", new ForeignKeys("productos", "idproducto"));
        $this->join("insumos", new ForeignKeys("insumos", "idinsumo"));
        $this->initialize();
    }

    function ID_field()
    {
        return "id";
    }
}

/**
 * Clase Lista_Precios
 */
class lista_precios extends objdb
{
    function __construct()
    {
        parent::__construct();
        $this->table = "lista_precios";
        $this->name = $this->table;
        $this->fields = ["idlista", "nombre", "descripcion"];
        $this->initialize();
    }

    function ID_field()
    {
        return "idlista";
    }
}

/**
 * Función para agregar eventos al log
 */

function addLog($evento, $texto)
{
    // Crear conexión a la base de datos
    $dbCMS = new objdb();
    $dbCMS->connect();

    // Crear instancia de eventlog y asignar la BD usando setDB()
    $tmp = new eventlog();
    $tmp->setDB($dbCMS); // ✅ Asignamos la BD correctamente

    // Insertar en la tabla eventlog
    $tmp->field("timestamp", date("Y-m-d H:i:s"));
    $tmp->field("evento", $evento);
    $tmp->field("texto", $texto);
    $tmp->store();
}

?>
