<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Insert extends MX_Controller {

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
	 
 
	public function index()//$s stands for supplier
	{	 
		$data['title'] = 'Εισαγωγή Προϊόντος';
		$this->load->view('templates/header' ,$data);
		$this->load->view('insert');
		$this->load->view('templates/footer');
	}

	public function doInsert(){

		if(!$_POST['cat']){
			echo 'no post';
			exit();
  
		}else{
			$cat = trim(strip_tags($_POST['cat']));
			$product_number = trim(strip_tags($_POST['product_number']));
			$title = trim(strip_tags($_POST['title']));
			$brand = trim(strip_tags($_POST['brand']));
			$description = trim(strip_tags($_POST['description']));

			if($cat == 'keyboard_mouse')
				$type = trim(strip_tags($_POST['type']));

			/*$image1=trim($_POST['image1']);
			$image2=trim($_POST['image2']);
			$image3=trim($_POST['image3']);
			$image4=trim($_POST['image4']);
			$image5=trim($_POST['image5']);*/

			$product = array(
				'category' => $cat,
				'product_number'=> $product_number,
				'title' => $title,
				'brand' => $brand,
				'description' => $description,
				'product_url'=>''
				);

			//Add to live table

			//if(Modules::run("live/checkLiveProduct", $product_number, $net_price,'etd')){

					$live = array(
						'category'=>$cat,
						'product_number'=>$product_number,
						'supplier' =>'etd',
						'status' => 'publish',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $product_number);
					$this->db->where('supplier', 'etd');
					$this->db->delete('live', $live);

					$this->db->insert('live', $live);

					unset($live);
				//}
			
			/*if($_POST['av']=='1' &&  is_numeric ( $net_price )){

				if(Modules::run("live/checkLiveProduct", $product_number, $net_price)){
				//if($this->checkLiveProduct($product_number, $net_price)){

					$live = array(
						'category'=>$cat,
						'product_number'=>$product_number ,
						'net_price'=>$net_price,
						'recycle_tax'=>0 ,
						'availability'=>$availability,
						'supplier' =>'etd',
						'status' => 'publish',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $product_number);
					$this->db->where('supplier', 'etd');
					$this->db->delete('live', $live);

					$this->db->insert('live', $live);

					unset($live);
				}else{
				 	echo "Το προϊόν υπάρχει ήδη στη βάση. Και έχει χαμηλότερη τιμή.";
				}
			}*/

			//Create SKU if Not existing, add to categories table, upload images

				//$images = array($image1,$image2,$image3,$image4,$image5);

				$chars = array();


				if($cat == 'keyboard_mouse')
					$chars['type']= $type;


				$newProduct = Modules::run("live/addProduct", $product, $chars, $images , 'etd');
				
					echo 'ok';
		}
	}
}

?> 