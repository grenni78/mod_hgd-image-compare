<?php
/*------------------------------------------------------------------------------------------------------/
				Holger Genth -Dienstleistungen-
/-------------------------------------------------------------------------------------------------------/	@version		1.0.0
	@created		11th July, 2017
	@package		mod_hgd-parallax-slider
	@author			Holger Genth <https://holger-genth.de/joomla>
	@copyright		Copyright (C) 2017. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-3.0.html
/------------------------------------------------------------------------------------------------------*/


// No direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
$options = modHgdImageCompare::prepare($params);
require JModuleHelper::getLayoutPath('mod_hgd-image-compare');