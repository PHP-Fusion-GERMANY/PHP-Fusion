<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: viewpage.php
| Author: Nick Jones (Digitanium)
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
require_once THEMES."templates/header.php";
require_once INCLUDES."comments_include.php";
require_once INCLUDES."ratings_include.php";
include LOCALE.LOCALESET."custom_pages.php";

if (!isset($_GET['page_id']) || !isnum($_GET['page_id']))
    redirect("index.php");

if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart']))
    $_GET['rowstart'] = 0;

$cp_result = dbquery("SELECT * FROM ".DB_CUSTOM_PAGES." WHERE page_id='".$_GET['page_id']."'");
if (dbrows($cp_result)) {
    $cp_data = dbarray($cp_result);
    add_to_title($locale['global_200'].$cp_data['page_title']);

    opentable($cp_data['page_title']);
    if (checkgroup($cp_data['page_access'])) {
        ob_start();
        eval("?>".stripslashes($cp_data['page_content'])."<?php ");
        $custompage = ob_get_contents();
        ob_end_clean();

        $custompage = preg_split("/<!?--\\s*pagebreak\\s*-->/i", $custompage);
        $pagecount  = count($custompage);
        echo $custompage[$_GET['rowstart']];
    } else {
        ?>
        <div class="alert alert-danger">
            <?= $locale['400'] ?>
            <a href="index.php" onclick="history.back(); return false;">
                <?= $locale['403'] ?>
            </a>
        </div>
        <?php
    }
} else {
    add_to_title($locale['global_200'].$locale['401']);

    opentable($locale['401']);
    ?>
    <div class="alert alert-danger">
        <?= $locale['402'] ?>
    </div>
    <?php
}

if (isset($pagecount) && $pagecount > 1) {
    ?>
    <div class="text-center">
        <?= makepagenav($_GET['rowstart'], 1, $pagecount, 3, FUSION_SELF."?page_id=".$_GET['page_id']."&amp;") ?>
    </div>
    <?php
}

closetable();

if (dbrows($cp_result) && checkgroup($cp_data['page_access'])) {
    if ($cp_data['page_allow_comments'])
        showcomments("C", DB_CUSTOM_PAGES, "page_id", $_GET['page_id'], FUSION_SELF."?page_id=".$_GET['page_id']);

    if ($cp_data['page_allow_ratings'])
        showratings("C", $_GET['page_id'], FUSION_SELF."?page_id=".$_GET['page_id']);
}

require_once THEMES."templates/footer.php";
