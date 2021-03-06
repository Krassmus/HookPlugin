<label>
    <?= _("URL") ?>
    <input type="text"
           name="data[then_settings][webhook_url]"
           placeholder="https://..."
           value="<?= htmlReady($hook['then_settings']['webhook_url']) ?>">
</label>

<label>
    <?= _("Payload") ?>
    <textarea name="data[then_settings][json]" placeholder="<?= _("JSON mit {{variablen}}") ?>"><?= htmlReady($hook['then_settings']['json']) ?></textarea>
</label>

<table class="default header">
    <caption><?= _("HTTP-Header") ?></caption>
    <thead>
    <tr>
        <th><?= _("Attribut") ?></th>
        <th><?= _("Wert") ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <? if ($hook['then_settings']['header']['keys']) : ?>
        <? foreach ($hook['then_settings']['header']['keys'] as $i => $key) : ?>
            <? if (trim($key)) : ?>
                <tr>
                    <td>
                        <input type="text"
                               name="data[then_settings][header][keys][]"
                               placeholder="some_id"
                               value="<?= htmlReady($key) ?>">
                    </td>
                    <td>
                            <textarea name="data[then_settings][header][values][]"
                                      placeholder="any value or :attribute"><?= htmlReady($hook['then_settings']['header']['values'][$i]) ?></textarea>
                    </td>
                    <td class="actions">
                        <a href="#" onClick="jQuery(this).closest('tr').remove(); return false;">
                            <?= Icon::create("trash", "clickable")->asImg(20) ?>
                        </a>
                    </td>
                </tr>
            <? endif ?>
        <? endforeach ?>
    <? endif ?>
    <tr>
        <td>
            <input type="text" name="data[then_settings][header][keys][]" placeholder="some_id">
        </td>
        <td>
            <textarea name="data[then_settings][header][values][]" placeholder="any value or {{attribute}}"></textarea>
        </td>
        <td class="actions">
            <a href="#" onClick="jQuery(this).closest('tr').remove(); return false;">
                <?= Icon::create("trash", "clickable")->asImg(20) ?>
            </a>
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="100">
            <a href="#" onClick="jQuery('table.default.header tbody > tr:last-child').clone().appendTo('table.default.header tbody').find('input, textarea').val(''); return false;">
                <?= Icon::create("add", "clickable")->asImg(20) ?>
            </a>
        </td>
    </tr>
    </tfoot>
</table>

<label>
    <?= _("Spezielle Zertifikatsdatei") ?>
    <textarea name="data[then_settings][cert]" placeholder="<?= _("-----BEGIN CERTIFICATE----- ...") ?>"><?= htmlReady($hook['then_settings']['cert']) ?></textarea>
</label>

<style>
    table.default.header tbody > tr:last-child > td.actions a {
        display: none;
    }
    table.default.json tbody > tr:last-child > td.actions a {
        display: none;
    }
    table.default.json tbody > input {

    }
</style>