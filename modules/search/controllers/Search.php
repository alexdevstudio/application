<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MX_Controller {

	



	

	public function index()
	{	
		
		$q = $_POST['search'];

		$this->load->model('search_model');
		$product = $this->search_model->getItemFromSku($q);

		

		if($product){
			
			$cat = $product['category'];
			$sku = $product['item']->row()->sku;
			
			if(isset($product['item']->row()->brand)){
				$brand = $product['item']->row()->brand;
			}else{
				$brand = $product['item']->row()->Brand;
			}
			if(isset($product['item']->row()->model)){
				$model = $product['item']->row()->model;
			}else{
				$model = '';
			}
			 if(isset($product['item']->row()->title)){
				$title = $product['item']->row()->title;
			 }else{
				$title = $product['item']->row()->Name;
			 }
			$pn = $product['item']->row()->product_number;

			$output='';

			if($model == ''){
				$name = $title;
			}else{
				$name = $model;
			}


			
			$output .= Modules::run("images/getFirstImage",$sku,true);
			$output .= "Κατηγορία: ".ucfirst($cat)."<br>";
			$output .= "Κατ/τής: ".$brand."<br>";
			$output .= "Όνομα: ".$name."<br><hr>";
			$output .= "SKU: ".$sku."<br>";
			$output .= "P/N: ".$pn."<hr>";

			$return = array();


			$return['output'] = $output;
			$return['link'] = $cat.'/'.$sku;

			echo json_encode($return);








		}else{
			echo 'Δεν βρέθηκε κάποιο προϊόν.';
		}

		
	}





}




?> 