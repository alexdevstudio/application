<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Descriptions_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function store_chars($table, $data, $id=false)
    {
        if($table == 'specific')
            $table = 'char_blocks_specific';
        else if($table == 'basic')
            $table = 'char_blocks_basic';

    	//$id = $this->char_exists($table, $data);

    	if(!$id)
    	{
        	$insert = $this->db->insert($table, $data);
    	}
    	else
    	{
    		$this->db->where('id', $id);
    		$insert = $this->db->update($table, $data);
    	}
    	return $insert;
	}

	/*function char_exists($table, $data)
	{
        if($table == 'specific')
            $table = 'char_blocks_specific';
        else if($table == 'basic')
            $table = 'char_blocks_basic';

		$this->db->where('category',$data['category']);
		$this->db->where('char',$data['char']);
		$this->db->where('char_spec',$data['char_spec']);

		$query = $this->db->get($table);
		if ($query->num_rows() > 0){
			return $query->row()->id;
		}
		else{
		    return false;
		}
	}*/

    function get_chars_by_id($table, $id)
    {
        if($table == 'specific')
            $table = 'char_blocks_specific';
        else if($table == 'basic')
            $table = 'char_blocks_basic';

        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array(); 

    }



}