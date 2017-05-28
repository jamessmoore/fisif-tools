<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://swasis.com
 * @since      0.0.7
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/includes
 * @author     James S. Moore <james@teamweb.us>
 */
class FISIF_Tools_Db {

	function FISIF_Tools_Db() {		// Constructor

		$this->mysql_username = 'MYSQL_USERNAME';
		$this->mysql_password = 'MYSQL_PASSWORD';
		$this->mySqlHost = 'MYSQL_HOST';
		$this->mySqlDb = 'MYSQL_DB';
		$this->mySqlPort = 'MYSQL_PORT';
		$this->mySqlPrefix = '';

	}

	function dbConnect() {

		if (!isset($this->mysql_username) &&  !isset($this->mysql_password)) { $this->FISIF_Tools_Db(); }

		$mysqli = new mysqli($this->mySqlHost, $this->mysql_username, $this->mysql_password, $this->mySqlDb, $this->mySqlPort);
        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        else {
            return $mysqli;
        }
	}

	function GetRecords($select_what, $tablename, $params) {
		$mysqli = $this->dbConnect();
		if ($select_what == "") {
			$select_what = "*";
		}
        $query = "SELECT $select_what FROM `$tablename` $params";
		$return = $mysqli->query($query);
        if (!$return) {
            throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
        }

        $result = array();
		while ($row = $return->fetch_assoc()) {
			$result[] = $row;
        }
		return $result;
	}

	function GetRecordsCount($select_what, $tablename, $params) {
		$mysqli = $this->dbConnect();
		$res = $mysqli->query("SELECT $select_what  COUNT(*) FROM $tablename $params");

        list($numrec) = $res->fetch_row();
		return $numrec;
	}

    // member related functions
    function getUserInfo($user_type, $id) {

			$mysqli = $this->dbConnect();
			$params = '';

        switch ($user_type) {

					case "agent":
						$tablename = "agencies";
						break;

					case "member":
					default:
						$tablename = "members";
						$params .= 'LEFT JOIN pc_main on members.id=pc_main.BilledID ';
						break;
        }
        $params .= "WHERE `id`='$id' ";
				$res = $mysqli->query("SELECT * FROM $tablename $params");
        $result = $res->fetch_assoc();

        // $result[0]['password'] = "XXXXXX"; // Kill the password data

        return $result;
    }

    function getAssocMembers($id) {

		$mysqli = $this->dbConnect();

        $tablename = "members";
        $params = "WHERE `agencyid`='$id' ORDER BY name ASC";
		$res = $mysqli->query("SELECT * FROM $tablename $params");

		$result = array();
		while ($row = $res->fetch_assoc()) {
            // $row['password'] = "XXXXXX";
			$result[] = $row;
		}
        return $result;
    }
	// function AddRecord($tablename, $values) {
	// 	$mysqli = $this->dbConnect();
	// 	$query = "INSERT into $tablename SET $values";
	// 	// echo "$query<br /><br />\n";
	// 	$result = mysql_query($query);
	// 	return $result;

	// }

	// function UpdateRecord($tablename, $values, $condition) {
	// 	$this->dbConnect();
	// 	$query = "UPDATE $tablename SET $values WHERE $condition LIMIT 1";
	// 	// echo $query;
	// 	$result = mysql_query($query);
	// 	return $result;

	// }

	// function RemoveRecords($tablename, $params, $limit) {
	// 	$this->dbConnect();
	// 	if ($limit !== "") {
	// 		$limit_params = "LIMIT $limit";
	// 	} else {
	// 		$limit_params = "";
	// 	}
	// 	$query = "DELETE FROM $tablename WHERE $params $limit_params";
	// 	// echo $query;
	// 	$result = mysql_query($query);
	// 	return $result;
	// }

	function GetColumnNames($tablename) {
		$mysqli = $this->dbConnect();
		$res = $mysqli->query("SHOW COLUMNS FROM `$tablename`");

		$result = array();
		while ($row = $res->fetch_assoc()) {
			$result[] = $row;
		}
		return $result;
	}
	// function EmptyTable($tablename) {
	// 	$this->dbConnect();
	// 	$query = "DELETE FROM `$tablename`";
	// 	// echo $query;
	// 	$result = mysql_query($query);
	// 	return $result;
	// }
	// function CheckTable($tablename) {
	// 	$this->dbConnect();
	// 	$query = "SHOW TABLES LIKE $tablename";
	// 	// echo $query;
	// 	$result = mysql_query($query);
	// 	return $result;
	// }
	// function CreateTable($tablename, $spec) {
	// 	$this->dbConnect();
	// 	$query = "CREATE TABLE $tablename $spec";
	// 	// echo $query;
	// 	$result = mysql_query($query);
	// 	return $result;
	// }
	// function AlterTable($tablename, $spec) {
	// 	$this->dbConnect();
	// 	$query = "ALTER TABLE $tablename $spec";
	// 	// echo $query."<br />";
	// 	$result = mysql_query($query);
	// 	return $result;
	// }
	// function DropTable($tablename) {
	// 	$this->dbConnect();
	// 	$query = "DROP TABLE IF EXISTS $tablename";
	// 	// echo $query;
	// 	$result = mysql_query($query);
	// 	return $result;
	// }
	function getDate() {
		return date("n/d/Y h:iA T");
	}
	function filterVars($data_array, $mode) {
		if (is_array($data_array)) {
			switch($mode) {
            case "dBinFilter":
                foreach($data_array as $key => $value) {
                    $data_array[$key] = $this->dBinFilter($value);
                }
                break;
            case "dBoutFilter":
                foreach($data_array as $key => $value) {
                    $data_array[$key] = $this->dBoutFilter($value);
                }
                break;
            default:
                echo "filterVars mode not defined\n";
                exit;
			}
			return $data_array;
		} else {
			$data_array = array();
			return $data_array;
		}
	}
	function dBinFilter($data) {
		$newdata = addslashes(trim($data));
		$newdata = htmlentities(trim($data), ENT_QUOTES);
		return $newdata;
	}
	function dBoutFilter($data) {
		$newdata = stripslashes($data);
		return $newdata;
	}

}
