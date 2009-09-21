<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="" />
<?php foreach ($headers['meta'] as $header) { ?>
<meta <?php if (isset($header['name'])) { ?>name="<?= $header['name']; ?>"<?php } elseif (isset($header['http-equiv'])) { ?>http-equiv="<?= $header['http-equiv']; ?>"<?php } ?> content="<?= $header['content']; ?>" />
<?php } ?>
<?php foreach ($headers['js'] as $header) { ?>
<script language="javascript" src="<?= $header; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($inline['js'] as $script) { ?>
<script language="javascript" type="text/javascript">
<?= $script; ?>
</script>
<?php } ?>
<?php foreach ($headers['css'] as $css) { ?>
<link rel="stylesheet" type="text/css" href="<?= $css; ?>">
<?php } ?>
<?php foreach ($inline['css'] as $css) { ?>
<style>
<?= $css; ?>
</style>;
<?php } ?>
<title><?= $title; ?></title>
</head>
<body>

</body>
</html>