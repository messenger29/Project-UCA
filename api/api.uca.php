<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require('class.dbconnect.php');

class UCA_connect extends dbconnect{
	function __construct(){
		$this->set_params("localhost","uca_public","m29test","uc_admissions");
		$this->connect();
	}

	function dbquery($sql){
		$results = array();
		$res = $this->conn->query($sql);
		while($r = $res->fetch_assoc()){
			$results[] = $r;
		}
		return $results;
	}

	function selectquery($sql,$params){
		$stmt = $this->conn->prepare($sql);
		// to a dynamic number of parameters into bind_param
		call_user_func_array(array($stmt,"bind_param"),$this->ref_values($params));
		$stmt->execute();

		$results = array();
		$res = $stmt->get_result();
		while($r = $res->fetch_assoc()){
			$results[] = $r;
		}
		$stmt->close();
		return $results;
	}

	private function ref_values($array) {
		//helps satisfies the condition that bind_param expects a referenced object
		$refs = array();
		foreach ($array as $key => $value) {
			$refs[$key] = &$array[$key]; 
		}
		return $refs; 
	}

	function get_city_list(){

	}

	function get_school_list_by_city($s_city_name){

	}

	function get_univ_list(){

	}

	//get the student counts for all years for specified high school and university
	function get_count_year_by_school_univ_data($s_school_name, $s_city_name, $s_univ_name){
		$a_data = array();
		$sql = "SELECT year,applicants,admits,enrollees FROM school_data WHERE school_name = ? AND city_name = ? AND univ_name = ? ORDER BY year ASC;";
		$res = $this->selectquery($sql,array('sss',$s_school_name, $s_city_name, $s_univ_name));
		foreach($res as $r){
			//sort results into array
			$a_data[] = [
				'year'=>$r['year'],
				'applicants'=>$r['applicants'],
				'admits'=>$r['admits'],
				'enrollees'=>$r['enrollees']
			];
		};
		return json_encode($a_data);	//encode the data into json for response
	}

	//get the student counts for each university for a particular year and high school
	function get_count_univ_by_year_school_data($s_school_name, $s_city_name, $i_year){
		$a_data = array();
		$sql = "SELECT univ_name, applicants, admits, enrollees FROM school_data WHERE school_name = '$s_school_name' AND year = $i_year AND city_name='$s_city_name' And univ_name != 'UniversityWide';";
		$res = $this->dbquery($sql);
		while($r = $res->fetch_assoc()){
			//sort results into array
			$a_data[] = [
				'univ_name'=>$r[univ_name],
				'applicants'=>$r['applicants'],
				'admits'=>$r['admits'],
				'enrollees'=>$r['enrollees']
			];
		};
		return json_encode($a_data);	//encode the data into json for transfer
	}
}

function checkCleanString($string){
	return preg_match('/^[A-Za-z\s\']*$/',$string) === 1;
}

if(!isset($_GET['query_type']))
	die(http_response_code(409));

$s_query_type = $_GET['query_type'];
if($s_query_type == 'studentcountbyschooluniv' && isset($_GET['school_name']) && isset($_GET['city_name']) && isset($_GET['univ_name'])){
	$s_school_name = $_GET['school_name'];
	$s_city_name = $_GET['city_name'];
	$s_univ_name = $_GET['univ_name'];

	//check to make sure 
	if(!checkCleanString($s_school_name)){
		die(http_response_code(409)."<br>"."school_name:'$s_school_name' not alphanumeric");
	}
	elseif(!checkCleanString($s_city_name)){
		die(http_response_code(409)."<br>"."city_name:'$s_city_name' not alphanumeric");
	}
	elseif(!checkCleanString($s_univ_name)){
		die(http_response_code(409)."<br>"."univ_name:'$s_univ_name' not alphanumeric");
	}

	$dbconn = new UCA_connect();
	print($dbconn->get_count_year_by_school_univ_data($s_school_name,$s_city_name,$s_univ_name));
}
else{
	die(http_response_code(409)."<br>".'query_type not valid');
}
?>