<?php

require_once 'DB.php';
require_once 'DataTable_class.php';

class Cliente {
	var $dataTable;

	function Cliente($db) {
		$this->dataTable = new DataTable($db);
		
		$this->dataTable->tableName = "clientes";
		
		$this->dataTable->keys["id"] = "";
		
		$this->dataTable->init();
	}
	
	function count($filter = '') {
		$_filter = array( new TableFilter("nombre_asegurado","LIKE","%".$filter."%",true,""),
							new TableFilter("numero","LIKE","%".$filter."%",true,"OR"),
							new TableFilter("apellido_paterno","LIKE","%".$filter."%",true,"OR"),
							new TableFilter("apellido_materno","LIKE","%".$filter."%",true,"OR"),
							new TableFilter("nombre","LIKE","%".$filter."%",true,"OR") );
		
		return 	$this->dataTable->count($_filter);
	}
	
	function getAll($maxitems = -1, $offset = 0) {
		return 	$this->dataTable->getAll(array(new TableFilter("1","=","1",false,"")), $maxitems, $offset);
	}
	
	function getFiltered($filter, $maxitems = -1, $offset = 0) {
		$_filter = array( new TableFilter("nombre_asegurado","LIKE","%".$filter."%",true,""),
							new TableFilter("numero","LIKE","%".$filter."%",true,"OR"),
							new TableFilter("apellido_paterno","LIKE","%".$filter."%",true,"OR"),
							new TableFilter("apellido_materno","LIKE","%".$filter."%",true,"OR"),
							new TableFilter("nombre","LIKE","%".$filter."%",true,"OR") );
		
		return 	$this->dataTable->getAll($_filter, $maxitems, $offset);
	}
	
	function getRows($criteria, $maxitems = -1, $offset = 0) {
		$filters = array(new TableFilter($criteria,"","",false,""));
		
		return 	$this->dataTable->getAll($filters, $maxitems, $offset);
	}
	
	function update() {
		return $this->dataTable->updateRow();	
	}
}

?>