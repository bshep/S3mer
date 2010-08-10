<?php

require_once 'DB.php';
require_once 'DataTable_class.php';

class Acreedor {
	var $dataTable;

	function Acreedor($db) {
		$this->dataTable = new DataTable($db);
		
		$this->dataTable->tableName = "acreedor";
		
		$this->dataTable->keys["id"] = "";
		
		$this->dataTable->init();
	}
	
	function getAll($maxitems = -1, $offset = 0) {
		return 	$this->dataTable->getAll(array(new TableFilter("1","=","1",false,"")), $maxitems, $offset);
	}
	
	function getFiltered($filter, $maxitems = -1, $offset = 0) {
		return 	$this->dataTable->getAll(array( new TableFilter("compania","LIKE","%".$filter."%",true,""),
												new TableFilter("nombre","LIKE","%".$filter."%",true,"OR") ), $maxitems, $offset);
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