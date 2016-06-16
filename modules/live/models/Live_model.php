<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Live_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    private function xml($url){


    	//$str = (string) file_get_contents($url);

		$xml=simplexml_load_file($url) or die("Error: Cannot create object");

    	return $xml;

    }



    public function oktabit(){

		if($xml = $this->xml("http://www.oktabit.gr/times_pelatwn/prices_xml.asp?customercode=012348&logi=evansmour")){
			if($desc_xml = $this->xml("http://www.oktabit.gr/times_pelatwn/perigrafes_xml.asp?customercode=012348&logi=evansmour")){
				if($char_xml = $this->xml("http://www.oktabit.gr/times_pelatwn/chars_xml.asp?customercode=012348&logi=evansmour")){

					$images = array();
				
					$this->db->where('supplier','oktabit');
					$this->db->delete('live');
				}
				else{
					die("Characteristics XML from Oktabit can not be loaded.");
				}
			}
			else{
				die("Description XML from Oktabit can not be loaded.");
			}
		}else{
			die("XML from Oktabit can not be loaded.");
		}

		$newProducts = array();
		$i=0;
		$f=0;
		//echo '<p style="color:red">Running Oktabit Insertion</p><br />';

		foreach($xml->children() as $product) {
			
			set_time_limit(50);
			//Rename categories for ETD.gr

			$cat = (string) $product->category;
			$sc = trim((string)$product->subcategory);

			$c = $cat;
			//$catArray = array();
			//$catExists = FALSE;

			switch ($cat) {
				case 'Mobile Computers':
					if (strpos($sc, 'Notebooks up to') !== false)
					{
						$c = 'laptops';
					}elseif($sc == 'Carrying Cases'){
						$c = 'carrying_cases';
					}


					
					break;
				case 'Computers':

					if($sc == 'Advanced PC' || $sc == 'All In One PC' || $sc == 'Business PC' || $sc == 'Workstations')
					{
						$c = 'desktops';
							
					}

					break;
					case 'Energy':
					
					if($sc == 'Power Bank')
					{
						$c = 'power_bank';
							
					}
					break;
					case 'Monitors':
					if($sc == 'LCD-TFT PC Monitors')
					{
						$c = 'monitors';
							
					}

					
					break;
					case 'Energy':
					
					if($sc == 'Power Bank')
					{
						$c = 'power_bank';
							
					}
					break;
					case 'Networking':
					
					if($sc == 'Routers')
					{
						$c = 'routers';
							
					}elseif($sc == 'Switches'){
						$c = 'switches';
					}
					break;
					case 'Power Protection':
					
					if($sc == 'Line Interactive UPS' || $sc == 'On Line UPS'  || $sc == 'Standby UPS')
					{
						$c = 'ups';
							
					}
					break;
					case 'Printers':
					
					if($sc == 'Color Laser Printers' || $sc == 'Inkjet Printers' )
					{
						$c = 'printers';
							
					}elseif($sc == 'Multifunction Printers'){
						$c = 'multifunction_printers';
					}
					break;
					case 'Servers':
					
					if($sc == 'Rackmount Systems' )
					{
						$c = 'servers';
							
					}
					break;
					case 'Entertainment':
					
					if($sc == 'Speakers' )
					{
						$c = 'speakers';
							
					}
					break;
					case 'Storage':
					
					if($sc == 'External Hard Drives' )
					{
						$c = 'external_hard_drives';
							
					}elseif($sc == 'Sata Hard Drives' )
					{
						$c = 'sata_hard_drives';
							
					}elseif($sc == 'Solid State Drives' )
					{
						$c = 'ssd';
							
					}
					break;
					case 'Cases-Peripherals':
					
					if($sc == 'Combo' || $sc == 'Keyboard' || $sc == 'Mouse')
					{
						$c = 'keyboard_mouse';
							
					}
					break;



				default:
					$c = $cat;
					break;
			}

			if($c!=$cat){

				$net_price = str_replace(",", ".", $product->timi);
				$recycle_tax = str_replace(",", ".", $product->anakykl);
				$pn = (string) trim($product->part_no);
				$pn = ($pn == '') ? (string) trim($product->code): $pn;
				$description = "";
				$brand = (string) trim($product->brand);
				$title = (string) trim($product->titlos);
				$product_url = "";
				//$code = (string) trim($product->code);

				$okt_prd_code = (string) trim($product->code);

				foreach($desc_xml->children() as $perigrafes) {
					$okt_desc_code = (string) trim($perigrafes->code);
					
					if($okt_prd_code == $okt_desc_code)
					{
						$description = (string) trim($perigrafes->perigrafi);
					}
				}
				if ($c == 'carrying_cases'){

					$chars_array = array(
						'brand'=>"",
						'type'=>"",
						'size'=>"",
						'material'=>"",
						'colour'=>"",
						'dimensions'=>""
						);

					foreach($char_xml->children() as $chars){

						$okt_chars_code = (string) trim($chars->product[0]);

						if($okt_prd_code == $okt_chars_code)
						{
							echo "in<br><br><br>";
							$chars_title = (string) trim($chars->atribute[0]);
							$chars_value = (string) trim($chars->value[0]);

							switch ($chars_title) {
								case 'Κατασκευαστής':
									$chars_array['brand'] = $chars_value;
									break;
								case 'Τύπος':
									$chars_array['type']=$chars_value;
									break;
								case 'Μέγεθος οθόνης':
									$chars_array['size']=$chars_value;
									break;
								case 'Υλικό κατασκευής':
									$chars_array['material']=$chars_value;
									break;
								case 'Χρώμα':
									$chars_array['colour']=$chars_value;
									break;
								case 'Διαστάσεις (πλάτος x ύψος x πάχος, σε mm)':
									$chars_array['dimensions']=$chars_value;
									break;
								default :

									break;
							}
							print_r($chars_array);
						}
					}
				}
				//1. Live

				if($this->checkLiveProduct($pn, $net_price)){

					$live = array(
						'category'=>(string) trim($c) ,
						'product_number'=>$pn ,
						'net_price'=>(string) trim($net_price) ,
						'availability'=>(string) trim($product->availability) ,
						'recycle_tax'=>(string) trim($recycle_tax) ,
						'supplier' =>(string) 'oktabit'
						);

					$this->db->insert('live', $live);
					unset($live);
				}

				//2. New products for charateristics tables that load Sku module
				$this->AddProduct ($c, $pn, $description, $brand, $title, $product_url, $newProducts, $i, $f, 'oktabit');
			}//if $c==$cat
				
			$f=0;
		
		}//end foreach

		echo '<p style="color:green">Finished</p><br />';
		//redirect('https://etd.gr/xml/');
		
		if (!empty($newProducts))//Send Mail Check
		{
			$message='<h2>Νέα προϊόντα</h2>';
		
			foreach ($newProducts as $newProduct){
				$message .= $newProduct['Κατηγορία'].' : '.$newProduct['Νέα προϊόντα'].' (<a href="https://etd.gr/xml/extract/xml/'.$newProduct['Κατηγορία'].'/new">Προβολή XML)</a><br>';
			}

			Modules::run('emails/send','Νέα προϊόντα',$message);
		}

    }

    public function logicom(){

    	$this->load->view('upload_xml', array('error' => ' ' ));
    }

	public function import_logicom($path){

		if($xml = $this->xml($path)){
			
			$images = array();
			
			$this->db->where('supplier','logicom');
			$this->db->delete('live');

		}

		$newProducts = array();
		$i=0;
		//$f=0;
		//echo '<p style="color:red">Running Logicom Insertion</p><br />';

		foreach($xml->children()->children()->children() as $product) {
			
			set_time_limit(50);
			//Rename categories for ETD.gr

			$c = $cat = $product->Details->CategoryList->attributes()->{'Name'};

			switch ($cat) {

				/*case 'Computers- / Accessories- / Bags and Cases':
					$c = 'carrying_cases';
					break;*/
				case 'Computers- / Computers- / All In One':
				case 'Computers- / Computers- / Desktop':
				case 'Computers- / Computers- / Mini PC':
				case 'Computers- / Computers- / Workstations':
					$c = 'desktops';
					break;
				case 'Computers- / Computers- / Notebooks':
					$c = 'laptops';
					break;
				case 'Computers- / Computers- / Tablet':
					$c = 'tablets';
					break;
				case 'Computers- / Servers- / Server':
					$c = 'servers';
					break;
				/*case 'Computers- / Software  /  Security- / Antivirus Solutions':
				case 'Computers- / Software  /  Security- / Applications':
				case 'Computers- / Software  /  Security- / Applications':
				case 'Computers- / Software  /  Security- / Operating Systems':
				case 'Computers- / Software  /  Security- / Server Software':
					$c = 'software';
					break;*/
				case 'Consumables- / Inkjet Cartridges - / HP':
					$c = 'cartridges';
					//<ManufacturerList Name="CONSUMABLES- / XEROX- / LASERJET CARTRIDGE" />
					break;
				case 'Consumables- / Laserjet Cartridges- / HP':
					$c = 'toners';
					//<ManufacturerList Name="CONSUMABLES- / XEROX- / LASERJET CARTRIDGE" />
					break;
				/*case 'Networking- / Routing':
					$c = 'routers';
					break;
				case 'Networking- / Switching':
					$c = 'switches';
					break;
				case 'Peripherals- / Accessories- / Keyboards':
				case 'Peripherals- / Accessories- / Mouse':
					$c = 'keyboard_mouse';
					break;*/
				case 'Peripherals- / Power Protection- / UPS':
					$c = 'ups';
					break;
				case 'Peripherals- / Printers Office- / Multifunctions':
					$c = 'multifunction_printers';
					break;
				case 'Peripherals- / Printers Office- / Printers':
					$c = 'printers';
					break;
				case 'Telecoms GSM- / SmartPhones':
					$c = 'smartphones';
					break;
				case strpos($cat,'Peripherals- / Monitors'):
					$c = 'monitors';
					break;
				default:
					$c = $cat;
					break;
			}

			if($c!=$cat){

	    		$prd = $product->attributes();   
	    		$pn = (string) trim($prd["SKU"]);
	    		$title = (string) trim($prd["Name"]);
	    		$description = (string) trim($prd["Description"]);
	    		$net_price = $prd["Price"];
	    		$availability = (string) trim($prd["Availability"]);
	    		$recycle_tax = $prd["RT"];
	    		$ManufacturerList = (string) trim($product->Details->ManufacturerList->attributes()->{'Name'});
	    		if($c == 'cartridges' || $c == 'toners')
	    		{
	    			$brand = explode('- /',trim($ManufacturerList))[1];
	    			
	    			if (strpos($brand, 'SUPPLIES'))
	    				$brand = explode('SUPPLIES',trim($brand))[0];
	    		}
	    		else
	    		{
	    			$brand = explode('- /',trim($ManufacturerList))[0];
	    		}
	    		
	    		// IMAGE
	    		$imageUrl = $product->Details->Images->Image[2]->attributes()->{'URL'};
	    		if($imageUrl == "")
	    			$imageUrl = $product->Details->Images->Image[1]->attributes()->{'URL'};
	    		
	    		// PRODUCT URL
	    		$product_url = $product->Details->ProductURL->attributes()->{'URL'};
				
				//1. Live

				if($this->checkLiveProduct($pn, $net_price)){

					$live = array(
						'category'=>$c ,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability ,
						'recycle_tax'=>$recycle_tax ,
						'supplier' =>(string) 'logicom'
						);

					$this->db->insert('live', $live);
					unset($live);
				}

				//2. New products for charateristics tables that load Sku module
				$this->AddProduct ($c, $pn, $description, $brand, $title, $product_url, $newProducts, $i, $imageUrl, 'logicom');

			}//if $c==$cat
		
		}//end foreach

		//echo '<p style="color:green">Finished</p><br />';
		redirect('https://etd.gr/xml/');

		if (!empty($newProducts))//Send Mail Check
		{
			$message='<h2>Νέα προϊόντα</h2>';
		
			foreach ($newProducts as $newProduct){
				$message .= $newProduct['Κατηγορία'].' : '.$newProduct['Νέα προϊόντα'].' (<a href="https://etd.gr/xml/extract/xml/'.$newProduct['Κατηγορία'].'/new">Προβολή XML)</a><br>';
			}

			Modules::run('emails/send','Νέα προϊόντα',$message);
		}
		
    }

    private function checkLiveProduct($pn, $price){

    	$query = $this->db->get_where('live', array('product_number' => $pn), 1, 0);

    	if($query->num_rows()>0){

    		foreach ($query->result() as $row)
				{
					$price = (float) $price;
				        
				    if($row->net_price > $price ){
				        $this->db->where('id',$row->id);
				        $this->db->delete('live');
				        return true;
				    }else{
				        return false;
				    }

				}

    	}else{
    		return true;
    	}
	
	}//private function checkLiveProduct($pn, $price){



	private function insertProductCategory($table, $pn, $data){

		echo $table.'<br />';

		if ($this->db->table_exists($table))
		{

			$query = $this->db->get_where($table, $pn);

	    	if($query->num_rows()<1){

	    		$this->db->insert($table, $data);
	    		return true;
	    		
	    	}

	    	return false;

		} 	
		return false;
    }//private function insertProductCategory(){



    private function AddProduct($c, $pn, $description, $brand, $title, $product_url, $newProducts, $i, $f ,$supplier){
    							
		$skuArray = array(
			'category'=> $c,
			'product_number' => $pn,
			);

		if($sku = Modules::run('sku/checkSku',$skuArray)){

			if($c == 'cartridges' || $c == 'toners'){
				$categoryData = array(
				'brand'=> $brand, 
				'sku'=> $sku,
				'product_number'=> $pn,
				'title'=> $title,
				'supplier_product_url'=> $product_url
				);
			}
			else
			{
				$categoryData = array(
				'brand'=> $brand, 
				'sku'=> $sku,
				'product_number'=> $pn,
				'title'=> $title,
				'description'=> $description,
				'supplier_product_url'=> $product_url
				);
			}

			if(Modules::run("categories/insert", $c, $categoryData)){

				if(isset($newProducts[$i]['Κατηγορία'])){

					if($newProducts[$i]['Κατηγορία'] == $c){

					$newProducts[$i]['Νέα προϊόντα']=$newProducts[$i]['Νέα προϊόντα']+1;

					}else{

						$i++;
					
						$newProducts[$i]['Κατηγορία'] = $c;
						$newProducts[$i]['Νέα προϊόντα'] = 1;

					}

				}else{
					$newProducts[$i]['Κατηγορία'] = $c;
					$newProducts[$i]['Νέα προϊόντα'] = 1;
				}
			}else{
				echo 'issue';
			}

			
			//3. Add Product Images
			$this->AddProductImages($f, $supplier, $sku, $brand, $pn);
			
		}//if($sku = Modules::run('sku/checkSku',$skuArray)){
    }

    private function AddProductImages($f, $supplier, $sku, $brand, $pn){

    	if ($supplier == 'oktabit')
    	{
    		while($f < 5){ // because we want to get max 5 images

				if($f=="0"){
					$tail='';
				}else{
					$tail = '_'.$f;
				}

				$imageData = array(
					'src' => "http://oktabit.gr/images/photos/".$product->code."".$tail.".jpg",
					'sku' => $sku ,
					'brand' => $brand ,
					'part_number' => $pn ,
					'tail' => $tail
				);
				//echo Modules::run('images/imageUrlExists',$imageData['src']);
				//echo '<br/>';
				if(!$exists=Modules::run('images/getImage',$imageData)){
					//echo Modules::run('images/imageUrlExists',$imageData['src']);
					$f=5;
				}else{
					
					$f++;
				}
			}
    	}
    	else if ($supplier == 'logicom')
    	{
    		$imageData = array(
					'src' => $f,
					'sku' => $sku ,
					'brand' => $brand ,
					'part_number' => $pn ,
					'tail' => ""
				);
				
				Modules::run('images/getImage',$imageData);
    	}
    }


}