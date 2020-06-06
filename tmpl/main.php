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
$document = JFactory::getDocument();
$document->addStyleSheet(getSiteUrl()."/plugins/system/t3/base/bootstrap/css/bootstrap-responsive.css");
$document->addStyleSheet(getSiteUrl()."/templates/usps-site/css/bootstrap-datepicker3.css");
$document->addStyleSheet(getSiteUrl()."/templates/usps-site/css/bootstrap.css");
$document->addScript(getSiteUrl()."/plugins/system/t3/base/js/jquery-1.11.2.js");
$document->addScript(getSiteUrl()."/plugins/system/t3/base-bs3/bootstrap/js/bootstrap.js");
$document->addScript(getSiteUrl()."/templates/usps-site/js/bootstrap-datepicker.js");
$document->addScript("scripts/JoesSlideShow.js");
?>
<style type="text/css">
.datepicker {
	background-color: #fff ;
	color: #333 ;
}
</style>
		<script>
			$(function(){
		   $(".datepicker").datepicker({
			format: "yyyy-mm-dd",
			orientation: "bottom auto",
			autoclose:"TRUE",
			todayHighlight: true
    			});
			});
function on_file(){
var form = (document.getElementById("fh1"));
var file = document.getElementById('in_file');
var gross_name = file.value;
var a_gross = gross_name.split("\\");
var file_name = a_gross[a_gross.length-1];
var subTotalField = document.getElementById("subTotalField1");

}
		</script>
<?php
	showHeader($setup['header'],$_SERVER['REQUEST_URI']);
$site_url = $setup['site_url'];

//*************************************************************
// display the header row 
$name = $setup['unit_name'];
// $_SESSION['setup'] = json_encode($setup);
$order = array	(
				'jdesc'=>'Job Description',
				'jobcode'=>"Job Code",
				'display_order'=>'Display Order',
				'update_order'=>"Update Order"
				);

?>
<section style="display: table;">
  <header style="display: table-row;">
	<div style="display:table-cell;" class="center"><?php echo $name ?>
		Jobs - District Year: <?php echo $setup['year']; ?>  
		Change of Watch on <input type='text' name='cow_date' class="datepicker" size='10' value='<?php echo $setup['cow'];?>' >
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
				<p>Reorder to: 
<?php		
					foreach($order as $col=>$nm){
						if ($setup['squad_no'] == '6243'){
							if ($col == 'update_order') continue;
							if ($col == 'display_order') continue;
						}	
?>
						<input type="radio" name='new_order' 
							value='<?php echo $col;?>' 
							<?php if ($setup['order'] == $col) echo 'checked'; ?>
							onclick='submit_all(this)' />
						<?php echo $nm;?>
<?php		
					}
?>	
				</p>
		<div  style = "height: 400px; overflow: auto;">
			<input type='hidden' name='updating' value='jobs' >  
			<!--Always plan to return here after update--> 
			<input type='hidden' name='next' id='next' value='jobs' >
			<input type='hidden' name='squad_no' id='squad_no' value='<?php echo $setup['squad_no']; ?>' >
			<!--Obtain data to display job assignments -->

			<!--Begin display-->
			<table id='tbl2'>	
<?php
$id = 0;
	foreach($JobList as $jobrow){
		$id++;
		$cert = $jobrow["cert"];
		$name = $jobrow['name'];
		show_job_row($id,$jobrow['committee'],$jobrow['jobcode'],$jobrow['jdesc'],$name,$setup['site_url'],$setup,$cert);}
	?></table>
		</div>
	</div>
    <div style="display: table-cell; vertical-align: top;">
		<div>   
				<select name='member_cert' size='23' id='member_cert' >" ;
					<?php show_option_list($mbr_list,"");?>
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
//*************************************************************
function show_job_row($id,$committee,$jobcode,$jdesc,$name,$site_url,$setup,$cert){
	if ($committee==3) 
		$style = " style='color:grey;' ";
	elseif (substr($jobcode,2,3) == '000')
		$style = " class='lead' ";
	else	
		$style = "";
?><tr id='tr_<?php echo $id;?>'>
	<input type='hidden' id='jobcode_<?php echo $id;?>' value='<?php echo $jobcode;?>' />
	<!--Display column 1  (Job or Committee Name)-->
	<td id='c1_<?php echo $id;?>' <?php echo $style;?> >
	<?php echo $jdesc;?>
	</td>
	<input type='hidden' id='cert_<?php echo $id;?>' value='<?php echo $cert;?>' />
	<!--Display column 2-->  
	<td id='c2_<?php echo $id;?>'><?php echo $name;?></td>
	<!--Display column 3-->
	<td id='c3_<?php echo $id;?>'>
<?php 
		if ($committee != 4)
		if ($committee == 0)
		{
?>
			<button type='button' id='del_<?php echo $id;?>' onclick='btn_del_job_assignment(<?php echo $id;?>)' title='Delete the member shown from this position' >
				<img src='<?php echo $site_url;?>/images/close.gif'>
			</button>
<?php
		} else {
?>
			<button type='button' id='cmte_<?php echo $id;?>' onclick='btn_show_committee(<?php echo $id;?>);' title='Switches focus to the Specify Committee Assignments Page.'>Show</button>
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
			<button type='button' id='add_<?php echo $id;?>' onclick='btn_add_job_assignment(<?php echo $id;?>);' title='Assign this job to a D5 Member. First select the member from the D5 Roster. A new job entry will be created if a member is already assigned to the job.'><img src='<?php echo $site_url;?>/images/left.gif' width='30'></button>
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
?></tr><?php
}