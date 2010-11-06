<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="Enter your field names, types and rules and FormIgniter will generate all the code, saving you hours. Never hand code a form again!" />

<title>FormIgniter - <?php if (isset($title)) {
                        echo $title;
                }
                else
                {
                echo 'Easy form generator for the CodeIgniter framework';        
                }
                ?>
</title>

<?php
// only output on formigniter.org to sort odd nginx proxy bug
if ("${_SERVER['HTTP_HOST']}" == 'formigniter.org'):
?>               
<style type='text/css' media='all'>@import url('/assets/css/core.css');</style>
<style type='text/css'>@import url('/assets/css/zenbox_overlay.css');</style>
<script type='text/javascript' src='/assets/js/zenbox_overlay.js'></script>
<?php else: ?>
<style type='text/css' media='all'>@import url('<?=BASE_URL()?>/assets/css/core.css');</style>
<style type='text/css'>@import url('<?=BASE_URL()?>assets/css/zenbox_overlay.css');</style>
<script type='text/javascript' src='<?=BASE_URL()?>assets/js/zenbox_overlay.js'></script>
<?php endif; ?>

</head>
<body>
<div id="container">
<div id="header">
<span class="title"><a href="http://formigniter.org">FormIgniter</a></span> - Easy form generator for the CodeIgniter framework