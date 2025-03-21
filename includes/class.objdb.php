<?php

$OBJDB_CACHE = array();

class objdb 
{
	protected $db;
	var $table;
	var $name;

	var $where;
	var $order_by;

	var $limit_from;
	var $limit_count;

	var $fields;
	var $values;

	var $cache;
	var $cache_ttl;

	var $enabled_tables;
	var $disableTables;
	var $joins;

	var $debug;

	function __construct() 
	{
		$this->db			= NULL;

		$this->table		= NULL;
		$this->name			= $this->table;

		$this->where		= NULL;
		$this->order_by		= NULL;

		$this->limit_from	= NULL;
		$this->limit_count	= NULL;

		$this->fields		= array();
		$this->values		= array();
		
		$this->cache		= 0;
		$this->cache_ttl	= 0;

		$this->enabled_tables   = array();
		$this->joins		= array();

		$this->debug		= 0;
	}
	public function setDB($dbConn) {
		if ($dbConn instanceof dbMysql) {
			$this->db = $dbConn;
			error_log("setDB() ejecutado correctamente.");
		} else {
			error_log("ERROR>> setDB(): La conexión pasada no es una instancia válida de dbMysql.");
		}
	}
	
	function initialize () 
	{
		foreach($this->fields as $value)
			$this->field($value, "");
	}

	function ID_field() 
	{
		return 'id';
	}

	function ID($value=NULL) 
	{
		return $this->field($this->ID_field(), $value);
	}

	public function field($fieldname, $value = null) {
		if (!is_array($this->values)) {
			$this->values = []; // 🔹 Asegura que `values` sea un array antes de usarlo
		}
	
		if ($value !== null) {
			$this->values[$fieldname] = $value;
		}
	
		return isset($this->values[$fieldname]) ? $this->values[$fieldname] : null;
	}
	

	function enableTables($tables)
	{
		foreach($tables as $tablename)
			$this->enabled_tables[$tablename] = 1;
	}
	
	function disableTables($tables)
	{
		foreach($tables as $tablename)
			if (array_key_exists($tablename, $this->enabled_tables))
				$this->enabled_tables[$tablename] = NULL;
	}
	
	function join($name, $join=NULL)
	{
		if (isset($join)) {$this->joins[$name] = $join;}
		return $this->joins[$name];
	}
	
	#**************************************
	# Methods
	#**************************************
	public function &fetch($ID = null) {
		global $OBJDB_CACHE;
	
		// Verificar que la conexión a la base de datos está establecida
		if (!$this->db || !$this->db->isOpen) {
			error_log("ERROR>> fetch(): No hay conexión a la base de datos.");
			$this->values = [];
			return $this->values;
		}
	
		// Si no se pasa un ID, usar el actual
		if ($ID === null) {
			$ID = $this->ID();
		}
	
		// Inicializar `values` como un array vacío para evitar errores de acceso
		$this->values = [];
	
		// Validar si el ID es válido
		if (!is_numeric($ID) || $ID <= 0) {
			error_log("ERROR>> fetch(): ID inválido ({$ID}) para la tabla '{$this->table}'.");
			return $this->values;
		}
	
		error_log("DEBUG>> fetch(): Cargando datos de '{$this->table}' con ID={$ID}");
	
		// Verificar si el dato está en caché y sigue siendo válido
		if ($this->cache && isset($OBJDB_CACHE[$this->table][$ID]) &&
			time() - $OBJDB_CACHE[$this->table][$ID]['time'] <= $this->cache_ttl) {
			
			if ($this->debug) print "Return from CACHE {$this->table}\n";
			$OBJDB_CACHE[$this->table][$ID]['time'] = time();
			$this->values = $OBJDB_CACHE[$this->table][$ID]['values'];
		} else {
			// Construir la consulta SQL
			$fields = array_map(fn($field) => "{$this->table}.$field", $this->fields);
			$sql = "SELECT " . implode(",", $fields) . " FROM {$this->table} WHERE {$this->ID_field()} = '{$ID}'";
	
			error_log("DEBUG>> Ejecutando SQL: {$sql}");
	
			// Ejecutar la consulta
			if (!$this->db->exec($sql)) {
				error_log("ERROR>> fetch(): Falló la consulta SQL: {$sql} - " . $this->db->Error);
				return $this->values;
			}
	
			// Obtener los datos
			$row = $this->db->getrow();
			if ($row) {
				$this->values = $row;
				$OBJDB_CACHE[$this->table][$ID]['values'] = $this->values;
				$OBJDB_CACHE[$this->table][$ID]['time'] = time();
				error_log("DEBUG>> fetch(): Datos cargados correctamente para ID={$ID}");
			} else {
				error_log("ERROR>> fetch(): No se encontraron datos para ID={$ID} en la tabla '{$this->table}'.");
				return $this->values; // Retorna array vacío si no hay datos
			}
		}
	
		// Manejar relaciones y joins
		foreach ($this->joins as $name => $j) {
    error_log("DEBUG>> Procesando relación: '{$name}' en '{$this->table}'");

    $obj = $j->obj();
    if (!$obj) {
        error_log("ERROR>> fetch(): No se pudo inicializar objeto de relación '{$name}' en '{$this->table}' - Clase esperada: '{$j->getClass()}'");
        continue;
    }

    error_log("DEBUG>> Relación '{$name}' en '{$this->table}' encontrada correctamente. Creando instancia de '{$j->getClass()}'");
    
    $obj->db = clone $this->db;
    $obj->enableTables(array_keys($this->enabled_tables));
    $obj->disableTables([$this->table]);
    $obj->debug = $this->debug;
    $obj->cache = $this->cache;
    $obj->cache_ttl = $this->cache_ttl;

    
	if ($j instanceof ForeignKeys && isset($this->enabled_tables[$obj->table])) {
		$foreignKey = $j->getKey();
		$relatedID = $this->values[$foreignKey] ?? null;
		$this->values[$name] = $obj->fetch($relatedID);
	} elseif ($j instanceof MultipleJoin && isset($this->enabled_tables[$obj->table])) {
		$obj->where = "{$obj->table}.{$j->getKey()}=" . $this->ID();
		if ($j->getWhere()) $obj->where .= " AND {$j->getWhere()}";
		$obj->order_by = $j->getOrderBy();
		$obj->limit_from = $j->getLimitFrom();
		$obj->limit_count = $j->getLimitCount();
		if ($j->getCount()) $this->values["{$name}_count"] = $obj->count();
		$this->values[$name] = $obj->fetchall();
	} else {
		$this->values[$name] = [];
	}
}
	
		error_log("DEBUG>> fetch(): Finalizado para ID={$ID} en tabla '{$this->table}'");
		return $this->values;
	}
	
	
	
	function &fetchby($name, $value)
	{
		$this->where       = $this->table.".$name = '$value'";
		$this->limit_count = 1;
		$result = $this->fetchall();
		if ($result[0])
			$this->values = $result[0];
		else
			$this->fetch(0);

		return $this->values;
	}

	function &fetchall()
	{
		$tables = array($this->table);
		$filter = "1 ";

		foreach($this->joins as $name => $j) 
		{
			if (get_class($j) == "ForeignKeys" && $this->enabled_tables[$obj->table])
			{
				$obj      = $j->obj();
				$tables[] = $obj->table;
// 				echo $obj->table."<br>--------";
				$filter .= " AND ".$this->table.".".$j->key."=".$obj->table.".".$obj->ID_field();
			}
		}

		$sql  = "SELECT ".$this->table.".".$this->ID_field();
		$sql .= " FROM ".join(",",$tables);
    		$sql .= " WHERE $filter";
		
		if ($this->where)       { $sql .= " AND ".$this->where;}
		if ($this->order_by)    { $sql .= " ORDER BY ".$this->order_by;}
		if (isset($this->limit_from) && isset($this->limit_count))  
			$sql .= " LIMIT $this->limit_from, $this->limit_count";
		elseif (isset($this->limit_count))  
			$sql .= " LIMIT $this->limit_count";

		$db = clone($this->db);
		$db->exec($sql);
		if ($this->debug) print "$sql (Error=".$this->db->Error.")";
		$rows = array();
		while ($db->getrow())
				$rows[] = $this->fetch($db->Fields[$this->ID_field()]);

		return $rows;
	}

/*	function numreg()
	{
		$tables = array($this->table);
		
		$sql  = "SELECT ".$this->table.".".$this->ID_field();
		$sql .= " FROM ".join(",",$tables);
    	
		$db = $this->db;
		$db->exec($sql);
		
		$c=0;
		while ($db->getrow())
				$c++;
				
		return $c;
	}*/

	function count()
	{
		$tables = array($this->table);
		$filter = "1 ";

		foreach($this->joins as $name => $j) 
		{
			if (get_class($j) == "ForeignKeys" && $this->enabled_tables[$obj->table])
			{ 
				$obj      = $j->obj();
				$tables[] = $obj->table;
				$filter .= " AND ".$this->table.".".$j->key."=".$obj->table.".".$obj->ID_field();
			}
		}

		$sql  = "SELECT count(*) as count ";
		$sql .= " FROM ".join(",",$tables);
		$sql .= " WHERE $filter";
		
		if ($this->where)       { $sql .= " AND ".$this->where;}

		$db = clone($this->db);
		$db->exec($sql);
		if ($this->debug) print "$sql (Error=".$this->db->Error.")";
		$db->getrow();
		return $db->Fields['count'];
	}
	


	//*******************************************
	// Realiza las altas y modificaciones
	//*******************************************
	function store() 
	{
		global $OBJDB_CACHE;

		if ($this->ID()>0) { $insert=false; } else { $insert=true;  }
		$first   = true;
		$fields  = "";
		$values  = "";

		if ($insert) 
		{
			$fields=" ( ";
			$values=" values ( ";
		}

		foreach($this->fields as $key) 
		{
			if ($key!=$this->ID_field())
			{
				if (!$first) {
					$fields  .=",";
					$values  .=",";
				} 
				else 
					$first = false;

				if ($insert) 
				{
					$fields  .= $key;
					$values  .= "'".$this->values[$key]."'";
				} 
				else 
				{
					$fields  .= $key;
					$fields  .= "='".$this->values[$key]."'";
				}
			}
		}

		if ($insert) 
		{
			$fields  .= " ) ";
			$values  .= " ) ";
			$sql      = "INSERT INTO ".$this->table.$fields.$values;
			$this->db->exec($sql);
			if ($this->debug) print "$sql (Error=".$this->db->Error.")";

			if ($this->db->Error) return 0;
			$this->ID($this->db->lastid());

		} 
		else 
		{
			$sql = "UPDATE ".$this->table." SET ".$fields." WHERE ".$this->ID_field()."='".$this->ID()."'";
			$this->db->exec($sql);
			if ($this->debug) print "$sql (Error=".$this->db->Error.")";

			if ($this->db->Error) return 0;
		}
		
		if ($this->cache) 
		{
			if ($this->debug) print "Update CACHE ".$this->table."\n";
			$OBJDB_CACHE[$this->table][$this->ID()][values] = $this->values;
			$OBJDB_CACHE[$this->table][$this->ID()][time] 	= time();
		} 
		return 1;
	}

	function delete($ID=NULL) 
	{
		if (!$ID) $ID = $this->ID();
		if (!$ID) return false;

		foreach($this->joins as $name => $j)
		{
			if (get_class($j) == "MultipleJoin")
			{
				$obj        = $j->obj();
				$obj->db    = clone($this->db);
				$obj->where = $obj->table.".".$j->key."='$ID'";
				foreach($obj->fetchall() as $row)
					$obj->delete($row[$obj->ID_field()]);
			}
		}

		$sql="DELETE FROM ".$this->table." WHERE ".$this->ID_field()."='$ID'";
		$this->db->exec($sql);
		if ($this->debug) print "$sql (Error=".$this->db->Error.")";

		if ($this->db->Error) return 0;

		if ($this->cache && isset($OBJDB_CACHE[$this->table][$ID])) 
		{
			if ($this->debug) print "Delete from CACHE ".$this->table."\n";
			$OBJDB_CACHE[$this->table][$ID()] = NULL;
		} 

		return 1;
	}


	#*******************************************
	# Devuelve los datos formateados
	#*******************************************
	function doformat($strtemplate, $tplpath=NULL) 
	{
		if (!$tplpath)
			$tplform     = new Template($strtemplate);
		else {
			$tplform     = new Template("");
			$tplform->setFileRoot($tplpath);
			$tplform->Open($strtemplate);
		}

		$tplform->setVars($this->values, $this->name);
		$tplform->setVar("{".$this->name.".where}",        $this->where);
		$tplform->setVar("{".$this->name.".order_by}",     $this->order_by);
		$tplform->setVar("{".$this->name.".limit_from}",   $this->limit_from);
		$tplform->setVar("{".$this->name.".limit_count}",  $this->limit_count);

		return $tplform->Template;
	}

	function doformatall($strtemplate, $tplpath=NULL) 
	{
		//error_log("DEBUG>> doformatall(): Finalizado para strtemplate={$strtemplate} en tplpath '{$tplpath}'");
		if (!$tplpath)
			$tpllist     = new Template($strtemplate);
		else 
		{
			$tpllist     = new Template("");
			$tpllist->setFileRoot($tplpath);
			$tpllist->Open($strtemplate);
		}

		$tpllist->setVars($this->fetchall(), $this->name);
		$tpllist->setVar("{".$this->name.".where}",        $this->where);
		$tpllist->setVar("{".$this->name.".order_by}",     $this->order_by);
		$tpllist->setVar("{".$this->name.".limit_from}",   $this->limit_from);
		$tpllist->setVar("{".$this->name.".limit_count}",  $this->limit_count);
		
		return $tpllist->Template;
	}
}

class Join {
    protected string $class;
    protected ?object $_obj = null;

    public function __construct(string $class) {
        if (empty($class)) {
            error_log("ERROR>> Join(): Nombre de clase vacío en class.objdb.php.");
            throw new InvalidArgumentException("El nombre de la clase no puede estar vacío.");
        }

        $this->class = $class;
        error_log("DEBUG>> Join(): Relación asignada a la clase '{$this->class}'.");
    }

    public function getClass(): string {
        return $this->class;
    }

    public function obj(): ?object {
        if ($this->_obj === null) {
            if (class_exists($this->class)) {
                $this->_obj = new $this->class();
                error_log("DEBUG>> Join.obj(): Instancia de '{$this->class}' creada correctamente.");
            } else {
                error_log("ERROR>> Join.obj(): La clase '{$this->class}' no existe en class.objdb.php.");
            }
        }
        return $this->_obj;
    }
}

class ForeignKeys extends Join {
    protected string $key;

    /**
     * Constructor de la clase ForeignKeys
     * @param string $class Nombre de la clase relacionada
     * @param string $key Clave de relación
     */
    public function __construct(string $class, string $key) {
        parent::__construct($class);

        if (empty($key)) {
            error_log("ERROR>> ForeignKeys(): Clave de relación vacía para la clase '{$class}'.");
            throw new InvalidArgumentException("La clave de relación no puede estar vacía.");
        }

        $this->key = $key;
        error_log("DEBUG>> ForeignKeys(): Relación creada con '{$class}' usando clave '{$key}'.");
    }
	public function getKey(): string {
        return $this->key;
    }
}


class MultipleJoin extends Join {
    protected string $key;
    protected ?string $where = null;
    protected ?string $order_by = null;
    protected ?int $limit_from = null;
    protected ?int $limit_count = null;
    protected ?bool $count = null;

    /**
     * Constructor de la clase MultipleJoin
     * @param string $class Nombre de la clase relacionada
     * @param string $key Clave de relación entre las tablas
     */
    public function __construct(string $class, string $key) {
        parent::__construct($class);

        if (empty($key)) {
            error_log("ERROR>> MultipleJoin(): Clave de relación vacía en la clase '{$this->getClass()}'.");
            throw new InvalidArgumentException("La clave de relación no puede estar vacía.");
        }

        $this->key = $key;
        error_log("DEBUG>> MultipleJoin(): Relación creada con la clase '{$this->getClass()}' usando clave '{$key}'.");
    }
	// Métodos para acceder a las propiedades protegidas
    public function getKey(): string {
        return $this->key;
    }

    public function getWhere(): ?string {
        return $this->where;
    }

    public function getOrderBy(): ?string {
        return $this->order_by;
    }

    public function getLimitFrom(): ?int {
        return $this->limit_from;
    }

    public function getLimitCount(): ?int {
        return $this->limit_count;
    }

    public function getCount(): ?bool {
        return $this->count;
    }
    /**
     * Obtiene los datos relacionados según el ID
     * @param int $id ID del objeto principal
     * @return array Datos relacionados
     */
    public function get_data(int $id): array {
        $obj = $this->obj();
        if (!$obj) {
            error_log("ERROR>> get_data(): No se pudo inicializar la clase '{$this->getClass()}' en MultipleJoin.");
            return [];
        }

        if ($id <= 0) {
            error_log("ERROR>> get_data(): ID inválido ({$id}) en MultipleJoin.");
            return [];
        }

        $query = "SELECT * FROM {$obj->table} WHERE {$this->key} = {$id}";

        error_log("DEBUG>> Ejecutando consulta en MultipleJoin: {$query}");

        $result = $obj->query($query);
        if (!$result) {
            error_log("ERROR>> get_data(): Falló la consulta SQL en MultipleJoin.");
            return [];
        }

        return $result;
    }
}

?>
