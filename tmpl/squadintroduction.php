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
?>
	<input type='hidden' name='updating' value='introduction' />  
	<input type='hidden' name='next' value='jobs' />
	<p>You may change existing job or committee assignments or redefine an existing job description through the following Job Assignments page.  The jobs list is displayed besides a list of squadron members.  Both lists scroll to allow you to access all members or job descriptions.</p>
	<?php 
		if ($setup['site_maint']) : 
	?>
		<p>As D5 Secretary or Webmaster you are permitted to specify the squadron.  
			Select the squadron you wish to update:  
		<select name='new_squad' size='5' id='new_squad' width='50'>
<?php		
			show_option_list($squad_list,$setup['squad_no']);
?>
		</select>
		<br>
		Press <input type='submit' name='command' value='Continue' />
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
	
