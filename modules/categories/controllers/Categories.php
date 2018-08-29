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

 public function index($table = null){

   if($table)
   return $this->show_category_products($table);

   $this->load->model('categories_model');

   $data['title'] = 'Κατηγορίες προϊόντων';
   $data['tables'] = Modules::run('categories/fullCategoriesArray');
   $data['product_count'] = $this->categories_model->countProducts($data['tables']);

   $this->load->view('templates/header',$data);
   $this->load->view('categories');
   $this->load->view('templates/footer');
 }

 public function products($table){
   $this->load->model('categories_model');
   $data['title'] = 'Προϊόντα κατηγορίας: '.$table;
   $data['tables'] = Modules::run('categories/fullCategoriesArray');
   $data['products'] = $this->categories_model->getProductsByCategory($table);
   $data['table'] = $table;


   $this->load->view('templates/header',$data);
   $this->load->view('products');
   $this->load->view('templates/footer');;
 }


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
    	$array = array('cable_accessories','copiers','desktops','docking_stations','laptops','printers', 'multifunction_printers',
    		'monitors','servers','ups','tablets','smartphones','software',
			'external_hard_drives','keyboard_mouse','ip_cameras','ip_phones','ip_cards','ip_gateways','ip_pbx','printer_drums',
			'printer_fuser','printer_belts','tv','firewalls','gaming_chairs');

    	return $array;
    }

    public function fullCategoriesArray(){
    	$array = array('accessories','cables','cable_accessories','card_readers','carrying_cases','cartridges','cases','cooling_pads','copiers','cpu',
    		'desktops','docking_stations','external_hard_drives','fans','firewalls','flash_drives','gaming_chairs','graphic_cards','hoverboards','ip_phones',
            'ip_cameras','ip_cards','ip_gateways','ip_pbx', 'keyboard_mouse','laptops','memories','monitors','motherboards','multifunction_printers','multiplugs','nas',
    		'optical_drives','papers','patch_panels','powerlines','power_bank','power_supplies','printers','printer_drums','printer_fusers','printer_belts','projectors','racks',
			'routers','sata_hard_drives','servers','server_controllers','server_cpu','server_hard_drives','server_memories','server_power_supplies',
			'smartphones','software','speakers','ssd','switches','tablets','toners', 'tv', 'ups');

    	return $array;
    }


     public function updateItem($c, $xml){

     	$this->load->model('categories_model');
     	echo $this->categories_model->updateItem($c, $xml);

     }

     public function makeShippingClass($data, $cat, $dynamic = null){

     	$this->load->model('categories_model');
     	return $this->categories_model->makeShippingClass($data, $cat, $dynamic);

     }

     function getWeight($shipping_class){
     	$this->load->model('categories_model');
     	return $this->categories_model->getWeight($shipping_class);
     }

     function volumeWeight($dimensions){

        $this->load->model('categories_model');
        return $this->categories_model->volumeWeight($dimensions);
     }

     function updateweight(){
     	$cats = $this->fullCategoriesArray();

     	foreach ($cats as $cat) {

     		if($cat=='monitors'  ){

     			$products = Modules::run('crud/get', $cat);
     			$products = $products->result_array();
     			foreach ($products as $product) {
     				$sku = $product['sku'];
     				$volumetric_weight = $this->getWeight($product['shipping_class']);
     				Modules::run('crud/update', $cat, array('sku'=>$sku), array('volumetric_weight'=>$volumetric_weight));
     			}
            }

     	}
     		//echo "$cat: OK<br />";
     }



     public function shippingByWeight($vweight){
        $this->load->model('categories_model');
        return $this->categories_model->shippingByWeight($vweight);
     }
}
?>
