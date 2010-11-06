<div class="top_right">
<?php if ($count != FALSE): ?>
ignited form number <?=$count?>
<?php endif; ?>
</div>
</div>

<div id="content">
<p class="important">
<?php if (!$error): ?>
MVC+SQL as .zip - <a href="<?=base_url()."formigniter/download/{$id}/all"?>">Download</a>
<?php else: // user isn't given the option to downoad the files if they were not successfully written to disk ?>
<?php echo $error?>
<?php endif; ?> 
</p>

<h4>Controller file<?php if (!$error): ?> - <a href="<?=base_url()."formigniter/download/{$id}/controller"?>">Download file</a><?php endif; ?></h4>
<textarea class="textarea" rows="28" cols="50" style="width: 100%;">
<?php echo $controller; ?>
</textarea>

<h4>View file<?php if (!$error): ?> - <a href="<?=base_url()."formigniter/download/{$id}/view"?>">Download file</a><?php endif; ?></h4>
<textarea class="textarea" rows="28" cols="50" style="width: 100%;">
<?php echo $view; ?>
</textarea>

<h4>Model file<?php if (!$error): ?> - <a href="<?=base_url()."formigniter/download/{$id}/model"?>">Download file</a><?php endif; ?></h4>
<textarea class="textarea" rows="28" cols="50" style="width: 100%;">
<?php echo $model; ?>
</textarea>

<h4>SQL file<?php if (!$error): ?> - <a href="<?=base_url()."formigniter/download/{$id}/sql"?>">Download file</a><?php endif; ?></h4>
<textarea class="textarea" rows="15" cols="50" style="width: 100%;">
<?php echo $sql; ?>
</textarea>

