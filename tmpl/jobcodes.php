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
$doc = JFactory::getDocument();
$doc->addScript("scripts/JoesSlideShow.js");
	showHeader($setup['header'],$_SERVER['REQUEST_URI']);
$site_url = $setup['site_url'];
//*************************************************************
?>
<table class='table table-bordered'>		<!--// Main display table -->
<colgroup><col id='xc1'><col id='xc2'></colgroup>
<?php	
	$code = $_POST['jobcode'];
	$purpose = "Modify";
	if ($setup['mode']=='d5'){
		$unit = "District 5";
		$jobgroup = '28__0';
		$list = modspecify_jobsHelper::get_committee_list('2___0');
	}else{
		$unit = "D5 Squadron";
		$jobgroup = '38__0';
		$list = modspecify_jobsHelper::get_committee_list('3___0');
	}
	// Displays parameters of a jobcode.  
	// Creates parameters for a new jobcode 
	$committee = $named_job = $group = $not_used = "";
	// Remember what changes are being made to inform PHP in $_POST array
?>
	<input type='hidden' name='updating' value='jobcode' />
	<!--// Always assume we will return focus to the jobcodes list -->
	<!--// Hidden value may be changed by script to return focus to higher level 'jobs' -->
	<input type='hidden' name='next' id='next' value='jobs' />
<?php
	if (!$ary){
		$purpose = 'Create';
		$new = true;
		$ary = array();
		// We assume that a new jobcode is a named job.  
		$ary['jobcode'] = $code = $codes->get_new_jobcode($jobgroup); 
		$ary['committee_code']=0;
		$ary['department']=0;
		$ary['committee']=0;		// Assume normal job
		$ary['jdesc']='===  Enter Job Name Here ===';
		$ary['d5_job'] = 1;
	}
	switch ($ary['committee']){
		case 0:	$named_job = 'CHECKED'; break; 		
		case 1:	$committee = 'CHECKED'; break; 		
		case 2:	$group = 'CHECKED'; break; 	
		case 3: $not_used = 'CHECKED'; break;	
	}
?>
		<input type='hidden' name='new' value=''>

	<tr>
		<th></th>
		<th>
			<h3><?php echo "$purpose $unit Job Description";?>
		
		</th>
	</tr>
	<tr>
		<td></td>
		<td>
			<div class='scroll'>
				<p>	Job Name: 
				<input type='text' name='jdesc' value='<?php echo $ary['jdesc'];?>' size='50' >
				&nbsp;&nbsp;&nbsp;
				Job Code: 
				<input type='text' name='jobcode' value='<?php echo $ary['jobcode'];?>' size='5' readonly >

				</p>
				<p>Select from the following to specify how this jobcode will be managed:
				<br/>
				<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input 
					type='radio' 
					name='committee' 
					onclick='rad_jobdesc_change(1);' 
					<?php echo $committee; ?>  
					value = '1' 
				/>&nbsp;&nbsp;Committee: This is a 'Traditional Committee'.  It may be populated with one or more 'Chairs', 'Assistants' or 'members'. As exampler - Nominating is a traditional committee. 
				<br/>	 
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input 
					type='radio' 
					name='committee' 
					onclick='rad_jobdesc_change(1);' 
					<?php echo $group; ?> 
					value = '2' 
				/>&nbsp;&nbsp;Group: &nbsp;&nbsp; Groups may only have 'members'.   An Example: Commander's Aides is a group.
				<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input 
					type='radio' 
					name='committee' 
					onclick='rad_jobdesc_change(0);' 
					<?php echo $named_job;?> 
					value = '0' 
				/>&nbsp;&nbsp;Named Job:   An individual job is shown in the jobs list.  An example: An officer is a type of named job that stands alone. A named job may also be associated with a committee.  If so, designate the committee for this named job. 
<?php 	
	if (($ary['committee']==0) and (substr($code,0,1)==2)){
?>
		You may optionally specify to associate this job with a committee.
<?php		
		$cmte = $ary['committee_code'];
		if ($cmte == '')
			$cmte = 0;
		$codes->show_committee_dd_list($list,$cmte);
	}
?>
				<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input 
					type='radio' 
					name='committee' 
					onclick='rad_jobdesc_change(0);' 
					<?php echo $not_used; ?> 
					value = '3' 
				/> &nbsp;&nbsp;Not Used:   Select to eliminate this jobcode from apperaing the normal list.
	</p>
	<p>
<?php
	if (substr($code,0,1)==2){
?>		
		Please specify the department where this job reports:
<?php		
		$exc->show_d5_department_dd_list_box($exc->get_department_list($setup['year']),$ary['department']);
?>
	</p>
	<p>
<?php
	if ($ary['d5_job']==1)
		echo "<input type='checkbox' name='d5_job' checked >";
	else 
		echo "<input type='checkbox' name='d5_job' >";
		echo "&nbsp;&nbsp;Job may be displayed in list;";
	} else {
	// for squadrons allow the Display Order to be updated		
?>
	<label title="Display Order specifies this jobcode's position in the booklet's squadron page. ">
		Display Order
	</label>
	<input name="display_order" type='text' value="<?php echo $ary['display_order'];?>"> 
	<label title="Update Order controls the display sequence in this tool.  Provided to synchronize with manual forms. ">
		Update Order
	</label>
	<input name="update_order" type='text' value="<?php echo $ary['update_order'];?>"> 
<?php
	}
?>	
	
	</p>
	<p>After making job assignment changes press 
	<input type='submit' name='command' value='Update'></p>
	</div>
	</td>
	</tr>
</table>
