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

      $this->db->select('category_name');
      $cats = $this->db->get('categories')->result();
      $catList = [];
      foreach ($cats as $cat) {
          $catList[] = $cat->category_name;
      }

      return $catList;


    }

    public function getCategories(){

      $cats = $this->db->get('categories')->result();
      return $cats;


    }

     public function insecat(){
      /*$categories = [["2","accessories","397"],
      ["3","barcode_scanners","18342"],
      ["4","barcode_printers","18335"],
      ["5","cables","169"],
      ["6","cable_accessories","9995"],
      ["7","card_readers","6664"],
      ["8","carrying_cases","398"],
      ["9","cartridges","173"],
      ["10","cases","6659"],
      ["11","cooling_pads","11849"],
      ["12","copiers","154"],
      ["13","cpu","6660"],
      ["14","desktops","158"],
      ["15","docking_stations","0"],
      ["16","external_hard_drives","6164"],
      ["17","fans","6662"],
      ["18","firewalls","151"],
      ["19","flash_drives","6665"],
      ["20","gaming_chairs","16816"],
      ["21","graphic_cards","6657"],
      ["22","hoverboards","8302"],
      ["23","ip_phones","3914"],
      ["24","ip_cameras","3915"],
      ["25","ip_cards","3919"],
      ["26","ip_gateways","3918"],
      ["27","ip_pbx","3917"],
      ["28","keyboard_mouse","399"],
      ["29","laptops","214"],
      ["30","memories","6661"],
      ["31","monitors","606"],
      ["32","motherboards","6656"],
      ["33","multifunction_printers","145"],
      ["34","multiplugs","17222"],
      ["35","nas","14064"],
      ["36","optical_drives","6663"],
      ["37","papers","13899"],
      ["38","patch_panels","168"],
      ["39","plotters","18828"],
      ["40","powerlines","12686"],
      ["41","power_bank","6230"],
      ["42","power_supplies","6658"],
      ["43","printers","144"],
      ["44","printer_drums","10767"],
      ["45","printer_fusers","10768"],
      ["46","printer_belts","10766"],
      ["47","projectors","15332"],
      ["48","racks","6432"],
      ["49","routers","165"],
      ["50","sata_hard_drives","8443"],
      ["51","scanners","18446"],
      ["52","servers","147"],
      ["53","server_controllers","15317"],
      ["54","server_cpu","15281"],
      ["55","server_hard_drives","14515"],
      ["56","server_memories","15121"],
      ["57","server_power_supplies","15158"],
      ["58","smartphones","141"],
      ["59","software","146"],
      ["60","speakers","6343"],
      ["61","ssd","6251"],
      ["62","switches","167"],
      ["63","tablets","140"],
      ["64","toners","174"],
      ["65","tv","10925"],
      ["66","ups","152"],
      ["67","video_conference","18639"]];

      foreach ($categories as $category) {
        $this->db->where('id', $category[0]);
        $this->db->update('categories', ['woo_category_id'=>$category[2]]);
      }*/
    //   $array = array('accessories', 'barcode_scanners', 'barcode_printers', 'cables','cable_accessories','card_readers','carrying_cases','cartridges','cases','cooling_pads','copiers','cpu',
    //     'desktops','external_hard_drives','fans','firewalls','flash_drives','gaming_chairs','graphic_cards','hoverboards','ip_phones',
    //         'ip_cameras','ip_cards','ip_gateways','ip_pbx', 'keyboard_mouse','laptops','memories','monitors','motherboards','multifunction_printers','multiplugs','nas',
    //     'optical_drives','papers','patch_panels','plotters','powerlines','power_bank','power_supplies','printers','printer_drums','printer_fusers','printer_belts','projectors','racks',
    //   'routers','sata_hard_drives','scanners','servers','server_controllers','server_cpu','server_hard_drives','server_memories','server_power_supplies',
    //   'smartphones','software','speakers','ssd','switches','tablets','toners', 'tv', 'ups', 'video_conference');
    //
    //   foreach ($array as $cat) {
    //     $data = [
    //       'category_name' => $cat
    //     ];
    //     $this->db->insert('categories', $data);
    //   }
    //   return $array;
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
