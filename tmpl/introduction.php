<?php
/**
* @package Author
* @author Joseph P. Gibson
* @website www.joesboat.org
* @email joe@joesboat.org
* @copyright Copyright (C) 2018 Joseph P. Gibson. All rights reserved.
* @license GNU General Public License version 2 or later; see LICENSE.txt
**/

defined('_JEXEC') or die('Restricted access');
	$doc = JFactory::getDocument();
	$doc->addScript("scripts/JoesSlideShow.js");
	showHeader($setup['header'],$_SERVER['REQUEST_URI']);
	$_SESSION['setup'] = json_encode($setup);
?>
	<table class='table table-bordered'>	<!--Main display table -->
	<colgroup><col id='xc1'><col id='xc2'></colgroup>
	<tr><td colspan='2'>
	<p>
<?php
	if ($setup['squad'] == '6243'){
		echo show_d5_introduction($setup['excom'],$setup, $vhqab);
	} else {
		echo show_squad_introduction($setup, $vhqab);
	}
?>
	<br>Press <input type='submit' name='command' value='Continue' /> 
	 to display the Jobs List.
	</p>
	</td></tr>
	</table>
<?php
//*************************************************************
function show_d5_introduction($excom, $setup, $vhqab){
	$site_url = $setup['site_url'];
	$exc = $vhqab->getExcomObject();
	$jobcode=$excom['jobcode'];
	//$row = $vhqab->getD5Member($excom['certificate']);
	if ($jobcode==21000 || $jobcode == 25710 || $jobcode == 25001) 
		$depts = 'all';
	else 
		$depts = 'your'; 
	// Display a page of instructions for the EXCOM Officer 
	// Remember what we are doing
?>
	<input type='hidden' name='updating' value='introduction' /> 
	<!--// Always plan to return here after update -->
	<input type='hidden' name='next' value='jobs' /> 
	<input type='hidden' name='order' value='jobcode' > 
	<p>Hello <?php echo $vhqab->getMemberNameAndRank($excom['certificate']);?>, USPS District 5 <?php echo $excom['excom_position'];?>.</p>
	<p>You may change existing job or committee assignments, redefine an existing job description, or create a new job description through the following main Job Assignments page.  It provides a list of all D5 members alongside a list of all job and committee positions within your department.
	<?php 
	if ($dept='all') {
	?>
		As you are D5 Commander (or Webmaster) the list includes jobs for all departments.  
	<?php
	}
	?>
	Both lists scroll to allow you to access all members or job descriptions.</p>
	<p>The member list is displayed on the left of all pages where needed.  It lists about 2200 D5 members and is organized alphabetically. It allows you to scroll to select a member for an assignment. </p> 
	<p>The jobs list includes Job or Committee names. Jobs that are typically assigned to one member are managed in the jobs list.  Committees typically consist of several members and are organized with a committee chairperson.  A &acute;Show&acute;button is displayed besides the committee name to switch focus to a window where committee assignments are managed.  Some jobs, such as Officer Aides, are assigned to multiple members.  They are considered &acute;Groups&acute;and are also managed in a separate widow.</p>
	<p>&acute;Delete&acute; or &acute;Assign&acute; buttons control all assignments.  The &acute;Delete&acute; button (<img src='<?php echo $site_url;?>/images/close.gif'>) removes a current assignment.  The &acute;Assign&acute; button (<img src='<?php echo $site_url;?>/images/right.gif'>) adds a member name to a job assignment.  We suggest that you first delete the current assignment.  Then, select the member name from the member list and use the &acute;Assign&acute; button to transfer that name into the job assignment.  After all assignment changes have been made in a form, the &acute;Update button at the bottom of each window will save changes to the database.  Job assignment changes are not permanent until an 'Update' button is pressed. </p>
	<p>On each jobs list entry the &acute;Job Definition&acute; button (<img src='<?php echo $site_url;?>/images/down.gif'>) is shown.  It provides access to a window where parameters of the job may be changed.  The first jobs list entry is &acute;Create a new job description&acute;.
	<p>After each &acute;Update&acute; the jobs list is displayed containing new or modified entries.  You may then continue to change assignments until the new set of assignments for your department is complete.</p>
<?php
}
//*************************************************************
function show_squad_introduction($setup, $vhqab){
$sqds = $vhqab->getSquadronObject();
$exc = $vhqab->getExcomObject();
$site_url = $setup['site_url'];	
$squad_list = $sqds->get_squadron_list();
?>
	<input type='hidden' name='updating' value='introduction' />  
	<input type='hidden' name='next' value='jobs' />
	<p>You may change existing job or committee assignments or redefine an existing job description through the following Job Assignments page.  The jobs list is displayed besides a list of squadron members.  Both lists scroll to allow you to access all members or job descriptions.</p>
	<?php 
		//if ($exc->excom_member($_SESSION['user_id'],"site_maint",$setup['year'])) :
		if ($setup['site_maint']) : 
	?>
		<p>As D5 Secretary or Webmaster you are permitted to specify the squadron.  Select the squadron you wish to update:  
		<?php 
		$sqds->showSquadronListBox('squad',sprintf("%04d",$setup['squad_no']),$squad_list,3); ?>
		<br>
		Press <input type='submit' name='command' value='Continue' />
<!--<input type='text' name='squad' size='4' value='<?php echo $setup['squad']; ?>' >-->
	<?php 
		else :
			//$squad_no = $vhqab->getSquadNumber($setup['']);
	?>			
			<input type='hidden' name='squad' value='<?php echo $setup["squad_no"]; ?>' />
	<?php 
	endif;  
	?>
	</p>  
	<p>	The jobs will be displayed in 'Display' order.  Display order lists squadron jobs in the order they will be displayed on the squadron page of the District 5 roster booklet.  If desired, you may switch the display to 'Jobcode' order using the following (Jobcode 
		<input type='radio' name='order' value='jobcode' title="Jobcode order lists elected officers followed by committee jobs that report to that officers department."/> or Display <input type='radio' name='order' value='display_order' checked title="Display order lists squadron jobs in the order they will be displayed on the squadron page of the District 5 roster booklet."/>) control. You may use a similar control on other pages to change the order.  </p>	
		<p>The jobs list includes Job or Committee names. Jobs that are typically assigned to one member are managed in the jobs list.  Committees typically consist of several members and are organized with a committee chairperson.  A &acute;Show&acute; button is displayed besides the committee name to switch focus to a window where committee assignments are managed.  Some jobs, such as Officer Aides, are assigned to multiple members.  They are considered &acute;Groups&acute; and are also managed in a separate widow.  At this time committee members are not ativated for squadrons.</p>
	<p>&acute;Delete&acute; or &acute;Assign&acute; buttons displayed to the right of each job name control all assignments.  The &acute;Delete&acute; button (<img src='<?php echo $site_url;?>/images/close.gif' class="form-group">) removes a current assignment.  The &acute;Assign&acute; button (<img src='<?php echo $site_url;?>/images/right.gif' class="form-group">) adds a member name to a job assignment.  We suggest that you first delete the current assignment.  Then, select the member name from the member list and use the &acute;Assign&acute; button to transfer that name into the job assignment.  Job assignment changes are displayed, but are not permanent until an 'Update' button is pressed!  You may make multiple assignments and then use the &acute;Update button at the bottom of the window to save changes to the database. 
	<?php 
		if ($setup['site_maint']) : 
	?>
	On each jobs list entry the &acute;Job Definition&acute; button (<img src='<?php echo $site_url;?>/images/down.gif' class="form-group">) is shown.  It provides access to a window where parameters of the job may be changed. 
	<?php 
		endif;
	?>	
	</p>
	
	<p>All changes are temporary until the &acute;Update&acute; button is used to store changes to the database.  After each &acute;Update&acute; the jobs list is displayed to show new or modified entries.  You may then continue to change assignments until the new set of assignments for your squadron is complete.</p>
	<p></p>
	
	<?php
	return;
}
?>