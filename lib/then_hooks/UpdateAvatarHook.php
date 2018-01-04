<?php

class UpdateAvatarHook implements ThenHook {

    static public function getName() {
        return _("Profilbild aktualisieren");
    }

    public function getEditTemplate(Hook $hook, $attributes) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("thens/updateavatar/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function perform(Hook $hook, $parameters) {
        $url = HookPlugin::formatTextTemplate($hook['then_settings']['avatar_url'], $parameters);
        $tmp_avatar = $GLOBALS['TMP_PATH']."/"."avatar_".$hook['user_id'].".jpg";
        $success = file_put_contents($tmp_avatar, file_get_contents($url));
        if ($success) {
            Avatar::getAvatar($hook['user_id'])->createFrom($tmp_avatar);
            @unlink($tmp_avatar);
        }
        return "Avatar wurde heruntergeladen und aktualisiert.";
    }
}