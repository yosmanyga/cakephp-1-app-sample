<?php
/* SVN FILE: $Id: dbo_oracle.php 4344 2007-01-28 20:32:01Z nate $ */
/**
 * Oracle layer for DBO
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP : Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.cake.libs.model.dbo
 * @since			CakePHP v 1.1.11.4041
 * @version			$Revision: 4344 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2007-01-28 14:32:01 -0600 (Sun, 28 Jan 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Include DBO.
 */
uses('model'.DS.'datasources'.DS.'dbo_source');
/**
 * Short description for class.
 *
 * Long description for class
 *
 * @package		cake
 * @subpackage	cake.cake.libs.model.dbo
 */
class DboOracle extends DboSource {
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access public
 */
	var $config;
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access public
 */
	var $alias = '';
	
 /**
  * The name of the model's sequence
  *
  * @var unknown_type
  */
	var $sequence = '';
	
/**
 * Transaction in progress flag
 *
 * @var boolean
 */
	var $__transactionStarted = false;
	
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access public
 */
	var $columns = array('primary_key' => array('name' => 'number NOT NULL'),
								'string' => array('name' => 'varchar2', 'limit' => '255'),
								'text' => array('name' => 'varchar2'),
								'integer' => array('name' => 'numeric'),
								'float' => array('name' => 'float'),
								'datetime' => array('name' => 'date'),
								'timestamp' => array('name' => 'date'),
								'time' => array('name' => 'date'),
								'date' => array('name' => 'date'),
								'binary' => array('name' => 'bytea'),
								'boolean' => array('name' => 'boolean'),
								'number' => array('name' => 'numeric'),
								'inet' => array('name' => 'inet'));
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $connection;
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_limit = -1;
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_offset = 0;
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_map;
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_currentRow;
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_numRows;
 /**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_results;
/**
 * Connects to the database using options in the given configuration array.
 *
 * @return boolean True if the database could be connected, else false
 * @access public
 */
	function connect() {
        $config = $this->config;
        $connect = $config['connect'];
        $this->connected = false;
        $this->connection = $connect($config['login'], $config['password'], $config['database']);              

		if ($this->connection) {
			$this->connected = true;
			$this->execute('ALTER SESSION SET NLS_SORT=BINARY_CI');
			$this->execute('ALTER SESSION SET NLS_COMP=ANSI');
		} else {
			$this->connected = false;
		}
		return $this->connected;
	}

/**
 * Disconnects from database.
 *
 * @return boolean True if the database could be disconnected, else false
 * @access public
 */
	function disconnect() {
		if ($this->connection) {
			return ocilogoff($this->connection);
		}
	}
/**
 * Scrape the incoming SQL to create the association map. This is an extremely
 * experimental method that creates the association maps since Oracle will not tell us.
 *
 * @param string $sql
 * @return false if sql is nor a SELECT
 * @access protected
 */
	function _scrapeSQL($sql) {

		$sql       = str_replace("\"", '', $sql);
		$preFrom   = explode('FROM', $sql);
		$preFrom   = $preFrom[0];
		$find	   = array('SELECT');
		$replace   = array('');
		$fieldList = trim(str_replace($find, $replace, $preFrom));
		$fields    = explode(', ', $fieldList);
						
		// clean fields of functions
		foreach ($fields as &$value) {
		  if ($value != 'COUNT(*) AS count') {
		      preg_match_all('/[[:alnum:]_]+\.[[:alnum:]_]+/', $value, $matches);
		      if ($matches[0]) {
		          $value = $matches[0][0];
		      }
		  }
		}
		
		$this->_map = array();
		foreach ($fields as $f) {
			$e = explode('.', $f);
			if (count($e) > 1) {
				$table = $e[0];
				$field = strtolower($e[1]);
			} else {
				$table = 0;
				$field = $e[0];
			}
			$this->_map[] = array($table, $field);
		}
	}
/**
 * Modify a SQL query to limit (and offset) the result set
 *
 * @param int $limit Maximum number of rows to return
 * @param int $offset Row to begin returning
 * @return modified SQL Query
 * @access public
 */
	function limit($limit, $offset = 0) {
		$this->_limit = (float) $limit;
		$this->_offset = (float) $offset;
	}
/**
 * Returns number of rows in previous resultset. If no previous resultset exists,
 * this returns false.
 *
 * @return int Number of rows in resultset
 * @access public
 */
	function lastNumRows() {
		return $this->_numRows;
	}

/**
 * Executes given SQL statement. This is an overloaded method.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier or null
 * @access protected
 */
	function _execute($sql) {	    
		$this->_statementId = ociparse($this->connection, $sql);
		if (!$this->_statementId) {
			return null;
		}
		if ($this->__transactionStarted) {
			$mode = OCI_DEFAULT;
		} else {
			$mode = OCI_COMMIT_ON_SUCCESS;
		}
		if (!ociexecute($this->_statementId, $mode)) {
			return false;
		}
		// fetch occurs here instead of fetchResult in order to get the number of rows
		switch (ocistatementtype($this->_statementId)) {
		    case 'DESCRIBE':
		    case 'SELECT':
		        $this->_scrapeSQL($sql);
		        break;
		    default:
		        return $this->_statementId;   
		}
		if ($this->_limit >= 1) {
			ocisetprefetch($this->_statementId, $this->_limit);
		} else {
			ocisetprefetch($this->_statementId, 3000);
		}
		$this->_numRows = ocifetchstatement($this->_statementId, $this->_results, $this->_offset, $this->_limit, OCI_NUM | OCI_FETCHSTATEMENT_BY_ROW);
		$this->_currentRow = 0;
		return $this->_statementId;
	}
/**
 * Enter description here...
 *
 * @return unknown
 * @access public
 */
	function fetchRow() {
		if ($this->_currentRow >= $this->_numRows) {
		    ocifreestatement($this->_statementId);
			return false;
		}
		$resultRow = array();
		foreach ($this->_results[$this->_currentRow] as $index => $field) {
			list($table, $column) = $this->_map[$index];
			if (strpos($column, ' count')) {
				$resultRow[0]['count'] = $field;
			} else {
				$resultRow[$table][$column] = $this->_results[$this->_currentRow][$index];
			}
		}
		$this->_currentRow++;
		return $resultRow;
	}
/**
 * Checks to see if a named sequence exists
 *
 * @param string $sequence
 * @return boolean
 * @access public
 */
	function sequenceExists($sequence) {
	    $sql = "SELECT SEQUENCE_NAME FROM USER_SEQUENCES WHERE SEQUENCE_NAME = '$sequence'";
	    if (!$this->execute($sql)) return false;
	    return $this->fetchRow();
	}
	
/**
 * Creates a database sequence
 *
 * @param string $sequence
 * @return boolean
 * @access public
 */
    function createSequence($sequence) {
        $sql = "CREATE SEQUENCE $sequence";
        return $this->execute($sql);
    }
    
    function createTrigger($table) {
        $sql = "CREATE OR REPLACE TRIGGER pk_$table" . "_trigger BEFORE INSERT ON $table FOR EACH ROW BEGIN SELECT pk_$table.NEXTVAL INTO :NEW.ID FROM DUAL; END;";
        return $this->execute($sql);
    }

/**
 * Returns an array of tables in the database. If there are no tables, an error is
 * raised and the application exits.
 *
 * @return array tablenames in the database
 * @access public
 */
	function listSources() {
	    $cache = parent::listSources();
		if ($cache != null) {
			return $cache;
		}
		$sql = 'SELECT view_name AS name FROM user_views UNION SELECT table_name AS name FROM user_tables';
		if (!$this->execute($sql)) {
		    return false;
		}
		$sources = array();
		while ($r = $this->fetchRow()) {
		    $sources[] = $r[0]['view_name AS name'];
		}
		parent::listSources($sources);
		return $sources;
	}
/**
 * Returns an array of the fields in given table name.
 *
 * @param object instance of a model to inspect
 * @return array Fields in table. Keys are name and type
 * @access public
 */
	function describe(&$model) {
		$cache = parent::describe($model);
		if ($cache != null) {
			return $cache;
		}
		$sql = 'SELECT COLUMN_NAME, DATA_TYPE FROM user_tab_columns WHERE table_name = \'';
		$sql .= strtoupper($model->table) . '\'';
        if (!$this->execute($sql)) {
            return false;
        }
		$fields = array();
		for ($i=0; $row = $this->fetchRow(); $i++) {
			$fields[$i]['name'] = strtolower($row[0]['COLUMN_NAME']);
			$fields[$i]['type'] = $this->column($row[0]['DATA_TYPE']);
		}
		$this->__cacheDescription($model->tablePrefix.$model->table, $fields);
		//$this->__cacheDescription($this->fullTableName($model, false), $fields);
		return $fields;
	}
/**
 * This method should quote Oracle identifiers. Well it doesn't.  
 * It would break all scaffolding and all of Cake's default assumptions.
 *
 * @param unknown_type $var
 * @return unknown
 * @access public
 */
	function name($var) {
	    switch ($var) {
	        /* the acl creation script uses illegal identifiers w/o quoting. the following
	           quotes only the illegal identifiers */
	        case '_create':
	        case '_read':
	        case '_update':
	        case '_delete':
	           return "\"$var\"";
	        default:
	           return $var;
	    }
	}
/**
 * Begin a transaction
 *
 * @param unknown_type $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions).
 */
	function begin(&$model) {
		//if (parent::begin($model)) {
			//if ($this->execute('BEGIN')) {
				$this->__transactionStarted = true;
				return true;
			//}
		//}
		return false;
	}
/**
 * Rollback a transaction
 *
 * @param unknown_type $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
	function rollback() {
		//if (parent::rollback($model)) {
			return ocirollback($this->connection);
		//}
		//return false;
	}
/**
 * Commit a transaction
 *
 * @param unknown_type $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
	function commit(&$model) {
		//if (parent::commit($model)) {
			$this->__transactionStarted = false;
			return ocicommit($this->connection);
		//}
		//return false;
	}
/**
 * Converts database-layer column types to basic types
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return string Abstract column type (i.e. "string")
 * @access public
 */
	function column($real) {
		if (is_array($real)) {
			$col = $real['name'];

			if (isset($real['limit'])) {
				$col .= '('.$real['limit'].')';
			}
			return $col;
		} else {
			$real = strtolower($real);
		}
		
		$col = r(')', '', $real);
		$limit = null;

		@list($col, $limit) = explode('(', $col);

		if (in_array($col, array('date', 'timestamp'))) {
			return $col;
		}
		if (strpos($col, 'number') !== false) {
			return 'integer';
		}
		if (strpos($col, 'integer') !== false) {
			return 'integer';
		}
		if (strpos($col, 'char') !== false) {
			return 'string';
		}
		if (strpos($col, 'text') !== false) {
			return 'text';
		}
		if (strpos($col, 'blob') !== false) {
			return 'binary';
		}
		if (in_array($col, array('float', 'double', 'decimal'))) {
			return 'float';
		}
		if ($col == 'boolean') {
			return $col;
		}
		return 'text';
	}
/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @return string Quoted and escaped
 * @access public
 */
	function value($data, $column_type = null) {
	    // this can also be accomplished through an Oracle NLS parameter
		switch ($column_type) {
			case 'date':
				$date = date('Y-m-d H:i:s', strtotime($data));
				return "TO_DATE('$date', 'YYYY-MM-DD HH24:MI:SS')";
			default:
				$data2 = str_replace("'", "''", $data);		
				return "'".$data2."'";
		}
	}
	
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param string
 * @return int
 * @access public
 */
	function lastInsertId($source) {
		$sequence = (!empty($this->sequence)) ? $this->sequence : 'pk_'.$source;
		$sql = "SELECT $sequence.currval FROM dual";
		if (!$this->execute($sql)) {
		    return false;
		}
		while ($row = $this->fetchRow()) {
		    return $row[$sequence]['currval'];
		}
		return false;
	}

/**
 * Returns a formatted error message from previous database operation.
 *
 * @return string Error message with error number
 * @access public
 */
	function lastError() {
		$errors = ocierror();
		if( ($errors != null) && (isset($errors["message"])) ) {
			return($errors["message"]);
		}
		return null;
	}
/**
 * Returns number of affected rows in previous database operation. If no previous operation exists, this returns false.
 *
 * @return int Number of affected rows
 * @access public
 */
	function lastAffected() {
		return $this->_statementId ? ocirowcount($this->_statementId): false;
	}
}
?>
