<?php
/**
* @package Author
* @author Joseph P. Gibson
* @website www.joesboat.org
* @email joe@joesboat.org
* @copyright Copyright (C) 2018 Joseph P. Gibson. All rights reserved.
* @license GNU General Public License version 2 or later; see LICENSE.txt
**/
defined('_JEXEC') or die;
jimport('usps.includes.routines');
//require_once(JPATH_LIBRARIES."/USPSaccess/dbUSPS.php");
//require_once(JPATH_LIBRARIES."/usps/tableD5VHQAB.php");
//require_once(JPATH_LIBRARIES."/usps/dbUSPSd5WebSites.php");
//require_once(JPATH_LIBRARIES."/usps/dbUSPSSquadrons.php");
//require_once(JPATH_LIBRARIES."/usps/tableAccess.php");
//require_once(JPATH_LIBRARIES."/usps/dbUSPSjoomla.php");
// no direct access
//include("../setupJoomlaAccess.php");
define('DCOMMANDER', '21000');
define('WEBMASTER', '25710');
defined('_JEXEC') or die('Restricted access');
require_once(dirname(__FILE__).'/helper.php');
$path=explode("/",$_SERVER['PHP_SELF']);
$me = $path[count($path)-1];

$modpath = '';
foreach($path as $index=>$value){
	if ($value == 'index.php'){
		$ix = $index;
	} else {
		if (isset($ix) and $index > $ix){
			$modpath .= "/$value";
		}
	}
}



$vhqab = JoeFactory::getLibrary("USPSd5tableVHQAB");
$mbr = $vhqab->getD5MembersObject();
$codes = $vhqab->getJobcodesObject();
$exc = $vhqab->getExcomObject();
$sqds = $vhqab->getSquadronObject();

$user = JFactory::getUser();
$username = $user->username;
$_SESSION['user_id'] = $username;
// Evaluate $_GET to determine if squadron or distric

$loging = false;

$mode = $params->get("mode");

//*********************************************************
function format_committee_position($name,$ary){
	$empty=false;
	$i = 0;
	$str = "<tbody id='cmte_$name'>";
	foreach($ary as $a){
		$id = $name."_".$i;
		$s =  	"<tr id='r_$id'>";
		$str .= $s; 
		$s =  	"<th id='rc1_$id'>".$name.": "; 
		$s .= 	"<input id='cert_$id' type='hidden' name='cert_$id' value='".$a['certificate']."'>";
		$s .= 	"</th>";
		$str .= $s; 
		$s =  	"<td id='rc2_$id'><input type='text' id='name_$id' size='50' value='";
		$mem = $vhqab->getMemberNameAndRank($a['certificate']);
		if ($empty) $s .=""; else $s .=  $mem;
		$s .=  	"'></td>";
		$str .= $s;
		$s =  	"<td id='rc3_$id'><button type='button' id='b_".$id."' this member from the committee.' onclick='btn_del_cmte_mbr(this);'><img src='$site_url/images/close.gif' ></button></td>";
		$str .= $s;
		$s = 	"</tr>";
		$str .= $s; 
		$i ++;
	}
	return $str."</tbody>"; 
}
//*********************************************************
function get_member_list_box($members,$size){
	$str = "<select name='member_cert' size='$size' id='member_cert' width='50'>" ;
	$str .= get_option_list($members,"");
	$str .= "</select>";
	return $str;
}
//*********************************************************
function get_option_list($ary, $sel){
	// The supplied array contains a list of items of the format:
	//	ID => NAME 
	// Function will build an option list in the following format:
	//	<option value="ID">NAME</option>
	// If $sel <> "" and is = to an ID:
	//	add the value "selected" following ID "
	// Otherwise add the following at end of list
	//	<option value="new" selected>Select a new item</select>
	$found=FALSE;
	$str = '';
	foreach($ary as $key=>$value){
		$str .= '<option value="' . $key . '"' ; 
		if (strtoupper($key) == strtoupper($sel)) {
			$str .= ' selected ' ; 
			$found = true ;
		}
		$str .= ">" . $value . '</option>' ; 
	}
	if ($found){
		return $str ; 
	}
	else 
	{
		return '<option value="" selected>Select from list.</option>'. $str ;
	} 
}
//*********************************************************
function make_cmte_transfer_button($type,$message){
	$str = "<button type='button' id='b_cmte_".$type."' ";
	$str1 = 'onclick="'."btn_add_cmte_mbr('".$type."');".'" '; 
	$str2 = "title='Add entry from D5 Member List to this committee position.  You may add multiple entries to each position.  We suggest you add names to a list before deleting existing names.'>$message</button>";
	return $str.$str1.$str2; 
}
//*************************************************************
if (isset($_POST['command'])){
	$setup = modspecify_jobsHelper::handle_command() ;
} else {
	$setup['next']='introduction';
	$setup = modspecify_jobsHelper::check_permissions($setup, $mode, $username);
}
switch ($setup['next']){
	case 'introduction':
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','introduction'));
		break;
	case 'jobs':
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','main'));
		break;
	case 'committee':
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','committe'));
		break;		
	case 'jobcode':
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','jobcodes'));
		break;
}
$vhqab->close();
?>