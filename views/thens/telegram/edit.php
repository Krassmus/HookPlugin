<label>
    <?= _("Dein Telegram-Nutzername") ?>
    <input type="text"
           name="data[then_settings][telegram_user]"
           placeholder="coolesau"
           value="<?= htmlReady($hook['then_settings']['telegram_user']) ?>">
</label>

<? if ($hook['then_settings']['telegram_user']) : ?>
    <? if (!$hook['then_settings']['chat_id']) : ?>
        <?= MessageBox::info(_("Füge über Telegram den Bot @Rasmusonstudip_bot zu einem Gruppenchat hinzu, schreibe darin eine Nachricht und lade diese Seite danach neu.")) ?>
    <? else : ?>
        <input type="hidden" name="data[then_settings][chat_id]" value="<?= htmlReady($hook['then_settings']['chat_id']) ?>">
        <input type="hidden" name="data[then_settings][chat_with_user]" value="<?= htmlReady($hook['then_settings']['chat_with_user']) ?>">
        <label>
            <?= _("Welche Nachricht soll verschickt werden? Benutzen Sie die Parameter vom 'Wenn'-Teil.") ?>
            <textarea name="data[then_settings][template]" placeholder="{{nachricht}}"><?= htmlReady($hook['then_settings']['template']) ?></textarea>
        </label>
    <? endif ?>
<? endif ?>
