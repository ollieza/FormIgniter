<?php
/**
 * FormIgniter v0.8 
 *
 * An easy form generator for the CodeIgniter framework
 *
 * @package		FormIgniter
 * @author		Ollie Rattue - orattue[at]toomanytabs.com
 * @license		http://www.opensource.org/licenses/mit-license.php
 * @link		http://formigniter.org
 */

class Formigniter_model extends Model {

    function __construct()
    {
        parent::Model();
    }
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
}
?>