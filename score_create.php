<?php

declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/layout.php';

$errors = [];
$form = [
    'student_name' => '',
    'subject' => '',
    'score' => '',
    'remark' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['student_name'] = trim((string) ($_POST['student_name'] ?? ''));
    $form['subject'] = trim((string) ($_POST['subject'] ?? ''));
    $form['score'] = trim((string) ($_POST['score'] ?? ''));
    $form['remark'] = trim((string) ($_POST['remark'] ?? ''));

    if ($form['student_name'] === '') {
        $errors[] = '请输入学生姓名';
    }

    if ($form['subject'] === '') {
        $errors[] = '请输入学科';
    }

    if ($form['score'] === '' || !is_numeric($form['score'])) {
        $errors[] = '请输入有效成绩';
    } elseif ((float) $form['score'] < 0 || (float) $form['score'] > 100) {
        $errors[] = '成绩范围应为 0 到 100';
    }

    if ($errors === []) {
        $stmt = db()->prepare(
            'INSERT INTO student_scores (student_name, subject, score, remark) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $form['student_name'],
            $form['subject'],
            number_format((float) $form['score'], 2, '.', ''),
            $form['remark'] === '' ? null : $form['remark'],
        ]);

        header('Location: score_list.php?saved=1');
        exit;
    }
}

render_admin_layout('成绩录入', 'score_create', function () use ($errors, $form): void {
    ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">学生成绩</p>
                <h2>录入成绩</h2>
            </div>
        </div>

        <?php if ($errors !== []): ?>
            <div class="alert" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= h($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form class="score-form" method="post" action="score_create.php">
            <div class="form-grid">
                <div>
                    <label for="student_name">学生姓名</label>
                    <input id="student_name" name="student_name" type="text" value="<?= h($form['student_name']) ?>" required>
                </div>

                <div>
                    <label for="subject">学科</label>
                    <input id="subject" name="subject" type="text" value="<?= h($form['subject']) ?>" required>
                </div>

                <div>
                    <label for="score">成绩</label>
                    <input id="score" name="score" type="number" min="0" max="100" step="0.01" value="<?= h($form['score']) ?>" required>
                </div>

                <div class="full-field">
                    <label for="remark">备注</label>
                    <textarea id="remark" name="remark" rows="4"><?= h($form['remark']) ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit">保存成绩</button>
                <a class="secondary-link" href="score_list.php">查看成绩</a>
            </div>
        </form>
    </section>
    <?php
});
