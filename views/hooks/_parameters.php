<? if ($parameters) : ?>
    <div>
        <?= _("Parameter") ?>
        <ul class="clean" style="display: inline-flex;">
            <? foreach ($parameters as $parameter) : ?>
                <li style="margin-right: 10px; background-color: #e7ebf1; padding: 5px;">{{<?= htmlReady($parameter) ?>}}</li>
            <? endforeach ?>
        </ul>
    </div>
<? endif ?>