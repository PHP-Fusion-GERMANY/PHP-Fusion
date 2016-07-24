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
            // fields
            $submit_info['news_subject'] = stripinput($_POST['news_subject']);
            $submit_info['news_cat']     = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
            $submit_info['news_snippet'] = nl2br(parseubb(stripinput($_POST['news_snippet'])));
            $submit_info['news_body']    = nl2br(parseubb(stripinput($_POST['news_body'])));
            $result                      = dbquery("INSERT INTO ".DB_SUBMISSIONS." 
                (submit_type, submit_user, submit_datestamp, submit_criteria) 
                    VALUES
                ('n', '".$userdata['user_id']."', '".time()."', '".addslashes(serialize($submit_info))."')
            ");

            add_to_title($locale['global_200'].$locale['450']);
            opentable($locale['450']);
            ?>
            <div class="alert alert-success">
                <?= $locale['460'] ?>
            </div>
            <ul class="list-unstyled">
                <li><a href='submit.php?stype=n'><?= $locale['461'] ?></a></li>
                <li><a href='index.php'><?= $locale['412'] ?></a></li>
            </ul>
            <?php
            closetable();
        }
    } else {
        $news_subject = "";
        $news_cat     = "0";
        $news_snippet = "";
        $news_body    = "";

        if (isset($_POST['preview_news'])) {
            // fields
            $news_subject = stripinput($_POST['news_subject']);
            $news_cat     = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
            $news_snippet = stripinput($_POST['news_snippet']);
            $news_body    = stripinput($_POST['news_body']);

            opentable($news_subject);
            echo $locale['478']." ".nl2br(parseubb($news_snippet))."<br /><br />";
            echo $locale['472']." ".nl2br(parseubb($news_body));
            closetable();
        }

        $cat_list = "";
        $s        = "";
        $result2  = dbquery("SELECT news_cat_id, news_cat_name FROM ".DB_NEWS_CATS." ORDER BY news_cat_name");
        if (dbrows($result2)) {
            while ($data2 = dbarray($result2)) {
                if (isset($_POST['preview_news'])) {
                    $s = ($news_cat == $data2['news_cat_id'] ? " selected" : "");
                }
                $cat_list .= "<option value='".$data2['news_cat_id']."'".$s.">".$data2['news_cat_name']."</option>\n";
            }
        }

        add_to_title($locale['global_200'].$locale['450']);
        opentable($locale['450']);
        ?>
        <div id="submission-guidelines" class="alert alert-info">
            <?= $locale['470'] ?>
        </div>

        <form class="form-horizontal" name="submit-form" method="post" action="<?= FUSION_SELF."?stype=n" ?>"
              onsubmit="return validateNews(this);">
            <div class="form-group required">
                <label for="news_subject" class="col-sm-2 control-label"><?= $locale['471'] ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="news_subject" name="news_subject"
                           value="<?= isset($news_subject) ? $news_subject : "" ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="news_cat" class="col-sm-2 control-label"><?= $locale['476'] ?></label>
                <div class="col-sm-10">
                    <select id="news_cat" name="news_cat" class="form-control">
                        <option value="0"><?= $locale['477'] ?></option>
                        <?= $cat_list ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="news_snippet" class="col-sm-2 control-label"><?= $locale['478'] ?></label>
                <div class="col-sm-10">
                    <textarea id="news_snippet" rows="8" class="form-control"
                              name="news_snippet"><?= isset($news_snippet) ? $news_snippet : "" ?></textarea>
                    <?= display_bbcodes("100%", "news_snippet", "submit-form", "b|i|u|center|small|url|mail|img") ?>
                </div>
            </div>
            <div class="form-group required">
                <label for="news_body" class="col-sm-2 control-label"><?= $locale['472'] ?></label>
                <div class="col-sm-10">
                    <textarea id="news_body" rows="8" class="form-control"
                              name="news_body"><?= isset($news_body) ? $news_body : "" ?></textarea>
                    <?= display_bbcodes("100%", "news_body", "submit-form", "b|i|u|center|small|url|mail|img") ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" name="preview_news" value="<?= $locale['474'] ?>" class="btn btn-default" />
                    <input type="submit" name="submit_news" value="<?= $locale['475'] ?>" class="btn btn-primary" />
                </div>
            </div>
        </form>
        <?php
        closetable();
    }
} elseif ($_GET['stype'] == "a") {
    if (isset($_POST['submit_article'])) {
        if ($_POST['article_subject'] != "" && $_POST['article_body'] != "") {
            $submit_info['article_cat']     = isnum($_POST['article_cat']) ? $_POST['article_cat'] : "0";
            $submit_info['article_subject'] = stripinput($_POST['article_subject']);
            $submit_info['article_snippet'] = nl2br(parseubb(stripinput($_POST['article_snippet'])));
            $submit_info['article_body']    = nl2br(parseubb(stripinput($_POST['article_body'])));
            $result                         = dbquery("INSERT INTO ".DB_SUBMISSIONS." 
                (submit_type, submit_user, submit_datestamp, submit_criteria) 
            VALUES 
                ('a', '".$userdata['user_id']."', '".time()."', '".addslashes(serialize($submit_info))."')
            ");

            add_to_title($locale['global_200'].$locale['500']);
            opentable($locale['500']);
            ?>
            <div class="alert alert-success">
                <?= $locale['510'] ?>
            </div>
            <ul class="list-unstyled">
                <li><a href='submit.php?stype=n'><?= $locale['511'] ?></a></li>
                <li><a href='index.php'><?= $locale['412'] ?></a></li>
            </ul>
            <?php
            closetable();
        }
    } else {
        $article_cat     = "0";
        $article_subject = "";
        $article_snippet = "";
        $article_body    = "";

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

        $cat_list = "";
        $s        = "";
        add_to_title($locale['global_200'].$locale['500']);
        opentable($locale['500']);
        $result = dbquery("SELECT article_cat_id, article_cat_name 
          FROM ".DB_ARTICLE_CATS." WHERE ".groupaccess("article_cat_access")." ORDER BY article_cat_name");

        if (dbrows($result)) {
            while ($data = dbarray($result)) {
                if (isset($_POST['preview_article'])) {
                    $s = $article_cat == $data['article_cat_id'] ? " selected" : "";
                }
                $cat_list .= "<option value='".$data['article_cat_id']."'".$s.">".$data['article_cat_name']."</option>\n";
            }
            ?>
            <div id="submission-guidelines" class="alert alert-info">
                <?= $locale['520'] ?>
            </div>

            <form class="form-horizontal" name="submit-form" method="post" action="<?= FUSION_SELF."?stype=a" ?>"
                  onsubmit="return validateArticle(this);">
                <div class="form-group">
                    <label for="article_cat" class="col-sm-2 control-label"><?= $locale['521'] ?></label>
                    <div class="col-sm-10">
                        <select id="article_cat" name="article_cat" class="form-control">
                            <?= $cat_list ?>
                        </select>
                    </div>
                </div>
                <div class="form-group required">
                    <label for="article_subject" class="col-sm-2 control-label"><?= $locale['522'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="article_subject" name="article_subject"
                               value="<?= isset($article_subject) ? $article_subject : "" ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="article_snippet" class="col-sm-2 control-label"><?= $locale['523'] ?></label>
                    <div class="col-sm-10">
                        <textarea id="article_snippet" rows="8" class="form-control"
                                  name="article_snippet"><?= isset($article_snippet) ? $article_snippet : "" ?></textarea>
                        <?= display_bbcodes("100%", "article_snippet", "submit-form", "b|i|u|center|small|url|mail|img") ?>
                    </div>
                </div>
                <div class="form-group required">
                    <label for="article_body" class="col-sm-2 control-label"><?= $locale['524'] ?></label>
                    <div class="col-sm-10">
                        <textarea id="article_body" rows="8" class="form-control"
                                  name="article_body"><?= isset($article_body) ? $article_body : "" ?></textarea>
                        <?= display_bbcodes("100%", "article_body", "submit-form", "b|i|u|center|small|url|mail|img") ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="preview_article"
                               value="<?= $locale['526'] ?>" class="btn btn-default" />
                        <input type="submit" name="submit_article"
                               value="<?= $locale['527'] ?>" class="btn btn-primary" />
                    </div>
                </div>
            </form>
            <?php
        } else {
            ?>
            <div class="alert alert-info"><?= $locale['551'] ?></div>
            <?php
        }
        closetable();
    }
} else {
    redirect("index.php");
}

ob_start();
?>
<script type="text/javascript">
    /*<![CDATA[*/
    function validateNews(form) {
        if(form.news_subject.value == "" || form.news_body.value == "") {
            alert("<?= $locale['550']  ?>");
            return false;
        }

        return true;
    }

    function validateArticle(form) {
        if(form.article_subject.value == "" || form.article_body.value == "") {
            alert("<?= $locale['550']  ?>");
            return false;
        }

        return true;
    }
    /*]]>*/
</script>
<?php
$content = ob_get_contents();
ob_end_clean();

add_to_footer($content);
unset($content);

require_once THEMES."templates/footer.php";
