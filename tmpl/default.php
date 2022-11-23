<?php

/*------------------------------------------------------------------------------------------------------/
Holger Genth -Dienstleistungen-
/-------------------------------------------------------------------------------------------------------/

@version        1.0.0
@created        7th July, 2017
@package        mod_hgd-image-compare
@author            Holger Genth <https://holger-genth.de/joomla/>
@copyright        Copyright (C) 2017. All Rights Reserved
@license        GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-3.0.html
/------------------------------------------------------------------------------------------------------*/

defined('_JEXEC') or die;

?>

<div class="hgd_image-compare col-lg-12 col-sm-12 col-xs-12" id="<?php echo $options["myid"]; ?>">
  <div class="mask">
    <div class="image image-1">
      <img class="img" src="<?php echo $options["image1"]["src"]; ?>" alt="<?php echo $options["image1"]["title"]; ?>"/>
      <div class="title"><?php echo $options["image1"]["title"]; ?></div>
    </div>
    <div class="image image-2">
      <img class="img" src="<?php echo $options["image2"]["src"]; ?>" alt="<?php echo $options["image2"]["title"]; ?>"/>
      <div class="title"><?php echo $options["image2"]["title"]; ?></div>
    </div>
  </div>
  <div class="separator">
    <div class="handler"><i class="icon-chevron-left"></i><i class="icon-chevron-right"></i></div>
  </div>
</div>
