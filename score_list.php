<?php

declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/layout.php';

$surname = trim((string) ($_GET['surname'] ?? ''));
$saved = (string) ($_GET['saved'] ?? '') === '1';

if ($surname !== '') {
    $stmt = db()->prepare(
        'SELECT student_name, subject, score, remark, created_at FROM student_scores WHERE student_name LIKE ? ORDER BY created_at DESC, id DESC'
    );
    $stmt->execute([$surname . '%']);
} else {
    $stmt = db()->query(
        'SELECT student_name, subject, score, remark, created_at FROM student_scores ORDER BY created_at DESC, id DESC'
    );
}

$scores = $stmt->fetchAll();

render_admin_layout('成绩展示', 'score_list', function () use ($scores, $surname, $saved): void {
    ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">学生成绩</p>
                <h2>成绩展示</h2>
            </div>

            <a class="primary-link" href="score_create.php">录入成绩</a>
        </div>

        <?php if ($saved): ?>
            <div class="success" role="status">成绩保存成功</div>
        <?php endif; ?>

        <form class="search-form" method="get" action="score_list.php">
            <label for="surname">按姓查询</label>
            <div class="search-row">
                <input id="surname" name="surname" type="text" placeholder="例如：王" value="<?= h($surname) ?>">
                <button type="submit">查询</button>
                <a class="secondary-link" href="score_list.php">重置</a>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>学生姓名</th>
                        <th>学科</th>
                        <th>成绩</th>
                        <th>备注</th>
                        <th>录入时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($scores === []): ?>
                        <tr>
                            <td class="empty" colspan="5">暂无成绩记录</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($scores as $score): ?>
                            <tr>
                                <td><?= h((string) $score['student_name']) ?></td>
                                <td><?= h((string) $score['subject']) ?></td>
                                <td><?= h((string) $score['score']) ?></td>
                                <td><?= h((string) ($score['remark'] ?? '')) ?></td>
                                <td><?= h((string) $score['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php
});
