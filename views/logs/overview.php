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
                        <?= htmlReady($log->hook['name']) ?>
                    </td>
                    <td>
                        <?= date("j.n.Y G:i", $log['mkdate']) ?>
                    </td>
                    <td>
                        <?= formatReady($log['log_text']) ?>
                    </td>
                </tr>
            <? endforeach ?>
        <? else : ?>
            <tr>
                <td colspan="100">
                    <?= _("Es existieren keine Logeinträge. Beachten Sie, dass Logeinträge werden nach maximal zwei Wochen gelöscht werden.") ?>
                </td>
            </tr>
        <? endif ?>
    </tbody>
</table>