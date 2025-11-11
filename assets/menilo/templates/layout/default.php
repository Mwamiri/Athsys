<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->element('title_meta', ['title' => $this->fetch('title')]) ?>

    <?= $this->fetch('css') ?>

    <?= $this->element('head_css') ?>
</head>

<body>

    <div id="layout-wrapper">

        <?= $this->element('header') ?>

        <?= $this->element('sidebar') ?>

        <?= $this->element('horizontal') ?>

        <main class="app-wrapper">

            <div class="container-fluid">

                <?= $this->fetch('content') ?>

            </div>

        </main>

        <?= $this->element('switcher') ?>

        <?= $this->element('scroll_to_top') ?>

        <?= $this->element('footer') ?>

    </div>

    <?= $this->element('vendor_scripts') ?>

    <?= $this->fetch('customScript') ?>

</body>

</html>