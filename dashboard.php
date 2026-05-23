<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/auth.php';
require __DIR__ . '/layout.php';

render_admin_layout('首页', 'home', function (): void {
    ?>
    <section class="hero-section" aria-labelledby="home-title">
        <div class="hero-copy">
            <p class="eyebrow">从需求到代码</p>
            <h2 id="home-title">用 Codex 学会更高效地编程</h2>
            <p>这里是登录后的主页，专注讲解如何使用 Codex 阅读项目、生成代码、修复问题、运行测试，并把想法一步步变成可运行的程序。</p>
        </div>
    </section>

    <section class="lesson-grid" aria-label="课程内容">
        <article class="lesson-card">
            <span>01</span>
            <h3>描述需求</h3>
            <p>学习如何把模糊想法拆成清晰任务，让 Codex 更准确地理解目标。</p>
        </article>

        <article class="lesson-card">
            <span>02</span>
            <h3>阅读代码</h3>
            <p>让 Codex 帮你梳理项目结构、解释核心文件、定位关键业务逻辑。</p>
        </article>

        <article class="lesson-card">
            <span>03</span>
            <h3>实现功能</h3>
            <p>从页面、接口到数据库，逐步完成改动并保持代码风格一致。</p>
        </article>

        <article class="lesson-card">
            <span>04</span>
            <h3>验证结果</h3>
            <p>运行语法检查、自动化测试和浏览器验证，确认功能真正可用。</p>
        </article>
    </section>
    <?php
});
