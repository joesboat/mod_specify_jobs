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

showHeader($setup['header'],$me.'?'.htmlspecialchars(SID));

echo $show_it;

echo "<p>Otherwise, to return to the Members Only Page press ";
showTrailer();
