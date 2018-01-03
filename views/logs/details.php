<h2><?= _("Hook") ?></h2>
<?= htmlReady($log->hook['name']) ?>

<h2><?= _("Verursacher des Events") ?></h2>
<? $user = User::find($log['user_id']) ?>
<? if ($user) : ?>
    <?= Avatar::getAvatar($user->getId())->getImageTag(Avatar::MEDIUM, array('style' => "max-width: 50px; max-height: 50px; vertical-align: middle;")) ?>
    <span style="vertical-align: middle;"><?= htmlReady($user->getFullName()) ?></span>
<? else : ?>
    <?= htmlReady($log['user_id']) ?>
<? endif ?>

<h2><?= _("Zeitpunkt") ?></h2>
<?= date("j.n.Y G:i", $log['mkdate']) ?>

<h2><?= _("Log-Detail") ?></h2>
<div class="message_body">
    <?= formatReady($log['log_text']) ?>
</div>
<div data-dialog-button>
    <?= \Studip\LinkButton::create(_("Zurück"), PluginEngine::getURL($plugin, array(), "logs/overview/".$log['hook_id']), array('data-dialog' => 1)) ?>
</div>