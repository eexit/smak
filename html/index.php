<?php require_once('./../inc/core.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title><?php echo $smak->getTplTitle() ?></title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
    <div id="header">
        <?php echo $smak->getNav(); ?>
    </div>
    <div id="side">
        <?php $smak->getSideTpl() ? require_once($smak->getSideTpl()) : null; ?>
    </div>
    <div id="content">
        <?php require_once($smak->getBodyTpl()); ?>
    </div>
</body>
</html>