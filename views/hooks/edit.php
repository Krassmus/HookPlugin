<form action="<?= PluginEngine::getLink($plugin, array(), "hooks/edit/".$hook->getId()) ?>"
      method="post"
      class="default"
      data-dialog>

    <fieldset>
        <legend><?= _("Daten des Hooks") ?></legend>
        <label>
            <?= _("Name des Hooks") ?>
            <input type="text" name="data[name]" value="<?= htmlReady($hook['name']) ?>" required>
        </label>

        <label>
            <?= _("Priorität") ?>
            <select name="data[cronjob]">
                <option value="1"<?= $hook->isNew() || $hook['cronjob'] ? " selected" : "" ?>><?= _("Gering (einmal ausführen pro Minute reicht)") ?></option>
                <option value="0"<?= !$hook->isNew() && !$hook['cronjob'] ? " selected" : "" ?>><?= _("Hoch (sofort ausführen)") ?></option>
            </select>
        </label>

        <label>
            <input type="checkbox" name="data[activated]" value="1"<?= $hook['activated'] || $hook->isNew() ? " checked" : "" ?>>
            <?= _("Aktiv") ?>
        </label>
    </fieldset>

    <fieldset>
        <legend><?= _("Wenn") ?></legend>

        <label>
            <?= _("Art des Events") ?>
            <select name="data[if_type]"
                    onChange="jQuery('.if_hook_template').css('opacity', 0.5).load(STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/hookplugin/hooks/edit_if_hook/' + this.value + '/<?= $hook->getId() ?>', function () { jQuery(this).css('opacity', 1); });"
                    required>
                <option></option>
                <? foreach (get_declared_classes() as $class) :
                    if (in_array('IfHook', class_implements($class))) : ?>
                        <? $then_hook = new $class() ?>
                        <option value="<?= htmlReady($class) ?>"<?= $hook['if_type'] === $class ? " selected" : "" ?>>
                            <?= htmlReady($class::getName()) ?>
                        </option>
                    <? endif;
                endforeach ?>
            </select>
        </label>

        <div class="if_hook_template">
            <? if ($hook['if_type']) : ?>
                <? $then_hook = new $hook['if_type']() ?>
                <?= $then_hook->getEditTemplate($hook)->render() ?>
            <? endif ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= _("Dann") ?></legend>

        <label>
            <?= _("Art des Events") ?>
            <select name="data[then_type]"
                    onChange="jQuery('.then_hook_template').css('opacity', 0.5).load(STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/hookplugin/hooks/edit_then_hook/' + this.value + '/<?= $hook->getId() ?>', function () { jQuery(this).css('opacity', 1); });"
                    required>
                <option></option>
                <? foreach (get_declared_classes() as $class) :
                    if (in_array('ThenHook', class_implements($class))) : ?>
                        <? $then_hook = new $class() ?>
                        <option value="<?= htmlReady($class) ?>"<?= $hook['then_type'] === $class ? " selected" : "" ?>>
                            <?= htmlReady($class::getName()) ?>
                        </option>
                    <? endif;
                endforeach ?>
            </select>
        </label>

        <div class="then_hook_template">
            <? if ($hook['then_type']) : ?>
                <? $then_hook = new $hook['then_type']() ?>
                <?= $then_hook->getEditTemplate($hook, array())->render() ?>
            <? endif ?>
        </div>
    </fieldset>

    <div data-dialog-button>
        <?= \Studip\Button::create(_("Speichern")) ?>
    </div>
</form>

<?
$actions = new ActionsWidget();
$actions->addLink(
    _("Hook erstellen"),
    PluginEngine::getURL($plugin, array(), "hooks/edit"),
    Icon::create($plugin->getPluginURL()."/assets/webhook_blue.svg"),
    array('data-dialog' => "reload-on-close")
);

Sidebar::Get()->addWidget($actions);