<?= _("Wenn eine Aktivität in Stud.IP auftaucht (siehe Startseite: Aktivitäten).") ?>
<label>
    <?= _("Von wem?") ?>
    <select name="data[if_settings][notmine]">
        <option value="0"><?= _("Von allen") ?></option>
        <option value="1" <?= $hook['if_settings']['notmine'] ? " selected" : "" ?>><?= _("Alle außer meine eigenen Aktivitäten") ?></option>
    </select>
</label>