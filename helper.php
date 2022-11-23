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

// No direct access
defined('_JEXEC') or die;

class ModHgdImageCompare
{
    public static function makeRGBA($hex, $opacity)
    {
        $matches = array(00, 00, 00);
        $ret = "rgba(";

        preg_match('/#{0,1}([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/', $hex, $matches);

        $ret .= hexdec($matches[1]) . ", " . hexdec($matches[2]) . ", " . hexdec($matches[3]) . ", " . ($opacity / 100) . ")";

        return $ret;
    }

    // Bereitet Daten für das Modul vor
    public static function prepare($params)
    {
        JHtml::_('jquery.framework');
        $base = JURI::base();
        $OPTIONS = array(
            "myid" => uniqid('HGD-image-compare'),
            "container" => array(
                "width" => $params->get("width"),
                "height" => $params->get("height"),
                "maxWidth" => $params->get("max_width"),
                "maxHeight" => $params->get("max_height"),
            ),
            "controls" => array(
                "bgcolor" => $params->get("bgcolor"),
                "opacity" => $params->get("opacity"),
            ),
            "autoHeight" => ($params->get("height") == "auto"),
            "image1" => array(
                "file" => $_SERVER["DOCUMENT_ROOT"] . '/' . $params->get('image1'),
                "src" => $base . '/' . $params->get('image1'),
                "title" => $params->get('title1'),
                "width" => 0,
                "height" => 0,
                "aspect" => 1.0,
            ),
            "image2" => array(
                "file" => $_SERVER["DOCUMENT_ROOT"] . '/' . $params->get('image2'),
                "src" => $base . '/' . $params->get('image2'),
                "title" => $params->get('title2'),
                "width" => 0,
                "height" => 0,
                "aspect" => 1.0,
            ),
        );

        $STAGES = array();

        // Größe der Bilder ermitteln und speichern
        $imagesize = @getimagesize($OPTIONS["image1"]["file"]);

        if (is_array($imagesize) && count($imagesize) >= 2) {

            $OPTIONS["image1"]["width"] = $imagesize[0];
            $OPTIONS["image1"]["height"] = $imagesize[1];
            $OPTIONS["image1"]["aspect"] = $imagesize[0] / $imagesize[1];

        }

        $imagesize = @getimagesize($OPTIONS["image2"]["file"]);

        if (is_array($imagesize) && count($imagesize) >= 2) {

            $OPTIONS["image2"]["width"] = $imagesize[0];
            $OPTIONS["image2"]["height"] = $imagesize[1];
            $OPTIONS["image2"]["aspect"] = $imagesize[0] / $imagesize[1];

        }

        $maxWidth = ($OPTIONS["container"]["maxWidth"] !== 0) ? "max-width: " . $OPTIONS["container"]["maxWidth"] : "";
        $maxHeight = ($OPTIONS["container"]["maxHeight"] !== 0) ? "max-height: " . $OPTIONS["container"]["maxHeight"] : "";

        // add necessary files to the document
        JHtml::_('jquery.framework');

        $doc = JFactory::getDocument();
        if (JDEBUG) {
            $doc->addStyleSheet($base . "modules/mod_hgd-image-compare/assets/css/mod_hgd-image-compare.css");
            $doc->addScript($base . "modules/mod_hgd-image-compare/assets/js/mod_hgd-image-compare.js");
        } else {
            $doc->addStyleSheet($base . "modules/mod_hgd-image-compare/assets/css/mod_hgd-image-compare.min.css");
            $doc->addScript($base . "modules/mod_hgd-image-compare/assets/js/mod_hgd-image-compare.min.js");
        }
        $doc->addStyleSheet($base . '/media/jui/css/icomoon.css');

        $doc->addScriptDeclaration("
        (function($){
          $(document).ready(function(){
            const config = " . json_encode($OPTIONS) . ";
            $('#'+config.myid).hgdImageCompare(config);
          });
        })(jQuery);
      ");

        $doc->addStyleDeclaration("
        div.hgd_image-compare {
          width:" . $OPTIONS["container"]["width"] . ";
          height:" . $OPTIONS["container"]["height"] . ";
          " . $maxWidth . "px;
          " . $maxHeight . "px;
        }

        .hgd_image-compare .separator,
        .hgd_image-compare .separator .handler{
          background-color: " . self::makeRGBA($OPTIONS["controls"]["bgcolor"], $OPTIONS["controls"]["opacity"]) . ";
        }
      ");

        return $OPTIONS;
    }
}
