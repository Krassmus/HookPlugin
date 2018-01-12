<?php

class ThenBlubberHook implements ThenHook {

    static public function getName() {
        return _("Blubber");
    }

    public function getEditTemplate(Hook $hook, $attributes) {
        $tf = new Flexi_TemplateFactory(__DIR__."/../../views");
        $template = $tf->open("thens/blubber/edit.php");
        $template->hook = $hook;
        $template->courses = Course::findMany(BlubberPosting::getMyBlubberCourses());
        return $template;
    }

    public function perform(Hook $hook, $parameters) {
        $maximum = 5;

        $history = $hook['then_settings']['public_history'] ? $hook['then_settings']['public_history']->getArrayCopy() : array();
        $within_last_hour = 0;
        foreach ($history as $key => $last_added) {
            if (time() - $last_added < 60 * 60) {
                $within_last_hour++;
            } else {
                unset($history[$key]);
            }
        }

        if ($hook['then_settings']['range'] !== "public" || ($within_last_hour < $maximum)) {
            $blubber = new BlubberPosting();
            if ($hook['then_settings']['range'] === "public") {
                $blubber['context_type'] = "public";
                $blubber['Seminar_id'] = $hook['user_id'];
            } else {
                $blubber['context_type'] = "course";
                $blubber['Seminar_id'] = $hook['then_settings']['range'];
            }
            $blubber['user_id'] = $hook['user_id'];
            $blubber['parent_id'] = 0;
            $blubber['description'] = HookPlugin::formatTextTemplate($hook['then_settings']['description'], $parameters);
            $blubber['name'] = $blubber['description'];
            $blubber->setId($blubber->getNewId());
            $blubber['root_id'] = $blubber->getId();
            $blubber->store();

            if ($hook['then_settings']['range'] !== "public") {
                $hook['then_settings']['public_history'][] = time();
                $hook->store();
            }
            return "Blubber wurde erstellt.";
        } else {
            throw new Exception(sprintf("Mehr als %s öffentliche Blubber pro Stunde! Konnte den Spam nicht mehr ertragen. Sorry.", $maximum));
        }
    }
}