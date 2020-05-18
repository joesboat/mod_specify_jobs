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
//*************************************************************
static function check_permissions($setup, $mode, $user_id){
require(JPATH_LIBRARIES."/USPSaccess/dbUSPS.php");
	$ok = false;
	$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
	$jobs = $vhqab->getJobsObject();
	$exc = $vhqab->getExcomObject();
	$blob = JoeFactory::getTable("tableD5Blobs",$db_d5);
	$year = $blob->get_manage_year();	
//	if (isset($_GET['mode'])){
//		$_SESSION['mode'] = strtolower($_GET['mode']);
//		if ($_SESSION['mode'] == 'squad')
//			$_SESSION['squad_no'] = $vhqab->getSquadNumber($username);
//		else
//			$_SESSION['squad_no'] = '6243';
//	}
	$setup['user_id'] = $user_id;
	switch($mode){
		case 'd5':
			$setup['dept_code']="";
			$setup['excom'] = $exc->get_highest_permission($setup['user_id'], $year) ;
			$setup['squad'] = '6243' ;
			$setup['dept_code'] = $exc->get_permission($setup['excom']);
			$setup['header'] = "D5 Job Assignments for $year. - ".$setup['excom']['excom_position']." Permissions";
			$setup['unit_name'] = 'D5';
			break; 
		default:
			//if ($loging) log_it("site_maint is  $site_maint");  
			$setup['squad_no'] = $vhqab->getSquadNumber($user_id);
			if (! $exc->excom_member($user_id,"webmaster",$year)){
				//if (! $exc->excom_member($user_id,"site_maint",$year)) 
				if (!($jobs->check_squadron_officer($_SESSION['user_id'],sprintf("%04d",$setup['squad_no']),$year)))
				{
				header("Location: inform_member.php?inform=squad_cdr&".
							htmlspecialchars(SID));
				log_it("Member ".$setup['user_id']." was not allowed to use this tool") ;
				exit(0);
				}
			
			}
			$setup['squad'] = $mode;
			$setup['dept_code'] = '31000'; 
			$setup['unit_name'] = $vhqab->getSquadronName(sprintf("%04d",$setup['squad_no']));
			$setup['header'] = "Squadron Job Assignments for $year. - ".$setup['unit_name'];
			$setup['order'] = "update_order" ;
			break;
	}
	$_SESSION['when_done'] = $_SERVER['PHP_SELF'];
	$setup['site_maint'] = $site_maint = $exc->excom_member($user_id,"site_maint",$year);
	$setup['site_url'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['CONTEXT_PREFIX'];
	$setup['year'] = $year;
	return $setup;
}
//*************************************************************
static function display_introduction($setup){
	$str = "<table class='table table-bordered'>";		// Main display table 
	$str .= "<colgroup><col id='xc1'><col id='xc2'></colgroup>";
	$str .=  "<tr><td colspan='2'>";
	$str .=  "<p>";
	if ($setup['squad'] == '6243'){
		$str .= xshow_d5_introduction($setup['excom'],$setup);
	} else {
		$str .= xshow_squad_introduction($setup);
	}
	$str .=  "<br>Press <input type='submit' name='command' value='Continue' /> ";
	$str .=  " to display the Jobs List.";
	$str .=  "</p>";
	$str .=  "</td></tr>";
	$str .=  "</table>";
	return $str;
}
//*************************************************************
static function handle_command(){
	$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
	$codes = $vhqab->getJobcodesObject();
	$jobs = $vhqab->getJobsObject();
	$cmd = strtolower($_POST['command']);
	if (isset($_SESSION['setup'])){
		$setup = json_decode($_SESSION['setup'],TRUE);
	} else {
		$setup = array();
	}
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
			// = $_POST['order'];
			if (isset($_POST['squad']))
				if ($_POST['squad'] != $setup['squad']){
					$setup['squad_no'] = $setup['squad'] = $_POST['squad'];
					$setup['unit_name'] = $vhqab->getSquadronShortName(sprintf("%04d",$_POST['squad']));
					$setup['header'] = "Squadron Job Assignments for ".$setup['year']." - ".$vhqab->getSquadronName($setup['squad']);
				}
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
			$committee_code = $_POST['committee_code'];
		if (isset($_POST['jobcode']))
			$jobcode = $_POST['jobcode'];
	}
	$vhqab->close();
	return $setup;
}

} // End of Class modspecify_jobsHelper
