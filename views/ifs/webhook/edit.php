<? if (!$hook->isNew()) : ?>
    <label>
        <?= _("URL, die aufgerufen werden muss") ?>
        <? $base_url = URLHelper::setBaseURL($GLOBALS['ABSOLUTE_URI_STUDIP']) ?>
        <input type="text" readonly value="<?= URLHelper::getLink("plugins.php/hookplugin/endpoints/hook/".$hook->getId(), array('s' => IfWebHook::getSecurityHash($hook->getId()))) ?>">
        <? URLHelper::setBaseURL($base_url) ?>
    </label>
<? else : ?>
    <?= _("Nach dem Speichern steht hier die URL, die aufgerufen werden muss, damit der Hook ausgeführt wird.") ?>
<? endif ?>