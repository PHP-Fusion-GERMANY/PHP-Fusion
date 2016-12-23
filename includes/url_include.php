<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: url_include.php
| Author: Dennis Vorpahl
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access denied");  }

/**
 * replaces unwanted character in URL and give us the complete url
 * @param string $file the php file we want to show
 *        btw news.php
 * @param string $page_title the title of site
 *        btw news_title
 * @param string $before string btw news_id, readmore etc. between file and title
 * @param string $after string after title btw comments section
 *        it can be filled with $_GET Parameters
 * @return complete link url
 */
function pageurl($file,$page_title, $before='', $after=''){

    $text = strip_tags(trim($page_title));

    $replace = array("ae", "ae", "ae", "ae", "ue", "ue", "ue", "ue", "oe", "oe", "oe", "oe", "ss", "-Euro-", "-Euro-", " ", "-und-", "-", "-", "-", "-", "-","-","-","-","-","-","-", "-");
    $search = array("Ä", "ä", "&auml;", "&Auml;", "ü", "Ü", "&uuml;", "&Uuml;", "ö", "Ö", "&ouml;", "&Ouml;", "&szlig;", "&euro;", "€", "&quot;", "&amp;", "&nbsp;", "&ndash;", "&bdquo;", "&ldquo;", "&mdash;", "&middot;", "\"", ".", ":", "?", "\'", "!");

    $text = str_replace($search, $replace, $text);

    $text = str_replace(" ", "-", $text);
    $text = str_replace(":", "-", $text);
    $text = str_replace("/", "-", $text);
    $text = str_replace("_", "-", $text);
    $text = preg_replace("/([\s\s]+|[--]+)/", "-", $text);

    $url  = $file."/";
    if($before != "") $url .= $before."/";
    $url .= $text;
    if($after != "") $url .= "/".$after;

    return $url;
}
?>
