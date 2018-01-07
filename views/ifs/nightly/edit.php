<?= _("Einmal in der Nacht zwischen 0 und 6 Uhr wird diese Aktion ausgeführt.") ?>

<input type="hidden" name="data[if_settings][minutes_after_midnight]" value="<?= htmlReady($hook['if_settings']['minutes_after_midnight'] ?: rand(0, 60 * 6)) ?>">
<input type="hidden" name="date[if_settings][last_execution]" value="<?= htmlReady($hook['if_settings']['last_execution']) ?>">