<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MX_Controller {
 
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
	

	public function insert($c, $categoryData)
	{	
		$this->load->model('categories_model');

		if($this->categories_model->insertItem($c, $categoryData)){
			return true;
		}

		echo 'Function insert() was not successfull: table name'.$c.'<br />';
		echo '<pre>';
		print_r($categoryData);
		return false;
 
	} 

	 public function categoriesArray(){
    	$array = array('desktops','laptops','printers', 'multifunction_printers',
    		'monitors','servers','ups','tablets','smartphones','software');

    	return $array;
    }

    public function fullCategoriesArray(){

    	$array = array('cables','card_readers','carrying_cases','cartridges',
    		'cases','cpu','desktops','external_hard_drives','fans','flash_drives','graphic_cards','keyboard_mouse',
    		'laptops','memories','monitors','motherboards','multifunction_printers','optical_drives','patch_panels',
    		'power_bank','power_supplies','printers','racks','routers','sata_hard_drives','servers','smartphones',
    		'software','speakers','ssd','switches','tablets','toners', 'ups', 'copiers','projectors');



    	return $array;
    }

 
     public function updateItem($c, $xml){

     	$this->load->model('categories_model');
     	echo $this->categories_model->updateItem($c, $xml);

     }

     public function makeShippingClass($data, $cat, $dynamic = null){

     	$this->load->model('categories_model');
     	echo $this->categories_model->makeShippingClass($data, $cat, $dynamic);

     }





}




?> 