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
define('DCOMMANDER', '21000');
define('WEBMASTER', '25710');
defined('_JEXEC') or die('Restricted access');
require_once(dirname(__FILE__).'/helper.php');
$loging = $params->get("log");
//*************************************************************
if (isset($_POST['command']) and ! isset($_POST['new_squad'])){
	$setup = json_decode($_SESSION['setup'],TRUE);
	$setup = modspecify_jobsHelper::handle_command($setup) ;
} else {
	$setup['next']='introduction';
	$setup = modspecify_jobsHelper::check_permissions($setup, $params->get("mode"));
	$_SESSION['setup'] = json_encode($setup);
}
switch ($setup['next']){
	case 'introduction':
		$squad_list = modspecify_jobsHelper::get_squadron_list();
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','introduction'));
		break;
	case 'committee':
		$mbr_list = modspecify_jobsHelper::get_member_list($setup['squad_no']);
		$cmte_mbrs = modspecify_jobsHelper::populate_committee_members($setup['committee_code'],$setup['squad_no'],$setup['year']);
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','committe'));
		break;		
	case 'jobs':
		$mbr_list = modspecify_jobsHelper::get_member_list($setup['squad_no']);
		$dept = $setup['dept_code'];
		if ($dept == 21000 || $dept == 25710 || $dept == 25000 || $dept == 25001) $dept = "";		
		if ($setup['mode']=='d5'){
			$job_list = modspecify_jobsHelper::get_d5_jobs_list($dept,$setup['order']);
		}else{
			$job_list = modspecify_jobsHelper::get_squadron_jobs_list($setup['order']);
		}
		$JobList = modspecify_jobsHelper::populate_jobs_list($job_list, $setup);			
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','main'));
		break;

	case 'jobcode':
		$ary = modspecify_jobsHelper::get_jobcode_record($setup['jobcode']);
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','jobcodes'));
		break;
}
?>