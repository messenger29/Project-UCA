<?php
require('class.dbconnect.php');

class UCA_connect extends dbconnect{
	function __construct(){
		$this->set_params("localhost","uca_public","m29test","uc_admissions");
		$this->connect();
	}

	function dbquery($sql){
		return $this->conn->query($sql);
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
		$sql = "SELECT year,applicants,admits,enrollees FROM school_data WHERE school_name = '$s_school_name' AND city_name = '$s_city_name' AND univ_name = '$s_univ_name' ORDER BY year ASC;";
		$res = $this->dbquery($sql);
		while($r = $res->fetch_assoc()){
			//sort results into array
			$a_data[] = [
				'year'=>$r[year],
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

if(!isset($_GET['query_type']))
	die();

$s_query_type = $_GET['query_type'];
if($s_query_type == 'studentcountbyschooluniv' && isset($_GET['school_name']) && isset($_GET['city_name']) && isset($_GET['univ_name'])){
	$s_school_name = $_GET['school_name'];
	$s_city_name = $_GET['city_name'];
	$s_univ_name = $_GET['univ_name'];
	if(!ctype_alnum($s_school_name) && !ctype_alnum($s_city_name) && !ctype_alnum($s_univ_name)){
		die("school_name/city_name/univ_name not valid");
	}

	$dbconn = new UCA_connect();
	print($dbconn->get_count_year_by_school_univ_data($s_school_name,$s_city_name,$s_univ_name));
}
else{
	die('query_type not valid');
}
?>