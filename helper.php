<?php
/**
* @package Author
* @author Joseph P. Gibson
* @website www.joesboat.org
* @email joe@joesboat.org
* @copyright Copyright (C) 2018 Joseph P. Gibson. All rights reserved.
* @license GNU General Public License version 2 or later; see LICENSE.txt
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_LIBRARIES."/usps/includes/routines.php");
require_once(JPATH_LIBRARIES."/usps/tableD5VHQAB.php");
require_once(JPATH_LIBRARIES."/usps/dbUSPSd5WebSites.php");
class modspecify_jobsHelper
{
static function check_permissions($setup, $mode){
require(JPATH_LIBRARIES."/USPSaccess/dbUSPS.php");
$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
$jobs = $vhqab->getJobsObject();
$exc = $vhqab->getExcomObject();
$blob = JoeFactory::getTable("tableD5Blobs",$db_d5);
	$year = $blob->get_manage_year();	
	$user = JFactory::getUser();
	$user_id = $user->username;
	$setup['user_id'] = $user_id;
	$setup['mode'] = $mode;
	switch($mode){
		case 'd5':
			$setup['dept_code']="";
			$setup['excom'] = $exc->get_highest_permission($setup['user_id'], $year) ;
			$setup['excom']['member_name'] = $vhqab->getMemberNameAndRank($setup['excom']['certificate']);
			$setup['squad_no'] = '' ;
			$setup['dept_code'] = $exc->get_permission($setup['excom']);
			$setup['header'] = "D5 Job Assignments for $year. - ".$setup['excom']['excom_position']." Permissions";
			$setup['unit_name'] = 'D5';
			$setup['order'] = "jobcode" ;
			$setup['cow'] = $vhqab->getSquadronCOW('6243');
			break; 
		default:
			if (! $exc->excom_member($user_id,"site_maint",$year)){
				//if (! $exc->excom_member($user_id,"site_maint",$year)) 
				if (!($jobs->check_squadron_officer($_SESSION['user_id'],sprintf("%04d",$setup['squad_no']),$year)))
				{
				header("Location: inform_member.php?inform=squad_cdr&".
							htmlspecialchars(SID));
				log_it("Member ".$setup['user_id']." was not allowed to use this tool") ;
				exit(0);
				}
			}
			if (isset($_POST['new_squad'])){
				// only possible if this is the 2nd loop through check permissions
				$setup['squad_no'] = $_POST['new_squad'];
				$setup['next'] = 'jobs';
			} else 
				$setup['squad_no'] = $vhqab->getSquadNumber($user_id);
			$setup['dept_code'] = '31000'; 
			$setup['unit_name'] = $vhqab->getSquadronName(sprintf("%04d",$setup['squad_no']));
			$setup['cow'] = $vhqab->getSquadronCOW($setup['squad_no']);
			$setup['header'] = "Squadron Job Assignments for $year. - ".$setup['unit_name'];
			$setup['order'] = "update_order" ;
			break;
	}
	$_SESSION['when_done'] = $_SERVER['PHP_SELF'];
	$setup['site_maint'] = $site_maint = $exc->excom_member($user_id,"site_maint",$year);
	$setup['site_url'] = getSiteUrl();
	$setup['year'] = $year;
	$dt = date("Y-m-d");
	return $setup;
}
//*************************************************************
static function get_d5_jobs_list($dept,$order="jdesc"){
$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
$codes = $vhqab->getJobcodesObject();
	// join jobdesc, jobs, members
	// omit jobdesc ending in 2 (committee members)
	// 
	$query = '';
	$rows=array();
	if ($dept != ""){
		$query .= "(";
		$r = substr($dept,4,1);	
		if ($r == '0'){
			$query .= "jobcodes.department = '";
			$query .= substr($dept,0,4)."1'";
			$query .= " or ";	
		}
		$query .= "jobcodes.department = '$dept'";
		$query .= ") and ";
	}
	$query .= "("; 
	$query .= "jobcode LIKE '2____' ";
	$query .= ")";
	$query .= "and d5_job = 1";
	$jcs = $codes->search_records_in_order($query,$order);
	return $jcs; 
}
//*********************************************************
static function get_jobcode_record($code){
	$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
	$codes = $vhqab->getJobcodesObject();
	return $codes->get_record("jobcode",$code);
}
//*********************************************************
static function get_member_list($unit){
	$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
	$mbr = $vhqab->getD5MembersObject();
	return	$mbr->get_d5_or_squad_member_list($unit);
}
//*************************************************************
static function get_squadron_jobs_list($order){
$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
$codes = $vhqab->getJobcodesObject();
	return $codes->get_squad_assignments($order);	
}
//*************************************************************
static function get_squadron_list(){
	$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
	return $vhqab->getD5SquadronList();
}
//*************************************************************
static function handle_command($setup){
	$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
	$codes = $vhqab->getJobcodesObject();
	$jobs = $vhqab->getJobsObject();
	$cmd = strtolower($_POST['command']);
	switch($cmd){
		case 'cancel':
		case 'finished':
			break;
		case 'return':		// The sub-page just wants to quit.
			$modify="";
			break;
		case 'new_order':
			$_POST['command'] = 'update';
		case 'update':
			if (isset($_POST['new_order'])){
				$setup['order'] = $_POST['new_order'];
			}
			switch ($_POST['updating']){
				case 'committee':
				 	$jobs->update_committee($_POST, $setup['year']);
					break;
				case 'jobs':
					if ($setup['cow'] != $_POST['cow_date']){
						$setup['cow'] = modspecify_jobsHelper::update_cow_date($_POST);
					}
					$jobs->update_jobs($_POST, $setup['year']);
					break;
				case 'jobcode':
					$err = $codes->update_jobcode($_POST);
					if ($err){
						$setup['error'] = $err;  
					}
					break;
				default:
					break;								
			}
			break;
		case 'continue':
			break;
		case 'create':			// Only valid for Commander and Dept. Heads
			$new_committee = $codes->get_new_code();
			$modify = $new_committee;
			break;
		case 'modify':			// Modify the membership of an existing committee
			$modify = $_POST['committee_code'];
			break;
	}
	if (isset($_POST['next'])){
		$updating=$_POST['updating'];
		$setup['next'] = $_POST['next'];
		if (isset($_POST['committee_code']))
			$setup['committee_code'] = $_POST['committee_code'];
		if (isset($_POST['jobcode']))
			$setup['jobcode'] = $_POST['jobcode'];
	}
	$vhqab->close();
	return $setup;
}
//*********************************************************
static function populate_committee_members($jobcode,$squad_no,$yr){
$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
$codes = $vhqab->getJobcodesObject();
$mbr = $vhqab->getD5MembersObject();

return $vhqab->populateGroupOrCommittee($jobcode,$yr,$squad_no,0);

	// Create an array containing members of this committee
	$committee=$codes->get_record("jobcode",$code);
	if ($committee["committee"] == "1"){
		$emer_code = $code + 9;
		$asst_code = $code + 1;
		$mem_code = $code + 2;
		$emeritus = $vhqab->getJobAssignments($emer_code,$setup['year'],$squad_no);
		$chairs = $vhqab->getJobAssignments($code,$setup['year'],$squad_no);
		$asst_chairs = $vhqab->getJobAssignments($code+1,$setup['year'],$squad_no);
	} else 
		$mem_code = $code;
	$members=$vhqab->getJobAssignments($mem_code,$setup['year'],$squad_no);
}
//*********************************************************
static function populate_jobs_list($job_list, $setup){
$list = array();
$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
	foreach($job_list as $jc){
		$jdesc = $jc['jdesc'];
		if ($jc['jobcode'] == 28071){
			$xyz = $jc['jobcode'];
		}
		switch($jc['committee']){
			case 0:			// Named Jobs
			case 1:			// Traditional Committee
				$rows = $vhqab->getJobAssignments($jc['jobcode'],$setup['year'],$setup['squad_no'],$jc['committee']==1);
				if (count($rows) == 0){
					$new = $jc;
					$new['name'] = $name = ''; 
					$new['cert'] = '';
					$list[] = $new;
					break;					
				} else				
				foreach($rows as $row){
					$cert = $row['certificate'];
					$new = $jc;
					$new['name'] = $name = $vhqab->getMemberNameAndRank($row['certificate']); 
					$new['cert'] = $row['certificate'];	
					$list[] = $new;
				} 
				
				break;
			case 2:			// Group 
				$new = $jc;
				$new['name'] = $name = ''; 
				$new['cert'] = '';				
				$list[] = $new;
				break;
			case 4:			// Officer 
				// it's a bridge level job.
				$row = $vhqab->getExcomMember($jc['jobcode'],$setup['year']);
				if ( ! $row ){
					$new = $jc;
					$new['name'] = $name = ''; 
					$new['cert'] = '';
					$list[] = $new;
					break;					
				}
				$new = $jc;
				$new['name'] = $name = $vhqab->getMemberNameAndRank($row['certificate']); 
				$new['cert'] = $row['certificate'];
				$list[] = $new;
				break;
			default:
				continue 2;
		}
		//$list[] = $new;

	}
	return $list;
}
//*********************************************************
static function update_cow_date($pst){
	$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
	$sqds = $vhqab->getSquadronObject();
	$row=array('cow_date'=>$pst['cow_date'],'squad_no'=>$pst['squad_no']);
	$sqds->update_record_changes('squad_no',$row);
	return $pst['cow_date'];
}
} // End of Class modspecify_jobsHelper

