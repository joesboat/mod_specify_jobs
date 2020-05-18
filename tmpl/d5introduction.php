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
	$excom = $setup['excom'];
	$jobcode=$excom['jobcode'];
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
	<p>Hello <?php echo $excom['member_name'];?>, USPS District 5 <?php echo $excom['excom_position'];?>.</p>
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
	<p>&acute;Delete&acute; or &acute;Assign&acute; buttons control all assignments.  The &acute;Delete&acute; button (<img src='<?php echo getSiteUrl();?>/images/close.gif'>) removes a current assignment.  The &acute;Assign&acute; button (<img src='<?php echo getSiteUrl();?>/images/right.gif'>) adds a member name to a job assignment.  We suggest that you first delete the current assignment.  Then, select the member name from the member list and use the &acute;Assign&acute; button to transfer that name into the job assignment.  After all assignment changes have been made in a form, the &acute;Update button at the bottom of each window will save changes to the database.  Job assignment changes are not permanent until an 'Update' button is pressed. </p>
	<p>On each jobs list entry the &acute;Job Definition&acute; button (<img src='<?php echo getSiteUrl();?>/images/down.gif'>) is shown.  It provides access to a window where parameters of the job may be changed.  The first jobs list entry is &acute;Create a new job description&acute;.</p>
	<p>After each &acute;Update&acute; the jobs list is displayed containing new or modified entries.  You may then continue to change assignments until the new set of assignments for your department is complete.</p>

