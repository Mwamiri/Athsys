<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->element('title_meta', ['title' => $this->fetch('title')]) ?>

    <?= $this->fetch('css') ?>

    <?= $this->element('head_css') ?>
</head>

<body>
    
    <?= $this->element('auth_background') ?>

    <?= $this->element('auth_header') ?>

    <?= $this->fetch('content') ?>

    <?= $this->element('vendor_scripts') ?>

    <?= $this->fetch('customScript') ?>

</body>

</html>