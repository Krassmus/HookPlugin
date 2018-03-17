<?= _("Wenn Blubber öffentlicher Blubber geschrieben wurde.") ?>

<label>
    <input type="checkbox" name="data[if_settings][onlymine]" value="1" <?= htmlReady($hook['if_settings']['onlymine'] ? " checked" : "") ?>">
    <?= _("Nur meine eigenen öffentlichen Blubber") ?>
</label>

