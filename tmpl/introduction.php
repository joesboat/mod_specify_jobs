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
	// $_SESSION['setup'] = json_encode($setup);
?>
	<table class='table table-bordered'>	<!--Main display table -->
	<colgroup><col id='xc1'><col id='xc2'></colgroup>
	<tr><td colspan='2'>
	<p>
<?php
	if ($setup['mode'] == 'd5'){
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','d5introduction'));
	} else {
		require(JModuleHelper::getLayoutPath('mod_specify_jobs','squadintroduction'));
	}
?>
	<br>Press <input type='submit' name='command' value='Continue' /> 
	 to display the Jobs List.
	</p>
	</td></tr>
	</table>
<?php
?>