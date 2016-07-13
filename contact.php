<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: contact.php
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
include LOCALE.LOCALESET."contact.php";

add_to_title($locale['global_200'].$locale['400']);

$error = "";
if (isset($_POST['sendmessage'])) {
    $mailname = isset($_POST['mailname']) ? substr(stripinput(trim($_POST['mailname'])), 0, 50) : "";
    $email    = isset($_POST['email']) ? substr(stripinput(trim($_POST['email'])), 0, 100) : "";
    $subject  = isset($_POST['subject'])
        ? substr(str_replace(array("\r", "\n", "@"), "", descript(stripslash(trim($_POST['subject'])))), 0, 50)
        : "";
    $message  = isset($_POST['message']) ? descript(stripslash(trim($_POST['message']))) : "";

    if ($mailname == "")
        $error .= " <li>".$locale['420']."</li>\n";

    if ($email == "" || !preg_match("/^[-0-9A-Z_\\.]{1,50}@([-0-9A-Z_\\.]+\\.){1,50}([0-9A-Z]){2,4}$/i", $email))
        $error .= " <li>".$locale['421']."</li>\n";

    if ($subject == "")
        $error .= " <li>".$locale['422']."</li>\n";

    if ($message == "")
        $error .= " <li>".$locale['423']."</li>\n";

    $_CAPTCHA_IS_VALID = false;
    include INCLUDES."captchas/".$settings['captcha']."/captcha_check.php";
    if ($_CAPTCHA_IS_VALID == false)
        $error .= " <li>".$locale['424']."</li>\n";

    if ($error == "") {
        require_once INCLUDES."sendmail_include.php";
        if (!sendemail($settings['siteusername'], $settings['siteemail'], $mailname, $email, $subject, $message)) {
            $error .= " <li>".$locale['425']."</li>\n";
        }
    }

    if ($error == "") {
        opentable($locale['400']);
        echo "<div class='alert alert-success'>\n".$locale['440']."<br /><br />\n".$locale['441']."</div>\n";
        closetable();
    }
}

if ($error != "" || $error == "" && !isset($_POST['sendmessage'])) {
    opentable($locale['400']);

    if ($error != "")
        echo "<div class='alert alert-danger'>\n"
            .$locale['442']
            ."<ul>\n".$error."</ul>\n<br />"
            .$locale['443']."</div>\n";
    ?>

    <p class="alert alert-info"><?= $locale['401'] ?></p>
    <form class="form-horizontal" name='userform' method='post' action='<?= FUSION_SELF ?>'>
        <div class="form-group required">
            <label for="mailname" class="control-label col-md-3"><?= $locale['402'] ?></label>
            <div class="col-md-9">
                <input type="text" name='mailname' id="mailname" class="form-control" />
            </div>
        </div>
        <div class="form-group required">
            <label for="email" class="control-label col-md-3"><?= $locale['403'] ?></label>
            <div class="col-md-9">
                <input type="text" name='email' id="email" class="form-control" />
            </div>
        </div>
        <div class="form-group required">
            <label for="subject" class="control-label col-md-3"><?= $locale['404'] ?></label>
            <div class="col-md-9">
                <input type="text" name='subject' id="subject" class="form-control" />
            </div>
        </div>
        <div class="form-group required">
            <label for="message" class="control-label col-md-3"><?= $locale['405'] ?></label>
            <div class="col-md-9">
                <textarea id="message" name="message" rows="10" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3"><?= $locale['407'] ?></label>
            <div class="col-md-9">
                <?php include INCLUDES."captchas/".$settings['captcha']."/captcha_display.php"; ?>
            </div>
        </div>
        <?php if (!isset($_CAPTCHA_HIDE_INPUT) || (isset($_CAPTCHA_HIDE_INPUT) && !$_CAPTCHA_HIDE_INPUT)) { ?>
            <div class="form-group required">
                <label for='captcha_code' class="control-label col-md-3"><?= $locale['408'] ?></label>
                <div class="col-md-9">
                    <input type='text' id='captcha_code' name='captcha_code' class='form-control' autocomplete='off' />
                </div>
            </div>
        <?php } ?>
        <div class="form-group">
            <div class="col-md-offset-3 col-md-9">
                <input type="submit" name="sendmessage" value="<?= $locale['406'] ?>" class="btn btn-primary" />
            </div>
        </div>
    </form>

    <?php
    closetable();
}

require_once THEMES."templates/footer.php";
