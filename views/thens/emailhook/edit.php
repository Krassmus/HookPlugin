<label>
    <?= _("An Email schreiben") ?>
    <input type="email"
           name="data[then_settings][to_email]"
           placeholder="someone@server.net"
           value="<?= htmlReady($hook['then_settings'] ? $hook['then_settings']['to_email'] : "") ?>">
</label>

<label>
    <?= _("Betreff") ?>
    <input type="text" name="data[then_settings][subject]" value="<?= htmlReady($hook['then_settings'] ? $hook['then_settings']['subject'] : "") ?>">
</label>

<label>
    <?= _("Nachricht") ?>
    <textarea name="data[then_settings][body]"><?= htmlReady($hook['then_settings'] ? $hook['then_settings']['body'] : "") ?></textarea>
</label>