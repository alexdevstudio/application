<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud extends MX_Controller {

	function __construct()
    {
        parent::__construct();

        $this->load->model('crud_model');
       
    }
	

	public function update($table, $where, $data)
	{	
		
		return $this->crud_model->update($table, $where, $data);
	}

	public function problematic($where, $problematic_type, $count)
	{

		return $this->crud_model->problematic($where, $problematic_type, $count);

	}

	public function delete($table, $where)
	{
        
        return $this->crud_model->delete($table, $where);
  	}
  	public function deleteWp($table, $where)
	{
        
        return $this->crud_model->deleteWp($table, $where);
  	}
	public function get($table, $where=null, $order_by=null, $limit=null){

    	
		return $this->crud_model->get($table, $where, $order_by, $limit);
    }
    public function getWp($table, $where=null){

    	
		return $this->crud_model->getWp($table, $where);
    }

    public function insert($table, $data){

    	
		return $this->crud_model->insert($table, $data);


    }

    public function insertWp($table, $data){

    	
		return $this->crud_model->insertWp($table, $data);


    }

	public function join($table, $join_table, $join)
	{
		
		return $this->crud_model->join($table, $join_table, $join);
	}

	public function updateWp($table, $where, $data)
	{	
		
		return $this->crud_model->updateWp($table, $where, $data);
	}

	


}




?> 