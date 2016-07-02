<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud extends MX_Controller {

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
	

	public function update($table, $where, $data)//$s stands for supplier
	{	

		$this->load->model('crud_model');
		return $this->crud_model->update($table, $where, $data);
		
			

		
	}


	public function get($table, $where){

    	$this->load->model('crud_model');
		return $this->crud_model->get($table, $where);

    }

    public function insert($table, $data){

    	$this->load->model('crud_model');
		return $this->crud_model->insert($table, $data);

    	


    }



}




?> 