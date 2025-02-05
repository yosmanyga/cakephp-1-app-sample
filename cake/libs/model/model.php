<?php
/* SVN FILE: $Id: model.php 4342 2007-01-28 01:53:35Z nate $ */

/**
 * Object-relational mapper.
 *
 * DBO-backed object data model, for mapping database tables to Cake objects.
 *
 * PHP versions 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
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
 * @subpackage		cake.cake.libs.model
 * @since			CakePHP v 0.10.0.0
 * @version			$Revision: 4342 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2007-01-27 19:53:35 -0600 (Sat, 27 Jan 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Included libs
 */
uses('class_registry', 'validation', 'overloadable', 'model' . DS . 'behavior', 'model' . DS . 'connection_manager', 'set');

/**
 * Object-relational mapper.
 *
 * DBO-backed object data model.
 * Automatically selects a database table name based on a pluralized lowercase object class name
 * (i.e. class 'User' => table 'users'; class 'Man' => table 'men')
 * The table is required to have at least 'id auto_increment', 'created datetime',
 * and 'modified datetime' fields.
 *
 * @package		cake
 * @subpackage	cake.cake.libs.model
 */
class Model extends Overloadable {

/**
 * The name of the DataSource connection that this Model uses
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'default';

/**
 * Enter description here... Still used?
 *
 * @var unknown_type
 * @access public
 * @todo Is this still used? -OJ 22 nov 2006
 */
	var $parent = false;

/**
 * Custom database table name.
 *
 * @var string
 * @access public
 */
	var $useTable = null;

/**
 * Custom display field name. Display fields are used by Scaffold, in SELECT boxes' OPTION elements.
 *
 * @var string
 * @access public
 */
	var $displayField = null;

/**
 * Value of the primary key ID of the record that this model is currently pointing to
 *
 * @var integer
 * @access public
 */
	var $id = false;

/**
 * Container for the data that this model gets from persistent storage (the database).
 *
 * @var array
 * @access public
 */
	var $data = array();

/**
 * Table name for this Model.
 *
 * @var string
 * @access public
 */
	var $table = false;

/**
 * The name of the ID field for this Model.
 *
 * @var string
 * @access public
 */
	var $primaryKey = null;

/**
 * Table metadata
 *
 * @var array
 * @access private
 */
	var $_tableInfo = null;

/**
 * List of validation rules. Append entries for validation as ('field_name' => '/^perl_compat_regexp$/')
 * that have to match with preg_match(). Use these rules with Model::validate()
 *
 * @var array
 * @access public
 */
	var $validate = array();

/**
 * Errors in validation
 * @var array
 * @access public
 */
	var $validationErrors = array();

/**
 * Database table prefix for tables in model.
 *
 * @var string
 * @access public
 */
	var $tablePrefix = null;

/**
 * Name of the model.
 *
 * @var string
 */
	var $name = null;

/**
 * Name of the current model.
 *
 * @var string
 */
	var $currentModel = null;

/**
 * List of table names included in the Model description. Used for associations.
 *
 * @var array
 * @access public
 */
	var $tableToModel = array();

/**
 * List of Model names by used tables. Used for associations.
 *
 * @var array
 * @access public
 */
	var $modelToTable = array();

/**
 * List of Foreign Key names to used tables. Used for associations.
 *
 * @var array
 * @access public
 */
	var $keyToTable = array();

/**
 * Alias table names for model, for use in SQL JOIN statements.
 *
 * @var array
 * @access public
 */
	var $alias = array();

/**
 * Whether or not transactions for this model should be logged
 *
 * @var boolean
 * @access public
 */
	var $logTransactions = false;

/**
 * Whether or not to enable transactions for this model (i.e. BEGIN/COMMIT/ROLLBACK)
 *
 * @var boolean
 * @access public
 */
	var $transactional = false;

/**
 * Whether or not to cache queries for this model.  This enables in-memory
 * caching only, the results are not stored beyond this execution.
 *
 * @var boolean
 * @access public
 */
	var $cacheQueries = false;

/**
 * belongsTo association
 *
 * @var array
 * @access public
 */
	var $belongsTo = array();

/**
 * hasOne association
 *
 * @var array
 * @access public
 */
	var $hasOne = array();

/**
 * hasMany association
 *
 * @var array
 * @access public
 */
	var $hasMany = array();

/**
 * hasAndBelongsToMany association
 *
 * @var array
 * @access public
 */
	var $hasAndBelongsToMany = array();

/**
 * List of behaviors to use
 *
 * @var array
 */
	var $actsAs = null;

/**
 * Behavior objects
 *
 * @var array
 */
	var $behaviors = array();

/**
 * Mapped behavior methods
 *
 * @var array
 * @access private
 */
	var $__behaviorMethods = array();

/**
 * Depth of recursive association
 *
 * @var integer
 * @access public
 */
	var $recursive = 1;

/**
 * Default ordering of model records
 *
 * @var mixed
 */
	var $order = null;

/**
 * Default association keys
 *
 * @var array
 * @access protected
 */
	var $__associationKeys = array(
		'belongsTo' => array('className', 'foreignKey', 'conditions', 'fields', 'order', 'counterCache'),
		'hasOne' => array('className', 'foreignKey','conditions', 'fields','order', 'dependent'),
		'hasMany' => array('className', 'foreignKey', 'conditions', 'fields', 'order', 'limit', 'offset', 'dependent', 'exclusive', 'finderQuery', 'counterQuery'),
		'hasAndBelongsToMany' => array('className', 'joinTable', 'foreignKey', 'associationForeignKey', 'conditions', 'fields', 'order', 'limit', 'offset', 'unique', 'finderQuery', 'deleteQuery', 'insertQuery')
	);

/**
 * Holds provided/generated association key names and other data for all associations
 *
 * @var array
 * @access protected
 */
	var $__associations = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');

/**
 * The last inserted ID of the data that this model created
 *
 * @var integer
 * @access protected
 */
	var $__insertID = null;

/**
 * The number of records returned by the last query
 *
 * @var integer
 * @access protected
 */
	var $__numRows = null;

/**
 * The number of records affected by the last query
 *
 * @var integer
 * @access protected
 */
	var $__affectedRows = null;

/**
 * Constructor. Binds the Model's database table to the object.
 *
 * @param integer $id
 * @param string $table Name of database table to use.
 * @param DataSource $ds DataSource connection object.
 */
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct();

		if ($this->name === null) {
			$this->name = get_class($this);
		}

		if ($this->primaryKey === null) {
			$this->primaryKey = 'id';
		}

		$this->currentModel = Inflector::underscore($this->name);

		ClassRegistry::addObject($this->currentModel, $this);
		$this->id = $id;

		if ($this->useTable !== false) {
			$this->setDataSource($ds);

			if ($table) {
				$tableName = $table;
			} else {
				if ($this->useTable) {
					$tableName = $this->useTable;
				} else {
					$tableName = Inflector::tableize($this->name);
				}
			}

			if (in_array('settableprefix', get_class_methods($this))) {
				$this->setTablePrefix();
			}

			$this->setSource($tableName);
			$this->__createLinks();

			if ($this->displayField == null) {
				if ($this->hasField('title')) {
					$this->displayField = 'title';
				}

				if ($this->hasField('name')) {
					$this->displayField = 'name';
				}

				if ($this->displayField == null) {
					$this->displayField = $this->primaryKey;
				}
			}
		}

		if ($this->actsAs !== null && empty($this->behaviors)) {
			$callbacks = array('setup', 'beforeFind', 'afterFind', 'beforeSave', 'afterSave', 'beforeDelete', 'afterDelete', 'afterError');
			$this->actsAs = Set::normalize($this->actsAs);

			foreach ($this->actsAs as $behavior => $config) {
				$className = $behavior . 'Behavior';

				if (!loadBehavior($behavior)) {
					// Raise an error
				} else {
					if (ClassRegistry::isKeySet($className)) {
						if (PHP5) {
							$this->behaviors[$behavior] = ClassRegistry::getObject($className);
						} else {
							$this->behaviors[$behavior] =& ClassRegistry::getObject($className);
						}
					} else {
						if (PHP5) {
							$this->behaviors[$behavior] = new $className;
						} else {
							$this->behaviors[$behavior] =& new $className;
						}
						ClassRegistry::addObject($className, $this->behaviors[$behavior]);
					}
					$this->behaviors[$behavior]->setup($this, $config);

					$methods = $this->behaviors[$behavior]->mapMethods;
					foreach ($methods as $method => $alias) {
						if (!array_key_exists($method, $this->__behaviorMethods)) {
							$this->__behaviorMethods[$method] = array($alias, $behavior);
						}
					}

					$methods = get_class_methods($this->behaviors[$behavior]);
					$parentMethods = get_class_methods('ModelBehavior');

					foreach ($methods as $m) {
						if (!in_array($m, $parentMethods)) {
							if (strpos($m, '_') !== 0 && !array_key_exists($m, $this->__behaviorMethods) && !in_array($m, $callbacks)) {
								$this->__behaviorMethods[$m] = array($m, $behavior);
							}
						}
					}
				}
			}
		}
	}
/**
 * Handles custom method calls, like findBy<field> for DB models,
 * and custom RPC calls for remote data sources.
 *
 * @param string $method    Name of method to call.
 * @param array $params     Parameters for the method.
 * @return unknown
 * @access protected
 */
	function call__($method, $params) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);

		$methods = array_map('strtolower', array_keys($this->__behaviorMethods));
		$call = array_values($this->__behaviorMethods);
		$map = array();

		if (!empty($methods) && !empty($call)) {
			$map = array_combine($methods, $call);
		}
		$count = count($call);

		if (in_array(low($method), $methods)) {
			return $this->behaviors[$map[low($method)][1]]->{$map[low($method)][0]}($this, $params);
		}

		for($i = 0; $i < $count; $i++) {
			if (strpos($methods[$i], '/') === 0 && preg_match($methods[$i] . 'i', $method)) {
				return $this->behaviors[$call[$i][1]]->{$call[$i][0]}($this, $params, $method);
			}
		}
		$return = $db->query($method, $params, $this);

		if (!PHP5) {
			if (isset($this->__backAssociation)) {
				$this->__resetAssociations();
			}
		}
		return $return;
	}
/**
 * Bind model associations on the fly.
 *
 * If $permanent is true, association will not be reset
 * to the originals defined in the model
 *
 * @param array $params
 * @param boolean $permanent
 * @return void
 */
	function bind($assoc, $params, $permanent = true) {

	}
/**
 * Bind model associations on the fly.
 *
 * If $reset is false, association will not be reset
 * to the originals defined in the model
 *
 * @param array $params
 * @param boolean $reset
 * @return boolean Always true
 */
	function bindModel($params, $reset = true) {

		foreach($params as $assoc => $model) {
			if($reset === true){
				$this->__backAssociation[$assoc] = $this->{$assoc};
			}

			foreach($model as $key => $value) {
				$assocName = $key;
				$modelName = $key;

				if (isset($value['className'])) {
					$modelName = $value['className'];
				}

				$this->__constructLinkedModel($assocName, $modelName);
				$this->{$assoc}[$assocName] = $model[$assocName];
				$this->__generateAssociation($assoc);
			}
		}
		return true;
	}
/**
 * Turn off associations on the fly.
 *
 * If $reset is false, association will not be reset
 * to the originals defined in the model
 *
 * Example: Turn off the associated Model Support request,
 * to temporarily lighten the User model:
 * <code>
 * $this->User->unbindModel( array('hasMany' => array('Supportrequest')) );
 * </code>
 *
 * @param array $params
 * @param boolean $reset
 * @return boolean Always true
 */
	function unbindModel($params, $reset = true) {
		foreach($params as $assoc => $models) {
			if($reset === true){
				$this->__backAssociation[$assoc] = $this->{$assoc};
			}

			foreach($models as $model) {
				$this->__backAssociation = array_merge($this->__backAssociation, $this->{$assoc});
				unset ($this->{$assoc}[$model]);
			}
		}
		return true;
	}
/**
 * Private helper method to create a set of associations.
 *
 * @access private
 */
	function __createLinks() {

		// Convert all string-based associations to array based
		foreach($this->__associations as $type) {
			if (!is_array($this->{$type})) {
				$this->{$type} = explode(',', $this->{$type});

				foreach($this->{$type} as $i => $className) {
					$className = trim($className);
					unset ($this->{$type}[$i]);
					$this->{$type}[$className] = array();
				}
			}

			foreach($this->{$type} as $assoc => $value) {
				if (is_numeric($assoc)) {
					unset ($this->{$type}[$assoc]);
					$assoc = $value;
					$value = array();
					$this->{$type}[$assoc] = $value;
				}

				$className = $assoc;

				if (isset($value['className']) && !empty($value['className'])) {
					$className = $value['className'];
				}
				$this->__constructLinkedModel($assoc, $className);

				if (isset($value['with']) && !empty($value['with'])) {
					$this->__constructLinkedModel($value['with'], $value['with']);
				}
			}
			$this->__generateAssociation($type);
		}
	}
/**
 * Private helper method to create associated models of given class.
 * @param string $assoc
 * @param string $className Class name
 * @param mixed $id Primary key ID of linked model
 * @param string $table Database table associated with linked model
 * @param string $ds Name of DataSource the model should be bound to
 * @access private
 */
	function __constructLinkedModel($assoc, $className, $id = false, $table = null, $ds = null) {
		$colKey = Inflector::underscore($className);

		if(!class_exists($className)){
			loadModel($className);
		}

		if (ClassRegistry::isKeySet($colKey)) {
			if (!PHP5) {
				$this->{$assoc} =& ClassRegistry::getObject($colKey);
				$this->{$className} =& $this->{$assoc};
			} else {
				$this->{$assoc} = ClassRegistry::getObject($colKey);
				$this->{$className} = $this->{$assoc};
			}
		} else {
			if (!PHP5) {
				$this->{$assoc} =& new $className($id, $table, $ds);
				$this->{$className} =& $this->{$assoc};
			} else {
				$this->{$assoc} = new $className($id, $table, $ds);
				$this->{$className} = $this->{$assoc};
			}
		}

		$this->alias[$assoc] = $this->{$assoc}->table;
		$this->tableToModel[$this->{$assoc}->table] = $className;
		$this->modelToTable[$assoc] = $this->{$assoc}->table;
	}
/**
 * Build array-based association from string.
 *
 * @param string $type "Belongs", "One", "Many", "ManyTo"
 * @access private
 */
	function __generateAssociation($type) {
		foreach($this->{$type} as $assocKey => $assocData) {
			$class = $assocKey;

			foreach($this->__associationKeys[$type] as $key) {
				if (!isset($this->{$type}[$assocKey][$key]) || $this->{$type}[$assocKey][$key] == null) {
					$data = '';

					switch($key) {
						case 'fields':
							$data = '';
						break;

						case 'foreignKey':
							$data = Inflector::singularize($this->table) . '_id';
							if ($type == 'belongsTo') {
								$data = Inflector::underscore($assocKey) . '_id';
							}
						break;

						case 'associationForeignKey':
							$data = Inflector::singularize($this->{$class}->table) . '_id';
						break;

						case 'joinTable':
							$tables = array($this->table, $this->{$class}->table);
							sort ($tables);
							$data = $tables[0] . '_' . $tables[1];
						break;

						case 'className':
							$data = $class;
						break;
					}

					$this->{$type}[$assocKey][$key] = $data;
				} elseif ($key == 'with') {
					$this->{$type}[$assocKey][$key] = Set::normalize($this->{$type}[$assocKey][$key]);
				}

				if ($key == 'foreignKey' && !isset($this->keyToTable[$this->{$type}[$assocKey][$key]])) {
					$this->keyToTable[$this->{$type}[$assocKey][$key]][0] = $this->{$class}->table;
					$this->keyToTable[$this->{$type}[$assocKey][$key]][1] = $this->{$class}->name;
				}
			}
		}
	}
/**
 * Sets a custom table for your controller class. Used by your controller to select a database table.
 *
 * @param string $tableName Name of the custom table
 */
	function setSource($tableName) {
		$this->setDataSource($this->useDbConfig);
		$db =& ConnectionManager::getDataSource($this->useDbConfig);

		if ($db->isInterfaceSupported('listSources')) {
			$sources = $db->listSources();
			if (is_array($sources) && !in_array(low($this->tablePrefix . $tableName), array_map('low', $sources))) {
				return $this->cakeError('missingTable', array(array(
						'className' => $this->name,
						'table' => $this->tablePrefix . $tableName
				)));
			} else {
				$this->table = $tableName;
				$this->tableToModel[$this->table] = $this->name;
				$this->_tableInfo = null;
				$this->loadInfo();
			}

		} else {
			$this->table = $tableName;
			$this->tableToModel[$this->table] = $this->name;
			$this->loadInfo();
		}
	}
/**
 * This function does two things: 1) it scans the array $one for the primary key,
 * and if that's found, it sets the current id to the value of $one[id].
 * For all other keys than 'id' the keys and values of $one are copied to the 'data' property of this object.
 * 2) Returns an array with all of $one's keys and values.
 * (Alternative indata: two strings, which are mangled to
 * a one-item, two-dimensional array using $one for a key and $two as its value.)
 *
 * @param mixed $one Array or string of data
 * @param string $two Value string for the alternative indata method
 * @return unknown
 */
	function set($one, $two = null) {
		if (is_object($one)) {
			if (is_a($one, 'xmlnode') || is_a($one, 'XMLNode')) {
				if ($one->name != Inflector::underscore($this->name)) {
					if (is_object($one->child(Inflector::underscore($this->name)))) {
						$one = $one->child(Inflector::underscore($this->name));
						$one = $one->attributes;
					} else {
						return null;
					}
				}
			} elseif (is_a($one, 'stdclass') || is_a($one, 'stdClass')) {
				$one = get_object_vars($one);
				$keys = array_keys($one);
				$count = count($keys);
				for ($i = 0; $i < $count; $i++) {
					if ($keys[$i] == '__identity__' || is_array($one[$keys[$i]]) || is_object($one[$keys[$i]])) {
						unset($one[$keys[$i]]);
					}
				}
			}
		}

		if (is_array($one)) {
			if (Set::countDim($one) == 1) {
				$data = array($this->name => $one);
			} else {
				$data = $one;
			}
		} else {
			$data = array($this->name => array($one => $two));
		}

		foreach($data as $n => $v) {
			if (is_array($v)) {

				foreach($v as $x => $y) {
					if ($n == $this->name) {
						if (isset($this->validationErrors[$x])) {
							unset ($this->validationErrors[$x]);
						}

						if ($x == $this->primaryKey) {
							$this->id = $y;
						}
					}

					$this->data[$n][$x] = $y;
				}
			}
		}
		return $data;
	}
/**
 * Returns an array of table metadata (column names and types) from the database.
 *
 * @return array Array of table metadata
 */
	function loadInfo() {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);

		if (!is_object($this->_tableInfo) && $db->isInterfaceSupported('describe')) {
			$this->_tableInfo = new Set($db->describe($this));
		}
		return $this->_tableInfo;
	}
/**
 * Returns an associative array of field names and column types.
 *
 * @return array
 */
	function getColumnTypes() {
		$columns = $this->loadInfo();
		$columns = $columns->value;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$cols = array();

		foreach($columns as $col) {
			$cols[$col['name']] = $col['type'];
		}
		return $cols;
	}
/**
 * Returns the column type of a column in the model
 *
 * @param string $column The name of the model column
 * @return string Column type
 */
	function getColumnType($column) {
		$columns = $this->loadInfo();
		$columns = $columns->value;
		$cols = array();

		foreach($columns as $col) {
			if ($col['name'] == $column) {
				return $col['type'];
			}
		}
		return null;
	}
/**
 * Returns true if this Model has given field in its database table.
 *
 * @param string $name Name of field to look for
 * @return boolean
 */
	function hasField($name) {
		if (is_array($name)) {
			foreach ($name as $n) {
				if ($this->hasField($n)) {
					return $n;
				}
			}
			return false;
		}

		if (empty($this->_tableInfo)) {
			$this->loadInfo();
		}

		if ($this->_tableInfo != null) {
			return in_array($name, $this->_tableInfo->extract('{n}.name'));
		}
		return false;
	}
/**
 * Initializes the model for writing a new record.
 *
 * @param array $data Optional data to assign to the model after it is created
 * @return array The current data of the model
 */
	function create($data = array()) {
		$this->id = false;
		$this->data = array();
		$defaults = array();

		$cols = $this->loadInfo();
		if (array_key_exists('default', $cols->value[0])) {
			$count = count($cols->value);
			for ($i = 0; $i < $count; $i++) {
				if ($cols->value[$i]['name'] != $this->primaryKey) {
					$defaults[$cols->value[$i]['name']] = $cols->value[$i]['default'];
				}
			}
		}
		$this->validationErrors = array();

		$this->set(am(array_filter($defaults), $data));
		return $this->data;
	}
/**
 * Returns a list of fields from the database
 *
 * @param mixed $id The ID of the record to read
 * @param mixed $fields String of single fieldname, or an array of fieldnames.
 * @return array Array of database fields
 */
	function read($fields = null, $id = null) {
		$this->validationErrors = array();

		if ($id != null) {
			$this->id = $id;
		}

		$id = $this->id;

		if (is_array($this->id)) {
			$id = $this->id[0];
		}

		if ($this->id !== null && $this->id !== false) {
			return $this->find(array($this->name . '.' . $this->primaryKey => $id), $fields);
		} else {
			return false;
		}
	}

/**
 * Returns contents of a field in a query matching given conditions.
 *
 * @param string $name Name of field to get
 * @param array $conditions SQL conditions (defaults to NULL)
 * @param string $order SQL ORDER BY fragment
 * @return field contents
 */
	function field($name, $conditions = null, $order = null) {
		if ($conditions === null) {
			$conditions = array($this->name . '.' . $this->primaryKey => $this->id);
		}
		if($this->recursive >= 1) {
			$recursive = -1;
		} else {
			$recursive = $this->recursive;
		}
		if ($data = $this->find($conditions, $name, $order, $recursive)) {

			if (strpos($name, '.') === false) {
				if (isset($data[$this->name][$name])) {
					return $data[$this->name][$name];
				} else {
					return false;
				}
			} else {
				$name = explode('.', $name);

				if (isset($data[$name[0]][$name[1]])) {
					return $data[$name[0]][$name[1]];
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

/**
 * Saves a single field to the database.
 *
 * @param string $name Name of the table field
 * @param mixed $value Value of the field
 * @param boolean $validate Whether or not this model should validate before saving (defaults to false)
 * @return boolean True on success save
 */
	function saveField($name, $value, $validate = false) {
		return $this->save(array($this->name => array($name => $value)), $validate);
	}

/**
 * Saves model data to the database.
 * By default, validation occurs before save.
 *
 * @param array $data Data to save.
 * @param boolean $validate If set, validation will be done before the save
 * @param array $fieldList List of fields to allow to be written
 * @return boolean success
 */
	function save($data = null, $validate = true, $fieldList = array()) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);

		$this->set($data);

		$whitelist = !(empty($fieldList) || count($fieldList) == 0);

		if ($validate && !$this->validates()) {
			return false;
		}

		if (!empty($this->behaviors)) {
			$behaviors = array_keys($this->behaviors);
			$ct = count($behaviors);
			for ($i = 0; $i < $ct; $i++) {
				if ($this->behaviors[$behaviors[$i]]->beforeSave($this) === false) {
					return false;
				}
			}
		}

		if (!$this->beforeSave()) {
			return false;
		}

		$fields = $values = array();
		$count = 0;

		if (count($this->data) > 1) {
			$weHaveMulti = true;
			$joined = false;
		} else {
			$weHaveMulti = false;
		}

		$newID = null;

		foreach($this->data as $n => $v) {
			if (isset($weHaveMulti) && isset($v[$n]) && $count > 0 && count($this->hasAndBelongsToMany) > 0) {
				$joined[] = $v;
			} else {
				if ($n === $this->name) {
					foreach (array('created', 'updated', 'modified') as $field) {
						if (array_key_exists($field, $v) && (empty($v[$field]) || $v[$field] === null)) {
							unset($v[$field]);
						}
					}

					foreach($v as $x => $y) {
						if ($this->hasField($x) && ($whitelist && in_array($x, $fieldList) || !$whitelist)) {
							$fields[] = $x;
							$values[] = $y;

							if ($x == $this->primaryKey && !empty($y)) {
								$newID = $y;
							}
						}
					}
				}
			}
			$count++;
		}
		$exists = $this->exists();

		if (!$exists && $this->hasField('created') && !in_array('created', $fields) && ($whitelist && in_array('created', $fieldList) || !$whitelist)) {
			$colType = $this->getColumnType('created');
			$fields[] = 'created';
			$values[] = date('Y-m-d H:i:s');
		}

		foreach (array('modified', 'updated') as $updateCol) {
			if ($this->hasField($updateCol) && !in_array($updateCol, $fields) && ($whitelist && in_array($updateCol, $fieldList) || !$whitelist)) {
				$colType = $this->getColumnType($updateCol);
				$fields[] = $updateCol;
				$values[] = date('Y-m-d H:i:s');
			}
		}

		if (!$exists) {
			$this->id = false;
		}

		if (count($fields)) {
			if (!empty($this->id)) {
				if ($db->update($this, $fields, $values)) {
					if (!empty($joined)) {
						$this->__saveMulti($joined, $this->id);
					}

					$this->afterSave();
					$this->data = false;
					$this->_clearCache();
					return true;
				} else {
					return false;
				}
			} else {
				if ($db->create($this, $fields, $values)) {

					if (!empty($this->belongsTo)) {
						foreach ($this->belongsTo as $parent => $assoc) {
							if (isset($assoc['counterCache']) && !empty($assoc['counterCache'])) {
								$parentObj =& $this->{$assoc['className']};

							}
						}
					}

					if (!empty($joined)) {
						$this->__saveMulti($joined, $this->id);
					}

					if (!empty($this->behaviors)) {
						$behaviors = array_keys($this->behaviors);
						$ct = count($behaviors);
						for ($i = 0; $i < $ct; $i++) {
							$this->behaviors[$behaviors[$i]]->afterSave($this);
						}
					}
					$this->afterSave();
					$this->data = false;
					$this->_clearCache();
					$this->validationErrors = array();
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}
/**
 * Saves model hasAndBelongsToMany data to the database.
 *
 * @param array $joined Data to save.
 * @param string $id
 * @return
 * @access private
 */
	function __saveMulti($joined, $id) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		foreach($joined as $x => $y) {
			foreach($y as $assoc => $value) {
				$joinTable[$assoc] = $this->hasAndBelongsToMany[$assoc]['joinTable'];
				$mainKey[$assoc] = $this->hasAndBelongsToMany[$assoc]['foreignKey'];
				$keys[] = $this->hasAndBelongsToMany[$assoc]['foreignKey'];
				$keys[] = $this->hasAndBelongsToMany[$assoc]['associationForeignKey'];
				$fields[$assoc]  = join(',', $keys);
				unset($keys);

				foreach($value as $update) {
					if (!empty($update)) {
						$values[]  = $db->value($id, $this->getColumnType($this->primaryKey));
						$values[]  = $db->value($update);
						$values    = join(',', $values);
						$newValues[] = "({$values})";
						unset ($values);
					}
				}

				if (!empty($newValues)) {
					$newValue[$assoc] = $newValues;
					unset($newValues);
				} else {
					$newValue[$assoc] = array();
				}
			}
		}

		$total = count($joinTable);

		if(is_array($newValue)) {
			foreach ($newValue as $loopAssoc => $val) {
				$db =& ConnectionManager::getDataSource($this->useDbConfig);
				$table = $db->name($db->fullTableName($joinTable[$loopAssoc]));
				$db->query("DELETE FROM {$table} WHERE {$mainKey[$loopAssoc]} = '{$id}'");

				if (!empty($newValue[$loopAssoc])) {
					$secondCount = count($newValue[$loopAssoc]);
					for($x = 0; $x < $secondCount; $x++) {
						$db->query("INSERT INTO {$table} ({$fields[$loopAssoc]}) VALUES {$newValue[$loopAssoc][$x]}");
					}
				}
			}
		}
	}
/**
 * Allows model records to be updated based on a set of conditions
 *
 * @param mixed $conditions
 * @param mixed $fields
 * @return boolean True on success, false on failure
 */
	function updateAll($conditions, $fields = null) {
		if (empty($fields)) {
			$fields = $conditions;
			$conditions = true;
		}
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db->update($this, $fields, null, $conditions);
	}
/**
 * Synonym for del().
 *
 * @param mixed $id
 * @see function del
 * @return boolean True on success
 */
	function remove($id = null, $cascade = true) {
		return $this->del($id, $cascade);
	}
/**
 * Removes record for given id. If no id is given, the current id is used. Returns true on success.
 *
 * @param mixed $id Id of record to delete
 * @return boolean True on success
 */
	function del($id = null, $cascade = true) {
		if (!empty($id)) {
			$this->id = $id;
		}

		if ($this->exists() && $this->beforeDelete()) {
			$db =& ConnectionManager::getDataSource($this->useDbConfig);

			if (!empty($this->behaviors)) {
				$behaviors = array_keys($this->behaviors);
				$ct = count($behaviors);
				for ($i = 0; $i < $ct; $i++) {
					if ($this->behaviors[$behaviors[$i]]->beforeDelete($this) === false) {
						return false;
					}
				}
			}

			if ($db->delete($this)) {
				$this->_deleteMulti($this->id);
				$this->_deleteHasMany($this->id, $cascade);
				$this->_deleteHasOne($this->id, $cascade);
				if (!empty($this->behaviors)) {
					for ($i = 0; $i < $ct; $i++) {
						if ($this->behaviors[$behaviors[$i]]->afterDelete($this) === false) {
							return false;
						}
					}
				}
				$this->afterDelete();
				$this->_clearCache();
				$this->id = false;
				return true;
			}
		}
		return false;
	}
/**
 * Alias for del()
 *
 * @param mixed $id Id of record to delete
 * @return boolean True on success
 */
	function delete($id = null, $cascade = true) {
		return $this->del($id, $cascade);
	}
/**
 * Cascades model deletes to hasMany relationships.
 *
 * @param string $id
 * @return null
 * @access protected
 */
	function _deleteHasMany($id, $cascade) {
		if (isset($this->__backAssociation)) {
			$savedAssociatons = $this->__backAssociation;
			unset ($this->__backAssociation);
		}
		foreach($this->hasMany as $assoc => $data) {
			if ($data['dependent'] === true && $cascade === true) {
				$model =& $this->{$data['className']};
				$field = $model->escapeField($data['foreignKey']);
				$model->recursive = 0;
				$records = $model->findAll("$field = '$id'", $model->primaryKey, null, null);

				if($records != false){
					foreach($records as $record) {
						$model->del($record[$data['className']][$model->primaryKey]);
					}
				}
			}
		}
		if (isset($savedAssociatons)) {
			$this->__backAssociation = $savedAssociatons;
		}
	}
/**
 * Cascades model deletes to hasOne relationships.
 *
 * @param string $id
 * @return null
 * @access protected
 */
	function _deleteHasOne($id, $cascade) {
		if (isset($this->__backAssociation)) {
			$savedAssociatons = $this->__backAssociation;
			unset ($this->__backAssociation);
		}
		foreach($this->hasOne as $assoc => $data) {
			if ($data['dependent'] === true && $cascade === true) {
				$model =& $this->{$data['className']};
				$field = $model->escapeField($data['foreignKey']);
				$model->recursive = 0;
				$records = $model->findAll("$field = '$id'", $model->primaryKey, null, null);

				if($records != false){
					foreach($records as $record) {
						$model->del($record[$data['className']][$model->primaryKey]);
					}
				}
			}
		}
		if (isset($savedAssociatons)) {
			$this->__backAssociation = $savedAssociatons;
		}
	}
/**
 * Cascades model deletes to HABTM join keys.
 *
 * @param string $id
 * @return null
 * @access protected
 */
	function _deleteMulti($id) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		foreach($this->hasAndBelongsToMany as $assoc => $data) {
			$db->query("DELETE FROM " . $db->name($db->fullTableName($data['joinTable'])) . " WHERE " . $db->name($data['foreignKey']) . " = '{$id}'");
		}
	}
/**
 * Allows model records to be deleted based on a set of conditions
 *
 * @param mixed $conditions
 * @param mixed $fields
 * @return boolean True on success, false on failure
 */
	function deleteAll($conditions, $cascade = true) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
	}
/**
 * Returns true if a record with set id exists.
 *
 * @return boolean True if such a record exists
 */
	function exists() {
		if ($this->getID() === false) {
			return false;
		}
		return ($this->findCount(array($this->name . '.' . $this->primaryKey => $this->getID()), -1) > 0);
	}
/**
 * Returns true if a record that meets given conditions exists
 *
 * @param array $conditions SQL conditions array
 * @return boolean True if such a record exists
 */
	function hasAny($conditions = null) {
		return ($this->findCount($conditions) != false);
	}
/**
 * Return a single row as a resultset array.
 * By using the $recursive parameter, the call can access further "levels of association" than
 * the ones this model is directly associated to.
 *
 * @param array $conditions SQL conditions array
 * @param mixed $fields Either a single string of a field name, or an array of field names
 * @param string $order SQL ORDER BY conditions (e.g. "price DESC" or "name ASC")
 * @param int $recursive The number of levels deep to fetch associated records
 * @return array Array of records
 */
	function find($conditions = null, $fields = null, $order = null, $recursive = null) {
		$data = $this->findAll($conditions, $fields, $order, 1, null, $recursive);

		if (empty($data[0])) {
			return false;
		}
		return $data[0];
	}
/**
 * Returns a resultset array with specified fields from database matching given conditions.
 * By using the $recursive parameter, the call can access further "levels of association" than
 * the ones this model is directly associated to.
 *
 * @param mixed $conditions SQL conditions as a string or as an array('field' =>'value',...)
 * @param mixed $fields Either a single string of a field name, or an array of field names
 * @param string $order SQL ORDER BY conditions (e.g. "price DESC" or "name ASC")
 * @param int $limit SQL LIMIT clause, for calculating items per page.
 * @param int $page Page number, for accessing paged data
 * @param int $recursive The number of levels deep to fetch associated records
 * @return array Array of records
 */
	function findAll($conditions = null, $fields = null, $order = null, $limit = null, $page = 1, $recursive = null) {

		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$this->id = $this->getID();
		$offset = null;

		if (empty($page) || !is_numeric($page) || intval($page) < 1) {
			$page = 1;
		}

		if ($page > 1 && $limit != null) {
			$offset = ($page - 1) * $limit;
		}

		if ($order == null) {
			if ($this->order == null) {
				$order = array();
			} else {
				$order = array($this->order);
			}
		} else {
			$order = array($order);
		}

		$queryData = array(
			'conditions' => $conditions,
			'fields'    => $fields,
			'joins'     => array(),
			'limit'     => $limit,
			'offset'	=> $offset,
			'order'     => $order
		);

		if (!empty($this->behaviors)) {
			$behaviors = array_keys($this->behaviors);
			$ct = count($behaviors);
			for ($i = 0; $i < $ct; $i++) {
				$ret = $this->behaviors[$behaviors[$i]]->beforeFind($this, $queryData);
				if (is_array($ret)) {
					$queryData = $ret;
				} elseif ($ret === false) {
					return null;
				}
			}
		}

		$ret = $this->beforeFind($queryData);
		if (is_array($ret)) {
			$queryData = $ret;
		} elseif ($ret === false) {
			return null;
		}

		$results = $db->read($this, $queryData, $recursive);

		if (!empty($this->behaviors)) {
			$b = array_keys($this->behaviors);
			$c = count($b);
			for ($i = 0; $i < $c; $i++) {
				$ret = $this->behaviors[$b[$i]]->afterFind($this, $results, true);
				if (is_array($ret)) {
					$results = $ret;
				}
			}
		}
		$return = $this->afterFind($results, true);

		if (isset($this->__backAssociation)) {
			$this->__resetAssociations();
		}

		return $return;
	}
/**
 * Method is called only when bindTo<ModelName>() is used.
 * This resets the association arrays for the model back
 * to the original as set in the model.
 *
 * @return unknown
 * @access private
 */
	function __resetAssociations() {
		foreach($this->__associations as $type) {
			if (isset($this->__backAssociation[$type])) {
				$this->{$type} = $this->__backAssociation[$type];
			}
		}

		unset ($this->__backAssociation);
		return true;
	}
/**
 * Runs a direct query against the bound DataSource, and returns the result.
 *
 * @param string $data Query data
 * @return array
 */
	function execute($data) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$data = $db->fetchAll($data, $this->cacheQueries);

		foreach($data as $key => $value) {
			foreach($this->tableToModel as $key1 => $value1) {
				if (isset($data[$key][$key1])) {
					$newData[$key][$value1] = $data[$key][$key1];
				}
			}
		}

		if (!empty($newData)) {
			return $newData;
		}
		return $data;
	}
/**
 * Returns number of rows matching given SQL condition.
 *
 * @param array $conditions SQL conditions array for findAll
 * @param int $recursize The number of levels deep to fetch associated records
 * @return int Number of matching rows
 * @see Model::findAll
 */
	function findCount($conditions = null, $recursive = 0) {
		list($data) = $this->findAll($conditions, 'COUNT(' . $this->name . '.' . $this->primaryKey . ') AS count', null, null, 1, $recursive);

		if (isset($data[0]['count'])) {
			return $data[0]['count'];
		} elseif (isset($data[$this->name]['count'])) {
			return $data[$this->name]['count'];
		}

		return false;
	}
/**
 * False if any fields passed match any (by default, all if $or = false) of their matching values.
 *
 * @param array $fields Field/value pairs to search (if no values specified, they are pulled from $this->data)
 * @param boolean $or If false, all fields specified must match in order for a false return value
 * @return boolean False if any records matching any fields are found
 */
	function isUnique($fields, $or = true) {
		if (!is_array($fields)) {
			$fields = func_get_args();
			if (is_bool($fields[count($fields) - 1])) {
				$or = $fields[count($fields) - 1];
				unset($fields[count($fields) - 1]);
			}
		}

		foreach ($fields as $field => $value) {
			if (is_numeric($field)) {
				unset($fields[$field]);

				$field = $value;
				if (isset($this->data[$this->name][$field])) {
					$value = $this->data[$this->name][$field];
				} else {
					$value = null;
				}
			}

			if (strpos($field, '.') === false) {
				unset($fields[$field]);
				$fields[$this->name . '.' . $field] = $value;
			}
		}
		if ($or) {
			$fields = array('or' => $fields);
		}
		return ($this->findCount($fields) == 0);
	}
/**
 * Special findAll variation for tables joined to themselves.
 * The table needs the fields id and parent_id to work.
 *
 * @param array $conditions Conditions for the findAll() call
 * @param array $fields Fields for the findAll() call
 * @param string $sort SQL ORDER BY statement
 * @return array
 * @todo Perhaps create a Component with this logic
 */
	function findAllThreaded($conditions = null, $fields = null, $sort = null) {
		return $this->__doThread(Model::findAll($conditions, $fields, $sort), null);
	}
/**
 * Private, recursive helper method for findAllThreaded.
 *
 * @param array $data
 * @param string $root NULL or id for root node of operation
 * @return array
 * @access private
 * @see findAllThreaded
 */
	function __doThread($data, $root) {
		$out = array();
		$sizeOf = sizeof($data);

		for($ii = 0; $ii < $sizeOf; $ii++) {
			if (($data[$ii][$this->name]['parent_id'] == $root) || (($root === null) && ($data[$ii][$this->name]['parent_id'] == '0'))) {
				$tmp = $data[$ii];

				if (isset($data[$ii][$this->name][$this->primaryKey])) {
					$tmp['children'] = $this->__doThread($data, $data[$ii][$this->name][$this->primaryKey]);
				} else {
					$tmp['children'] = null;
				}

				$out[] = $tmp;
			}
		}

		return $out;
	}
/**
 * Returns an array with keys "prev" and "next" that holds the id's of neighbouring data,
 * which is useful when creating paged lists.
 *
 * @param string $conditions SQL conditions for matching rows
 * @param string $field Field name (parameter for findAll)
 * @param unknown_type $value
 * @return array Array with keys "prev" and "next" that holds the id's
 */
	function findNeighbours($conditions = null, $field, $value) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);

		if (!is_null($conditions)) {
			$conditions = $conditions . ' AND ';
		}

		@list($prev) = Model::findAll($conditions . $field . ' < ' . $db->value($value), $field, $field . ' DESC', 1, null, 0);
		@list($next) = Model::findAll($conditions . $field . ' > ' . $db->value($value), $field, $field . ' ASC', 1, null, 0);

		if (!isset($prev)) {
			$prev = null;
		}

		if (!isset($next)) {
			$next = null;
		}

		return array('prev' => $prev, 'next' => $next);
	}
/**
 * Returns a resultset for given SQL statement. Generic SQL queries should be made with this method.
 *
 * @param string $sql SQL statement
 * @return array Resultset
 */
	function query() {
		$params = func_get_args();
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return call_user_func_array(array(&$db, 'query'), $params);
	}
/**
 * Returns true if all fields pass validation, otherwise false.
 *
 * @return boolean True if there are no errors
 */
	function validates($data = array()) {
		if (!empty($data)) {
			trigger_error(__('(Model::validates) Parameter usage is deprecated, set the $data property instead'), E_USER_WARNING);
		}
		$errors = $this->invalidFields($data);
		if (is_array($errors)) {
			return count($errors) === 0;
		}
		return $errors;
	}
/**
 * Returns an array of invalid fields.
 *
 * @param array $data
 * @return array Array of invalid fields
 */
	function invalidFields($data = array()) {
		if (empty($data)) {
			$data = $this->data;
		} else {
			trigger_error(__('(Model::invalidFields) Parameter usage is deprecated, set the $data property instead'), E_USER_WARNING);
		}

		if (empty($data) || !$this->beforeValidate()) {
			return false;
		}

		if (!isset($this->validate) || empty($this->validate)) {
			return array();
		}

		if (isset($data[$this->name])) {
			$data = $data[$this->name];
		}

		$Validation = new Validation();

		foreach($this->validate as $fieldName => $validator) {
			if (!is_array($validator) && isset($data[$fieldName])) {
				$validator = array('rule' => $validator);
			}

			$validator = am(array(
				'allowEmpty' => true,
				'message' => 'This field cannot be left blank',
				'rule' => 'blank',
				'on' => null
			), $validator);

			if (empty($validator['on']) || ($validator['on'] == 'create' && !$this->exists()) || ($validator['on'] == 'update' && $this->exists())) {
				if (!isset($data[$fieldName]) && $validator['allowEmpty'] == false) {
					$this->invalidate($fieldName, $validator['message']);
				} elseif (isset($data[$fieldName])) {
					if (is_array($validator['rule'])) {
						$rule = $validator['rule'][0];
						unset($validator['rule'][0]);
						$ruleParams = am(array($data[$fieldName]), array_values($validator['rule']));
					} else {
						$rule = $validator['rule'];
						$ruleParams = array($data[$fieldName]);
					}

					$valid = true;
					if (method_exists($this, $rule)) {
						$valid = call_user_func_array(array(&$this, $rule), $ruleParams);
					} elseif (method_exists($Validation, $rule)) {
						$valid = call_user_func_array(array(&$Validation, $rule), $ruleParams);
					} elseif (!is_array($validator['rule'])) {
						$valid = preg_match($rule, $data[$fieldName]);
					}
					if (!$valid) {
						$this->invalidate($fieldName, $validator['message']);
					}
				}
			}
		}
		return $this->validationErrors;
	}
/**
 * Sets a field as invalid
 *
 * @param string $field The name of the field to invalidate
 * @param mixed $value
 * @return void
 */
	function invalidate($field, $value = 1) {
		if (!is_array($this->validationErrors)) {
			$this->validationErrors = array();
		}
		$this->validationErrors[$field] = $value;
	}
/**
 * Returns true if given field name is a foreign key in this Model.
 *
 * @param string $field Returns true if the input string ends in "_id"
 * @return True if the field is a foreign key listed in the belongsTo array.
 */
	function isForeignKey($field) {
		$foreignKeys = array();

		if (count($this->belongsTo)) {
			foreach($this->belongsTo as $assoc => $data) {
				$foreignKeys[] = $data['foreignKey'];
			}
		}
		return (bool)(in_array($field, $foreignKeys));
	}
/**
 * Gets the display field for this model
 *
 * @return string The name of the display field for this Model (i.e. 'name', 'title').
 */
	function getDisplayField() {
		return $this->displayField;
	}
/**
 * Returns a resultset array with specified fields from database matching given conditions.
 * Method can be used to generate option lists for SELECT elements.
 *
 * @param mixed $conditions SQL conditions as a string or as an array('field' =>'value',...)
 * @param string $order SQL ORDER BY conditions (e.g. "price DESC" or "name ASC")
 * @param int $limit SQL LIMIT clause, for calculating items per page
 * @param string $keyPath A string path to the key, i.e. "{n}.Post.id"
 * @param string $valuePath A string path to the value, i.e. "{n}.Post.title"
 * @param string $groupPath A string path to a value to group the elements by, i.e. "{n}.Post.category_id"
 * @return array An associative array of records, where the id is the key, and the display field is the value
 */
	function generateList($conditions = null, $order = null, $limit = null, $keyPath = null, $valuePath = null, $groupPath = null) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);

		if ($keyPath == null && $valuePath == null && $groupPath == null && $this->hasField($this->displayField)) {
			$fields = array($this->primaryKey, $this->displayField);
		} else {
			$fields = null;
		}
		$recursive = $this->recursive;

		if($groupPath == null && $recursive >= 1) {
			$this->recursive = -1;
		} else if($groupPath && $recursive >= 1){
			$this->recursive = 0;
		}
		$result = $this->findAll($conditions, $fields, $order, $limit);
		$this->recursive = $recursive;

		if(!$result) {
			return false;
		}

		if ($keyPath == null) {
			$keyPath = '{n}.' . $this->name . '.' . $this->primaryKey;
		}

		if ($valuePath == null) {
			$valuePath = '{n}.' . $this->name . '.' . $this->displayField;
		}

		$keys = Set::extract($result, $keyPath);
		$vals = Set::extract($result, $valuePath);

		if (!empty($keys) && !empty($vals)) {
			$out = array();

			if ($groupPath != null) {
				$group = Set::extract($result, $groupPath);
				if (!empty($group)) {
					$c = count($keys);
					for ($i = 0; $i < $c; $i++) {
						if(!isset($group[$i])) {
							$group[$i] = 0;
						}
						if (!isset($out[$group[$i]])) {
							$out[$group[$i]] = array();
						}
						$out[$group[$i]][$keys[$i]] = $vals[$i];
					}
					return $out;
				}
			}

			$return = array_combine($keys, $vals);
			return $return;
		}
		return null;
	}
/**
 * Escapes the field name and prepends the model name. Escaping will be done according to the current database driver's rules.
 *
 * @param unknown_type $field
 * @return string The name of the escaped field for this Model (i.e. id becomes `Post`.`id`).
 */
	function escapeField($field, $alias = null) {
		if ($alias == null) {
			$alias = $this->name;
		}
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db->name($alias) . '.' . $db->name($field);
	}
/**
 * Returns the current record's ID
 *
 * @param unknown_type $list
 * @return mixed The ID of the current record
 */
	function getID($list = 0) {
		if (empty($this->id) || (is_array($this->id) && isset($this->id[0]) && empty($this->id[0]))) {
			return false;
		}

		if (!is_array($this->id)) {
			return $this->id;
		}

		if (count($this->id) == 0) {
			return false;
		}

		if (isset($this->id[$list]) && !empty($this->id[$list])) {
			return $this->id[$list];
		} elseif (isset($this->id[$list])) {
			return false;
		}

		foreach($this->id as $id) {
			return $id;
		}

		return false;
	}
/**
 * Returns the ID of the last record this Model inserted
 *
 * @return mixed
 */
	function getLastInsertID() {
		return $this->getInsertID();
	}
/**
 * Returns the ID of the last record this Model inserted
 *
 * @return mixed
 */
	function getInsertID() {
		return $this->__insertID;
	}
/**
 * Sets the ID of the last record this Model inserted
 *
 * @param mixed $id
 * @return void
 */
	function setInsertID($id) {
		$this->__insertID = $id;
	}
/**
 * Returns the number of rows returned from the last query
 *
 * @return int
 */
	function getNumRows() {
		//return $this->__numRows;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db->lastNumRows();
	}
/**
 * Returns the number of rows affected by the last query
 *
 * @return int
 */
	function getAffectedRows() {
		//return $this->__affectedRows;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db->lastAffected();
	}
/**
 * Sets the DataSource to which this model is bound
 *
 * @param string $dataSource The name of the DataSource, as defined in Connections.php
 * @return boolean True on success
 */
	function setDataSource($dataSource = null) {
		if ($dataSource != null) {
			$this->useDbConfig = $dataSource;
		}

		$db =& ConnectionManager::getDataSource($this->useDbConfig);

		if (isset($db->config['prefix'])) {
			$this->tablePrefix = $db->config['prefix'];
		}

		if (empty($db) || $db == null || !is_object($db)) {
			return $this->cakeError('missingConnection', array(array('className' => $this->name)));
		}
	}
/**
 * Gets the DataSource to which this model is bound.
 * Not safe for use with some versions of PHP4, because this class is overloaded.
 *
 * @return DataSource A DataSource object
 */
	function &getDataSource() {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db;
	}
/**
 * Gets all the models with which this model is associated
 *
 * @param string $type
 * @return array
 */
	function getAssociated($type = null) {
		if ($type == null) {
			$associated = array();
			foreach ($this->__associations as $assoc) {
				if (!empty($this->{$assoc})) {
					$models = array_keys($this->{$assoc});
					foreach ($models as $m) {
						$associated[$m] = $assoc;
					}
				}
			}
			return $associated;
		} elseif (in_array($type, $this->__associations)) {
			if (empty($this->{$type})) {
				return array();
			}
			return array_keys($this->{$type});
		} else {
			$assoc = am($this->hasOne, $this->hasMany, $this->belongsTo, $this->hasAndBelongsToMany);
			if (array_key_exists($type, $assoc)) {
				foreach ($this->__associations as $a) {
					if (isset($this->{$a}[$type])) {
						$assoc[$type]['association'] = $a;
						break;
					}
				}
				return $assoc[$type];
			}
			return null;
		}
	}
/**
 * Before find callback
 *
 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
 * @return boolean True if the operation should continue, false if it should abort
 */
	function beforeFind($queryData) {
		return true;
	}
/**
 * After find callback. Can be used to modify any results returned by find and findAll.
 *
 * @param mixed $results The results of the find operation
 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 */
	function afterFind($results, $primary = false) {
		return $results;
	}
/**
 * Before save callback
 *
 * @return boolean True if the operation should continue, false if it should abort
 */
	function beforeSave() {
		return true;
	}
/**
 * After save callback
 *
 * @return void
 */
	function afterSave() {
		return true;
	}
/**
 * Before delete callback
 *
 * @return boolean True if the operation should continue, false if it should abort
 */
	function beforeDelete() {
		return true;
	}
/**
 * After delete callback
 *
 * @return void
 */
	function afterDelete() {
		return true;
	}
/**
 * Before validate callback
 *
 * @return True if validate operation should continue, false to abort
 */
	function beforeValidate() {
		return true;
	}
/**
 * DataSource error callback
 *
 * @return void
 */
	function onError() {
	}
/**
 * Private method.  Clears cache for this model
 *
 * @param string $type If null this deletes cached views if CACHE_CHECK is true
 *                     Will be used to allow deleting query cache also
 * @return boolean true on delete
 */
	function _clearCache($type = null) {
		if ($type === null) {
			if (defined('CACHE_CHECK') && CACHE_CHECK === true) {
				$assoc[] = strtolower(Inflector::pluralize($this->name));

				foreach($this->__associations as $key => $association) {
					foreach($this->$association as $key => $className) {
						$check = strtolower(Inflector::pluralize($className['className']));

						if (!in_array($check, $assoc)) {
							$assoc[] = strtolower(Inflector::pluralize($className['className']));
						}
					}
				}
				clearCache($assoc);
				return true;
			}
		} else {
			//Will use for query cache deleting
		}
	}
/**
 * Called when serializing a model
 *
 * @return array
 */
	function __sleep() {
		$return = array_keys(get_object_vars($this));
		return $return;
	}
/**
 * Called when unserializing a model
 *
 * @return void
 */
	function __wakeup() {
	}
}

if (!defined('CAKEPHP_UNIT_TEST_EXECUTION')) {
	Overloadable::overload('Model');
}

?>