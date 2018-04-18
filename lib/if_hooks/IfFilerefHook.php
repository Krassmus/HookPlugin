<?php

class IfFilerefHook implements IfHook
{

    static public function getName()
    {
        return _("Neue Dateien");
    }

    public function getParameters(Hook $hook)
    {
        return array("id", "user_id", "name", "von_name");
    }

    public function listenToNotificationEvents()
    {
        return array("FileRefDidCreate", "FileRefDidStore", "FoldertypeShowsUp");
    }

    public function findHooksByObject($object)
    {
        if (is_a($object, "FileRef")) {
            switch ($object->folder['range_type']) {
                case "course":
                    $statement = DBManager::get()->prepare("
                        SELECT hooks.*
                        FROM hooks 
                            INNER JOIN seminar_user ON (seminar_user.user_id = hooks.user_id)
                        WHERE seminar_user.Seminar_id = :range_id
                            AND hooks.user_id != :user_id
                    ");
                    $statement->execute(array(
                        'range_id' => $object->folder['range_id'],
                        'user_id' => $object['user_id']
                    ));
                    $hooks = array();
                    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
                        $hooks[] = Hook::buildExisting($data);
                    }
                    return $hooks;
                case "institute":
                    $statement = DBManager::get()->prepare("
                        SELECT hooks.*
                        FROM hooks 
                            INNER JOIN user_inst ON (user_inst.user_id = hooks.user_id)
                        WHERE user_inst.Institut_id = :range_id
                            AND hooks.user_id != :user_id
                    ");
                    $statement->execute(array(
                        'range_id' => $object->folder['range_id'],
                        'user_id' => $object['user_id']
                    ));
                    $hooks = array();
                    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
                        $hooks[] = Hook::buildExisting($data);
                    }
                    return $hooks;
                case "user":
                    //we simply assume that noone wants to receive push events from their own files
                    return array();
                    break;
            }
        }
    }

    public function getEditTemplate(Hook $hook)
    {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("ifs/fileref/edit.php");
        $template->hook = $hook;
        return $template;
    }

    public function check(Hook $hook, $type, $event, $fileref)
    {
        if ($fileref->foldertype->isFileDownloadable($fileref['id'], $hook['user_id'])) {
            return array(
                'id' => $fileref['id'],
                'user_id' => $fileref['user_id'],
                'von_name' => User::find($fileref['user_id'])->getFullName(),
                'name' => $fileref->name,
                'range_type' => $fileref->folder['range_type']
            );
        }
    }
}