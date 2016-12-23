<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: index.php
| Author:  Dennis Vorpahl
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "maincore.php";
/**
 * @var string $request full browser path.
 * @var int[] $params count get parameters.
 * @var $params[0] is file or infusions folder
 * @var if $params[0] infusions folder, then $params[1] file in infusions folder
 */
#remove unwanted extensions
$request  = str_replace('.html', "", cleanurl($_SERVER['REQUEST_URI']));
$request  = str_replace('.php', "", $request);
// removes the Get paremters we don't want in param
$request  = preg_replace('/[?](.*)/', "", $request);
#remove the directory path we don't want
if($settings['site_path'] == '/') $request  = substr($request, 1);
else str_replace($settings['site_path'], "", $request);
#explode the path by '/'
$params = explode("/", $request);

// load the page we want
// core
if(file_exists($params[0].'.php')) require_once $params[0].'.php';
// infusions
elseif (isset($params[1]) && file_exists(INFUSIONS.$params[0].'/'.$params[1].'.php')) require_once  INFUSIONS.$params[0].'/'.$params[1].'.php';
// startpage
elseif ($params[0]=='index' || $params[0] == "") require_once $settings['opening_page'];
// errorpage
else require_once '404.php';
?>
