<?php
//
// Copyright (C) 2000-2006 Garcia Rodrigo.
// All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//

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
		return $self->joins[$name];
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
	
		// Verificar si el dato está en caché y sigue siendo válido
		if ($this->cache && isset($OBJDB_CACHE[$this->table][$ID]) &&
			time() - $OBJDB_CACHE[$this->table][$ID]['time'] <= $this->cache_ttl) {
			
			if ($this->debug) print "Return from CACHE {$this->table}\n";
			$OBJDB_CACHE[$this->table][$ID]['time'] = time();
			$this->values = $OBJDB_CACHE[$this->table][$ID]['values'];
		} else {
			// Construir la consulta
			$fields = array_map(fn($field) => "{$this->table}.$field", $this->fields);
			$sql = "SELECT " . implode(",", $fields) . " FROM {$this->table} WHERE {$this->ID_field()} = '{$ID}'";
	
			// Ejecutar la consulta
			if (!$this->db->exec($sql)) {
				error_log("ERROR>> fetch(): Falló la consulta SQL: {$sql} - " . $this->db->Error);
				return $this->values; // Retorna array vacío
			}
	
			// Obtener los datos
			$row = $this->db->getrow();
			if ($row) {
				$this->values = $row;
				$OBJDB_CACHE[$this->table][$ID]['values'] = $this->values;
				$OBJDB_CACHE[$this->table][$ID]['time'] = time();
			} else {
				error_log("ERROR>> fetch(): No se encontraron datos para ID={$ID} en la tabla '{$this->table}'.");
				return $this->values; // Retorna array vacío si no hay datos
			}
		}
	
		// Manejar relaciones y joins
		foreach ($this->joins as $name => $j) {
			$obj = $j->obj();
			if (!$obj) {
				error_log("ERROR>> fetch(): No se pudo inicializar objeto de relación '{$name}' en '{$this->table}'.");
				continue;
			}
	
			$obj->db = clone $this->db;
			$obj->enableTables(array_keys($this->enabled_tables));
			$obj->disableTables([$this->table]);
			$obj->debug = $this->debug;
			$obj->cache = $this->cache;
			$obj->cache_ttl = $this->cache_ttl;
	
			if ($j instanceof ForeignKeys && isset($this->enabled_tables[$obj->table])) {
				$foreignKey = $j->key;
				$relatedID = $this->values[$foreignKey] ?? null;
				$this->values[$name] = $obj->fetch($relatedID);
			} elseif ($j instanceof MultipleJoin && isset($this->enabled_tables[$obj->table])) {
				$obj->where = "{$obj->table}.{$j->key}=" . $this->ID();
				if ($j->where) $obj->where .= " AND {$j->where}";
				$obj->order_by = $j->order_by;
				$obj->limit_from = $j->limit_from;
				$obj->limit_count = $j->limit_count;
				if ($j->count) $this->values["{$name}_count"] = $obj->count();
				$this->values[$name] = $obj->fetchall();
			} else {
				$this->values[$name] = [];
			}
		}
	
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

class Join
{
	var $class;
	var $_obj;
	
	function Join($class)
	{
		$this->class = $class;
		$this->_obj  = NULL;
	}

	function obj() {
		if (!$this->_obj) {
			if (class_exists($this->class)) {
				$this->_obj = new $this->class();
			} else {
				error_log("ERROR>> La clase {$this->class} no existe.");
				$this->_obj = null;
			}
		}
		
		return $this->_obj;
	}
}

class ForeignKeys extends Join
{
	var $key;
	
	function ForeignKeys($class, $key)
	{
		Join::Join($class);
		$this->key	= $key;
	}
}

class MultipleJoin extends Join
{
	var $key;
	var $where;
	var $order_by;
	var $limit_from;
	var $limit_count;
	var $count;
	
	function MultipleJoin($class, $key)
	{
		Join::Join($class);
		$this->key		    = $key;
		$this->where		= NULL;
		$this->order_by		= NULL;
		$this->limit_from	= NULL;
		$this->limit_count	= NULL;
		$this->count        = NULL;
	}
}
?>
