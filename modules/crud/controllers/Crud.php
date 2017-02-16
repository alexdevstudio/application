<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud extends MX_Controller {

<<<<<<< HEAD
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
=======
	function __construct()
    {
        parent::__construct();

        $this->load->model('crud_model');
       
    }
>>>>>>> parent of bcac9d7... commit
	

	public function update($table, $where, $data)
	{	
<<<<<<< HEAD
		$this->load->model('crud_model');
		return $this->crud_model->update($table, $where, $data);
	}


	public function delete($table, $where)
	{
        $this->load->model('crud_model');
=======
		
		return $this->crud_model->update($table, $where, $data);
	}

	public function problematic($where, $problematic_type, $count)
	{

		return $this->crud_model->problematic($where, $problematic_type, $count);

	}

	public function delete($table, $where)
	{
        
>>>>>>> parent of bcac9d7... commit
        return $this->crud_model->delete($table, $where);
  	}
  	public function deleteWp($table, $where)
	{
<<<<<<< HEAD
        $this->load->model('crud_model');
=======
        
>>>>>>> parent of bcac9d7... commit
        return $this->crud_model->deleteWp($table, $where);
  	}
	public function get($table, $where=null, $order_by=null, $limit=null){

<<<<<<< HEAD
    	$this->load->model('crud_model');
=======
    	
>>>>>>> parent of bcac9d7... commit
		return $this->crud_model->get($table, $where, $order_by, $limit);
    }
    public function getWp($table, $where=null){

<<<<<<< HEAD
    	$this->load->model('crud_model');
=======
    	
>>>>>>> parent of bcac9d7... commit
		return $this->crud_model->getWp($table, $where);
    }

    public function insert($table, $data){

<<<<<<< HEAD
    	$this->load->model('crud_model');
=======
    	
>>>>>>> parent of bcac9d7... commit
		return $this->crud_model->insert($table, $data);


    }

    public function insertWp($table, $data){

<<<<<<< HEAD
    	$this->load->model('crud_model');
=======
    	
>>>>>>> parent of bcac9d7... commit
		return $this->crud_model->insertWp($table, $data);


    }

	public function join($table, $join_table, $join)
	{
<<<<<<< HEAD
		$this->load->model('crud_model');
=======
		
>>>>>>> parent of bcac9d7... commit
		return $this->crud_model->join($table, $join_table, $join);
	}

	public function updateWp($table, $where, $data)
	{	
<<<<<<< HEAD
		$this->load->model('crud_model');
=======
		
>>>>>>> parent of bcac9d7... commit
		return $this->crud_model->updateWp($table, $where, $data);
	}

	


}




?> 