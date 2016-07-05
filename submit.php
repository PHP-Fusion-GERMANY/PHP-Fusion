<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: submit.php
| Author: Nick Jones (Digitanium)
| Co-Author: Daywalker
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
include_once INCLUDES."bbcode_include.php";
include LOCALE.LOCALESET."submit.php";

if (!iMEMBER) {
    redirect("index.php");
}

if (!isset($_GET['stype']) || !preg_check("/^[a-z]$/", $_GET['stype'])) {
    redirect("index.php");
}

$submit_info = array();

if ($_GET['stype'] == "n") {
    if (isset($_POST['submit_news'])) {
        if ($_POST['news_subject'] != "" && $_POST['news_body'] != "") {
            $submit_info['news_subject'] = stripinput($_POST['news_subject']);
            $submit_info['news_cat']     = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
            $submit_info['news_snippet'] = nl2br(parseubb(stripinput($_POST['news_snippet'])));
            $submit_info['news_body']    = nl2br(parseubb(stripinput($_POST['news_body'])));
            $result                      = dbquery("INSERT INTO ".DB_SUBMISSIONS." (submit_type, submit_user, submit_datestamp, submit_criteria) VALUES('n', '".$userdata['user_id']."', '".time()."', '".addslashes(serialize($submit_info))."')");
            add_to_title($locale['global_200'].$locale['450']);
            opentable($locale['450']);
            echo "<div style='text-align:center'><br />\n".$locale['460']."<br /><br />\n";
            echo "<a href='submit.php?stype=n'>".$locale['461']."</a><br /><br />\n";
            echo "<a href='index.php'>".$locale['412']."</a><br /><br />\n</div>\n";
            closetable();
        }
    } else {
        if (isset($_POST['preview_news'])) {
            $news_subject = stripinput($_POST['news_subject']);
            $news_cat     = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
            $news_snippet = stripinput($_POST['news_snippet']);
            $news_body    = stripinput($_POST['news_body']);
            opentable($news_subject);
            echo $locale['478']." ".nl2br(parseubb($news_snippet))."<br /><br />";
            echo $locale['472']." ".nl2br(parseubb($news_body));
            closetable();
        }
        if (!isset($_POST['preview_news'])) {
            $news_subject = "";
            $news_cat     = "0";
            $news_snippet = "";
            $news_body    = "";
        }
        $cat_list = "";
        $sel      = "";
        $result2  = dbquery("SELECT news_cat_id, news_cat_name FROM ".DB_NEWS_CATS." ORDER BY news_cat_name");
        if (dbrows($result2)) {
            while ($data2 = dbarray($result2)) {
                if (isset($_POST['preview_news'])) {
                    $sel = ($news_cat == $data2['news_cat_id'] ? " selected" : "");
                }
                $cat_list .= "<option value='".$data2['news_cat_id']."'".$sel.">".$data2['news_cat_name']."</option>\n";
            }
        }
        add_to_title($locale['global_200'].$locale['450']);
        opentable($locale['450']);
        ?>
        <div id="submission-guidelines" class="alert alert-info">
            <?= $locale['470'] ?>
        </div>
        <form class="form-horizontal" name="submit-form" method="post" action="<?= FUSION_SELF."?stype=n" ?>" onsubmit="return validateNews(this);">
            <div class="form-group">
                <label for="news_subject" class="col-sm-2 control-label"><?= $locale['471'] ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="news_subject" value="<?= isset($news_subject) ? $news_subject : "" ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="news_cats" class="col-sm-2 control-label"><?= $locale['476'] ?></label>
                <div class="col-sm-10">
                    <select id="news_cats" name="news_cats" class="form-control">
                        <option value="0"><?= $locale['477'] ?></option>
                        <?= $cat_list ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="news_snippet" class="col-sm-2 control-label"><?= $locale['478'] ?></label>
                <div class="col-sm-10">
                    <textarea id="news_snippet" rows="8" class="form-control" name="news_snippet"><?= isset($news_snippet) ? $news_snippet : "" ?></textarea>
                    <?= display_bbcodes("100%", "news_snippet", "submit_form", "b|i|u|center|small|url|mail|img|color") ?>
                </div>
            </div>
            <div class="form-group">
                <label for="news_body" class="col-sm-2 control-label"><?= $locale['478'] ?></label>
                <div class="col-sm-10">
                    <textarea id="news_body" rows="8" class="form-control" name="news_body"><?= isset($news_body) ? $news_body : "" ?></textarea>
                    <?= display_bbcodes("100%", "news_body", "submit_form", "b|i|u|center|small|url|mail|img|color") ?>
                </div>
            </div>
        </form>
        <?php
        echo "<div class='submission-guidelines'>".$locale['470']."</div>\n";
        echo "<form name='submit_form' method='post' action='".FUSION_SELF."?stype=n' onsubmit='return validateNews(this);'>\n";
        echo "<table cellpadding='0' cellspacing='0' class='center'>\n<tr>\n";
//        echo "<td class='tbl'>".$locale['471']."<span style='color:#ff0000'>*</span></td>\n";
//        echo "<td class='tbl'><input type='text' name='news_subject' value='$news_subject' maxlength='64' class='textbox' style='width:300px;' /></td>\n";
//        echo "</tr>\n<tr>\n";
//        echo "<td width='100' class='tbl'>".$locale['476']."</td>\n";
//        echo "<td width='80%' class='tbl'><select name='news_cat' class='textbox'>\n<option value='0'>".$locale['477']."</option>\n".$cat_list."</select></td>\n";
//        echo "</tr>\n<tr>\n";
//        echo "<td valign='top' class='tbl'>".$locale['478']."</td>\n";
//        echo "<td class='tbl'><textarea name='news_snippet' cols='60' rows='8' class='textbox dummy_classname' style='width:300px;'>$news_snippet</textarea></td>\n";
//        echo "</tr>\n";
//        echo "<tr>\n<td class='tbl'></td>\n<td class='tbl'>\n";
//        echo display_bbcodes("100%", "news_snippet", "submit_form", "b|i|u|center|small|url|mail|img|color");
//        echo "</td>\n</tr>\n";
//        echo "<tr>\n";
//        echo "<td valign='top' class='tbl'>".$locale['472']."<span style='color:#ff0000'>*</span></td>\n";
//        echo "<td class='tbl'><textarea name='news_body' cols='60' rows='8' class='textbox dummy_classname' style='width:300px;'>$news_body</textarea></td>\n";
//        echo "</tr>\n";
//        echo "<tr>\n<td class='tbl'></td>\n<td class='tbl'>\n";
//        echo display_bbcodes("100%", "news_body", "submit_form", "b|i|u|center|small|url|mail|img|color");
//        echo "</td>\n</tr>\n";
//        echo "<tr>\n";
        echo "<td align='center' colspan='2' class='tbl'><br /><br />\n";
        echo "<input type='submit' name='preview_news' value='".$locale['474']."' class='button' />\n";
        echo "<input type='submit' name='submit_news' value='".$locale['475']."' class='button' />\n</td>\n";
        echo "</tr>\n</table>\n</form>\n";
        closetable();
    }
} elseif ($_GET['stype'] == "a") {
    if (isset($_POST['submit_article'])) {
        if ($_POST['article_subject'] != "" && $_POST['article_body'] != "") {
            $submit_info['article_cat']     = isnum($_POST['article_cat']) ? $_POST['article_cat'] : "0";
            $submit_info['article_subject'] = stripinput($_POST['article_subject']);
            $submit_info['article_snippet'] = nl2br(parseubb(stripinput($_POST['article_snippet'])));
            $submit_info['article_body']    = nl2br(parseubb(stripinput($_POST['article_body'])));
            $result                         = dbquery("INSERT INTO ".DB_SUBMISSIONS." (submit_type, submit_user, submit_datestamp, submit_criteria) VALUES ('a', '".$userdata['user_id']."', '".time()."', '".addslashes(serialize($submit_info))."')");
            add_to_title($locale['global_200'].$locale['500']);
            opentable($locale['500']);
            echo "<div style='text-align:center'><br />\n".$locale['510']."<br /><br />\n";
            echo "<a href='submit.php?stype=a'>".$locale['511']."</a><br /><br />\n";
            echo "<a href='index.php'>".$locale['412']."</a><br /><br />\n</div>\n";
            closetable();
        }
    } else {
        if (isset($_POST['preview_article'])) {
            $article_cat     = isnum($_POST['article_cat']) ? $_POST['article_cat'] : "0";
            $article_subject = stripinput($_POST['article_subject']);
            $article_snippet = stripinput($_POST['article_snippet']);
            $article_body    = stripinput($_POST['article_body']);
            opentable($article_subject);
            echo $locale['523']." ".nl2br(parseubb($article_snippet))."<br /><br />";
            echo $locale['524']." ".nl2br(parseubb($article_body));
            closetable();
        }
        if (!isset($_POST['preview_article'])) {
            $article_cat     = "0";
            $article_subject = "";
            $article_snippet = "";
            $article_body    = "";
        }
        $cat_list = "";
        $sel      = "";
        add_to_title($locale['global_200'].$locale['500']);
        opentable($locale['500']);
        $result = dbquery("SELECT article_cat_id, article_cat_name FROM ".DB_ARTICLE_CATS." WHERE ".groupaccess("article_cat_access")." ORDER BY article_cat_name");
        if (dbrows($result)) {
            while ($data = dbarray($result)) {
                if (isset($_POST['preview_article'])) {
                    $sel = $article_cat == $data['article_cat_id'] ? " selected" : "";
                }
                $cat_list .= "<option value='".$data['article_cat_id']."'".$sel.">".$data['article_cat_name']."</option>\n";
            }
            echo "<div class='submission-guidelines'>".$locale['520']."</div>\n";
            echo "<form name='submit_form' method='post' action='".FUSION_SELF."?stype=a' onsubmit='return validateArticle(this);'>\n";
            echo "<table cellpadding='0' cellspacing='0' class='center'>\n<tr>\n";
            echo "<td width='100' class='tbl'>".$locale['521']."</td>\n";
            echo "<td class='tbl'><select name='article_cat' class='textbox'>\n$cat_list</select></td>\n";
            echo "</tr>\n<tr>\n";
            echo "<td class='tbl'>".$locale['522']."<span style='color:#ff0000'>*</span></td>\n";
            echo "<td class='tbl'><input type='text' name='article_subject' value='$article_subject' maxlength='64' class='textbox dummy_classname' style='width:300px;' /></td>\n";
            echo "</tr>\n<tr>\n";
            echo "<td valign='top' class='tbl'>".$locale['523']."<span style='color:#ff0000'>*</span></td>\n";
            echo "<td class='tbl'><textarea name='article_snippet' cols='60' rows='3' class='textbox dummy_classname' style='width:300px;'>$article_snippet</textarea></td>\n";
            echo "</tr>\n";
            echo "<tr>\n<td class='tbl'></td>\n<td class='tbl'>\n";
            echo display_bbcodes("100%", "article_snippet", "submit_form", "b|i|u|center|small|url|mail|img|color");
            echo "</td>\n</tr>\n";
            echo "<tr>\n";
            echo "<td valign='top' class='tbl'>".$locale['524']."<span style='color:#ff0000'>*</span></td>\n";
            echo "<td class='tbl'><textarea name='article_body' cols='60' rows='8' class='textbox dummy_classname' style='width:300px;'>$article_body</textarea></td>\n";
            echo "</tr>\n";
            echo "<tr>\n<td class='tbl'></td>\n<td class='tbl'>\n";
            echo display_bbcodes("100%", "article_body", "submit_form", "b|i|u|center|small|url|mail|img|color");
            echo "</td>\n</tr>\n";
            echo "<tr>\n";
            echo "<td align='center' colspan='2' class='tbl'><br /><br />\n";
            echo "<input type='submit' name='preview_article' value='".$locale['526']."' class='button' />\n";
            echo "<input type='submit' name='submit_article' value='".$locale['527']."' class='button' />\n</td>\n";
            echo "</tr>\n</table>\n</form>\n";
        } else {
            echo "<div style='text-align:center'><br />\n".$locale['551']."<br /><br />\n</div>\n";
        }
        closetable();
    }
} else {
    redirect("index.php");
}

$submit_js = '<script type="text/javascript">';
$submit_js .= "/*<![CDATA[*/";
/************ news ******/
$submit_js .= "function validateNews(frm){";
$submit_js .= 'if(frm.news_subject.value=="" || frm.news_body.value==""){';
$submit_js .= 'alert("'.$locale['550'].'"); return false;';
$submit_js .= "}";
$submit_js .= "}";
/************ articles **/
$submit_js .= "function validateArticle(frm){";
$submit_js .= 'if(frm.article_subject.value=="" || frm.article_snippet.value=="" || frm.article_body.value==""){';
$submit_js .= 'alert("'.$locale['550'].'"); return false;';
$submit_js .= "}";
$submit_js .= "}";
/************ -- end -- */
$submit_js .= "/*]]>*/";
$submit_js .= "</script>";

add_to_footer($submit_js);
unset($submit_js);

require_once THEMES."templates/footer.php";
?>