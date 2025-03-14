<?php

require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
/**
 * Definicion de la clase de Usuarios
 * Relationships: usuarios_grupos.idusuario
**/

class usuarios extends objdb 
{
	function __construct() {
		parent::__construct(); // Llamar al constructor de objdb
		$this->table    = 'usuarios';
		$this->name     = $this->table;
		$this->fields   = ['idusuario', 'username', 'password', 'email', 'nombre', 'apellido', 'telefono', 'celular', 'direccion', 'dni'];
		$this->join('grupos', new MultipleJoin('Usuarios_Grupos', 'idusuario'));
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

	function checkPermission($nombre = "") {
		$sql = "SELECT count(*) as permisook FROM usuarios as u
				JOIN usuarios_grupos as ug ON u.idusuario = ug.idusuario
				JOIN grupos_permisos as gp ON ug.idgrupo = gp.idgrupo
				JOIN permisos as p ON gp.idpermiso = p.idpermiso
				WHERE u.idusuario='" . $this->ID() . "' AND p.nombre='$nombre'";
		
		$this->db->exec($sql);
		if ($this->debug) error_log("Ejecutando SQL: $sql (Error=" . $this->db->Error . ")");
		
		$row = $this->db->getrow();
		return ($row && $row["permisook"] > 0) ? 1 : 0;
	}

	public function &fetch($ID = null) {
		$values = parent::fetch($ID);

		if (!is_array($this->values) || empty($this->values)) {
			error_log("ERROR>> fetch(): No se encontraron datos para ID=$ID en la tabla {$this->table}");
			$this->values = [];  // Evita que devuelva NULL
		} else {
			$this->values['fullname'] = $this->values['apellido'] . ", " . $this->values['nombre'];
		}

		return $this->values;
	}

	public function delete($ID = null) {
		if ($ID !== null) {
			$this->fetch($ID);
		}

		$tmp = new usuarios_grupos();
		$tmp->db = $this->db;
		$tmp->where = "idusuario=" . $this->ID();
		foreach ($tmp->fetchall() as $row) {
			$tmp->ID($row[$tmp->ID_field()]);
			$tmp->delete();
		}

		parent::delete($ID);
	}
}

/**
 * Definicion de la clase de Permisos
 * Relationships: grupos_permisos.idpermiso
**/
class permisos extends objdb
{
	function permisos()
	{
		objdb::objdb();
		$this->table    = 'permisos';
		$this->name     = $this->table;
		$this->fields   = array('idpermiso', 'nombre', 'descripcion');
		$this->join('grupos', new MultipleJoin('grupos_permisos', 'idpermiso'));
		$this->initialize();
	}
	
	function ID_field() {
		return 'idpermiso';
	}
	
	public function delete($ID = null) {
		if ($ID !== null) {
			$this->fetch($ID); // Asegura que el usuario esté cargado si se pasa un ID
		}
		$tmp = new grupos_permisos();
		$tmp->db = $this->db;
		$tmp->where = "idpermiso=".$this->ID();
		foreach ($tmp->fetchall() as $row)
		{
			$tmp->ID($row[$tmp->ID_field()]);
			$tmp->delete();
		}
		
		objdb::delete();		
	}
}

/**
 * Definicion de la clase de Grupos
 * Relationships: usuarios_grupos.idgrupo, grupos_permisos.idgrupo
**/
class grupos extends objdb 
{
	function grupos(){
		objdb::objdb();
		$this->table    = 'grupos';
		$this->name     = $this->table;
		$this->fields   = array('idgrupo', 'nombre');
		$this->join('permisos', new MultipleJoin('grupos_permisos', 'idgrupo'));
		$this->join('usuarios', new MultipleJoin('usuarios_grupos', 'idgrupo'));
		$this->initialize();
	}
	
	function ID_field() {
		return 'idgrupo';
	}

	public function delete($ID = null) {
		if ($ID !== null) {
			$this->fetch($ID); // Asegura que el usuario esté cargado si se pasa un ID
		}
		$tmp = new usuarios_grupos();
		$tmp->db = $this->db;
		$tmp->where = "idgrupo=".$this->ID();
		foreach ($tmp->fetchall() as $row)
		{
			$tmp->ID($row[$tmp->ID_field()]);
			$tmp->delete();
		}
		
		$tmp = new grupos_permisos();
		$tmp->db = $this->db;
		$tmp->where = "idgrupo=".$this->ID();
		foreach ($tmp->fetchall() as $row)
		{
			$tmp->ID($row[$tmp->ID_field()]);
			$tmp->delete();
		}

		objdb::delete();
	}
}

/**
 * Definicion de la clase de Usuarios_Grupos
 * Relationships: usuarios.idusuario, grupos.idgrupo
**/
class usuarios_grupos extends objdb
{
	function usuarios_grupos(){
		objdb::objdb();
		$this->table    = 'usuarios_grupos';
		$this->name     = $this->table;
		$this->fields   = array('id', 'idusuario', 'idgrupo');
		$this->join('grupo', new ForeignKeys('Grupos', 'idgrupo'));
		$this->join('usuario', new ForeignKeys('Usuarios', 'idusuario'));
		$this->initialize();
	}
}

/**
 * Definicion de la clase de Grupos_Permisos
 * Relationships: grupos.idgrupo, permisos.idpermiso
**/
class grupos_permisos extends objdb 
{
	function grupos_permisos(){
		objdb::objdb();
		$this->table    = 'grupos_permisos';
		$this->name     = $this->table;
		$this->fields   = array('id', 'idpermiso', 'idgrupo');
		$this->join('grupo', new ForeignKeys('Grupos', 'idgrupo'));
		$this->join('permiso', new ForeignKeys('Permisos', 'idpermiso'));
		$this->initialize();
	}
}

/**
 * Definicion de la clase de Eventlog
 * Relationships: None
**/

class eventlog extends objdb 
{
	function eventlog()
	{
		objdb::objdb();
		$this->table    = 'eventlog';
		$this->name     = $this->table;
		$this->fields   = array('id', 'timestamp', 'evento', 'texto');
		$this->initialize();
	}
	
	
	public function &fetch($ID = null) {
		$values = parent::fetch($ID);
		objdb::fetch($ID);
		$this->values['timestamp.nice'] = date("d-m-Y", strtotime($this->values['timestamp']));
		return $this->values;
	}

}



function addLog ($evento, $texto)
{
        // MySQL Config
        $db_host = "localhost";
        $db_name = $_SESSION["MYSQL"]["NAME"];
        $db_user = $_SESSION["MYSQL"]["USER"];
        $db_passwd = $_SESSION["MYSQL"]["PASSWORD"];
				
        $dbCMS = new dbMysql($db_host, $db_name, $db_user, $db_passwd);
        $dbCMS->connect();
					
        $tmp = new eventlog();
        $tmp->db = $dbCMS;
	
        $tmp->field("timestamp", date("Y-m-d H:i:s"));
		$tmp->field('evento', $evento);
		$tmp->field('texto', $texto);
		$tmp->store();
}		
function dateDiff($start, $end) {

	$segundos = strtotime($start) -  strtotime($end);
	
	return $segundos;
	
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
 * Clase Relación Producto-Insumo
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
 * Clase Lista de Precios
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
 * Clase Relación Lista de Precios - Producto
 */
class lista_precio_producto extends objdb {
    function __construct() {
        parent::__construct();
        $this->table = 'lista_precio_producto';
        $this->name = $this->table;
        $this->fields = ['id', 'idlista', 'idproducto', 'precio'];
        $this->initialize();
    }
    function ID_field() {
        return 'id';
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
        $this->fields = ['idchat', 'idcliente', 'mensaje', 'original_idchat','timestamp', 'visto', 'sender'];
		//$this->join('clientes', new ForeignKeys('Clientes', 'idcliente'));
        $this->initialize();
    }
    function ID_field() {
        return 'idchat';
    }
}

?>
