<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cross_sales extends MX_Controller {

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
	function __construct()
    {
    	$this->load->model('cross_sales_model');
    	
    }

	 function auto_laptop($sku, $brand, $size, $price){
    	
    	return $this->cross_sales_model->auto_laptop($sku, $brand, $size, $price);
    }







}




?> 