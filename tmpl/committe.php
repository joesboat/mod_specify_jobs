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

$mbr = $vhqab->getD5MembersObject();
$name = $setup['unit_name'];
$codes = $vhqab->getJobcodesObject();
$exc = $vhqab->getExcomObject();
$_SESSION['setup'] = json_encode($setup);
$code = $_POST['committee_code']
?>
<p>Fill out the committee roster by adding or deleting names in this form.  Use the <img src="<?php echo "$site_url/images/close.gif"; ?>" /> button besides the name to delete a member from the committee.  To add, first select the member's name from the 'All D5 Members' list.  Then use one of the three committee position buttons to add that member to the form. You must press the 'Update' button to save the changes.</p>
	<br />
	<input type="hidden" name="updating" value="committee" />
	<input type="hidden" name="next" id="next" value="jobs" />
	
	<table class='table table-bordered'> 
<colgroup><col id='xc1'><col id='xc2'></colgroup>	
	<tr>
		<th>
			<?php echo $name; ?> Jobs - District Year: <input type='text' name='year' size='8' value='<?php echo $setup['year'];?>'>
		</th>
		<th>
			<?php echo $name; ?> Members
		</th>
	</tr>	
	<!--display the main scroll row -->
	<tr>
		<td>
			<div class='scroll'>
<?php	
				if ($setup['squad']=='6243')
					$squad_no = '';
				else{
					$squad_no = $setup['squad'];
					echo "<input type='hidden' name='squad_no' id='squad_no' value='$squad_no' />";
				}
//					show_committee($_POST['committee_code'], $setup['dept_code'],$setup['year'], $vhqab, $site_url );
					$members = array();
					//	Called from specify jobs 
					//  Displays a committee.
					$new = false;
					// Remember what we are doing to inform PHP in $_POST array
?>
<?php
					if ($committee=$codes->get_record("jobcode",$code)){
						if ($committee["committee"] == "1"){
							$emer_code = $code + 9;
							$asst_code = $code + 1;
							$mem_code = $code + 2;
							$emeritus = $vhqab->getJobAssignments($emer_code,$setup['year'],$squad_no);
							$chairs = $vhqab->getJobAssignments($code,$setup['year'],$squad_no);
							$asst_chairs = $vhqab->getJobAssignments($code+1,$setup['year'],$squad_no);
							if ($setup['squad']=='6243'){
?>
								<input type="hidden" name="jobcode_emeritus" id="jobcode_emeritus" 
									value="<?php echo $emer_code; ?>" />
<?php 
							}
?>
							<input type="hidden" name="jobcode_chair" id="jobcode_chair" 
								value="<?php echo $code; ?>" />
							<input type="hidden" name="jobcode_asst" id="jobcode_asst" 
								value="<?php echo $asst_code; ?>" />
<?php
						} else 
							$mem_code = $code;
						$members=$vhqab->getJobAssignments($mem_code,$setup['year'],$squad_no);
?>
						<input type="hidden" name="jobcode_member" id="jobcode_member" value="<?php echo $mem_code; ?>" />
<?php
					}
?>

<?php
					if ($committee["committee"] == "1"){
						if ($setup['squad']=='6243' and ($setup['dept_code'] == DCOMMANDER or $setup['dept_code'] == WEBMASTER)){
							show_cmte_transfer_button("Emeritus","Add Emeritus");
						}
						show_cmte_transfer_button("Chair","Add Chairman");
						show_cmte_transfer_button("Asst","Add Asst");
					}
					show_cmte_transfer_button("Member","Add Member");
?>
					<br/>
					<table id="cmte">
						<tbody id="cmte_hd">
						<tr id="cmte_r1">
							<th id="cmte_r1c1">Committee Name: </th>
							<td id="cmte_r1c2">
								<input id="cmte_name" type="text" name="committee_name" 
<?php
								if (!$new) {
?>			
									value="<?php echo $committee['jdesc']; ?>"
<?php
								} 
?>
								readonly size="50" />
							</td>
						</tr>
<?php	
					if ($setup['dept_code'] == WEBMASTER) {
?>
						<tr>
							<th>Committee Code:</th>
							<td><?php echo $code; ?></td>
						</tr>
<?php
					}
?>
						</tbody>
						<br/>
<?php
						if ($committee['committee'] == '1'){
						show_committee_position_lines("Emeritus",$emeritus, $vhqab, $site_url);
						show_committee_position_lines("Chair",$chairs, $vhqab, $site_url);
						show_committee_position_lines("Asst",$asst_chairs, $vhqab, $site_url);
				}
				show_committee_position_lines("Member",$members, $vhqab, $site_url);
				$named = $vhqab->get_named_job_assignments($code,false,$setup['year']);
				foreach($named as $n){
?>
					<tr>
						<td><?php echo $n[0]; ?></td>
						<td><?php echo $n[1]; ?></td>
					</tr>	
<?php
				}
?>
	</table>
<?php 			
//				}else{
//					show_squad_committee($_POST['committee_code'], $setup['squad'],$setup['year'], $vhqab, $site_url);
//				}
?>	
			</div>
		</td>
		<td>
<?php 	
		if ($setup['squad']=='6243'){
			$unit = '' ;
		}else{
			$unit = $setup['squad'];
		}
		$mbr->show_member_list_box($mbr->get_d5_or_squad_member_list($unit),22);
?>
		</td>
	</tr>
		<tr>
			<td>
				After making job assignment changes press <input type='submit' name='command' value='Update'>
			</td>
			<td></td>
		</tr>
	</table>
<p>Otherwise, to return to the Members Only Page press 
<?php 
showTrailer();
//*********************************************************
function show_cmte_transfer_button($type,$message){
?>
<button type="button" id="b_cmte_<?php echo $type; ?>" 
	onclick="btn_add_cmte_mbr('<?php echo $type; ?>')" 
	title="Add entry from D5 Member List to this committee position.  You may add multiple entries to each position.  We suggest you add names to a list before deleting existing names.">
	<?php echo $message; ?>
</button>
<?php 
}
//*********************************************************
function show_committee_position_lines($name,$ary, $vhqab, $site_url){
	$empty=false;
	$i = 0;
	?>
	<tbody id="cmte_<?php echo $name; ?>">
	<?php
	foreach($ary as $a){
		$id = $name."_".$i;
		show_committee_position_line($name, $a, $id, $vhqab, $site_url );
		$i ++;
	}
	?>
	</tbody>
	<?php 
}
//*********************************************************
function show_committee_position_line($name, $a, $id, $vhqab, $site_url){
?>
	<tr id="r_<?php echo $id; ?>">
		<th id="rc1_<?php echo $id; ?>">
			<?php echo $name; ?> :  
			<input id="cert_<?php echo $id; ?>" type="hidden" name="cert_<?php echo $id; ?>" value="<?php echo $a['certificate']; ?>" />
		</th>
		<td id="rc2_<?php echo $id; ?>">
			<input type="text" id="name_<?php echo $id; ?>" size="50" value=
				"<?php echo $vhqab->getMemberNameAndRank($a['certificate']); ?>">
		</td>
		<td id="rc3_<?php echo $id; ?>">
		<button type="button" id="b_<?php echo $id; ?>" onclick="btn_del_cmte_mbr(this);">
			<img src="<?php echo "$site_url/images/close.gif"; ?>" >
		</button>
		</td>
	</tr>
<?php
}

?>