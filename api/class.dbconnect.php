<?php
class dbconnect{
	private $servername = "";
	private $username = "";
	private $password = "";
	private $dbname = "";
	protected $conn = FALSE;

	function __construct(){
		
	}

	function __destruct() {
		//$this->conn->close();
	}

	function set_params($new_servername, $new_username, $new_password, $new_dbname){
		$this->servername = $new_servername;
		$this->username = $new_username;
		$this->password = $new_password;
		$this->dbname = $new_dbname;
	}

	function connect(){
		$new_conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		$this->conn = $new_conn;
		//return $new_conn;
	}

	function dbtest($curr_conn = ''){
		if($curr_conn == '')
			$curr_conn = $this->conn;
		// Check connection
		if ($curr_conn->connect_error) {
		    die("Connection failed: " . $curr_conn->connect_error);
		} 
		echo "Connected successfully\n";

	}

	function get_connection(){
		return $this->conn;
	}
}
?>