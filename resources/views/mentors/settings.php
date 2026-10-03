<?php
/**
 * @var array<string,mixed>|null $mentor
 * @var list<int> $skillIds
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 */
?>
<section class="page-header">
    <h1>Mentor Settings</h1>
    <p class="muted">Enable your mentor profile and set availability.</p>
</section>

<div class="card">
    <form method="post" action="<?= e(url('/mentors/settings')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="bio">Mentor bio</label>
            <textarea id="bio" name="bio" rows="5" maxlength="5000"><?= e((string) ($mentor['bio'] ?? old('bio', ''))) ?></textarea>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="accepting_requests" value="1"
                    <?= ($mentor === null || !empty($mentor['accepting_requests'])) ? 'checked' : '' ?>>
                Accepting mentorship requests
            </label>
        </div>
        <div class="form-group">
            <label for="max_mentees">Max mentees</label>
            <input type="number" id="max_mentees" name="max_mentees" min="1" max="10"
                   value="<?= e((string) ($mentor['max_mentees'] ?? old('max_mentees', '3'))) ?>">
        </div>
        <div class="form-group">
            <label for="preferred_frequency">Preferred frequency</label>
            <input id="preferred_frequency" name="preferred_frequency"
                   value="<?= e((string) ($mentor['preferred_frequency'] ?? old('preferred_frequency', 'weekly'))) ?>">
        </div>
        <div class="form-group">
            <label for="preferred_session_length">Session length</label>
            <input id="preferred_session_length" name="preferred_session_length"
                   value="<?= e((string) ($mentor['preferred_session_length'] ?? old('preferred_session_length', '45 minutes'))) ?>">
        </div>
        <div class="form-group">
            <label for="languages">Languages</label>
            <input id="languages" name="languages" value="<?= e((string) ($mentor['languages'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="communication_style">Communication style</label>
            <input id="communication_style" name="communication_style"
                   value="<?= e((string) ($mentor['communication_style'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label>Skills you mentor</label>
            <div class="skill-checkbox-grid">
                <?php foreach ($allSkills as $sk): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="skill_ids[]" value="<?= (int) $sk['id'] ?>"
                            <?= in_array((int) $sk['id'], $skillIds, true) ? 'checked' : '' ?>>
                        <?= e($sk['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Save mentor profile</button>
    </form>
</div>

<?php if ($mentor !== null): ?>
<p class="muted"><a href="<?= e(url('/mentors/' . \App\Core\Auth::user()?->username)) ?>">View public mentor page</a></p>
<?php endif; ?>
