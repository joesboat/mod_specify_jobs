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
// display the header row 
$name = $setup['unit_name'];
$_SESSION['setup'] = json_encode($setup);
$order = array(	'jdesc'=>'Job Description',
				'jobcode'=>"Job Code",
				'display_order'=>'Display Order',
				'update_order'=>"Update Order");
	$dept = $setup['dept_code'];
	if ($dept == 21000 || $dept == 25710 || $dept == 25000 || $dept == 25001) 
		$dept = "";
?>
<section style="display: table;">
  <header style="display: table-row;">
	<div style="display:table-cell;" class="center"><?php echo $name ?>
		Jobs - District Year: <?php echo $setup['year']; ?>
	</div>
	<div style="display:table-cell;" class="center">
		<?php echo $name ?> Members
	</div>
	<div></div>
  </header>
<!--	
	Page consists of two main columns 
		Committee member jobcodes are ignored.  They are managed in the committee page
		Committee chair job description only is listed with links to another page.  
			When link is activated current assignments are used to update database and 
	  		then the user is transferred to 'Show Committee' page 
			For remaining jobs the following columns are listed. 
				Job Description as TH 
		 		Member assigned as TD
				Icon to delete currently assigned member
				Icon to transfer highlighted name from members column to this job.  
	 Contents of table rows are obtained from jobdesc and jobs tables 
		case 0:	$named_job = 'CHECKED'; break; 		
		case 1:	$committee = 'CHECKED'; break; 		
		case 2:	$group = 'CHECKED'; break; 	
		case 3: $not_used = 'CHECKED'; break;		// 
	 Ignore any jobcode ending in 2 (Committee Member)
	 Display link to 'Show Committee' for each jobdesc with committee in name 
	 For other jobs display the job description followed by member assigned 
	 Display blank area when no member assigned
	 Display multiple lines when more than one member assigned.  
	 Table row For each displayed line consists of:
	 Remember what we are doing
-->  
  <div style="display: table-row;">
	<div style="display: table-cell; vertical-align: top;">
		<div  style = "height: 400px; overflow: auto;">
			<input type='hidden' name='updating' value='jobs' >  
			<!--Always plan to return here after update--> 
			<input type='hidden' name='next' id='next' value='jobs' >
			<input type='hidden' name='squad_no' id='squad_no' value='<?php echo $setup['squad']; ?>' >
			<!--Obtain data to display job assignments -->

			<!--Begin display-->
			<table id='tbl2'>	
<?Php
	if ($setup['squad']=='6243'){
		$list = $codes->get_active_d5_committes($dept);
	}else{
?>
		<p>Reorder to: 
<?php		
		foreach($order as $col=>$nm){
?>
		<input type="radio" name='new_order' value='<?php echo $col;?>' 
			<?php if ($setup['order'] == $col) echo 'checked'; ?>
			onclick='submit_all(this)' />
		<?php echo $nm;?>
<?php		
		}
?>	
		</p>
<?php
		$list = $codes->get_squad_assignments($setup['order']);
	}			
	show_jobs_list($list,$setup['squad'],$setup['year'], $setup, $vhqab, $exc);
?>
			</table>
		</div>
	</div>
    <div style="display: table-cell; vertical-align: top;">
		<div>   
<?php
			if ($setup['squad']=='6243')
				{$unit = '';}
			else
				{$unit = $setup['squad'];}
			$list = $mbr->get_d5_or_squad_member_list($unit);
?>
				<select name='member_cert' size='23' id='member_cert' >" ;
					<?php echo get_option_list($list,"");?>
				</select>
		</div> 
	</div>
  </div>
</section>
<div>
	<p></p>
	<p></p>	
	After making job assignment changes press <input type='submit' name='command' value='Update'/ ></p)
</div>
<?php 
echo "<p>Otherwise, use menu's to select a different page. </p>";
showTrailer();
//*************************************************************
function show_jobs_list($list,$squad,$year,$setup, $vhqab, $exc){
$id = 0;
	foreach($list as $jc){
		if ($jc['jobcode'] == 22600){
			$jjj = $jc;
		}
		$jdesc = $jc['jdesc'];
		$jobcode = $jc['jobcode'];
		if ($jc['committee']==3)		// Not used
			continue;
		if ($jc['committee']==4){
			// it's a bridge level job.
			$row = $vhqab->getExcomMember($jc['jobcode'],$year);
			if ( ! $row ) continue;
			
			$name = $vhqab->getMemberNameAndRank($row['certificate']); 
			show_job_row($id+=1,$jc['committee'],$jobcode,$jdesc,$row['certificate'],$name,$setup['site_url'],$setup);
			continue ;
		}
		if ($jc['committee']==1){
			$rows = $vhqab->getJobAssignments($jc['jobcode'],$year,$squad,TRUE);
		} else {
			$rows = $vhqab->getJobAssignments($jc['jobcode'],$year,$squad);
		}
		if (count($rows)>0 and $jc['committee'] < 2){
			// For Named Jobs or Traditional Committees	
			foreach($rows as $row){
				$cert = $row['certificate'];
				switch ($jc['committee']){
					case 0:
					case 1:
						$name = $vhqab->getMemberNameAndRank($row['certificate']); 
						break; 
					default:
						$column_data[2] = "Select 'Show' to list." ; 	
				}
				show_job_row($id+=1,$jc['committee'],$jobcode,$jdesc,$cert,$name,$setup['site_url'],$setup);
			}
		}else
			show_job_row($id+=1,$jc['committee'],$jobcode,$jdesc,'','',$setup['site_url'],$setup);
	}

}
//*************************************************************
function show_job_row($id,$committee,$jobcode,$jdesc,$cert,$name,$site_url,$setup){
	if ($committee==3) 
		$style = " style='color:grey;' ";
	elseif (substr($jobcode,2,3) == '000')
		$style = " class='lead' ";
	else	
		$style = "";
	$col3 = $col4 = '';
	$col2 = "<input type='hidden' id='cert_$id' value='$cert' /> . $name" ;
	switch ($committee){
		case 0:
			$col3 =	"<button type='button' id=del_$id onclick='btn_del_job_assignment($id);' ".
					"title='Delete the member shown from this position' >".
					"<img src='$site_url/images/close.gif'>".
					"</button>";
			$col4 = "<button type='button' id=add_$id ".
					"onclick='btn_add_job_assignment($id);' ".
					"title='Assign this job to a D5 Member. First select the member from the D5 Roster.  ".
					"A new job entry will be created if a member is already assigned to the job.'>".
					"<img src='$site_url/images/left.gif' width='30'>".
					"</button>";
			break;
		default:
			$col3 =	"<button type='button' id='cmte_$id' onclick='btn_show_committee($id);' ".
					"title='Switches focus to the Specify Committee Assignments Page.'>".
					"Show".
					"</button>";
	}
	$col5 = "<button type='button' id='define_$id' ".
			"onclick='btn_define_jobcode($id);' ".
			"title='Switches focus to the Setup Jobcode Description Page ".
			"where you may modify the name and parameters of this jobcode.' >".
			"<img src='$site_url/images/down.gif' width='30' ".
			"</button>";

	if ($committee == 4) 
		$col3 = $col4 = $col5 = '';
?>
	<tr id='tr_<?php echo $id;?>'>
	<input type='hidden' id='jobcode_<?php echo $id;?>' value='<?php echo $jobcode;?>' />
	<input type='hidden' id='cert_<?php echo $id;?>' value='<?php echo $cert;?>' />
	<!--Display column 1  (Job or Committee Name)-->
	
	<td id='c1_<?php echo $id;?>' <?php echo $style;?> >
	<?php echo $jdesc;?>
	</td>
	
	<!--Display column 2-->  
	<td id='c2_<?php echo $id;?>'><?php echo $name;?></td>

	<!--Display column 3-->  
	<td id='c3_<?php echo $id;?>'>
<?php 
		if ($committee != 4)
		if ($committee == 0){
?>			
			<button type='button' id='del_<?php echo $id;?>' onclick='btn_del_job_assignment(<?php echo $id;?>)' title='Delete the member shown from this position' >
				<img src='<?php echo $site_url;?>/images/close.gif'>
			</button>
<?php
		} else {
?>
			<button type='button' id='cmte_<?php echo $id;?>' onclick='btn_show_committee(<?php echo $id;?>);' title='Switches focus to the Specify Committee Assignments Page.'>
				Show
			</button>

<?php			
		}
?>
	</td>

	<!--Display column 4-->  
	<td id='c4_<?php echo $id;?>'>
<?php 		
		if ($committee != 4)
		if ($committee == 0){
?>			
			<button type='button' id='add_<?php echo $id;?>' onclick='btn_add_job_assignment(<?php echo $id;?>);' title='Assign this job to a D5 Member. First select the member from the D5 Roster. A new job entry will be created if a member is already assigned to the job.'>
				<img src='<?php echo $site_url;?>/images/left.gif' width='30'>
			</button>
<?php
		}
?>		
	</td>		
	<!--Display column 5-->
<?php 
	if ($setup['site_maint'] and $committee != 4){
?>
		<td id='c5_<?php echo $id;?>'>
			<button type='button' id='define_<?php echo $id;?>' onclick='btn_define_jobcode(<?php echo $id;?>);' title='Switches focus to the Setup Jobcode Description Page where you may modify the name and parameters of this jobcode.' >
				<img src='<?php echo $site_url;?>/images/down.gif' width='30'>
			</button>
		</td>
<?php	
	}
?>
	</tr>
<?php	
}
