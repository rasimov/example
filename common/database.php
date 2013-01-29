<?php
defined( '_BASE_' ) or die;

class database {
        /** @var string Internal variable to hold the query sql */
        var $_sql                       = '';
        /** @var int Internal variable to hold the database error number */
        var $_errorNum          = 0;
        /** @var string Internal variable to hold the database error message */
        var $_errorMsg          = '';
        /** @var string Internal variable to hold the prefix used on all database tables */
        var $_table_prefix      = '';
        /** @var Internal variable to hold the connector resource */
        var $_resource          = '';
        /** @var Internal variable to hold the last query cursor */
        var $_cursor            = null;
        /** @var boolean Debug option */
        var $_debug                     = 0;
        /** @var int The limit for the query */
        var $_limit                     = 0;
        /** @var int The for offset for the limit */
        var $_offset            = 0;
        /** @var int A counter for the number of queries performed by the object instance */
        var $_ticker            = 0;
        /** @var array A log of queries */
        var $_log                       = null;
        /** @var string The null/zero date string */
        var $_nullDate          = '0000-00-00 00:00:00';
        /** @var string Quote for named objects */
        var $_nameQuote         = '`';
        /**
        * Database object constructor
        * @param string Database host
        * @param string Database user name
        * @param string Database user password
        * @param string Database name
        * @param string Common prefix for all tables
        * @param boolean If true and there is an error, go offline
        */
        function database( $host='localhost', $user, $pass, $db='', $table_prefix='', $goOffline=true ) {
                // perform a number of fatality checks, then die gracefully
                if (!function_exists( 'mysql_connect' )) {
                        $SystemError = 1;
                        if ($goOffline) {
                                $basePath = dirname( __FILE__ );
                                exit();
                        }
                }
                if (phpversion() < '4.2.0') {
                        if (!($this->_resource = @mysql_connect( $host, $user, $pass ))) {
                                $SystemError = 2;
                                if ($goOffline) {
                                        $basePath = dirname( __FILE__ );
                                        exit();
                                }
                        }
                } else {                
                        if (!($this->_resource = @mysql_connect( $host, $user, $pass, true ))) {
                                $SystemError = 2;
                                if ($goOffline) {
                                        $basePath = dirname( __FILE__ );
                                        include $basePath . '/config.php';
                                        exit();
                                }
                        }
                }
                if ($db != '' && !mysql_select_db( $db, $this->_resource )) {
                        $this->_errorMsg = 'There is no base!';
                        $this->_errorNum = 1;
 
                        $SystemError = 3;
                        if ($goOffline) {
                                $basePath = dirname( __FILE__ );
                                include $basePath . '/config.php';
                                exit();
                        }
                }

                $this->_cursor = mysql_query( "set session character_set_server=cp1251;", $this->_resource );
                $this->_cursor = mysql_query( "set session character_set_database=cp1251;", $this->_resource );
                $this->_cursor = mysql_query( "set session character_set_connection=cp1251;", $this->_resource );
                $this->_cursor = mysql_query( "set session character_set_results=cp1251;", $this->_resource );
                $this->_cursor = mysql_query( "set session character_set_client=cp1251;", $this->_resource );

                $this->_table_prefix = $table_prefix;
                $this->_ticker = 0;
                $this->_log = array();
        }
        /**
         * Returns an array of public properties
         * @return array
         */
        function getPublicProperties() {
                static $cache = null;
                if (is_null( $cache )) {
                        $cache = array();
                        foreach (get_class_vars( get_class( $this ) ) as $key=>$val) {
                                if (substr( $key, 0, 1 ) != '_') {
                                        $cache[] = $key;
                                }
                        }
                }
                return $cache;
        }
        /**
         * @param int
         */
        function debug( $level ) {
                $this->_debug = intval( $level );
        }
        /**
         * @return int The error number for the t recent query
         */
        function getErrorNum() {
                return $this->_errorNum;
        }
        /**
        * @return string The error message for the t recent query
        */
        function getErrorMsg() {
                return str_replace( array( "\n", "'" ), array( '\n', "\'" ), $this->_errorMsg );
        }
        /**
        * Get a database escaped string
        * @return string
        */
        function getEscaped( $text ) {
                return mysql_escape_string( $text );
        }
        /**
        * Get a quoted database escaped string
        * @return string
        */
        function Quote( $text ) {
                return '\'' . $this->getEscaped( $text ) . '\'';
        }
        /**
         * Quote an identifier name (field, table, etc)
         * @param string The name
         * @return string The quoted name
         */
        function NameQuote( $s ) {
                $q = $this->_nameQuote;
                if (strlen( $q ) == 1) {
                        return $q . $s . $q;
                } else {
                        return $q{0} . $s . $q{1};
                }
        }
        /**
         * @return string The database prefix
         */
        function getPrefix() {
                return $this->_table_prefix;
        }
        /**
         * @return string Quoted null/zero date string
         */
        function getNullDate() {
                return $this->_nullDate;
        }
        /**
        * Sets the SQL query string for later execution.
        *
        * This function replaces a string identifier <var>$prefix</var> with the
        * string held is the <var>_table_prefix</var> class variable.
        *
        * @param string The SQL query
        * @param string The offset to start selection
        * @param string The number of results to return
        * @param string The common table prefix
        */
        function setQuery( $sql, $offset = 0, $limit = 0, $prefix='#__' ) {
            global $conf_logging_qry,$confstorelog;
                $this->_sql = $this->replacePrefix( $sql, $prefix );
            if($conf_logging_qry){
             $fp = fopen($confstorelog.unique().".txt", 'a');
             fwrite($fp, $this->_sql."\r\n");
             fclose($fp);
            }
                $this->_limit = intval( $limit );
                $this->_offset = intval( $offset );
        }

        /**
         * This function replaces a string identifier <var>$prefix</var> with the
         * string held is the <var>_table_prefix</var> class variable.
         *
         * @param string The SQL query
         * @param string The common table prefix
         * @author thede, David McKinnis
         */
        function replacePrefix( $sql, $prefix='#__' ) {
                $sql = trim( $sql );

                $escaped = false;
                $quoteChar = '';

                $n = strlen( $sql );

                $startPos = 0;
                $literal = '';
                while ($startPos < $n) {
                        $ip = strpos($sql, $prefix, $startPos);
                        if ($ip === false) {
                                break;
                        }

                        $j = strpos( $sql, "'", $startPos );
                        $k = strpos( $sql, '"', $startPos );
                        if (($k !== FALSE) && (($k < $j) || ($j === FALSE))) {
                                $quoteChar      = '"';
                                $j                      = $k;
                        } else {
                                $quoteChar      = "'";
                        }

                        if ($j === false) {
                                $j = $n;
                        }

                        $literal .= str_replace( $prefix, $this->_table_prefix, substr( $sql, $startPos, $j - $startPos ) );
                        $startPos = $j;

                        $j = $startPos + 1;

                        if ($j >= $n) {
                                break;
                        }

                        // quote comes first, find end of quote
                        while (TRUE) {
                                $k = strpos( $sql, $quoteChar, $j );
                                $escaped = false;
                                if ($k === false) {
                                        break;
                                }
                                $l = $k - 1;
                                while ($l >= 0 && $sql{$l} == '\\') {
                                        $l--;
                                        $escaped = !$escaped;
                                }
                                if ($escaped) {
                                        $j      = $k+1;
                                        continue;
                                }
                                break;
                        }
                        if ($k === FALSE) {
                                // error in the query - no end quote; ignore it
                                break;
                        }
                        $literal .= substr( $sql, $startPos, $k - $startPos + 1 );
                        $startPos = $k+1;
                }
                if ($startPos < $n) {
                        $literal .= substr( $sql, $startPos, $n - $startPos );
                }
                return $literal;
        }
        /**
        * @return string The current value of the internal SQL vairable
        */
        function getQuery() {
                return "<pre>" . htmlspecialchars( $this->_sql ) . "</pre>";
        }
        /**
        * Execute the query
        * @return mixed A database resource if successful, FALSE if not.
        */
        function query() {
                global $Config_debug;
                global $conf_dbreadonly;
                global $conf_use_locales,$conf_curr_locale;
                if(isset($conf_dbreadonly)&&$conf_dbreadonly){
                 $currsql =  strtolower(" ".$this->_sql);
                 if(
                   (strpos($currsql," insert ")!==false) ||
                   (strpos($currsql," update ")!==false) ||
                   (strpos($currsql," create ")!==false) ||
                   (strpos($currsql," drop "  )!==false) ||
                   (strpos($currsql," delete ")!==false)
                   ) return false;
                }
                if ($this->_debug) {
                        $this->_ticker++;
                        $this->_log[] = $this->_sql;
                }
                if ($this->_limit > 0 || $this->_offset > 0) {
                        $this->_sql .= "\nLIMIT $this->_offset, $this->_limit";
                }

                $this->_errorNum = 0;
                $this->_errorMsg = '';
                $this->_cursor = mysql_query( $this->_sql, $this->_resource );
                if (!$this->_cursor) {
                        $this->_errorNum = mysql_errno( $this->_resource );
                        $this->_errorMsg = mysql_error( $this->_resource )." SQL=$this->_sql";
                        if ($this->_debug) {
                                trigger_error( mysql_error( $this->_resource ), E_USER_NOTICE );
                                echo "<pre>" . $this->_sql . "</pre>\n";
                                if (function_exists( 'debug_backtrace' )) {
                                        foreach( debug_backtrace() as $back) {
                                                if (@$back['file']) {
                                                        echo '<br />'.$back['file'].':'.$back['line'];
                                                }
                                        }
                                }
                        }
                        return false;
                }
                return $this->_cursor;
        }

        /**
         * @return int The number of affected rows in the previous operation
         */
        function getAffectedRows() {
                return mysql_affected_rows( $this->_resource );
        }

        function query_batch( $abort_on_error=true, $p_transaction_safe = false) {
                $this->_errorNum = 0;
                $this->_errorMsg = '';
                if ($p_transaction_safe) {
                        $si = mysql_get_server_info( $this->_resource );
                        preg_match_all( "/(\d+)\.(\d+)\.(\d+)/i", $si, $m );
                        if ($m[1] >= 4) {
                                $this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
                        } else if ($m[2] >= 23 && $m[3] >= 19) {
                                $this->_sql = 'BEGIN WORK;' . $this->_sql . '; COMMIT;';
                        } else if ($m[2] >= 23 && $m[3] >= 17) {
                                $this->_sql = 'BEGIN;' . $this->_sql . '; COMMIT;';
                        }
                }
                $query_split = preg_split ("/[;]+/", $this->_sql);
                $error = 0;
                foreach ($query_split as $command_line) {
                        $command_line = trim( $command_line );
                        if ($command_line != '') {
                                $this->_cursor = mysql_query( $command_line, $this->_resource );
                                if (!$this->_cursor) {
                                        $error = 1;
                                        $this->_errorNum .= mysql_errno( $this->_resource ) . ' ';
                                        $this->_errorMsg .= mysql_error( $this->_resource )." SQL=$command_line <br />";
                                        if ($abort_on_error) {
                                                return $this->_cursor;
                                        }
                                }
                        }
                }
                return $error ? false : true;
        }

        /**
        * Diagnostic function
        */
        function explain() {
                $temp = $this->_sql;
                $this->_sql = "EXPLAIN $this->_sql";
                $this->query();

                if (!($cur = $this->query())) {
                        return null;
                }
                $first = true;

                $buf = "<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" bgcolor=\"#000000\" align=\"center\">";
                $buf .= $this->getQuery();
                while ($row = mysql_fetch_assoc( $cur )) {
                        if ($first) {
                                $buf .= "<tr>";
                                foreach ($row as $k=>$v) {
                                        $buf .= "<th bgcolor=\"#ffffff\">$k</th>";
                                }
                                $buf .= "</tr>";
                                $first = false;
                        }
                        $buf .= "<tr>";
                        foreach ($row as $k=>$v) {
                                $buf .= "<td bgcolor=\"#ffffff\">$v</td>";
                        }
                        $buf .= "</tr>";
                }
                $buf .= "</table><br />&nbsp;";
                mysql_free_result( $cur );

                $this->_sql = $temp;

                return "<div style=\"background-color:#FFFFCC\" align=\"left\">$buf</div>";
        }
        /**
        * @return int The number of rows returned from the t recent query.
        */
        function getNumRows( $cur=null ) {
                return mysql_num_rows( $cur ? $cur : $this->_cursor );
        }

        /**
        * This method loads the first field of the first row returned by the query.
        *
        * @return The value returned in the query or null if the query failed.
        */
        function loadResult() {
                if (!($cur = $this->query())) {
                        return null;
                }
                $ret = null;
                if ($row = mysql_fetch_row( $cur )) {
                        $ret = $row[0];
                }
                mysql_free_result( $cur );
                return $ret;
        }
        /**
        * Load an array of single field results into an array
        */
        function loadResultArray($numinarray = 0) {
                if (!($cur = $this->query())) {
                        return null;
                }
                $array = array();
                while ($row = mysql_fetch_row( $cur )) {
                        $array[] = $row[$numinarray];
                }
                mysql_free_result( $cur );
                return $array;
        }
        /**
        * Load a assoc list of database rows
        * @param string The field name of a primary key
        * @return array If <var>key</var> is empty as sequential list of returned records.
        */
        function loadAssocList( $key='' ) {
                if (!($cur = $this->query())) {
                        return null;
                }
                $array = array();
                while ($row = mysql_fetch_assoc( $cur )) {
                        if ($key) {
                                $array[$row[$key]] = $row;
                        } else {
                                $array[] = $row;
                        }
                }
                mysql_free_result( $cur );
                return $array;
        }
        /**
        * This global function loads the first row of a query into an object
        *
        * If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
        * If <var>object</var> has a value of null, then all of the returned query fields returned in the object.
        * @param string The SQL query
        * @param object The address of variable
        */
        function loadObject( &$object ) {
                if ($object != null) {
                        if (!($cur = $this->query())) {
                                return false;
                        }
                        if ($array = mysql_fetch_assoc( $cur )) {
                                mysql_free_result( $cur );
                                BindArrayToObject( $array, $object, null, null, false );
                                return true;
                        } else {
                                return false;
                        }
                } else {
                        if ($cur = $this->query()) {
                                if ($object = mysql_fetch_object( $cur )) {
                                        mysql_free_result( $cur );
                                        return true;
                                } else {
                                        $object = null;
                                        return false;
                                }
                        } else {
                                return false;
                        }
                }
        }

        function loadObjectfixtime( &$object ) {
                if ($object != null) {
                        if (!($cur = $this->query())) {
                                return false;
                        }
                        if ($array = mysql_fetch_assoc( $cur )) {
                                mysql_free_result( $cur );
                                BindArrayToObjectfixtime( $array, $object, null, null, false );
                                return true;
                        } else {
                                return false;
                        }
                } else {
                        if ($cur = $this->query()) {
                                if ($object = mysql_fetch_object( $cur )) {
                                        mysql_free_result( $cur );
                                        return true;
                                } else {
                                        $object = null;
                                        return false;
                                }
                        } else {
                                return false;
                        }
                }
        }

        /**
        * Load a list of database objects
        * @param string The field name of a primary key
        * @return array If <var>key</var> is empty as sequential list of returned records.
        * If <var>key</var> is not empty then the returned array is indexed by the value
        * the database key.  Returns <var>null</var> if the query fails.
        */
        function loadObjectList( $key='' ) {
                if (!($cur = $this->query())) {
                        return null;
                }
                $array = array();
                while ($row = mysql_fetch_object( $cur )) {
                        if ($key) {
                                $array[$row->$key] = $row;
                        } else {
                                 //$this->fixtime($row);
                                 $array[] = $row;
                        }
                }
                mysql_free_result( $cur );
                return $array;
        }


        /**
        * @return The first row of the query.
        */
        function loadRow() {
                if (!($cur = $this->query())) {
                        return null;
                }
                $ret = null;
                if ($row = mysql_fetch_row( $cur )) {
                        $ret = $row;
                }
                mysql_free_result( $cur );
                return $ret;
        }
        /**
        * Load a list of database rows (numeric column indexing)
        * @param string The field name of a primary key
        * @return array If <var>key</var> is empty as sequential list of returned records.
        * If <var>key</var> is not empty then the returned array is indexed by the value
        * the database key.  Returns <var>null</var> if the query fails.
        */
        function loadRowList( $key='' ) {
                if (!($cur = $this->query())) {
                        return null;
                }
                $array = array();
                while ($row = mysql_fetch_array( $cur )) {
                        if ($key) {
                                $array[$row[$key]] = $row;
                        } else {
                                $array[] = $row;
                        }
                }
                mysql_free_result( $cur );
                return $array;
        }
        /**
        * Document::db_insertObject()
        *
        * { Description }
        *
        * @param [type] $keyName
        * @param [type] $verbose
        */
        function insertObject( $table, &$object, $keyName = NULL, $verbose=false ) {
                global $conf_dbreadonly;
                if(isset($conf_dbreadonly)&&$conf_dbreadonly){return;}
                $fmtsql = "INSERT INTO $table ( %s ) VALUES ( %s ) ";
                $fields = array();
                $vars = $object->getPublicProperties();
                foreach ($vars as $k) {
                        $v =& $object->$k;
                        if (is_array( $v ) or is_object( $v ) or $v === NULL) {
                                continue;
                        }
                        $fields[] = $this->NameQuote( $k );;
                        $values[] = $this->Quote( $v );
                }
                $this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
                ($verbose) && print "$sql<br />\n";
                if (!$this->query()) {
                        return false;
                }
                $id = mysql_insert_id( $this->_resource );
                ($verbose) && print "id=[$id]<br />\n";
                if ($keyName && $id) {
                        $object->$keyName = $id;
                }
                return true;
        }

        function insertObjectIgnore( $table, &$object, $keyName = NULL, $verbose=false ) {
                $fmtsql = "INSERT IGNORE INTO $table ( %s ) VALUES ( %s ) ";
                $fields = array();
                $vars = $object->getPublicProperties();
                foreach ($vars as $k) {
                        $v =& $object->$k;
                        if (is_array( $v ) or is_object( $v ) or $v === NULL) {
                                continue;
                        }
                        $fields[] = $this->NameQuote( $k );;
                        $values[] = $this->Quote( $v );
                }
                $this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
                ($verbose) && print "$sql<br />\n";
                if (!$this->query()) {
                        return false;
                }
                $id = mysql_insert_id( $this->_resource );
                ($verbose) && print "id=[$id]<br />\n";
                if ($keyName && $id) {
                        $object->$keyName = $id;
                }
                return true;
        }

        /**
        * Document::db_updateObject()
        *
        * { Description }
        *
        * @param [type] $updateNulls
        */

        function lockObject($table){
                $fmtsql = "LOCK TABLES $table WRITE";
                $this->setQuery($fmtsql);
                return $this->query();
	}

        function unlockObject(){
                $fmtsql = "UNLOCK TABLES";
                $this->setQuery($fmtsql);
                return $this->query();
	}

        function updateObject( $table, &$object, $keyName, $updateNulls=true ) {
                global $conf_dbreadonly;
                if(isset($conf_dbreadonly)&&$conf_dbreadonly){return;}
                $fmtsql = "UPDATE $table SET %s WHERE %s";
                $tmp = array();
                foreach (get_object_vars( $object ) as $k => $v) {
                        if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
                                continue;
                        }
                        if( $k == $keyName ) { // PK not to be updated
                                $where = $keyName . '=' . $this->Quote( $v );
                                continue;
                        }
                        if ($v === NULL && !$updateNulls) {
                                continue;
                        }
                        if( $v == '' ) {
                                $val = "''";
                        } else {
                                $val = $this->Quote( $v );
                        }
                        $tmp[] = $this->NameQuote( $k ) . '=' . $val;
                }
                $this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
                return $this->query();
        }

        /**
        * @param boolean If TRUE, displays the last SQL statement sent to the database
        * @return string A standised error message
        */
        function stderr( $showSQL = false ) {
                return "DB function failed with error number $this->_errorNum"
                ."<br /><font color=\"red\">$this->_errorMsg</font>"
                .($showSQL ? "<br />SQL = <pre>$this->_sql</pre>" : '');
        }

        function insertid() {
                return mysql_insert_id( $this->_resource );
        }

        function getVersion() {
                return mysql_get_server_info( $this->_resource );
        }

        /**
         * @return array A list of all the tables in the database
         */
        function getTableList() {
                $this->setQuery( 'SHOW TABLES' );
                return $this->loadResultArray();
        }
        /**
         * @param array A list of table names
         * @return array A list the create SQL for the tables
         */
        function getTableCreate( $tables ) {
                $result = array();

                foreach ($tables as $tblval) {
                        $this->setQuery( 'SHOW CREATE table ' . $this->getEscaped( $tblval ) );
                        $rows = $this->loadRowList();
                        foreach ($rows as $row) {
                                $result[$tblval] = $row[1];
                        }
                }

                return $result;
        }
        /**
         * @param array A list of table names
         * @return array An array of fields by table
         */
        function getTableFields( $tables ) {
                $result = array();

                foreach ($tables as $tblval) {
                        $this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
                        $fields = $this->loadObjectList();
                        foreach ($fields as $field) {
                                $result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
                        }
                }

                return $result;
        }

        /**
        * Fudge method for ADOdb compatibility
        */
        function GenID( $foo1=null, $foo2=null ) {
                return '0';
        }
}

class DBTable {
        /** @var string Name of the table in the db schema relating to child class */
        var $_tbl               = '';
        /** @var string Name of the primary key field in the table */
        var $_tbl_key   = '';
        /** @var string Error message */
        var $_error     = '';
        /** @var Database Database connector */
        var $_db                = null;

        /**
        *       Object constructor to set table and key field
        *
        *       Can be overloaded/supplemented by the child class
        *       @param string $table name of the table in the db schema relating to child class
        *       @param string $key name of the primary key field in the table
        */
        function DBTable( $table, $key, &$db ) {
                $this->_tbl = $table;
                $this->_tbl_key = $key;
                $this->_db =& $db;
        }

        /**
         * Returns an array of public properties
         * @return array
         */
        function getPublicProperties() {
                static $cache = null;
                if (is_null( $cache )) {
                        $cache = array();
                        foreach (get_class_vars( get_class( $this ) ) as $key=>$val) {
                                if (substr( $key, 0, 1 ) != '_') {
                                        $cache[] = $key;
                                }
                        }
                }
                return $cache;
        }
        /**
         * Filters public properties
         * @access protected
         * @param array List of fields to ignore
         */
        function filter( $ignoreList=null ) {
                $ignore = is_array( $ignoreList );

                $iFilter = new InputFilter();
                foreach ($this->getPublicProperties() as $k) {
                        if ($ignore && in_array( $k, $ignoreList ) ) {
                                continue;
                        }
                        $this->$k = $iFilter->process( $this->$k );
                }
        }
        /**
         *      @return string Returns the error message
         */
        function getError() {
                return $this->_error;
        }
        /**
        * Gets the value of the class variable
        * @param string The name of the class variable
        * @return mixed The value of the class var (or null if no var of that name exists)
        */
        function get( $_property ) {
                if(isset( $this->$_property )) {
                        return $this->$_property;
                } else {
                        return null;
                }
        }
        /**
        * Set the value of the class variable
        * @param string The name of the class variable
        * @param mixed The value to assign to the variable
        */
        function set( $_property, $_value ) {
                $this->$_property = $_value;
        }

        /**
         * Resets public properties
         * @param mixed The value to set all properties to, default is null
         */
        function reset( $value=null ) {
                $keys = $this->getPublicProperties();
                foreach ($keys as $k) {
                        $this->$k = $value;
                }
        }
        /**
        *       binds a named array/hash to this object
        *
        *       can be overloaded/supplemented by the child class
        *       @param array $hash named array
        *       @return null|string     null is operation was satisfactory, otherwise returns an error
        */
        function bind( $array, $ignore='' ) {
                if (!is_array( $array )) {
                        $this->_error = strtolower(get_class( $this ))."::bind failed.";
                        return false;
                } else {
                       return BindArrayToObject( $array, $this, $ignore );
                }
        }

        /**
        *       create where statement
        *       @param int $oid optional argument, if not specifed then the value of current key is used
        *       @return count of records from the database operation
        */

        function getWhere($oid=null){
                if(is_array($oid)){
                        $where =array();
                        foreach($oid as $key=>$value){
                           if(is_numeric($value)){
                             $where[]=$key.'='.$value;
                           }
                           elseif(is_array($value)){
                             foreach($value as $operator=>$operand){
                                 $where[]=$key.' '.$operator.' '.$operand;
                             } 
                           }else{
                             $where[]=$key.'=\''.$value.'\'';
                           }
                        } 
                        $where =implode(" and ",$where);

                }else{
                        $k = $this->_tbl_key;
                        if ($oid !== null) {
                                $this->$k = $oid;
                        }
                        $oid = $this->$k;
                        if ($oid === null) {
                                return false;
                        }
                        $where = "$this->_tbl_key='$oid'";
                }
                return $where;
        }

        /**
        *       get max $this->_tbl_key value
        *       @param int $oid optional argument, if not specifed then the value of current key is used
        *       @return count of records from the database operation
        */
         
        function max( $oid=null, $add=null ) {
                if(($where = $this->getWhere($oid)) === false) return false;
                $this->_db->setQuery( "SELECT max(".$this->_tbl_key.") as cnt FROM $this->_tbl ".
                                      $this->getJoinClause($add).
                                      "WHERE ".$where." ".
                                      $this->getOrderClause($add).
                                      $this->getLimitClause($add)
                                    );


                $cnt  = $this->_db->loadObjectList();
                return $cnt[0]->cnt==""?0:$cnt[0]->cnt;
        }

        /**
        *       get rows count 
        *       @param int $oid optional argument, if not specifed then the value of current key is used
        *       @return count of records from the database operation
        */
         
        function count( $oid=null, $add=null ) {
                if(($where = $this->getWhere($oid)) === false) return false;
//                var_dump($where);
                $this->_db->setQuery( "SELECT count(0) as cnt FROM $this->_tbl ".
                                      $this->getJoinClause($add).
                                      "WHERE ".$where." ".
                                      $this->getOrderClause($add).
                                      $this->getLimitClause($add)
                                    );
                $cnt  = $this->_db->loadObjectList();
                return $cnt[0]->cnt;
        }

        /**
        *       load object
        *       @param int $oid optional argument, if not specifed then the value of current key is used
        *       @return any result from the database operation
        */
        function load( $oid=null ) {
                if(($where = $this->getWhere($oid)) === false) return false;
//                var_dump($where);
                $this->reset();
                $this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE ".$where );
                return $this->_db->loadObject( $this );
        }

        /**
        *       get orderby clauses 
        */
        function getOrderClause($add=null) {
                if(!is_array($add)) return " ";
                $clauses = " ";
                foreach($add as $key=>$value){
                     if($key=='orderby'){
                        $clauses .= "order by";
                        $o = array();
                        foreach($value as $field => $direction){
                              $o[] = $field.' '.$direction;
                        }
                        $clauses .= " ".implode(",",$o);

                     }
                }
                return $clauses;
        }

        /**
        *       get limit clauses 
        */
        function getLimitClause($add=null) {
                if(!is_array($add)) return " ";
                $clauses = " ";
                foreach($add as $key=>$value){
                     if($key=="limit"){
                        $clauses .= " limit ".$value;
                     }
                }
                return $clauses;
        }

        /**
        *       get join clauses 
        */
        function getJoinClause($add=null) {
                if(!is_array($add)) return " ";
                $clauses = " ";
                foreach($add as $key=>$value){
                     if($key=='join'){
                        foreach($value as $k => $v){
                           switch($k){
                             case 'i': $clauses.=' inner join '.$v ; break;
                             case 'l': $clauses.=' left join  '.$v ;break;
                             case 'r': $clauses.=' right join '.$v ;break;
                             case 'on': 
                               $clauses.=' on ';
                               $z = array();
                               foreach($v as $t1=>$t2){
                                 if(is_array($t2)){
                                   foreach($t2 as $f1=>$f2){
                                     $z[]= $this->_tbl.".".$t1.$f1.$f2;
                                   }
                                 }else{
                                   $z[]= $this->_tbl.".".$t1."=".$t2;
                                 }
                               }
                               $clauses .= implode(" and ",$z);
                             break;
                           }
                        }
                        
                        $clauses .= " ";
                     }
                }
                return $clauses;
        }


        /**
        *       get additional select from join clauses 
        */
        function getSelectJoinClause($add=null) {
                if(!is_array($add)) return " ";
                $clauses = " "; $o = array();
                foreach($add as $key=>$value){
                     if($key=='sel'){
                        foreach($value as $k){
                          $o[]=$k;
                        }
                     }
                }
                if(count($o)>0){
                  $clauses .=",".implode(',',$o);
                }
                return $clauses;
        }




        /**
        *       load object list 
        *       @param int $oid optional argument, if not specifed then the value of current key is used
        *       @return array which is result from the database operation
        */
        function loadList( $oid=null, $add=null) {
                if(($where = $this->getWhere($oid)) === false) return false;
//                var_dump($where);
                $this->reset();
                if(is_array($add)){

                }
                
                $this->_db->setQuery( "SELECT $this->_tbl.*".
                                      $this->getSelectJoinClause($add).
                                      " FROM $this->_tbl ".
                                      $this->getJoinClause($add).
                                      "WHERE ".$where." ".
                                      $this->getOrderClause($add).
                                      $this->getLimitClause($add)
                                    );
                return $this->_db->loadObjectList();
        }


        /**
        *       generic check method
        *
        *       can be overloaded/supplemented by the child class
        *       @return boolean True if the object is ok
        */
        function check() {
                return true;
        }

        /**
        * Inserts a new row if id is zero or updates an existing row in the database table
        *
        * Can be overloaded/supplemented by the child class
        * @param boolean If false, null object variables are not updated
        * @return null|string null if successful otherwise returns and error message
        */
        function store( $updateNulls=false ) {

                $k = $this->_tbl_key;
                $lock = $this->_db->lockObject($this->_tbl);
                if( !$lock ) {
                        $this->_error = strtolower(get_class( $this ))."::can't lock table <br />" . $this->_db->getErrorMsg();
                        return false;
                } 
                if ($this->$k) {
          
                        $ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
                } else {

                        $ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
                }

                if( !$ret ) {

                        $this->_error = strtolower(get_class( $this ))."::store failed <br />" . $this->_db->getErrorMsg();
                        return false;
                } else {

                $ret1 = $this->_db->unlockObject();
                        return true;
                }
        }
        /**
        */
        function move( $dirn, $where='' ) {
                $k = $this->_tbl_key;

                $sql = "SELECT $this->_tbl_key, ordering FROM $this->_tbl";

                if ($dirn < 0) {
                        $sql .= "\n WHERE ordering < $this->ordering";
                        $sql .= ($where ? "\n   AND $where" : '');
                        $sql .= "\n ORDER BY ordering DESC";
                        $sql .= "\n LIMIT 1";
                } else if ($dirn > 0) {
                        $sql .= "\n WHERE ordering > $this->ordering";
                        $sql .= ($where ? "\n   AND $where" : '');
                        $sql .= "\n ORDER BY ordering";
                        $sql .= "\n LIMIT 1";
                } else {
                        $sql .= "\nWHERE ordering = $this->ordering";
                        $sql .= ($where ? "\n AND $where" : '');
                        $sql .= "\n ORDER BY ordering";
                        $sql .= "\n LIMIT 1";
                }

                $this->_db->setQuery( $sql );

                $row = null;
                if ($this->_db->loadObject( $row )) {
                        $query = "UPDATE $this->_tbl"
                        . "\n SET ordering = '$row->ordering'"
                        . "\n WHERE $this->_tbl_key = '". $this->$k ."'"
                        ;
                        $this->_db->setQuery( $query );

                        if (!$this->_db->query()) {
                                $err = $this->_db->getErrorMsg();
                                die( $err );
                        }

                        $query = "UPDATE $this->_tbl"
                        . "\n SET ordering = '$this->ordering'"
                        . "\n WHERE $this->_tbl_key = '". $row->$k. "'"
                        ;
                        $this->_db->setQuery( $query );

                        if (!$this->_db->query()) {
                                $err = $this->_db->getErrorMsg();
                                die( $err );
                        }

                        $this->ordering = $row->ordering;
                } else {
                        $query = "UPDATE $this->_tbl"
                        . "\n SET ordering = '$this->ordering'"
                        . "\n WHERE $this->_tbl_key = '". $this->$k ."'"
                        ;
                        $this->_db->setQuery( $query );


                        if (!$this->_db->query()) {
                                $err = $this->_db->getErrorMsg();
                                die( $err );
                        }
                }
        }
        /**
        * Compacts the ordering sequence of the selected records
        * @param string Additional where query to limit ordering to a particular subset of records
        */
        function updateOrder( $where='' ) {
                $k = $this->_tbl_key;

                if (!array_key_exists( 'ordering', get_class_vars( strtolower(get_class( $this )) ) )) {
                        $this->_error = "attention: ".strtolower(get_class( $this ))." isn't implement ordering.";
                        return false;
                }

                if ($this->_tbl == "#__content_frontpage") {
                        $order2 = ", content_id DESC";
                } else {
                        $order2 = '';
                }

                $query = "SELECT $this->_tbl_key, ordering"
                . "\n FROM $this->_tbl"
                . ( $where ? "\n WHERE $where" : '' )
                . "\n ORDER BY ordering$order2 "
                ;
                $this->_db->setQuery( $query );
                if (!($orders = $this->_db->loadObjectList())) {
                        $this->_error = $this->_db->getErrorMsg();
                        return false;
                }
                // first pass, compact the ordering numbers
                for ($i=0, $n=count( $orders ); $i < $n; $i++) {
                        if ($orders[$i]->ordering >= 0) {
                                $orders[$i]->ordering = $i+1;
                        }
                }

                $shift = 0;
                $n=count( $orders );
                for ($i=0; $i < $n; $i++) {
                        //echo "i=$i id=".$orders[$i]->$k." order=".$orders[$i]->ordering;
                        if ($orders[$i]->$k == $this->$k) {
                                // place 'this' record in the desired location
                                $orders[$i]->ordering = min( $this->ordering, $n );
                                $shift = 1;
                        } else if ($orders[$i]->ordering >= $this->ordering && $this->ordering > 0) {
                                $orders[$i]->ordering++;
                        }
                }
        //echo '<pre>';print_r($orders);echo '</pre>';
                // compact once more until I can find a better algorithm
                for ($i=0, $n=count( $orders ); $i < $n; $i++) {
                        if ($orders[$i]->ordering >= 0) {
                                $orders[$i]->ordering = $i+1;
                                $query = "UPDATE $this->_tbl"
                                . "\n SET ordering = '". $orders[$i]->ordering ."'"
                                . "\n WHERE $k = '". $orders[$i]->$k ."'"
                                ;
                                $this->_db->setQuery( $query);
                                $this->_db->query();
        //echo '<br />'.$this->_db->getQuery();
                        }
                }

                // if we didn't reorder the current record, make it last
                if ($shift == 0) {
                        $order = $n+1;
                        $query = "UPDATE $this->_tbl"
                        . "\n SET ordering = '$order'"
                        . "\n WHERE $k = '". $this->$k ."'"
                        ;
                        $this->_db->setQuery( $query );
                        $this->_db->query();
        //echo '<br />'.$this->_db->getQuery();
                }
                return true;
        }
        /**
        *       Generic check for whether dependancies exist for this object in the db schema
        *
        *       can be overloaded/supplemented by the child class
        *       @param string $msg Error message returned
        *       @param int Optional key index
        *       @param array Optional array to compiles standard joins: format [label=>'Label',name=>'table name',idfield=>'field',joinfield=>'field']
        *       @return true|false
        */
        function canDelete( $oid=null, $joins=null ) {
                $k = $this->_tbl_key;
                if ($oid) {
                        $this->$k = intval( $oid );
                }
                if (is_array( $joins )) {
                        $select = $k;
                        $join = '';
                        foreach( $joins as $table ) {
                                $select .= ",\n COUNT(DISTINCT {$table['idfield']}) AS {$table['idfield']}";
                                $join .= "\n LEFT JOIN {$table['name']} ON {$table['joinfield']} = $k";
                        }

                        $query = "SELECT $select"
                        . "\n FROM $this->_tbl"
                        . $join
                        . "\n WHERE $k = ". $this->$k
                        . "\n GROUP BY $k"
                        ;
                        $this->_db->setQuery( $query );

                        if ($obj = $this->_db->loadObject()) {
                                $this->_error = $this->_db->getErrorMsg();
                                return false;
                        }
                        $msg = array();
                        foreach( $joins as $table ) {
                                $k = $table['idfield'];
                                if ($obj->$k) {
                                        $msg[] = $AppUI->_( $table['label'] );
                                }
                        }

                        if (count( $msg )) {
                                $this->_error = "noDeleteRecord" . ": " . implode( ', ', $msg );
                                return false;
                        } else {
                                return true;
                        }
                }

                return true;
        }

        /**
        *       Default delete method
        *
        *       can be overloaded/supplemented by the child class
        *       @return true if successful otherwise returns and error message
        */
        function delete( $oid=null ) {
                //if (!$this->canDelete( $msg )) {
                //      return $msg;
                //}

                $k = $this->_tbl_key;
                if ($oid) {
                        $this->$k = intval( $oid );
                }

                $query = "DELETE FROM $this->_tbl"
                . "\n WHERE $this->_tbl_key = '". $this->$k ."'"
                ;
                $this->_db->setQuery( $query );

                if ($this->_db->query()) {
                        return true;
                } else {
                        $this->_error = $this->_db->getErrorMsg();
                        return false;
                }
        }

        /**
         * Checks out an object
         * @param int User id
         * @param int Object id
         */
        function checkout( $user_id, $oid=null ) {
                if (!array_key_exists( 'checked_out', get_class_vars( strtolower(get_class( $this )) ) )) {
                        $this->_error = "Attention: ".strtolower(get_class( $this ))." can't unblock table";
                        return false;
                }
                $k = $this->_tbl_key;
                if ($oid !== null) {
                        $this->$k = $oid;
                }
                $time = date( 'Y-m-d H:i:s' );
                if (intval( $user_id )) {
                        $user_id = intval( $user_id );
                        // new way of storing editor, by id
                        $query = "UPDATE $this->_tbl"
                        . "\n SET checked_out = $user_id, checked_out_time = '$time'"
                        . "\n WHERE $this->_tbl_key = '". $this->$k ."'"
                        ;
                        $this->_db->setQuery( $query );

                        $this->checked_out = $user_id;
                        $this->checked_out_time = $time;
                } else {
                        $user_id = $this->_db->Quote( $user_id );
                        // old way of storing editor, by name
                        $query = "UPDATE $this->_tbl"
                        . "\n SET checked_out = 1, checked_out_time = '$time', editor = $user_id"
                        . "\n WHERE $this->_tbl_key = '". $this->$k ."'"
                        ;
                        $this->_db->setQuery( $query );

                        $this->checked_out = 1;
                        $this->checked_out_time = $time;
                        $this->checked_out_editor = $user_id;
                }

                return $this->_db->query();
        }

        /**
         * Checks in an object
         * @param int Object id
         */
        function checkin( $oid=null ) {
                if (!array_key_exists( 'checked_out', get_class_vars( strtolower(get_class( $this )) ) )) {
                        $this->_error = "Attention: ".strtolower(get_class( $this ))." cant't use lock.";
                        return false;
                }
                $k = $this->_tbl_key;
                if ($oid !== null) {
                        $this->$k = intval( $oid );
                }
                $time = date( 'H:i:s' );
                $nullDate = $this->_db->getNullDate();
                $query = "UPDATE $this->_tbl"
                . "\n SET checked_out = 0, checked_out_time = '$nullDate'"
                . "\n WHERE $this->_tbl_key = ". $this->$k
                ;
                $this->_db->setQuery( $query );

                $this->checked_out = 0;
                $this->checked_out_time = '';

                return $this->_db->query();
        }

        /**
         * Increments the hit counter for an object
         * @param int Object id
         */
        function hit( $oid=null ) {
                global $Config_enable_log_items;

                $k = $this->_tbl_key;
                if ($oid !== null) {
                        $this->$k = intval( $oid );
                }

                $query = "UPDATE $this->_tbl"
                . "\n SET hits = ( hits + 1 )"
                . "\n WHERE $this->_tbl_key = '$this->id'"
                ;
                $this->_db->setQuery( $query );
                $this->_db->query();

        }

        /**
         * Tests if item is checked out
         * @param int A user id
         * @return boolean
         */
        function isCheckedOut( $user_id=0 ) {
                if ($user_id) {
                        return ($this->checked_out && $this->checked_out != $user_id);
                } else {
                        return $this->checked_out;
                }
        }

        /**
        * Generic save function
        * @param array Source array for binding to class vars
        * @param string Filter for the order updating
        * @returns TRUE if completely successful, FALSE if partially or not succesful
        * NOTE: Filter will be deprecated in verion 1.1
        */
        function save( $source, $order_filter='' ) {
                if (!$this->bind( $source )) {
                        return false;
                }
                if (!$this->check()) {
                        return false;
                }
                if (!$this->store()) {
                        return false;
                }
                if (!$this->checkin()) {
                        return false;
                }
                
                if ($order_filter) {
                        $filter_value = $this->$order_filter;
                        $this->updateOrder( $order_filter ? "`$order_filter` = '$filter_value'" : '' );
                }
                $this->_error = '';
                return true;
        }

        /**
         * Generic Publish/Unpublish function
         * @param array An array of id numbers
         * @param integer 0 if unpublishing, 1 if publishing
         * @param integer The id of the user performnig the operation
         * @since 1.0.4
         */
        function publish( $cid=null, $publish=1, $user_id=0 ) {
                ArrayToInts( $cid, array() );
                $user_id = intval( $user_id );
                $publish = intval( $publish );

                if (count( $cid ) < 1) {
                        $this->_error = "No items selected.";
                        return false;
                }

                $cids = 'id=' . implode( ' OR id=', $cid );

                $query = "UPDATE $this->_tbl"
                . "\n SET published = " . intval( $publish )
                . "\n WHERE ($cids)"
                . "\n AND (checked_out = 0 OR checked_out = $user_id)"
                ;
                $this->_db->setQuery( $query );
                if (!$this->_db->query()) {
                        $this->_error = $this->_db->getErrorMsg();
                        return false;
                }

                if (count( $cid ) == 1) {
                        $this->checkin( $cid[0] );
                }
                $this->_error = '';
                return true;
        }

        /**
        * Export item list to xml
        * @param boolean Map foreign keys to text values
        */
        function toXML( $mapKeysToText=false ) {
                $xml = '<record table="' . $this->_tbl . '"';

                if ($mapKeysToText) {
                        $xml .= ' mapkeystotext="true"';
                }
                $xml .= '>';
                foreach (get_object_vars( $this ) as $k => $v) {
                        if (is_array($v) or is_object($v) or $v === NULL) {
                                continue;
                        }
                        if ($k[0] == '_') { // internal field
                                continue;
                        }
                        $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
                }
                $xml .= '</record>';

                return $xml;
        }
}

function BindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
        if (!is_array( $array ) || !is_object( $obj )) {
                return (false);
        }

        foreach (get_object_vars($obj) as $k => $v) {
                if( substr( $k, 0, 1 ) != '_' ) {                       // internal attributes of an object are ignored
                        if (strpos( $ignore, $k) === false) {
                                if ($prefix) {
                                        $ak = $prefix . $k;
                                } else {
                                        $ak = $k;
                                }
                                if (isset($array[$ak])) {
                                        $obj->$k = $array[$ak];
                                }

                        }
                }
        }

        return true;
}

$database = new database($conf_host,$conf_user,$conf_pass,$conf_db,$conf_dbprefix,false);
