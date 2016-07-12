<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: search.php
| Author: Robert Gaudyn (Wooya)
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
include LOCALE.LOCALESET."search.php";

add_to_title($locale['global_202']);

if (isset($_GET['stext']) && is_array($_GET['stext']))
    redirect(FUSION_SELF);

$_GET['stext']     = isset($_GET['stext']) ? urlencode(stripinput($_GET['stext'])) : "";
$_GET['method']    = isset($_GET['method']) && in_array($_GET['method'], array("OR", "AND")) ? $_GET['method'] : "OR";
$_GET['datelimit'] = isset($_GET['datelimit']) && isnum($_GET['datelimit']) ? $_GET['datelimit'] : 0;
$_GET['fields']    = isset($_GET['fields']) && isnum($_GET['fields']) ? $_GET['fields'] : 2;
$_GET['order']     = isset($_GET['order']) && isnum($_GET['order']) ? $_GET['order'] : 0;
$_GET['chars']     = isset($_GET['chars']) && isnum($_GET['chars']) ? ($_GET['chars'] > 200 ? 200 : $_GET['chars']) : 50;
$_GET['rowstart']  = isset($_GET['rowstart']) && isnum($_GET['rowstart']) ? $_GET['rowstart'] : 0;
$_GET['sort']      = isset($_GET['sort']) && in_array($_GET['sort'], array(
    "datestamp",
    "subject",
    "author"
)) ? $_GET['sort'] : "datestamp";

// will be filled in includes/search/search_***_include_button.php
$radio_button  = array();
$form_elements = array();

$available = array();
$dh        = opendir(INCLUDES."search");
while (false !== ($entry = readdir($dh))) {
    if ($entry != "." && $entry != ".." && preg_match("/include_button.php/i", $entry)) {
        $available[] = str_replace("search_", "", str_replace("_include_button.php", "", $entry));
    }
}
closedir($dh);
$available[] = "all";

if (!isset($_GET['stype']))
    $_GET['stype'] = $settings['default_search'];

$_GET['stype'] = isset($_GET['stype']) && in_array($_GET['stype'], $available) ? $_GET['stype'] : "all";

// fill radio_button and form_elements array
for ($i = 0; $i < count($available) - 1; $i++) {
    include(INCLUDES."search/search_".$available[$i]."_include_button.php");
}
sort($radio_button);

opentable($locale['400']);
?>
    <form id="searchform" name="searchform" method="get" action="<?= FUSION_SELF ?>">
        <div class="row">
            <fieldset class="col-md-6">
                <div class="form-group">
                    <label for="stext"><?= $locale['401'] ?></label>
                    <div class="input-group">
                        <input name="stext" type="text" class="form-control"
                               value="<?= urldecode($_GET['stext']) ?>" id="stext" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit" name="search"><?= $locale['402'] ?></button>
                        </span>
                    </div>
                </div>
            </fieldset>
            <fieldset class="col-md-6">
                <div class="radio">
                    <label>
                        <input type="radio" name="method"
                               value="OR"<?= $_GET['method'] == "OR" ? " checked='checked'" : "" ?> />
                        <?= $locale['403'] ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="method"
                               value="AND"<?= $_GET['method'] == "AND" ? " checked='checked'" : "" ?> />
                        <?= $locale['404'] ?>
                    </label>
                </div>
                <?php if ($_GET['stext'] != "") { ?>
                    <p class="text-right">
                        <button type="button"
                                data-toggle="collapse"
                                data-target="#collapseDetails"
                                aria-expanded="<?= $_GET['stext'] == "" ? "true" : "false" ?>"
                                aria-controls="collapseDetails" class="btn btn-default">
                            <span class="caret"></span>
                        </button>
                    </p>
                <?php } ?>
            </fieldset>
        </div>
        <div class="collapse<?= $_GET['stext'] == "" ? " in" : "" ?>" id="collapseDetails">
            <hr />
            <div class="row">
                <fieldset class="col-md-6">
                    <p><strong><?= $locale['405'] ?></strong></p>
                    <?php foreach ($radio_button as $key => $value) echo "<div class='radio'>".$value."</div>\n"; ?>
                    <div class="radio">
                        <label>
                            <input type='radio' name='stype'
                                   value='all'<?= $_GET['stype'] == "all" ? " checked='checked'" : "" ?>
                                   onclick="display(this.value)" />
                            <?= $locale['407'] ?>
                        </label>
                    </div>
                </fieldset>
                <fieldset class="col-md-6">
                    <p><strong><?= $locale['406'] ?></strong></p>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="datelimit" class="col-sm-3 control-label"><?= $locale['420'] ?></label>
                            <div class="col-sm-9">
                                <select id="datelimit" name="datelimit"
                                    <?= $_GET['stype'] != "all"
                                        ? (in_array("datelimit", $form_elements[$_GET['stype']]['disabled'])
                                            ? " disabled='disabled'"
                                            : "")
                                        : "" ?>
                                        class="form-control">
                                    <option value="0"<?= $_GET['datelimit'] == 0 ? " selected='selected'" : "" ?>>
                                        <?= $locale['421'] ?>
                                    </option>
                                    <option value="86400"<?= $_GET['datelimit'] == 86400 ? " selected='selected'" : "" ?>>
                                        <?= $locale['422'] ?>
                                    </option>
                                    <option value="604800"<?= $_GET['datelimit'] == 604800 ? " selected='selected'" : "" ?>>
                                        <?= $locale['423'] ?>
                                    </option>
                                    <option value="1209600"<?= $_GET['datelimit'] == 1209600 ? " selected='selected'" : "" ?>>
                                        <?= $locale['424'] ?>
                                    </option>
                                    <option value="2419200"<?= $_GET['datelimit'] == 2419200 ? " selected='selected'" : "" ?>>
                                        <?= $locale['425'] ?>
                                    </option>
                                    <option value="7257600"<?= $_GET['datelimit'] == 7257600 ? " selected='selected'" : "" ?>>
                                        <?= $locale['426'] ?>
                                    </option>
                                    <option value="14515200"<?= $_GET['datelimit'] == 14515200 ? " selected='selected'" : "" ?>>
                                        <?= $locale['427'] ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3">
                                <div class="radio">
                                    <label>
                                        <input type="radio"
                                               id="fields1"
                                               name="fields"
                                               value="2"<?= ($_GET['fields'] == 2
                                            ? " checked='checked'"
                                            : "")
                                        .($_GET['stype'] != "all"
                                            ? (in_array("fields1", $form_elements[$_GET['stype']]['disabled'])
                                                ? " disabled='disabled'"
                                                : "")
                                            : "") ?> />
                                        <?= $locale['430'] ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"
                                               id="fields2"
                                               name="fields"
                                               value="1"<?= ($_GET['fields'] == 1
                                            ? " checked='checked'"
                                            : "")
                                        .($_GET['stype'] != "all"
                                            ? (in_array("fields2", $form_elements[$_GET['stype']]['disabled'])
                                                ? " disabled='disabled'"
                                                : "")
                                            : "") ?> />
                                        <?= $locale['431'] ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"
                                               id="fields3"
                                               name="fields"
                                               value="0"<?= ($_GET['fields'] == 0
                                            ? " checked='checked'"
                                            : "")
                                        .($_GET['stype'] != "all"
                                            ? (in_array("fields3", $form_elements[$_GET['stype']]['disabled'])
                                                ? " disabled='disabled'"
                                                : "")
                                            : "") ?> />
                                        <?= $locale['432'] ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sort" class="col-sm-3 control-label"><?= $locale['440'] ?></label>
                            <div class="col-sm-9">
                                <select id="sort" name="sort"
                                    <?= $_GET['stype'] != "all"
                                        ? (in_array("sort", $form_elements[$_GET['stype']]['disabled'])
                                            ? " disabled='disabled'"
                                            : "")
                                        : "" ?>
                                        class="form-control">
                                    <option value='datestamp' <?= ($_GET['sort'] == "datestamp" ? " selected='selected'" : "") ?>>
                                        <?= $locale['441'] ?>
                                    </option>
                                    <option value='subject' <?= $_GET['sort'] == "subject" ? " selected='selected'" : "" ?>>
                                        <?= $locale['442'] ?>
                                    </option>
                                    <option value='author' <?= $_GET['sort'] == "author" ? " selected='selected'" : "" ?>>
                                        <?= $locale['443'] ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3">
                                <div class="radio">
                                    <label>
                                        <input type="radio"
                                               id="order1"
                                               name="order"
                                               value="0"<?= ($_GET['order'] == 0
                                            ? " checked='checked'"
                                            : "")
                                        .($_GET['stype'] != "all"
                                            ? (in_array("order1", $form_elements[$_GET['stype']]['disabled'])
                                                ? " disabled='disabled'"
                                                : "")
                                            : "") ?> />
                                        <?= $locale['450'] ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"
                                               id="order2"
                                               name="order"
                                               value="1"<?= ($_GET['order'] == 1
                                            ? " checked='checked'"
                                            : "")
                                        .($_GET['stype'] != "all"
                                            ? (in_array("order2", $form_elements[$_GET['stype']]['disabled'])
                                                ? " disabled='disabled'"
                                                : "")
                                            : "") ?> />
                                        <?= $locale['451'] ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="chars" class="col-sm-3 control-label"><?= $locale['460'] ?></label>
                            <div class="col-sm-9">
                                <select id="chars" name="chars"
                                    <?= $_GET['stype'] != "all"
                                        ? (in_array("chars", $form_elements[$_GET['stype']]['disabled'])
                                            ? " disabled='disabled'"
                                            : "")
                                        : "" ?>
                                        class="form-control">
                                    <option value='50'<?= ($_GET['chars'] == 50 ? " selected='selected'" : "") ?>>
                                        50
                                    </option>
                                    <option value='100'<?= ($_GET['chars'] == 100 ? " selected='selected'" : "") ?>>
                                        100
                                    </option>
                                    <option value='150'<?= ($_GET['chars'] == 150 ? " selected='selected'" : "") ?>>
                                        150
                                    </option>
                                    <option value='200'<?= ($_GET['chars'] == 200 ? " selected='selected'" : "") ?>>
                                        200
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
<?php
closetable();

$element_rules = "";
// generate form element rules for jQuery
foreach ($form_elements as $form_element => $data) {
    $element_rules .= "case '".$form_element."'\n";

    $element_rules .= "\tbreak;\n";
}

ob_start();
?>
    <script type='text/javascript'>
        /*<![CDATA[*/
        function display(elemId) {
            switch(elemId) {
            <?php foreach ($form_elements as $form_element => $data) {
                echo "case '$form_element':\n";
                foreach ($data as $option => $elements) {
                    foreach ($elements as $element => $value) {
                        switch ($option) {
                            case "enabled":
                                echo "jQuery('#".$value."').prop('disabled', false);\n";
                                break;
                            case "disabled":
                                echo "jQuery('#".$value."').prop('disabled', true);\n";
                                break;
                            case "display":
                                echo "jQuery('#".$value."').show();\n";
                                break;
                            case "nodisplay":
                                echo "jQuery('#".$value."').hide();\n";
                                break;
                        }
                    }
                }
                echo "break;\n";
            } ?>
                case 'all':
                    jQuery('#datelimit').prop("disabled", false);
                    jQuery('#fields1').prop("disabled", false);
                    jQuery('#fields2').prop("disabled", false);
                    jQuery('#fields3').prop("disabled", false);
                    jQuery('#sort').prop("disabled", false);
                    jQuery('#order1').prop("disabled", false);
                    jQuery('#order2').prop("disabled", false);
                    jQuery('#chars').prop("disabled", false);
                    break;
            }
        }
        /*]]>*/
    </script>
<?php
$search_js = ob_get_contents();
ob_end_clean();
add_to_footer($search_js);

$composevars = "method=".$_GET['method']
    ."&amp;datelimit=".$_GET['datelimit']
    ."&amp;fields=".$_GET['fields']
    ."&amp;sort=".$_GET['sort']
    ."&amp;order=".$_GET['order']
    ."&amp;chars=".$_GET['chars']
    ."&amp;";

$memory_limit = str_replace("m", "", strtolower(ini_get("memory_limit"))) * 1024 * 1024;
$memory_limit = !isnum($memory_limit)
    ? 8 * 1024 * 1024
    : $memory_limit < 8 * 1024 * 1024
        ? 8 * 1024 * 1024
        : $memory_limit;
$memory_limit = $memory_limit - ceil($memory_limit / 4);

$global_string_count = 0;
$site_search_count   = 0;
$search_result_array = array();
$navigation_result   = "";
$items_count         = "";

$_GET['stext'] = urldecode($_GET['stext']);
if ($_GET['stext'] != "" && strlen($_GET['stext']) >= 3) {
    add_to_title($locale['global_201'].$locale['408']);
    opentable($locale['408']);

    $fswords   = explode(" ", $_GET['stext']);
    $swords    = array();
    $iwords    = array();
    $c_fswords = count($fswords);
    for ($i = 0; $i < $c_fswords; $i++) {
        if (strlen($fswords[$i]) >= 3) {
            $swords[] = $fswords[$i];
        } else {
            $iwords[] = $fswords[$i];
        }
    }
    unset($fswords);

    $c_swords = count($swords);
    if ($c_swords == 0)
        redirect(FUSION_SELF);

    // warn if words < 3 are ignored
    $c_iwords = count($iwords);
    if ($c_iwords) {
        $txt = "";
        for ($i = 0; $i < $c_iwords; $i++) {
            $txt .= $iwords[$i].($i < $c_iwords - 1 ? ", " : "");
        }
        echo "<div class='alert alert-warning'>".sprintf($locale['502'], $txt)."</div><br />";
    }

    if ($_GET['stype'] == "all") {
        $dh = opendir(INCLUDES."search");
        while (false !== ($entry = readdir($dh))) {
            if ($entry != "." && $entry != ".." && preg_match("/include.php/i", $entry)) {
                include(INCLUDES."search/".$entry);
            }
        }
        closedir($dh);
    } else {
        include INCLUDES."search/search_".$_GET['stype']."_include.php";
    }

    if ($_GET['stype'] == "all") {
        $navigation_result = search_navigation(0);
        echo $items_count
            ."<hr /><p>"
            .THEME_BULLET." "
            .(($site_search_count > 100 || search_globalarray(""))
                ? sprintf($locale['530'], $site_search_count)
                : $site_search_count." ".$locale['510'])
            ."</p><hr />";
    } else {
        echo $items_count."<hr />";
        echo(($site_search_count > 100 || search_globalarray(""))
            ? "<div class='alert alert-info'>".sprintf($locale['530'], $site_search_count)."</div>\n"
            : "");
    }

    $c_search_result_array = count($search_result_array);
    if ($_GET['stype'] == "all") {
        $from = $_GET['rowstart'];
        $to   = ($c_search_result_array - ($_GET['rowstart'] + 10)) <= 0
            ? $c_search_result_array : $_GET['rowstart'] + 10;
    } else {
        $from = 0;
        $to   = $c_search_result_array < 10 ? $c_search_result_array : 10;
    }

    ?>
    <div class="search_result">
        <?php for ($i = $from; $i < $to; $i++) echo $search_result_array[$i]; ?>
    </div>
    <?php
    echo $navigation_result;
    closetable();

    $higlight = "";
    $i        = 1;
    foreach ($swords as $hlight) {
        $higlight .= "'".$hlight."'";
        $higlight .= ($i < $c_swords ? "," : "");
        $i++;
    }

    add_to_footer("<script type='text/javascript' src='".INCLUDES."jquery/jquery.highlight.js'></script>");
    ob_start();
    ?>
    <script type="text/javascript">
        /*<![CDATA[*/
        jQuery(document).ready(function() {
            jQuery('.search_result').highlight([<?= $higlight ?>], {wordsOnly : false});
            jQuery('.highlight').wrap('<mark>');
        });
        /*]]>*/
    </script>
    <?php
    $highlight_js = ob_get_contents();
    ob_end_clean();
    add_to_footer($highlight_js);
} else {
    add_to_title($locale['global_201'].$locale['408']);
    opentable($locale['408']);
    echo "<div class='alert alert-info'>\n".$locale['501']."\n</div>\n";
    closetable();
}

require_once THEMES."templates/footer.php";

function search_striphtmlbbcodes($text)
{
    $text = preg_replace("[\\[(.*?)\\]]", "", $text);
    $text = preg_replace("<\\<(.*?)\\>>", "", $text);

    return $text;
}

function search_textfrag($text)
{
    if ($_GET['chars'] != 0) {
        $text = nl2br(stripslashes(substr($text, 0, $_GET['chars'])."..."));
    } else {
        $text = nl2br(stripslashes($text));
    }

    return $text;
}

function search_stringscount($text)
{
    global $swords;

    $count    = 0;
    $c_swords = count($swords); //sizeof($swords)
    for ($i = 0; $i < $c_swords; $i++) {
        $count += substr_count(strtolower($text), strtolower($swords[$i]));
    }

    return $count;
}

function search_querylike($field)
{
    global $swords;

    $querylike = "";
    $c_swords  = count($swords); //sizeof($swords)
    for ($i = 0; $i < $c_swords; $i++) {
        $querylike .= $field." LIKE '%".$swords[$i]."%'".($i < $c_swords - 1 ? " ".$_GET['method']." " : "");
    }

    return $querylike;
}

function search_fieldsvar()
{
    $fieldsvar = "(";
    $numargs   = func_num_args();
    for ($i = 0; $i < $numargs; $i++) {
        $fieldsvar .= func_get_arg($i).($i < $numargs - 1 ? " || " : "");
    }
    $fieldsvar .= ")";

    return $fieldsvar;
}

function search_globalarray($search_result)
{
    global $search_result_array, $global_string_count, $memory_limit;

    $global_string_count += strlen($search_result);
    if ($memory_limit > $global_string_count) {
        $search_result_array[] = $search_result;
        $memory_exhaused       = false;
    } else {
        $memory_exhaused = true;
    }

    return $memory_exhaused;
}

function search_navigation($rows)
{
    global $site_search_count, $composevars;

    $site_search_count += $rows;
    $navigation_result = "<div class='text-center'>\n"
        .makepagenav(
            $_GET['rowstart'],
            10,
            ($site_search_count > 100 || search_globalarray("") ? 100 : $site_search_count),
            3,
            FUSION_SELF."?stype=".$_GET['stype']."&amp;stext=".urlencode($_GET['stext'])."&amp;".$composevars
        )."\n</div>\n";

    return $navigation_result;
}