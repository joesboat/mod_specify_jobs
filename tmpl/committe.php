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
//if (! $cmte_mbrs or count($cmte_mbrs == 0)){
//	echo "An error has occurred.  Please notify webmaster.";
//	exit;	
//}
//*************************************************************
	if ($setup['mode']=='squad'){
		$squad_no = $setup['squad_no'];
		echo "<input type='hidden' name='squad_no' id='squad_no' value='$squad_no' />";
	}
?>
<p>Fill out the committee roster by adding or deleting names in this form.  Use the <img src="<?php echo getSiteUrl()."/images/close.gif"; ?>" /> button besides the name to delete a member from the committee.  To add, first select the member's name from the 'All D5 Members' list.  Then use one of the three committee position buttons to add that member to the form. You must press the 'Update' button to save the changes.</p>
	<br />
	<input type="hidden" name="updating" value="committee" />
	<input type="hidden" name="next" id="next" value="jobs" />
	
	<table class='table table-bordered'> 
<colgroup><col id='xc1'><col id='xc2'></colgroup>	
	<tr>
		<th>
			<?php echo $setup['unit_name']; ?> Jobs - District Year: <input type='text' name='year' size='8' value='<?php echo $setup['year'];?>'>
		</th>
		<th>
			<?php echo $setup['unit_name']; ?> Members
		</th>
	</tr>	
	<!--display the main scroll row -->
	<tr>
		<td>
			<div class='scroll'>
<?php	
//				show_committee($_POST['committee_code'], $setup['dept_code'],$setup['year'], $vhqab, $site_url );
				$members = array();
				//	Called from specify jobs 
				//  Displays a committee.
				// Remember what we are doing to inform PHP in $_POST array
?>
<?php
				$committee_code = $setup['committee_code'];
				if ($cmte_mbrs[$committee_code]['type'] == "1"){
					$emer_code = $committee_code + 9;
					$asst_code = $committee_code + 1;
					$mem_code = $committee_code + 2;
//					$emeritus = $vhqab->getJobAssignments($emer_code,$setup['year'],$squad_no);
//					$chairs = $vhqab->getJobAssignments($committee_code,$setup['year'],$squad_no);
//					$asst_chairs = $vhqab->getJobAssignments($committee_code+1,$setup['year'],$squad_no);
					if ($setup['mode']=='d5'){
?>						
						<input type="hidden" name="jobcode_emeritus" id="jobcode_emeritus" 
							value="<?php echo $emer_code; ?>" />
<?php 
					}
?>
					<input type="hidden" name="jobcode_chair" id="jobcode_chair" 
						value="<?php echo $committee_code; ?>" />
					<input type="hidden" name="jobcode_asst" id="jobcode_asst" 
						value="<?php echo $asst_code; ?>" />
<?php
				} else 
					$mem_code = $committee_code;
?>
				<input type="hidden" name="jobcode_member" id="jobcode_member" value="<?php echo $mem_code; ?>" />
<?php
				if ($cmte_mbrs[$setup['committee_code']]['type'] == "1"){
					if ($setup['mode']=='d5' and ($setup['dept_code'] == DCOMMANDER or $setup['dept_code'] == WEBMASTER)){
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
									value="<?php echo $cmte_mbrs[$committee_code]['title']; ?>"
								readonly size="50" />
							</td>
						</tr>
<?php	
						if ($setup['dept_code'] == WEBMASTER) {
?>
							<tr>
								<th>Committee Code:</th>
								<td><?php echo $committee_code; ?></td>
							</tr>
<?php
						}
?>
						</tbody>
						<br/>
<?php
				if ($cmte_mbrs[$committee_code]['type'] == '1'){
					if (isset($cmte_mbrs[$committee_code]['Chair Emeritus'] ))	
						show_committee_position_lines("Emeritus",$cmte_mbrs[$committee_code]['Chair Emeritus']);
					if (isset($cmte_mbrs[$committee_code]['Chair'] ))	
						show_committee_position_lines("Chair",$cmte_mbrs[$committee_code]['Chair']);
					if (isset($cmte_mbrs[$committee_code]['asst'] ))	
						show_committee_position_lines("Asst",$cmte_mbrs[$committee_code]['asst']);
				}
				if (isset($cmte_mbrs[$committee_code]['mbrs'] ))
					show_committee_position_lines("Member",$cmte_mbrs[$committee_code]['mbrs']);
			if ($setup['mode'] == 'd5'){
				foreach($cmte_mbrs[$committee_code]['named'] as $n){
?>
					<tr>
						<td><?php echo $n[0]; ?></td>
						<td><?php echo $n[1]; ?></td>
					</tr>	
<?php
				}
			}
?>
					</table>
				</div>
			</td>
			<td>
				<select name='member_cert' size='23' id='member_cert' >" ;
					<?php show_option_list($mbr_list,"");?>
				</select>
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
function show_committee_position_lines($name,$ary){
	$empty=false;
	$i = 0;
	?>
	<tbody id="cmte_<?php echo $name; ?>">
	<?php
	foreach($ary as $ix=>$a){
		$id = $name."_".$i;
		show_position_line($name, $ix, $a, $id);
		$i ++;
	}
	?>
	</tbody>
	<?php 
}
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
function show_position_line($name, $ix, $a, $id){
?>
	<tr id="r_<?php echo $id; ?>">
		<th id="rc1_<?php echo $id; ?>"><?php echo $name; ?>:<input id="cert_<?php echo $id; ?>" type="hidden" name="cert_<?php echo $id; ?>" value="<?php echo $ix; ?>" />
		</th>
		<td id="rc2_<?php echo $id; ?>">
			<input type="text" id="name_<?php echo $id; ?>" size="50" value=
				"<?php echo $a; ?>">
		</td>
		<td id="rc3_<?php echo $id; ?>">
		<button type="button" id="b_<?php echo $id; ?>" onclick="btn_del_cmte_mbr(this);">
			DEL <img src="<?php echo getSiteUrl()."/images/close.gif"; ?>" >
		</button>
		</td>
	</tr>
<?php
}