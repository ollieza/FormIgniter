<?php

/**
 * FormIgniter
 *
 * An easy form generator for the CodeIgniter framework
 * 
 * @package   FormIgniter
 * @version   0.8
 * @author    Ollie Rattue, Too many tabs <orattue[at]toomanytabs.com>
 * @copyright Copyright (c) 2011, Ollie Rattue
 * @license   http://www.opensource.org/licenses/mit-license.php
 * @link      http://github.com/ollierattue/FormIgniter
 * @link	  http://formigniter.org
 */

define('DB', FALSE);

class Formigniter extends CI_Controller
{
	function Formigniter()
	{
		parent::__construct();	
		
		// Comment this out for a local version running without statistics.
		if (DB)
		{
			$this->load->database();		
		}
		
		$this->load->library('form_validation');
		$this->load->library('zip');
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->helper('download');
		//$this->output->enable_profiler(TRUE);
		
		$this->load->model('formigniter_model');
		
		//getting the name values
		$this->formname = $this->input->post("formname");
		$this->controllername = $this->input->post("controllername");
		$this->modelname = $this->input->post("modelname");
		$this->tablename = $this->input->post("tablename");

		//set default values if needed
		if($this->formname == "") {
			$this->formname = "myform";
		}
		if($this->controllername == "") {
			$this->controllername = $this->formname;
		}
		if($this->modelname == "") {
			$this->modelname = $this->formname.'_model';
		}
		if($this->tablename == "") {
			$this->tablename = $this->formname;
		}
		// filenames 
		$this->files = array(
	                        'model' 		=> $this->modelname,
	                        'view' 			=> $this->formname.'_view',
	                        'controller' 	=> $this->controllername,
	                        'sql'  			=> 'sql'
	                        );
	
		// $this->load->helper('date_helper'); not required for v1
	}

	// --------------------------------------------------------------------

	function index($field_total = '5')
	{
		// setup fieldset count for view file
		if (!in_array($field_total,array('5', '10','20','40')))
		{
			$field_total = 5;
		}
		
		// make this available to my callback function
		$this->field_total = $field_total;
			
		$fields_array = array('view_field_name1','view_field_name2','view_field_name3');
		
		for($counter=1; $field_total >= $counter; $counter++)
		{
			if ($counter != 1) // better to do it this way round as this statement will be fullfilled more than the one below
			{
				$this->form_validation->set_rules("view_field_label{$counter}",'form field label','trim|xss_clean');       
			}
			else
			{
				// the first field always needs to be required i.e. we need to have at least one field in our form
				$this->form_validation->set_rules("view_field_label{$counter}",'form field label','trim|required|xss_clean');
			}
			
			$this->form_validation->set_rules("view_field_name{$counter}",'form field name',"trim|requiredif[view_field_label{$counter}]|callback_no_match[{$counter}]|xss_clean");
			$this->form_validation->set_rules("view_field_type{$counter}",'form field type',"trim|requiredif[view_field_label{$counter}]|xss_clean");
			$this->form_validation->set_rules("db_field_type{$counter}",'db field type',"trim|requiredif[view_field_label{$counter}]|xss_clean");
			$this->form_validation->set_rules("db_field_length_value{$counter}",'db field length',"trim|requiredif[view_field_label{$counter}]|xss_clean");
			$this->form_validation->set_rules('validation_rules'.$counter.'[]','validation rules','trim|xss_clean');
		}
			
		$this->form_validation->set_error_delimiters('<div class="error">Error: ', '</div>');
		
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			if (DB)
			{
				$data['form_count'] = $this->formigniter_model->get_form_count();
			}
			
			$data['field_total'] = $field_total;
			
			if (!empty($_POST))
			{
				$data['form_error'] = TRUE;
			}
			else
			{
				$data['form_error'] = FALSE;
			}
			$this->build_page('initial_form','Easy form generator for the CodeIgniter framework', $data);
		}
		else // passed validation proceed to second page
		{
			$model = $this->build_model($field_total);
			$view = $this->build_view($field_total);
			$controller = $this->build_controller($field_total);
			$sql =  $this->build_sql($field_total);
			
			if ($view == FALSE || $controller == FALSE || $model == FALSE || $sql == FALSE) // not correct syntax
			{
				// something went wrong when trying to build the form
				log_message('error', "The form was not built. There was an error with one of the build_() functions. Probably caused by total fields variable not being set");
				$this->session->set_flashdata('error', 'Wow! There was a problem igniting your form. It would be great if you could let me know what happened. Thanks.');
				redirect('formigniter');
			}
		        
		        // write to files mvc + sql
		        
				// we need something unique to build the file directory. unix timestamp seemed like a good choice
				$id = time(); 
				
				if (DB)
				{
					// Comment this out for a local version running without a database
					// as it is probably undesirable to store statistics
					$id = $this->formigniter_model->save_stats();
              	                
					if ($id == FALSE)
					{
						// something went wrong saving date to the database
						// fall back onto the id as time() option
						log_message('error', "An id was not returned from save_stats() model function. Likely to becaused by problem inserting into the db");
						$id = time(); 
					}
              	}               
				$data['id'] = $id;
				$data['error'] = FALSE;
				$error_msg = 'Oopps there was problem. FormIgniter was unable to write the files to the directory forms/ at the base of your codeigniter installation. You can copy and paste your code but will not be able to download. Please create the directory forms and make sure it is write enable (CHMOD 777).';
              	                
				if (!@mkdir("./forms/{$id}/",0777))
				{
					log_message('error', "failed to make directory ./forms/{$id}/");
					$data['error'] = $error_msg;
				}
				else
				{
					// loop to save all the files to disk - considered using a db but this makes things more portable 
					// and easier for a user to install
					
					foreach($this->files as $key => $value)
					{
						if ( ! write_file("./forms/{$id}/{$value}.php", ${$key}))
						{
							log_message('error', "failed to write file ./forms/{$id}/{$value}/");
							$data['error'] = $error_msg;
							break;
						}
					}
				}
              	                
                        // make the variables available to the view file		
			$data['view'] = $view;
			$data['controller'] = $controller;
			$data['model'] = $model;
			$data['sql'] = $sql;
			
			$data['count'] = FALSE;
			
			if (DB)
			{
				$data['count'] = $this->formigniter_model->get_form_count();
			}
			$this->build_page('built_form','Your form', $data);
		}
	}

	// --------------------------------------------------------------------

	function download($id = NULL, $file = 'all')
	{ 
		if ($id == NULL)
		{
			log_message('error', "download requested for a blank id");
			redirect('formigniter');
		}

		if (!is_dir("./forms/{$id}/"))
		{
			log_message('error', "directory ./forms/{$id}/ doesn't exit. Checked from download function ");
			redirect('formigniter');
		}
	
		if (in_array($file,array('model','view','controller','sql')))
		{
			$file_path = "./forms/{$id}/".$this->files["{$file}"].".php";
                  
			if (!is_file($file_path))
			{
				log_message('error', "directory ./forms/{$id}/ doesn't exit. Checked from download function ");
				redirect('formigniter');
			}
			
			$data = file_get_contents($file_path); // Read the file's contents
			$name = $this->files["{$file}"].".php";
			
			force_download($name, $data);
		}

		// handle all separately with as I need to zip
		if ($file == 'all') // this if statement isn't actually required but it provides clarity
		{
			$dir_path = "./forms/{$id}/";
                        
			$this->read_dir($dir_path); 
			
			$this->zip->download('formigniter-myform-'.date('d-m-Y').'.zip');
		}
	}

	// --------------------------------------------------------------------

   /** 
    * function build_view()
    *
    * write view file
    * @access private
    * @param $field_total - integar
    * @return string
    *
    */
	private function build_view($field_total = NULL)
	{
	      if ($field_total == NULL)
	      {
	              return FALSE;
	      }  
	
	    $view = '<?php // Change the css classes to suit your needs    

$attributes = array(\'class\' => \'\', \'id\' => \'\');
echo form_open(\''.$this->controllername.'\', $attributes); ?>
';

                for($counter=1; $field_total >= $counter; $counter++)
        		{
        			$maxlength = NULL; // reset this variable

					// only build on fields that have data entered. 
					//Due to the requiredif rule if the first field is set the the others must be

        	        if (set_value("view_field_label{$counter}") == NULL)
        	        {
        		        continue; 	// move onto next iteration of the loop
        	        }

        	        $field_label = set_value("view_field_label{$counter}");
        	        $field_name = set_value("view_field_name{$counter}");
					$field_type = set_value("view_field_type{$counter}");
		
		if ($field_type != 'checkbox') // checkbox appears to the left of the checkbox so I can't add now for a checkbox
		{
		
			$view .= <<<EOT

<p>
        <label for="{$field_name}">{$field_label}
EOT;
		
		} else {
			$view .= <<<EOT

<p>
	
EOT;
		}
                        // set a friendly variable name
                        $validation_rules = NULL;

						if (isset($_POST["validation_rules{$counter}"]))
						{
							$validation_rules = $_POST["validation_rules{$counter}"];
						}   
						
						// Not sure why the below does not work - 05/19/2011
						//set_value('validation_rules'.$counter.'[]'); 
						
                        if (is_array($validation_rules))
                        {       
                                // rules have been selected for this fieldset
                              	foreach($validation_rules as $key => $value)
                                {
                                        if($value == 'required')
                                        {
                                                $view .= ' <span class="required">*</span>';
                                        }
                                }
                        }
                
                
                        switch($field_type)
                        {
                        
                        // Some consideration has gone into how these should be implemented
                        // I came to the conclusion that it should just setup a mere framework
                        // and leave helpful comments for the developer
                        // Formigniter is meant to have a minimium amount of features. 
                        // It sets up the parts of the form that are repitive then gets the hell out
                        // of the way.
                        
                        // This approach maintains these aims/goals
                        
						case('textarea'):
		
		$view .= "</label>
	<?php echo form_error('$field_name'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => '$field_name', 'rows' => '5', 'cols' => '80', 'value' => set_value('$field_name') ) )?>
</p>";
						break;
						
                        case('radio'):
                        
		$view .= '</label>
        <?php echo form_error(\''.$field_name.'\'); ?>
        <br />
                <?php // Change or Add the radio values/labels/css classes to suit your needs ?>
                <input id="'.$field_name.'" name="'.$field_name.'" type="radio" class="" value="option1" <?php echo $this->form_validation->set_radio(\''.$field_name.'\', \'option1\'); ?> />
        		<label for="'.$field_name.'" class="">Radio option 1</label>

        		<input id="'.$field_name.'" name="'.$field_name.'" type="radio" class="" value="option2" <?php echo $this->form_validation->set_radio(\''.$field_name.'\', \'option2\'); ?> />
        		<label for="'.$field_name.'" class="">Radio option 2</label>
</p>

';
                        break;                        
                        
                        case('select'):
                        // decided to use ci form helper here as I think it makes selects/dropdowns a lot easier
                        $view .= <<<EOT
</label>
        <?php echo form_error('{$field_name}'); ?>
        
        <?php // Change the values in this array to populate your dropdown as required ?>
        
EOT;
                         $view .= '<?php $options = array(';

                         $view .= '
                                                  \'\'  => \'Please Select\',
                                                  \'example_value1\'    => \'example option 1\'
                                                ); ?>

        <br /><?php echo form_dropdown(\''.$field_name.'\', $options, set_value(\''.$field_name.'\'))?>
</p>                                             
                        ';
                        break;
                        
                        case('checkbox'):

                        $view .= <<<EOT

        <?php echo form_error('{$field_name}'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="{$field_name}" name="{$field_name}" value="enter_value_here" class="" <?php echo set_checkbox('{$field_name}', 'enter_value_here'); ?>> 
                   
	<label for="{$field_name}">{$field_label}</label>
</p> 
EOT;
                        break;
                         
                       	case('input'):
						case('password'):
                        default: // input.. added bit of error detection setting select as default
						
						if ($field_type == 'input')
						{
							$type = 'text';
						}
						else
						{
							$type = 'password';
						}
						if (set_value("db_field_length_value{$counter}") != NULL)
						{
							$maxlength = 'maxlength="'.set_value("db_field_length_value{$counter}").'"';
						}
						
                        $view .= <<<EOT
</label>
        <?php echo form_error('{$field_name}'); ?>
        <br /><input id="{$field_name}" type="{$type}" name="{$field_name}" {$maxlength} value="<?php echo set_value('{$field_name}'); ?>"  />
</p>

EOT;

                        break;
                        
		        } // end switch
                        
                            
                } // end for loop
		$view .= <<<EOT


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>

EOT;
        return $view;

	}

	// --------------------------------------------------------------------

   /** 
    * function build_controller()
    *
    * write view file
    * @access private
    * @param $field_total - integar
    * @return string
 	*
	*/

	private function build_controller($field_total = NULL)
	{
	      if ($field_total == NULL)
	      {
	              return FALSE;
	      }

              $controller = '<?php

class '.ucfirst($this->controllername).' extends CI_Controller {
               
	function __construct()
	{
 		parent::__construct();
		$this->load->library(\'form_validation\');
		$this->load->database();
		$this->load->helper(\'form\');
		$this->load->helper(\'url\');
		$this->load->model(\''.$this->modelname.'\');
	}	
	function index()
	{';


		// loop to set form validation rules
		$last_field = 0;
		for($counter=1; $field_total >= $counter; $counter++)
		{
			// only build on fields that have data entered. 
	
			//Due to the requiredif rule if the first field is set the the others must be
	
			if (set_value("view_field_label{$counter}") == NULL)
			{
				continue; 	// move onto next iteration of the loop
			}
			// we set this variable as it will be used to place the comma after the last item to build the insert db array
			$last_field = $counter;
			
			$controller .= '			
		$this->form_validation->set_rules(\''.set_value("view_field_name{$counter}").'\', \''.set_value("view_field_label{$counter}").'\', \'';
			
    		// set a friendly variable name
    		$validation_rules = NULL;

			if (isset($_POST["validation_rules{$counter}"]))
			{
				$validation_rules = $_POST["validation_rules{$counter}"];
			}   
			
			// Not sure why the below does not work - 05/19/2011
			// set_value('validation_rules'.$counter.'[]');
            
            // rules have been selected for this fieldset
            $rule_counter = 0;

            if (is_array($validation_rules))
            {       
				// add rules such as trim|required|xss_clean
				foreach($validation_rules as $key => $value)
				{
					if ($rule_counter > 0)
					{
						$controller .= '|';
					}
				
					$controller .= $value;
					$rule_counter++;
				}
            }
			
			if (set_value("db_field_length_value{$counter}") != NULL)
			{
				if ($rule_counter > 0)
				{
					$controller .= '|';
				}

				$controller .= 'max_length['.set_value("db_field_length_value{$counter}").']';
			}
			
			$controller .= "');";
		}
	
		$controller .= '
			
		$this->form_validation->set_error_delimiters(\'<br /><span class="error">\', \'</span>\');
	
		if ($this->form_validation->run() == FALSE) // validation hasn\'t been passed
		{
			$this->load->view(\''.$this->formname.'_view\');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			
			$form_data = array(';
				
		// loop to build form data array
		for($counter=1; $field_total >= $counter; $counter++)
		{
			//Due to the requiredif rule if the first field is set the the others must be
			if (set_value("view_field_label{$counter}") == NULL)
			{
				continue; 	// move onto next iteration of the loop
			}
			
			$controller .= '
					       	\''.set_value("view_field_name{$counter}").'\' => set_value(\''.set_value("view_field_name{$counter}").'\')';
			
			if ($counter != $last_field)
			{
				// add the comma in
				$controller .= ',';
			}
		}
	
		$controller .= '
						);';
		
		$controller .= '
					
			// run insert model to write data to db
		
			if ($this->'.$this->modelname.'->SaveForm($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				redirect(\''.$this->controllername.'/success\');   // or whatever logic needs to occur
			}
			else
			{
			echo \'An error occurred saving your information. Please try again later\';
			// Or whatever error handling is necessary
			}
		}
	}
	function success()
	{
			echo \'this form has been successfully submitted with all validation being passed. All messages or logic here. Please note
			sessions have not been used and would need to be added in to suit your app\';
	}
}
?>';
		return $controller;            
	}

	// --------------------------------------------------------------------

   /** 
    * function build_controller()
    *
    * write view file
    * @access private
    * @param $field_total - integar
    * @return string
    */

	private function build_model($field_total = NULL)
	{
		if ($field_total == NULL)
		{
			return FALSE;
		}
		$model = '<?php

class '.ucfirst($this->modelname).' extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------

      /** 
       * function SaveForm()
       *
       * insert form data
       * @param $form_data - array
       * @return Bool - TRUE or FALSE
       */

	function SaveForm($form_data)
	{
		$this->db->insert(\''.$this->tablename.'\', $form_data);
		
		if ($this->db->affected_rows() == \'1\')
		{
			return TRUE;
		}
		
		return FALSE;
	}
}
?>';
		return $model;
	}
	
	// --------------------------------------------------------------------
       
   /** 
    * function build_sql()
    *
    * write view file
    * @access private
    * @param $field_total - integar
    * @return string
    */

	private function build_sql($field_total = NULL)
	{
		if ($field_total == NULL)
		{
			return FALSE;
		}
		
		$sql = 'CREATE TABLE IF NOT EXISTS  `'.$this->tablename.'` (
 id int(40) NOT NULL auto_increment,';
		
		for($counter=1; $field_total >= $counter; $counter++)
		{
			//Due to the requiredif rule if the first field is set the the others must be
			if (set_value("view_field_label{$counter}") == NULL)
			{
				continue; 	// move onto next iteration of the loop
			}

		$sql .= '
 '.set_value("view_field_name{$counter}").' '.set_value("db_field_type{$counter}");
		
			if (!in_array(set_value("db_field_type{$counter}"), array('TEXT', 'DATETIME'))) // There are no doubt more types where a value/length isn't possible - needs investigating
			{
				$sql .= '('.set_value("db_field_length_value{$counter}").')';
			}
		

		$sql .= ' NOT NULL,';
		
		}
		
		$sql .= '
 PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		
		return $sql;
		
		
		// ip_address varchar(16) DEFAULT '0' NOT NULL,
		// user_agent varchar(50) NOT NULL,
		// last_activity int(10) unsigned DEFAULT 0 NOT NULL,
		// user_data text NOT NULL,
	}
	
	// --------------------------------------------------------------------
	
   	/** 
   	* function build_page()
   	*
   	* write view file
   	* @access private
   	* @param $page - string
   	* @param $title - string
   	* @param $data - string
   	* @return string
   	*/

	private function build_page($page, $title,$data = null)
	{
		$data['title'] = $title;
		$data['page'] = $page;
		$this->load->vars($data);
		$this->load->view('formigniter/template');
	}
    
	// --------------------------------------------------------------------
	
	/** Custom Form Validation Callback Rule
	 *
	 * Checks that one field doesn't match all the others.
	 * This code is not really portable. Would of been nice to create a rule that accepted an array
	 *
	 * @access	public
	 * @param	string
	 * @param	fields array
	 * @return	bool
	 */

	function no_match($str, $fieldno)
	{		
		for($counter=1; $this->field_total >= $counter; $counter++)
		{
			// nothing has been entered into this field so we don't need to check
			// or the field being checked is the same as the field we are checking from
			if ($_POST["view_field_name$counter"] == '' || $fieldno == $counter) 			
			{
				continue;				
			}
			
			if ($str == $_POST["view_field_name{$counter}"])
			{
				$this->form_validation->set_message('no_match', "Field names must be unique!");
				return FALSE;
			}
		}
		
		return TRUE;
	}

	// --------------------------------------------------------------------
	
   	/**
   	* Makes directory, returns TRUE if exists or made
   	*
   	* @param string $pathname The directory path.
   	* @return boolean returns TRUE if exists or made or FALSE on failure.
   	* http://uk2.php.net/manual/en/function.mkdir.php#81656
   	*/

   	private function mkdir_recursive($pathname, $mode)
   	{
		is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
		return is_dir($pathname) || @mkdir($pathname, $mode);
   	}
   
	// --------------------------------------------------------------------

    /**
     * Read a directory and add it to the zip.
     *
     * This is a customised version of the standard zip library function
     * The directory structure is removed and a readmefile is included if it exists
     * 
     * This function recursively reads a folder and everything it contains (including
     * sub-folders) and creates a zip based on it.  Whatever directory structure
     * is in the original file path will be recreated in the zip file.
     *
     * @access	public
     * @param	string	path to source
     * @return	bool
     */	

	private function read_dir($path)
	{
		if ($fp = @opendir($path))
		{
			while (FALSE !== ($file = readdir($fp)))
        	{
				if (@is_dir($path.$file) && substr($file, 0, 1) != '.')
				{
					$this->read_dir($path.$file."/");
        		}
				elseif (substr($file, 0, 1) != ".")
        		{
					if (FALSE !== ($data = file_get_contents($path.$file)))
        			{
						// removed this as I don't want the full path structure in my zip					
						// $this->add_data(str_replace("\\", "/", $path).$file, $data);
				        
						$this->zip->add_data($file, $data);
        			}
        		}
        	}
    
            // I have not included a readme on the source download
            // but this is used on my live server at http://toomanytabs.com/formigniter
            // to distribute to users who have built a form online
            $readme_path = './documents/formigniter_readme.txt';

            if (is_file($readme_path))
            {
				$data = file_get_contents($readme_path);
				$this->zip->add_data($readme_path, $data);  
            }

            return TRUE;
        }
	}

	// --------------------------------------------------------------------
}
?>