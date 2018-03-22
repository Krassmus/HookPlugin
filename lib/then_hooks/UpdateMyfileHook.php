<?php

class UpdateMyfileHook implements ThenHook {

    static public function getName() {
        return _("Datei ersetzen");
    }

    public function getEditTemplate(Hook $hook, $attributes) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("thens/updatemyfile/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function perform(Hook $hook, $parameters, $multicurl = null) {
        $fileref = new FileRef($hook['then_settings']['fileref_id']);

        $url = HookPlugin::formatTextTemplate($hook['then_settings']['url'], $parameters);
        $tmp_file = $GLOBALS['TMP_PATH']."/"."avatar_".$hook['user_id'].".jpg";
        $success = file_put_contents($tmp_file, file_get_contents($url));
        if (!$success) {
            throw new Exception("Konnte die Datei nicht herunterladen.");
        }
        $file = array(
            'tmp_name' => $tmp_file,
            'name' => $fileref['name'],
            'size' => filesize($tmp_file),
            'type' => get_mime_type($fileref['name'])
        );
        $result = FileManager::updateFileRef(
            $fileref,
            User::findCurrent(),
            $file,
            false,
            (bool) $hook['then_settings']['update_other_references']
        );
        @unlink($tmp_file);
        if (is_a($result, "FileRef")) {
            $fileref->chdate = time();
            $fileref->store();
            return "Datei wurde erfolgreich heruntergeladen und ersetzt.";
        } else {
            throw new Exception(implode("; ", $result));
        }
    }
}