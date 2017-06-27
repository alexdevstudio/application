<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extract extends MX_Controller {

	function __construct()
    {
        parent::__construct();

        $this->load->model('extract_model');
       
    }

	public function xml($table=null,$all=false)
	{	
		//if(!$table){redirect('https://etd.gr/','refresh');}

		
		
		if($this->extract_model->xml($table,$all)){

			if(!$all || $all=='new')
				echo '<a class="btn btn-block btn-success btn-md"
			 target="_blank" href="'.base_url().'/files/'.$table.'_new_items.xml" download>Λήψη</a>';
			 else
			 	echo '<a class="btn btn-block btn-success btn-md"
			 target="_blank" href="'.base_url().'/files/'.$table.'_all_items.xml" download>Λήψη</a>';
		}else{
			echo 'Σφάλμα';
		}
	}


	public function allImport($table, $numrows, $imagesOnly, $skus=null){

		 
		 echo $this->extract_model->allImport($table, $numrows, $imagesOnly, $skus);
	}


	public function getPrice($net, $recycle, $category){
		
		return $this->extract_model->priceTax($net, $recycle, $category);
		
	}


	public function updateWp($product,$customVar=null){
		
		return $this->extract_model->updateWp($product, $customVar);
		
	}

}
?> 