<?= _("Wenn ich eine Stud.IP-Nachricht bekomme.") ?>

<label>
    <?= _("Nur Nachrichten mit diesem Schlagwort") ?>
    <input type="text" name="data[if_settings][tag_filter]" value="<?= htmlReady($hook['if_settings'] ? $hook['if_settings']['tag_filter'] : "") ?>">
</label>

