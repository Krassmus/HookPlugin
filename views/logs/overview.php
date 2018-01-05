<table class="default">
    <thead>
        <tr>
            <th></th>
            <th><?= _("Hook") ?></th>
            <th><?= _("Zeit") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <? if (count($logs)) : ?>
            <? foreach ($logs as $log) : ?>
                <tr>
                    <td>
                        <? if (!$log['exception']) : ?>
                            <?= Icon::create("accept", "status-green")->asImg(20) ?>
                        <? else : ?>
                            <?= Icon::create("decline", "status-red")->asImg(20) ?>
                        <? endif ?>
                    </td>
                    <td>
                        <a href="<?= PluginEngine::getLink($plugin, array(), "logs/details/".$log->getId()) ?>" data-dialog>
                            <?= htmlReady($log->hook['name']) ?>
                        </a>
                    </td>
                    <td>
                        <?= date("j.n.Y G:i", $log['mkdate']) ?>
                    </td>
                    <td class="actions">
                        <a href="<?= PluginEngine::getLink($plugin, array(), "logs/details/".$log->getId()) ?>" data-dialog>
                            <?= Icon::create("log+new", "clickable")->asImg() ?>
                        </a>
                    </td>
                </tr>
            <? endforeach ?>
        <? else : ?>
            <tr>
                <td colspan="100">
                    <?= _("Es existieren keine Logeinträge. Beachten Sie, dass Logeinträge nach maximal zwei Wochen gelöscht werden.") ?>
                </td>
            </tr>
        <? endif ?>
    </tbody>
</table>