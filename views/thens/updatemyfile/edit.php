<label>
    <?= _("Zu ersetzende Datei") ?>
    <select name="data[then_settings][fileref_id]">
        <? foreach (FileRef::findBySQL("user_id = ?", array($GLOBALS['user']->id)) as $fileref) : ?>
            <option value="<?= htmlReady($fileref->getId()) ?>"<?= $fileref->getId() === $hook['then_settings']['fileref_id'] ? " selected" : "" ?>>
                <?= htmlReady($fileref['name']) ?>
            </option>
        <? endforeach ?>
    </select>
</label>

<label>
    <?= _("URL der Datei") ?>
    <input type="text"
           name="data[then_settings][url]"
           value="<?= htmlReady($hook['then_settings']['url']) ?>"
           placeholder="https://...">
</label>

<label>
    <input type="checkbox" name="data[then_settings][update_other_references]" value="1"<?= $hook['then_settings']['update_other_references'] ? " checked" : "" ?>>
    <?= _("Alle Kopien der Datei ebenfalls ersetzen?") ?>
</label>