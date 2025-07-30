<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view("templates/header"); ?>
</head>
<body class="p-3" style="background: url(<?= base_url("assets/image/background.jpg"); ?>); background-size: 100% 100vh; background-repeat: no-repeat;">
    <?php $this->load->view("content/".$content); ?>
    <?php $this->load->view("templates/footer"); ?>
</body>
</html>
<?php $this->load->view("templates/footer_js"); ?>
<?php !empty($js) && $this->load->view("javascript/".$js); ?>