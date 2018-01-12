<label>
    <?= _("Text der Benachrichtigung") ?>
    <input type="text"
           name="data[then_settings][text]"
           value="<?= htmlReady($hook['then_settings']['text']) ?>"
           placeholder="Sie haben ...">
</label>

<label>
    <?= _("Link für die Benachrichtigung") ?>
    <input type="text"
           name="data[then_settings][url]"
           value="<?= htmlReady($hook['then_settings']['url']) ?>"
           placeholder="https://...">
</label>

<label>
    <?= _("Bild für die Benachrichtigung (als URL)") ?>
    <input type="text"
           name="data[then_settings][avatar]"
           value="<?= htmlReady($hook['then_settings']['avatar']) ?>"
           placeholder="https://...">
</label>

<label>
    <input type="checkbox" name="data[then_settings][dialog]" value="1"<?= $hook['then_settings']['dialog'] ? " checked" : "" ?>>
    <?= _("Soll der Link im Dialog geöffnet werden?") ?>
</label>