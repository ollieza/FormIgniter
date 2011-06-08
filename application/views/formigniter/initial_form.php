<script type="text/javascript"> 
           
        /** ------------------------------------
        /**  Live URL Title Function
        /**     Code from ExpressionEngine v 1.6.3
        /**     Slightly modified to accept the parameter fieldcount
        /** -------------------------------------*/
        
        function liveUrlTitle(fieldcount)
        {
        	var defaultTitle = '';
			var NewText = document.getElementById("view_field_label" + fieldcount).value;
			
			if (defaultTitle != '')
			{
				if (NewText.substr(0, defaultTitle.length) == defaultTitle)
				{
					NewText = NewText.substr(defaultTitle.length)
				}	
			}
			
			NewText = NewText.toLowerCase();
			var separator = "_";
			
			if (separator != "_")
			{
				NewText = NewText.replace(/\_/g, separator);
			}
			else
			{
				NewText = NewText.replace(/\-/g, separator);
			}
	
			// Foreign Character Attempt
			
			var NewTextTemp = '';
			for(var pos=0; pos<NewText.length; pos++)
			{
				var c = NewText.charCodeAt(pos);
				
				if (c >= 32 && c < 128)
				{
					NewTextTemp += NewText.charAt(pos);
				}
				else
				{
					if (c == '223') {NewTextTemp += 'ss'; continue;}
				if (c == '224') {NewTextTemp += 'a'; continue;}
				if (c == '225') {NewTextTemp += 'a'; continue;}
				if (c == '226') {NewTextTemp += 'a'; continue;}
				if (c == '229') {NewTextTemp += 'a'; continue;}
				if (c == '227') {NewTextTemp += 'ae'; continue;}
				if (c == '230') {NewTextTemp += 'ae'; continue;}
				if (c == '228') {NewTextTemp += 'ae'; continue;}
				if (c == '231') {NewTextTemp += 'c'; continue;}
				if (c == '232') {NewTextTemp += 'e'; continue;}
				if (c == '233') {NewTextTemp += 'e'; continue;}
				if (c == '234') {NewTextTemp += 'e'; continue;}
				if (c == '235') {NewTextTemp += 'e'; continue;}
				if (c == '236') {NewTextTemp += 'i'; continue;}
				if (c == '237') {NewTextTemp += 'i'; continue;}
				if (c == '238') {NewTextTemp += 'i'; continue;}
				if (c == '239') {NewTextTemp += 'i'; continue;}
				if (c == '241') {NewTextTemp += 'n'; continue;}
				if (c == '242') {NewTextTemp += 'o'; continue;}
				if (c == '243') {NewTextTemp += 'o'; continue;}
				if (c == '244') {NewTextTemp += 'o'; continue;}
				if (c == '245') {NewTextTemp += 'o'; continue;}
				if (c == '246') {NewTextTemp += 'oe'; continue;}
				if (c == '249') {NewTextTemp += 'u'; continue;}
				if (c == '250') {NewTextTemp += 'u'; continue;}
				if (c == '251') {NewTextTemp += 'u'; continue;}
				if (c == '252') {NewTextTemp += 'ue'; continue;}
				if (c == '255') {NewTextTemp += 'y'; continue;}
				if (c == '257') {NewTextTemp += 'aa'; continue;}
				if (c == '269') {NewTextTemp += 'ch'; continue;}
				if (c == '275') {NewTextTemp += 'ee'; continue;}
				if (c == '291') {NewTextTemp += 'gj'; continue;}
				if (c == '299') {NewTextTemp += 'ii'; continue;}
				if (c == '311') {NewTextTemp += 'kj'; continue;}
				if (c == '316') {NewTextTemp += 'lj'; continue;}
				if (c == '326') {NewTextTemp += 'nj'; continue;}
				if (c == '353') {NewTextTemp += 'sh'; continue;}
				if (c == '363') {NewTextTemp += 'uu'; continue;}
				if (c == '382') {NewTextTemp += 'zh'; continue;}
				if (c == '256') {NewTextTemp += 'aa'; continue;}
				if (c == '268') {NewTextTemp += 'ch'; continue;}
				if (c == '274') {NewTextTemp += 'ee'; continue;}
				if (c == '290') {NewTextTemp += 'gj'; continue;}
				if (c == '298') {NewTextTemp += 'ii'; continue;}
				if (c == '310') {NewTextTemp += 'kj'; continue;}
				if (c == '315') {NewTextTemp += 'lj'; continue;}
				if (c == '325') {NewTextTemp += 'nj'; continue;}
				if (c == '352') {NewTextTemp += 'sh'; continue;}
				if (c == '362') {NewTextTemp += 'uu'; continue;}
				if (c == '381') {NewTextTemp += 'zh'; continue;}
				
				}
			}
    
			NewText = NewTextTemp;
			
			NewText = NewText.replace('/<(.*?)>/g', '');
			NewText = NewText.replace('/\&#\d+\;/g', '');
			NewText = NewText.replace('/\&\#\d+?\;/g', '');
			NewText = NewText.replace('/\&\S+?\;/g','');
			NewText = NewText.replace(/['\"\?\.\!*$\#@%;:,=\(\)\[\]]/g,'');
			NewText = NewText.replace(/\s+/g, separator);
			NewText = NewText.replace(/\//g, separator);
			NewText = NewText.replace(/[^a-z0-9-_]/g,'');
			NewText = NewText.replace(/\+/g, separator);
			NewText = NewText.replace(/[-_]+/g, separator);
			NewText = NewText.replace(/\&/g,'');
			NewText = NewText.replace(/-$/g,'');
			NewText = NewText.replace(/_$/g,'');
			NewText = NewText.replace(/^_/g,'');
			NewText = NewText.replace(/^-/g,'');
			
			
				document.getElementById("view_field_name" + fieldcount).value = "" + NewText;			
				
		}
</script>

<div class="top_right">

Maximum number of fields 
<?php
for ($count = 5; $count <= 50; $count = $count * 2) // loop to build 10 form boxes
{
?>
<a href="<?php echo base_url()."formigniter/index/{$count}"; ?>/" 
<?php if ($count == $field_total)
{
     echo 'class="current"';   
} ?>
><?php echo $count; ?>
</a> | 
<?php } // end loop ?>
 
</div></div>

<div id="content">

<?php if ($count != FALSE): ?>
<?php if (DB):?><p><?php echo $form_count?> forms ignited so far.</p><?php endif; ?>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
<div class="top_error"><?php echo $this->session->flashdata('error')?></div>
<?php endif; ?>

<?php // echo validation_errors(); for debuggging only ?>

<?php echo form_open( "formigniter/index/$field_total/" ) ?>

<?php if ($form_error): ?>
<div class="important">
<h4>Looks like you have an error or two. Scroll down and correct the highlighted field boxes making sure all fields are filled out.</h4>
</div>
<?php endif; ?>

<h3>Fill out the fields you want in your form, with field types, database type, and your validation rules and FormIgniter will output the code for the MVC + a database schema. Give it a go!</h3>

<div class="names">
	<h4>MVC + db names (optional)</h4>
	<label for="formname">Form name </label><input type="text" id="formname" name="formname" />
	<label for="controllername">Controller name </label><input type="text" id="controllername" name="controllername" />
	<label for="modelname">Model name </label><input type="text" id="modelname" name="modelname" />
	<label for="tablename">Table name </label><input type="text" id="tablename" name="tablename" />
</div>

<?php
for ($count = 1; $count <= $field_total; $count++) // loop to build 10 form boxes
{
?>

<?php
$box_error = NULL;

if (form_error("view_field_label{$count}") != NULL || form_error("view_field_name{$count}") != NULL || form_error("view_field_type{$count}") != NULL || form_error("db_field_type{$count}") != NULL || form_error("db_field_length_value{$count}") != NULL || form_error('cont_validation_rules'.$count.'[]') != NULL) // detect an form_validtion error message and change background if one occurs
{
	$box_error = 'background: #FBE6F2 !important; border: 1px solid #D893A1 !important;';
}

// this checks to see if the number is odd
if ($count % 2): 
?>
<div class="container_blue" style="margin-right: 10px;<?php echo $box_error; ?>">  
<?php else: ?>
<div class="container_blue" style="<?php echo $box_error; ?>">  	
<?php endif; ?>
<div class="field_heading"><?php echo $count; ?></div> 
<div class="type_heading">Field details <?php if ($box_error != NULL) { echo ' - <b>Error needs fixing!</b>'; } ?></div>

<?php echo form_error("view_field_label{$count}"); ?>
<?php echo form_error("view_field_name{$count}"); ?>
<?php echo form_error("view_field_type{$count}"); ?>

<div class="input_box">

<label for="view_field_label<?php echo $count; ?>">Label <span class="required">*</span></label>

<br /><input name="view_field_label<?php echo $count; ?>" id="view_field_label<?php echo $count; ?>" type="text" value="<?php echo set_value("view_field_label{$count}"); ?>" onkeyup="liveUrlTitle(<?php echo $count; ?>);" />
</div>

<div class="input_box">
<label for="view_field_name">Name (no spaces) <span class="required">*</span></label>
<br /><input name="view_field_name<?php echo $count; ?>" id="view_field_name<?php echo $count; ?>" type="text" value="<?php echo set_value("view_field_name{$count}"); ?>" maxlength="30" />
</div>

<div class="input_box">
<label for="view_field_type<?php echo $count; ?>">Type <span class="required">*</span></label>

<?php
$view_field_types = array(
                        'input' 	=> 'INPUT',
						'textarea' 	=> 'TEXTAREA',
                        'select' 	=> 'SELECT',
                        'radio' 	=> 'RADIO',
                        'checkbox' 	=> 'CHECKBOX',
						'password' 	=> 'PASSWORD'
                        );
?>
<br /><?php echo form_dropdown("view_field_type{$count}", $view_field_types, set_value("view_field_type{$count}")); ?>
</div>

<div class="type_heading">Database Schema</div>

<?php echo form_error("db_field_type{$count}"); ?>
<?php echo form_error("db_field_length_value{$count}"); ?>

<div class="input_box">
<label for="db_field_type<?php echo $count; ?>">Type <span class="required">*</span></label>

<?php
$db_field_types = array(
						'VARCHAR' 		=> 'VARCHAR',
						'TINYINT' 		=> 'TINYINT',
						'TEXT' 			=> 'TEXT',
						'DATE' 			=> 'DATE',
						'SMALLINT' 		=> 'SMALLINT',
						'MEDIUMINT' 	=> 'MEDIUMINT',
						'INT' 			=> 'INT',
						'BIGINT' 		=> 'BIGINT',
						'FLOAT' 		=> 'FLOAT',
						'DOUBLE' 		=> 'DOUBLE',
						'DECIMAL' 		=> 'DECIMAL',
						'DATETIME' 		=> 'DATETIME',
						'TIMESTAMP' 	=> 'TIMESTAMP',
						'TIME' 			=> 'TIME',
						'YEAR' 			=> 'YEAR',
						'CHAR' 			=> 'CHAR',
						'TINYBLOB' 		=> 'TINYBLOB',
						'TINYTEXT' 		=> 'TINYTEXT',
						'BLOB' 			=> 'BLOB',
						'MEDIUMBLOB' 	=> 'MEDIUMBLOB',
						'MEDIUMTEXT' 	=> 'MEDIUMTEXT',
						'LONGBLOB' 		=> 'LONGBLOB',
						'LONGTEXT' 		=> 'LONGTEXT',
						'ENUM' 			=> 'ENUM',
						'SET' 			=> 'SET',
						'BIT' 			=> 'BIT',
						'BOOL' 			=> 'BOOL',
						'BINARY' 		=> 'BINARY',
						'VARBINARY' 	=> 'VARBINARY'
                        );
?>
<br /><?php echo form_dropdown("db_field_type{$count}", $db_field_types, set_value("db_field_type{$count}")); ?>

</div>
<div class="input_box">
<label for="db_field_length_value<?php echo $count; ?>">Length/Values</label>
<br /><input name="db_field_length_value<?php echo $count; ?>" type="text" value="<?php echo set_value("db_field_length_value{$count}"); ?>" />
</div>

<div class="type_heading">Validation Rules (optional)</div>

<?php echo form_error('cont_validation_rules'.$count.'[]'); ?>

<input type="checkbox" name="validation_rules<?php echo $count; ?>[]" id="validation_rules<?php echo $count; ?>[]" value="required" <?php echo set_checkbox('validation_rules'.$count.'[]', 'required'); ?>  /> 
<label for="cont_validation_rules<?php echo $count; ?>[]">required</label>

<input type="checkbox" name="validation_rules<?php echo $count; ?>[]" id="validation_rules<?php echo $count; ?>[]" value="trim" <?php echo set_checkbox('validation_rules'.$count.'[]', 'trim'); ?> /> 
<label for="cont_validation_rules[]">trim</label>

<input type="checkbox" name="validation_rules<?php echo $count; ?>[]" id="validation_rules<?php echo $count; ?>[]" value="xss_clean" <?php echo set_checkbox('validation_rules'.$count.'[]', 'xss_clean'); ?> /> 
<label for="cont_validation_rules<?php echo $count?>[]">xss_clean</label>

<input type="checkbox" name="validation_rules<?php echo $count; ?>[]" id="validation_rules<?php echo $count; ?>[]" value="valid_email" <?php echo set_checkbox('validation_rules'.$count.'[]', 'valid_email'); ?> /> 
<label for="cont_validation_rules<?php echo $count?>[]">valid_email</label>

<input type="checkbox" name="validation_rules<?php echo $count; ?>[]" id="validation_rules<?php echo $count; ?>[]" value="is_numeric" <?php echo set_checkbox('validation_rules'.$count.'[]', 'is_numeric'); ?> /> 
<label for="cont_validation_rules<?php echo $count; ?>[]">is_numeric</label>

</div>
<?php
} // end loop
?>

<div style="clear:both"></div>
<p><?php echo form_submit('submit', 'Build this form'); ?></p>

<?php echo form_close()?>
</div>