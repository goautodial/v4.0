<?php
/**
	The MIT License (MIT)
	
	Copyright (c) 2015 Ignacio Nieto Carvajal
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

namespace creamy;

/**
 * The DbConnector interface defines the common API all database connectors must
 * implement to be used in Creamy. The work of a DbConnector is to connect to a
 * database and expose a number of methods that will allow the DbHandler and other
 * classes in Creamy access and interact with the CRM database.
 *
 * Every DbConnector must adhere to the Singleton design pattern, thus implementing
 * the getInstance() method for retrieving the shared, singleton instance of the class.
 * @author Ignacio Nieto Carvajal <contact@digitalleaves.com>
 * @copyright Copyright (c) 2010
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 * @version   1.0
 */
interface DbConnector {
	/**
     * This method retrieves the shared instance (Singleton pattern) of this DbConnector.
     * Inheriting this class would require reloading connection info.
     * @uses $db = MyConnectorType::getInstance();
     * @return object Returns the current instance.
     */
    public static function getInstance();

    /**
     * This method allows to set a prefix to the database for all tables/queries.
     * @param string $prefix     Contains a tableprefix
     */
    public function setPrefix($prefix = '');
    
	/**
	 * Gets the count of the rows obtained in the last query, applying filters and limits.
	 */
	public function getRowCount();

	/**
	 * Gets the count of rows that would have been obtained if the last query didn't have a LIMIT clause.
	 * This is useful for pagination in datatables.
	 */
	public function getUnlimitedRowCount();

    /**
     * Pass in a raw query and an array containing the parameters to bind to the prepaird statement.
     *
     * @param string $query      			Contains a user-provided query.
     * @param array  $bindParams 			All variables to bind to the SQL statment.
     * @param bool   $sanitize   			If query should be filtered before execution
     * @param bool	 $countFilteredResults 	If true, SQL_CALC_FOUND_ROWS would be added to the query and a second query will be performed to calculate the filtered "unlimited" row count.
     * @return array Contains the returned rows from the query or NULL if an error happened.
     */
    public function rawQuery ($query, $bindParams = null, $sanitize = true, $countFilteredResults = false);
    
    /**
     * Performs a query with the given $query string and returns the result without binding parameters to the prepaid statement.
     * @param string $query   Contains a user-provided select query.
     * @param int    $numRows The number of rows total to return.
     * @param bool	 $countFilteredResults 	If true, SQL_CALC_FOUND_ROWS would be added to the query and a second query will be performed to calculate the filtered "unlimited" row count.
     * @return array Contains the returned rows from the query.
     */
    public function query($query, $numRows = null, $countFilteredResults = false);

    /**
     * A convenient SELECT * function. If $countFilteredResults is set, once 
     * the results are obtained, it performs a second mysql query to obtain
     * the total filtered row count (useful for datatables paging).
     *
     * @param string  $tableName 				The name of the database table to work with.
     * @param integer $numRows   				The number of rows total to return.
     * @param bool	  $countFilteredResults 	If true, SQL_CALC_FOUND_ROWS would be added to the query and a second query will be performed to calculate the filtered "unlimited" row count.
     * @return array  Contains the returned rows from the select query.
     */
    public function get($tableName, $numRows = null, $columns = '*', $countFilteredResults = false);

    /**
     * A convenient SELECT * function to get one record.
     * @param string  $tableName 				The name of the database table to work with.
     * @param bool	  $countFilteredResults 	If true, SQL_CALC_FOUND_ROWS would be added to the query and a second query will be performed to calculate the filtered "unlimited" row count.
     * @return array Contains the returned rows from the select query.
     */
    public function getOne($tableName, $columns = '*', $countFilteredResults = false);
    
    /**
     * A convenient SELECT * function to get one value.
     *
     * @param string  $tableName 				The name of the database table to work with.
     * @param bool	  $countFilteredResults 	If true, SQL_CALC_FOUND_ROWS would be added to the query and a second query will be performed to calculate the filtered "unlimited" row count.
     * @return array Contains the returned column from the select query.
     */
    public function getValue($tableName, $column, $countFilteredResults = false);

    /**
     * Performs an INSERT in a table with a $tableName and an associative array of $insertData, of type: "field" => "value".
     * @param string $tableName The name of the table.
     * @param array $insertData Data containing information for inserting into the DB.
     * @return boolean Boolean indicating whether the insert query was completed succesfully.
     */
    public function insert($tableName, $insertData);

    /**
     * A convenient function that returns TRUE if exists at least an element that
     * satisfy the where condition specified calling the "where" method before this one.
     * @param string  $tableName The name of the database table to work with.
     * @return array Contains the returned rows from the select query.
     */
    public function has($tableName);

    /**
     * Update query. Be sure to first call the "where" method.
     * The $tableData is an associative array of type: "field" => "new value".
     * @param string $tableName The name of the database table to work with.
     * @param array  $tableData Array of data to update the desired row.
     * @return boolean Indicates success. 0 or 1.
     */
    public function update($tableName, $tableData);

    /**
     * Delete query. Call the "where" method first.
     * @param string  $tableName The name of the database table to work with.
     * @param integer $numRows   The number of rows to delete.
     * @return boolean Indicates success. 0 or 1.
     */
    public function delete($tableName, $numRows = null);

    /**
     * DROP table query. Use with caution.
     * @param string  $tableName The name of the database table to drop.
     * @param boolean $cascade if true, drop table in CASCADE mode.
     * @return boolean Indicates success. 0 or 1.
     */
    public function dropTable($tableName, $cascade = false);

    /**
     * ALTER the column type from a table query. 
     *
     * @param string  $tableName The name of the database table to alter the column from.
     * @param string  $columnName The name of the column to alter its type.
     * @param string  $columnNewType The new type for the column.
     *
     * @return boolean Indicates success. 0 or 1.
     */
    public function alterColumnFromTable($tableName, $columnName, $columnNewType);

    /**
     * DROP column from table query. Use with caution.
     * @param string  $tableName The name of the database table to drop the column from.
     * @param string  $columnName The name of the column to drop.
     * @return boolean Indicates success. 0 or 1.
     */
    public function dropColumnFromTable($tableName, $columnName);

	/**
	 * ALTER table, adding a unique field to the table.
	 *
     * @param string  $tableName The name of the database table to drop the column from.
     * @param string  $columnName The name of the column to drop.
     *
     * @return boolean Indicates success. 0 or 1.
	 */
    public function setColumnAsUnique($tableName, $columnName);

    /**
     * ADD column to table query. Use with caution.
     * @param string  $tableName The name of the database table to drop the column from.
     * @param string  $columnName The name of the column to drop.
     * @return boolean Indicates success. 0 or 1.
     */
    public function addColumnToTable($tableName, $columnName, $columnType, $defaultValue = null);

	/**
	 * CREATE a table.
	 * @param string 	$tableName The name of the table to create.
	 * @param array		fields an associative array containing the names of the fields as keys and the data types as values. 
	 *        I.E: ["id" => "INT(11) AUTO_INCREMENT", "phone" => "VARCHAR(80)", "description" => "TEXT"...]
	 * @param array		$unique_keys an array containing the unique keys for the table as strings. I.E: ["passport_number", "name"]
	 * @return boolean Indicating success. 0 or 1.
	 */
	public function createTable($tableName, $fields, $unique_keys = null);

	/**
	 * Drops an event from the database if it exists.
	 * @param String $eventName name of the event to drop.
	 * @return bool true if deletion succeed, false otherwise.
	 */
	public function dropEvent($eventName);
	
    /**
     * This method allows you to specify multiple (method chaining optional) AND WHERE statements for SQL queries.
     * @uses $MySqliDb->where('id', 7)->where('title', 'MyTitle');
     * @param string $whereProp  The name of the database field.
     * @param mixed  $whereValue The value of the database field.
     * @return DbConnector the instance of the connector to chain other where calls.
     */
    public function where($whereProp, $whereValue = null, $operator = null);

    /**
     * This method allows you to specify multiple (method chaining optional) OR WHERE statements for SQL queries.
     * @uses $MySqliDb->orWhere('id', 7)->orWhere('title', 'MyTitle');
     * @param string $whereProp  The name of the database field.
     * @param mixed  $whereValue The value of the database field.
     * @return DbConnector the instance of the connector to chain other where calls.
     */
    public function orWhere($whereProp, $whereValue = null, $operator = null);
    
    /**
     * This method allows you to concatenate joins for the final SQL statement.
     * @uses $MySqliDb->join('table1', 'field1 <> field2', 'LEFT')
     * @param string $joinTable The name of the table.
     * @param string $joinCondition the condition.
     * @param string $joinType 'LEFT', 'INNER' etc.
     * @return DbConnector the instance of the connector to chain other where calls.
     */
     public function join($joinTable, $joinCondition, $joinType = '');
     
    /**
     * This method allows you to specify multiple (method chaining optional) ORDER BY statements for SQL queries.
     * @uses $MySqliDb->orderBy('id', 'desc')->orderBy('name', 'desc');
     * @param string $orderByField The name of the database field.
     * @param string $orderByDirection Order direction.
     * @return DbConnector the instance of the connector to chain other where calls.
     */
    public function orderBy($orderByField, $orderbyDirection = "DESC", $customFields = null);

    /**
     * This method allows you to specify multiple (method chaining optional) GROUP BY statements for SQL queries.
     * @uses $MySqliDb->groupBy('name');
     * @param string $groupByField The name of the database field.
     * @return DbConnector the instance of the connector to chain other where calls.
     */
    public function groupBy($groupByField);

    /**
     * This methods returns the ID of the last inserted item
     * @return integer The last inserted item ID.
     */
    public function getInsertId();

    /**
     * Escape harmful characters which might affect a query.
     * @param string $str The string to escape.
     * @return string The escaped string.
     */
    public function escape($str);

    /**
     * Method returns last executed query
     * @return string
     */
    public function getLastQuery();

    /**
     * Method returns mysql error
     * @return string
     */
    public function getLastError();

    /**
     * Method returns generated interval function as a string
     * @param string interval in the formats:
     *        "1", "-1d" or "- 1 day" -- For interval - 1 day
     *        Supported intervals [s]econd, [m]inute, [h]hour, [d]day, [M]onth, [Y]ear
     *        Default null;
     * @param string Initial date
     * @return string
    */
    public function interval ($diff, $func = "NOW()");
    
    /**
     * Method returns generated interval function as an insert/update function
     * @param string interval in the formats:
     *        "1", "-1d" or "- 1 day" -- For interval - 1 day
     *        Supported intervals [s]econd, [m]inute, [h]hour, [d]day, [M]onth, [Y]ear
     *        Default null;
     * @param string Initial date
     * @return array
    */
    public function now ($diff = null, $func = "NOW()");

    /**
     * Method generates incremental function call
     * @param int increment amount. 1 by default
     */
    public function inc($num = 1);

    /**
     * Method generates decrimental function call
     * @param int increment amount. 1 by default
     */
    public function dec ($num = 1);
    
    /**
     * Method generates change boolean function call
     * @param string column name. null by default
     */
    public function not ($col = null);

    /**
     * Method generates user defined function call
     * @param string user function body
     */
    public function func ($expr, $bindParams = null);

    /**
     * Begin a transaction, in the systems that permit it. The transaction will later
     * have to be commited with commit() or dismissed with rollback().
     */
    public function startTransaction();

    /**
     * Transaction commit. Used after startTransaction() to confirm the changes.
     */
    public function commit();

    /**
     * Transaction rollback function
     */
    public function rollback();
}

?>