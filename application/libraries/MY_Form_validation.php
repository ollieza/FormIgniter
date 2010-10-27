<?php
class MY_Form_validation extends CI_Form_validation {

        function MY_Form_validation()
        {
			parent::CI_Form_validation();
        }
        
        // --------------------------------------------------------------------
        /**
       	 * Required if another field has a value (related fields)
       	 *
       	 * @access	public
       	 * @param	string
       	 * @return	bool
       	 */
       	 
        function requiredif($str, $field)
       	{
	        if ($_POST[$field] == '')
			{
				return TRUE;	// the related form is blank			
			}
			
			// the related form is set proceed with normal required
			
			if ( ! is_array($str)) 
     		{
     			return (trim($str) == '') ? FALSE : TRUE;
     		}
     		else
     		{
     			return ( ! empty($str));
     		}
        }
}
?>