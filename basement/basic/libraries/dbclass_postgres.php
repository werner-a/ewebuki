<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $main_script_name = "$Id$";
    $main_script_desc = "abstraction object for postgres";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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
        var $DEBUG          = DEBUG;
        var $VERSION        = "eWeBuKi postgres-driver v0.1.6 ";

        // connection to our db - change defines in config.inc.php
        // this is a regular connect
        // returns true on success!
        // used via $db->connect();
        function connect() {
            $user = $this->USER;
            $pass = $this->PASS;
            $host = $this->HOST;
            if ( $host != "" ) {
                $hoststring = "host=".$host." ";
            }
            $db   = $this->DB;
            $conn = @pg_connect($hoststring."dbname=".$db." user=".$user." password=".$pass);
            $return = false;

            // error-handling first for connection, second for
            // db-finding

            if(!$conn) {
                $return = $this->error("Connection to $db on $host failed.");
            } else {
                $return = $db;
            }

            #if($this->ROOT_RUN != "yes") {
            #   $return = $this->selectDb($this->DB,false);
            #}

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
            if ( $host != "" ) {
                $hoststring = "host=".$host." ";
            }
            $db   = $this->DB;
            $conn = @pg_pcconnect($hoststring."dbname=".$db." user=".$user." password=".$pass);
            $return = false;

            // error-handling first for connection, second for
            // db-finding

            if(!$conn) {
                $return = $this->error("Connection to $db on $host failed.");
            } else {
                $return = $db;
            }

            #if($this->ROOT_RUN != "yes") {
            #    $this->selectDb($this->DB,false);
            #}

            $this->CONN = $conn;
            return $return;
        }

        // this handles all errors
        // call it via $db->error(your_text);
        function error($text) {
            global $sql; ### nicht sauber
            $sqlquery = ": ".$sql." ";

            // emulate mysql_errno()
            if ( strstr(pg_errormessage(), "Cannot insert a duplicate key") ) $no = "1062";
            $msg = pg_errormessage();

            if ($text) {
                $error = "<font color=\"red\">".$text.$sqlquery." ( ".$no." : ".$msg." )</font>";
                return $error;
            } else {
                return $no;
            }
            #exit;
        }

        function selectDb($database,$err) {
            // befehl nicht vorhanden, deswegen neuer connect :)
            #$return = mysql_select_db($database);
            $this->DB = $database;
            $return = $this->connect();
            if( $return ) {
                $this->DB = $database;
                return $return;
            } else {
                if($err) {
                  $return = $this->error("Can't select database $database");
                  return $return;
                }
            }
        }

        // doing a general query
        // use it via $db->query(your_sql_statement);
        function query($sql) {
            $conn = $this->CONN;

            // wir drehen fuer postgresql < 7.3 das limit :(
            if ( preg_match("/LIMIT [0-9]+,[0-9]+/Ui",$sql) ) {
                $limit = explode(" ", strstr($sql,"LIMIT"),2);
                $oldlimit = $limit[0]." ".$limit[1];
                $orglimitvalue = $limit[1];
                $limitarray = explode(",", $orglimitvalue,2);
                $newlimit = "LIMIT ".$limitarray[1]." OFFSET ".$limitarray[0];
                $sql = str_replace($oldlimit, $newlimit, $sql);
            }

            // nun kann auch postgres regex :)
            $sql = preg_replace("/REGEXP/Ui","~",$sql);

            // was fuer ein bloedsinn case sensitive? - also umbauen
            if ( stristr($sql,"LIKE") ) {
                $sql = str_replace("LIKE","ILIKE",$sql);
            }

            // was fuer ein bloedsinn, concat geht nicht mit postges (scheiss datenbank)
            if ( stristr($sql,"CONCAT(") ) {
                $suche = strstr($sql,"CONCAT(");
                $ende = strpos($suche,")");
                $concat = substr($suche,7,$ende-7);

                $neu = str_replace(","," || ",$concat);

                $sql = str_replace("CONCAT(".$concat.")", $neu, $sql);
            }

            // @ disables buggy pg warning
            $results = pg_query($conn, $sql);
            // this way we don't have to check the results
            // in the main code for false.
            if(!$results) {

                // tweak sequence tables :)
                if ( stristr($sql, "insert") && strstr(pg_errormessage(), "Cannot insert a duplicate key") ) {
                    // find table
                    $table = explode(" ", $sql, 4);

                    // find id field
                    $columns = $this->show_columns($table[2]);

                    // find id max value
                    $tsql   = "select ".$columns[0][0]." from ".$table[2]." order by ".$columns[0][0]." desc";
                    $result = $this->query_quiet($tsql);
                    if ( !$result ) die("tweak sequence - find id max value failed (".$table[2].").");
                    $data   = $this->fetch_row($result);

                    // set sequence name
                    $sequence_name = $table[2]."_".$columns[0][0]."_seq";

                    // update sequence
                    $tsql   = "select setval('".$sequence_name."', ".$data[0].")";
                    $result = $this->query_quiet($tsql);
                    if ( !$result ) die("tweak sequence - update sequence failed (".$table[2].").");

                    // orginal query, second attempt
                    $results = $this->query_quiet($sql);

                } else {
                    $this->error("Something bad happened quering the database: ");
                }
            }

            $this->RESULTS  = $results;
            return $results;
        }

        // doing a general query without error-checking
        function query_quiet($sql) {
            $conn = $this->CONN;
            // @ disables pg warning
            $results  = @pg_query($conn, $sql);
            return $results;
        }

        // give me last insert id
        function lastid() {
            global $sql;
            $table = split(" ", $sql, 4);

            $sql = "select * from ".$table[2];
            $result = $this->query_quiet($sql);
            $field = $this->field_name($result,0);

            $sql = "select currval('".$table[2]."_".$field."_seq')";
            $id = $this->fetch_row($this->query_quiet($sql));
            return $id[0];
        }

        function fetch_row($result) {
            $row = pg_fetch_row($result);
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
            $object_db = pg_fetch_object($result);
            $object_return = $object_db->$object;
            $this->free_result($result);
            return $object_return;
        }

        function fetch_array($result,$int="") {
            switch($int) {
                case 1:
                    #$row = pg_fetch_array($result,$nop,PGSQL_ASSOC);
                    $row = pg_fetch_array($result);
                    break;

                case 2:
                    #$row = pg_fetch_array($result,$nop,PGSQL_NUM);
                    $row = pg_fetch_array($result);
                    break;

                case 3:
                    #$row = pg_fetch_array($result,$nop,PGSQL_BOTH);
                    $row = pg_fetch_array($result);
                    break;

                default:
                    #$row = pg_fetch_array($result,$nop,PGSQL_BOTH);
                    $row = pg_fetch_array($result);
                    break;
            }
            return $row;
        }

        function data_seek($result,$offset) {
            $success = pg_result_seek($result,$offset);
            return $success;
        }

        function free_result($result) {
            pg_free_result($result);
        }

        function fetch_object($result) {
            $object = pg_fetch_object($result);
            return $object;
        }

        function num_rows($result) {
            $num = pg_numrows($result);
            return $num;
        }

        function affected_rows($result) {
            $num = pg_affected_rows($result);
            return $num;
        }

        // new num_fields function for pg
        function num_fields($result) {
            $numfields = pg_numfields($result);
            return $numfields;
        }

        // new field_name function for pg
        function field_name($result,$int) {
            $fieldname = pg_fieldname($result,$int);
            return $fieldname;
        }

        // new field_type function for pg
        function field_type($result,$int) {
            $fieldtype = pg_fieldtype($result,$int);
            #echo $fieldtype.", ".$fieldlen."<br />";
            #if ( strstr($fieldtype, "char") ) $fieldtype = "string";
            #if ( $fieldtype == "text" ) $fieldtype = "blob";
            return $fieldtype;
        }

        // new show_columns funktion for pg
        function show_columns($table) {
            $sql = "SELECT
                        a.attnum,
                        a.attname AS field,
                        t.typname AS type,
                        a.attlen AS length,
                        a.atttypmod AS lengthvar,
                        a.attnotnull AS notnull,
                        d.adsrc AS default
                    FROM
                        (pg_class c INNER JOIN pg_attribute a ON (a.attrelid = c.oid))
                        INNER JOIN pg_type t ON (a.atttypid = t.oid )
                        LEFT JOIN pg_attrdef d ON (d.adnum = a.attnum and d.adrelid = c.oid)
                    WHERE
                        c.relname = '".$table."'
                        and a.attnum > 0
                    ORDER BY a.attnum";

            $result = $this->query_quiet($sql);
            while ( $row = $this->fetch_array($result,$nop) ) {
                if ( $row["lengthvar"] != -1 ) $length = $row["lengthvar"]-4;
                if ( $row["type"] == "timestamp" ) $row["type"] = "datetime";
                ( $row["notnull"] == "f" ) ? $row["notnull"] = "YES" : $row["notnull"] = "";
                if ( strstr($row["default"],"nextval") ) {
                    $row["default"] = "";
                } elseif ( strstr($row["default"],"'::") ){
                    $zeichen = strpos($row["default"],"'::");
                    $row["default"] = substr($row["default"],1,$zeichen-1);
                }
                $columns[] = array(
                    0=>$row["field"],
                    "Field"=>$row["field"],
                    1=>$row["type"],
                    "Type"=>$row["type"]."(".$length.")",
                    2=>$row["notnull"],
                    "Null"=>$row["notnull"],
                    3=>$row["default"],
                    "Default"=>$row["default"]
                );
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
            pg_close($this->CONN);
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
            if ( ( $i = get_magic_quotes_gpc() ) == 0) {
                $sql = addslashes($sql);
            }
            return $sql;
        }

    }
    return true;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
