<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $main_script_name = "$Id$";
    $main_script_desc = "abstraction object for mysql";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of eWeBuKi

    eWeBuKi is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    eWeBuKi is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with eWeBuKi; If you did not, you may download a copy at:

    URL:  http://www.gnu.org/licenses/gpl.txt

    You may also request a copy from:

    Free Software Foundation, Inc.
    59 Temple Place, Suite 330
    Boston, MA 02111-1307
    USA

    You may contact the author/development team at:

    Chaos Networks
    c/o Werner Ammon
    Lerchenstr. 11c

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    class DB_connect {

        var $classname      = "DB_connect";
        var $CONN           = "";
        var $ROW            = "";
        var $RESULTS        = "";
        var $UID            = "";
        var $HOST           = DB_HOST;
        var $DB             = DATABASE;
        var $USER           = DB_USER;
        var $PASS           = DB_PASSWORD;
        var $ROOT_RUN       = "";
        var $DEBUG          = "";
        var $VERSION        = "eWeBuKi mysql-driver v0.1.6";

        // connection to our db - change defines in config.php
        // this is a regular connect
        // returns true on success!
        // used via $db->connect();
        function connect() {
            $user = $this->USER;
            $pass = $this->PASS;
            $host = $this->HOST;
            $db   = $this->DB;
            $conn = mysql_connect($host,$user,$pass);
            $return = false;
            
            // error-handling first for connection, second for
            // db-finding

            if(!$conn) {
                if ( $host == "" ) $host = "localhost";
                $return = $this->error("Connection to $db on $host failed.");
            }

            if($this->ROOT_RUN != "yes") {
                $return = $this->selectDb($this->DB,True);
            }

            $this->CONN = $conn;
            return $return;
        }

        // connection to our db - change defines in config.php
        // this is a persisten connection
        // returns true on success
        // used vi $db->pconnect();
        function pconnect() {
            $user = $this->USER;
            $pass = $this->PASS;
            $host = $this->HOST;
            $db   = $this->DB;
            $conn = mysql_pconnect($host,$user,$pass);
            $return = false;

            // error-handling first for connection, second for
            // db-finding

            if(!$conn) {
                if ( $host == "" ) $host = "localhost";            
                $return = $this->error("Connection to $db on $host failed.");                
            }

            if($this->ROOT_RUN != "yes") {
                $return = $this->selectDb($this->DB,true);
            }

            $this->CONN = $conn;
            return $return;
        }

        // this handles all errors
        // call it via $db->error(your_text);
        #function error($text) { $no = mysql_errno();
        #  $msg= mysql_error();
        #  #$error = "<font color='red'>[$text] ( $no : $msg )</font>";
        #  $error = "<font color='red'>$text ( $no : $msg )</font>";
        #  return $error;
        #  // exit;
        #}

        // this handles all errors
        // call it via $db->error(your_text);
        function error($text) {
            $no = mysql_errno();
            $msg= mysql_error();
            if ($text) {
                $error = "<font color='red'>$text ( $no : $msg )</font>";
                return $error;
            } else {
                return $no;
            }
        }

        function selectDb($database,$err) {
            $return = @mysql_select_db($database);           
            if ( $return ) {
                $this->DB = $database;
                return $database;
            } else {               
                if ( $err ) {
                    $return = $this->error("Can't select database $database");
                    return $return;
                }
            }
        }

        /*
        function dropDb($database) {
            $success = mysql_drop_db($database);
            if($success) {
                return $success;
            } else {
                $this->error("Can't drop database $database");
            }
        }

        function createDb($database) {
            $success = mysql_create_db($database);
            if($success) {
                return $success;
            } else {
                $this->error("Can't create database $database");
            }
        }
        */

        // doing a general query
        // use it via $db->query(your_sql_statement);
        function query($sql) {
            $conn     = $this->CONN;
            $results  = mysql_query($sql,$conn);

            // this way we don't have to check the results
            // in the main code for false.
            if(!$results) {
                 $this->error("Something bad happened quering the database: ");
            }
            $this->RESULTS  = $results;
            return $results;
        }

        // doing a general query without error-checking
        function query_quiet($sql) {
            $conn = $this->CONN;
            $results  = mysql_query($sql,$conn);
            return $results;
        }

        // give me last insert id
        function lastid() {
            $conn = $this->CONN;
            $id = mysql_insert_id($conn);
            return $id;
        }

        /*
        // this function return true if $database.$table exists
        function table_exists($database,$table) {
            $result2 = mysql_list_tables($database);
            $i = 0;
            while ($i < mysql_num_rows($result2)) {
                $table_name = mysql_tablename($result2, $i);
                if($table_name == $table) {
                    $table_exists = "yes";
                }
                $i++;
            }
            mysql_free_result($result2);
            if ( isset($table_exists) && $table_exists == "yes" ) {
                return true;
            }
        }
        */

        function fetch_row($result) {
            $row = mysql_fetch_row($result);
            return $row;
        }

        function get_object($object,$table,$field,$value,$check) {
            $value = $this->doSlashes($value);
            $sql = "SELECT $object FROM $table WHERE $field='$value'";
            if($check == "QUIET") {
                $result = $this->query_quiet($sql);
            } else {
                $result = $this->query($sql);
            }
            $object_db = mysql_fetch_object($result);
            $object_return = $object_db->$object;
            $this->free_result($result);
            return $object_return;
        }

        function fetch_array($result,$int="") {
            switch($int) {
                  case 1:
                      $array = mysql_fetch_array($result,MYSQL_ASSOC);
                      break;

                  case 2:
                      $array = mysql_fetch_array($result,MYSQL_NUM);
                      break;

                  case 3:
                      $array = mysql_fetch_array($result,MYSQL_BOTH);
                      break;

                  default:
                      $array = mysql_fetch_array($result,MYSQL_BOTH);
                      break;
            }
            return $array;
        }

        function free_result($result) {
            mysql_free_result($result);
        }

        function fetch_object($result) {
            $object = mysql_fetch_object($result);
            return $object;
        }

        function num_rows($result) {
            $num = mysql_num_rows($result);
            return $num;
        }

        function affected_rows($result) {
            $conn = $this->CONN;
            $num = mysql_affected_rows($conn);
            return $num;
        }


        // new num_fields function for pg
        function num_fields($result) {
            $numfields = mysql_num_fields($result);
            return $numfields;
        }

        // new field_name function for pg
        function field_name($result,$int) {
            $fieldname = mysql_field_name($result,$int);
            return $fieldname;
        }

        // new field_type function for pg
        function field_type($result,$int) {
            $fieldtype = mysql_field_type($result,$int);
        #    echo $fieldtype." ";
            return $fieldtype;
        }

        // new show_columns funktion for pg
        function show_columns($table) {
            $sql = "SHOW COLUMNS FROM ". $table;
            $result = $this->query_quiet($sql);
            while ( $row = $this->fetch_array($result,$nop) ) {
                #$columns[] = array( "Field"=>$row["Field"], "Type"=>$row["Type"], "Null"=>$row["Null"] );
                $columns[] = $row;
            }

            #if ( $debugging["html_enable"] ) {
            #   ob_start();
            #   print("<pre>");
            #   print_r($columns);
            #   print("</pre>");
            #   $debugging["ausgabe"] .= ob_get_contents().$debugging["char"];
            #   ob_end_clean();
            #}
            return $columns;
        }

        function close() {
            mysql_close($this->CONN);
        }

        // mutator-funktionen
        function setUser($user) {
            $this->USER = $user;
        }

        function setPass($pass) {
            $this->PASS = $pass;
        }

        function setHost($host) {
            $this->HOST = $host;
        }

        function setDB($database) {
            $this->DB = $database;
        }

        function setRootRun() {
            $this->ROOT_RUN = "yes";
        }

        function setStuff($user,$pass,$host,$database) {
            $this->setUser($user);
            $this->setPass($pass);
            $this->setHost($host);
            $this->setDb($database);
        }

        function getVersion() {
            return $this->VERSION;
        }

        function getDb() {
            return $this->DB;
        }

        function doSlashes($sql) {
            if ( ( $i = get_magic_quotes_gpc() ) == 0 ) {
                $sql = addslashes($sql);
            }
            return $sql;
        }
    }
    return true;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
