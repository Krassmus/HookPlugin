<table class="default">
    <thead>
        <tr>
            <th><?= _("Aktiv") ?></th>
            <th><?= _("Name") ?></th>
            <th><?= _("Typ") ?></th>
            <th><?= _("Letzte Ausführung") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <? if (count($hooks)) : ?>
            <? foreach ($hooks as $hook) : ?>
                <tr>
                    <td>
                        <a href="#" class="active_indicator <?= $hook['activated'] ? "activated" : "" ?>"
                           onClick="jQuery.post(STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/hookplugin/hooks/toggle/<?= $hook->getId() ?>'); jQuery(this).toggleClass('activated');">
                            <?= Icon::create("checkbox-checked", "clickable")->asImg(20, array('class' => "checked")) ?>
                            <?= Icon::create("checkbox-unchecked", "clickable")->asImg(20, array('class' => "unchecked")) ?>
                        </a>
                    </td>
                    <td>
                        <? $last_log_entry = HookLog::findOneBySQL("hook_id = ? ORDER BY mkdate DESC LIMIT 1", array($hook->getId())) ?>
                        <? if ($last_log_entry && $last_log_entry['exception'] && $hook['editable']) : ?>
                            <a href="<?= PluginEngine::getLink($plugin, array(), "logs/details/".$last_log_entry->getId()) ?>"
                               data-dialog
                               title="<?= _("Fehler beim letzten Ausführen. Schauen Sie in die Details des Logs.") ?>">
                                <?= Icon::create("decline", "status-red")->asImg(20, ['class' => "text-bottom"]) ?>
                            </a>
                        <? endif ?>
                        <?= htmlReady($hook['name']) ?>
                    </td>
                    <td>
                        <?= htmlReady($hook['if_type'] ? $hook['if_type']::getName() : "") ?>
                        <?= Icon::create("arr_2right", "inactive")->asImg(16, ['class' => "text-bottom"]) ?>
                        <?= htmlReady($hook['then_type'] ? $hook['then_type']::getName() : "") ?>
                    </td>
                    <td>
                        <?= $hook['last_triggered'] ? date("j.n.Y G:i", $hook['last_triggered']) : "-" ?>
                    </td>
                    <td class="actions">
                        <? if ($hook['editable']) : ?>
                            <a href="<?= PluginEngine::getLink($plugin, array(), "hooks/edit/".$hook->getId()) ?>" data-dialog>
                                <?= Icon::create("edit", "clickable")->asImg(20) ?>
                            </a>
                        <? else : ?>
                            <?= Icon::create("edit", "inactive")->asImg(20, array('title' => _("Dieser Hook kann nicht bearbeitet, sondern nur deaktiviert oder gelöscht werden."))) ?>
                        <? endif ?>
                        <? if ($hook['last_triggered'] && $hook['editable']) : ?>
                            <a href="<?= PluginEngine::getLink($plugin, array(), "logs/overview/".$hook->getId()) ?>" data-dialog>
                                <?= Icon::create("log", "clickable")->asImg(20) ?>
                            </a>
                        <? else : ?>
                            <?= Icon::create("log", "inactive")->asImg(20) ?>
                        <? endif ?>
                        <form action="<?= PluginEngine::getLink($plugin, array(), "hooks/delete/".$hook->getId()) ?>" method="post" style="display: inline-block;" onSubmit="return window.confirm('<?= _("Wirklich löschen?") ?>');">
                            <button style="border: none; background: none; cursor: pointer;">
                                <?= Icon::create("trash", "clickable")->asImg(20) ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <? endforeach ?>
        <? else : ?>
            <tr>
                <td colspan="100">
                    <div style="text-align: center;">
                        <?= Icon::create($plugin->getPluginURL()."/assets/webhook_grey.svg")->asImg(100) ?>
                    </div>
                    <div>
                        <?= _("Noch wurde kein Hook angelegt. Hooks sind eine einzigartige Schnittstelle, um Stud.IP mit dem Rest der Welt zu verbinden. Zum Beispiel können Sie, wenn Sie eine bestimmte Stud.IP-Nachricht bekommen, diese per Telegram auf Ihr Handy schicken. Oder viele andere lustige Dinge.") ?>
                    </div>
                </td>
            </tr>
        <? endif ?>
    </tbody>
</table>

<style>
    .active_indicator .checked {
        display: none;
    }
    .active_indicator.activated .unchecked {
        display: none;
    }
    .active_indicator.activated .checked {
        display: inline-block;
    }
</style>

<?
$actions = new ActionsWidget();
$actions->addLink(
    _("Hook erstellen"),
    PluginEngine::getURL($plugin, array(), "hooks/edit"),
    Icon::create($plugin->getPluginURL()."/assets/webhook_blue.svg"),
    array('data-dialog' => 1)
);

Sidebar::Get()->addWidget($actions);