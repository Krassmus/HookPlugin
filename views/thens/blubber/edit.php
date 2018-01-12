<label>
    <?= _("Wo soll der Blubber erscheinen") ?>
    <select name="data[then_settings][range]">
        <option value="public"<?= $hook['then_settings']['range'] === $course->id ? " selected" : "" ?>><?= _("Globaler Stream") ?></option>
        <? foreach ($courses as $course) : ?>
            <option value="<?= htmlReady($course->id) ?>"<?= $hook['then_settings']['range'] === $course->id ? " selected" : "" ?>>
                <?= htmlReady($course['name']) ?>
            </option>
        <? endforeach ?>
    </select>
</label>

<label>
    <?= _("Text") ?>
    <textarea name="data[then_settings][description]"><?= htmlReady($hook['then_settings']['description']) ?></textarea>
</label>