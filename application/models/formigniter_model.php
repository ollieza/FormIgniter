<?php

/**
 * FormIgniter
 *
 * An easy form generator for the CodeIgniter framework
 * 
 * @package   FormIgniter
 * @version   0.8
 * @license   http://www.opensource.org/licenses/mit-license.php
 */

class Formigniter_model extends CI_Model 
{	
	function __construct()
    {
        parent::__construct();
    }

	// --------------------------------------------------------------------

    function get_form_count()
    {
		$this->db->select('count(id) count');
		$this->db->set('type','form');
		$query = $this->db->get('statistics');
          
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
		
		$row = $query->row();
		return $row->count;
    }

	// --------------------------------------------------------------------

    function save_stats()
    {
        $this->db->set('created_at', time()); 
        $this->db->insert('statistics');
                      
		if ($this->db->affected_rows() != '1')
		{
	        // log error
	        return FALSE;
		}
		return $this->db->insert_id();
    }

	// -------------------------------------------------------------------- 
}
?>
