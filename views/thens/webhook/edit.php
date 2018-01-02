<label>
    <?= _("URL") ?>
    <input type="text"
           name="data[then_settings][webhook_url]"
           placeholder="https://..."
           value="<?= htmlReady($hook['then_settings']['webhook_url']) ?>">
</label>