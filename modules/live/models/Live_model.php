<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Live_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    private function xml($url){

		$xml=simplexml_load_file($url) or die("Error: Cannot create object");

    	return $xml;
    }


    public function oktabit(){

		if($xml = $this->xml("http://www.oktabit.gr/times_pelatwn/prices_xml.asp?customercode=012348&logi=evansmour")){
			if($desc_xml = $this->xml("http://www.oktabit.gr/times_pelatwn/perigrafes_xml.asp?customercode=012348&logi=evansmour")){
				if($char_xml = $this->xml("http://www.oktabit.gr/times_pelatwn/chars_xml.asp?customercode=012348&logi=evansmour")){
 
					$images = array();

					$this->updateLive('oktabit');
				
					
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

		foreach($xml->children() as $product) {
			
			set_time_limit(50);
			//Rename categories for ETD.gr

			$cat = (string) trim($product->category);
			$sc = trim((string)$product->subcategory);

			$c = $cat;
			

			$brand = (string) trim($product->brand);
			if ($brand == 'VERO' || $brand == 'MAXBALL' || $brand == 'LEXMARK')
			{
				$cat = '';
			}

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
				case 'Networking':
					if($sc == 'Routers')
					{
						$c = 'routers';
					}
					elseif($sc == 'Switches'){
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
					if($sc == 'Color Laser Printers' || $sc == 'Inkjet Printers' || $sc == 'B-W Laser Printers' )
					{
						$c = 'printers';	
					}
					elseif($sc == 'Multifunction Printers'){
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
							
					}
					elseif($sc == 'Sata Hard Drives' )
					{
						$c = 'sata_hard_drives';
							
					}
					elseif($sc == 'Solid State Drives' )
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
				$net_price = (string) trim($net_price);

				$recycle_tax = str_replace(",", ".", $product->anakykl);
				$pn = (string) trim($product->part_no);
				$pn = ($pn == '') ? (string) trim($product->code): $pn;

				$description = "";
				$brand = (string) trim($product->brand);
				$title = (string) trim($product->titlos);
				$product_url = "";
				$code = (string) trim($product->code);

				// Get The Description
				foreach($desc_xml->children() as $perigrafes) {
					$okt_desc_code = (string) trim($perigrafes->code);
					
					if($code == $okt_desc_code)
					{
						$description = strip_tags((string) (trim($perigrafes->perigrafi)));
						continue;
					}
				}

				// Get the characteristics
				$chars_array = $this->addProductChars($c, $code, $char_xml);

				//1. Live

				if($this->checkLiveProduct($pn, $net_price)){

					$availability = $this->makeAvailability((string) trim($product->availability), 'oktabit');

					if(!$availability){
						$f=0;
						continue;
					}

					$live = array(
						'category'=>$c,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability,
						'recycle_tax'=>(string) trim($recycle_tax) ,
						'supplier' =>'oktabit',
						'status' => 'published',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', 'oktabit');
					$this->db->delete('live', $live);

					$this->db->insert('live', $live);

					unset($live);
				}



				//Array for categories table

				$okt_product = array(
					'category' => $c,
					'product_number' => $pn,
					'description' => $description,
					'brand' => $brand,
					'title' => $title,
					'product_url' => $product_url,
					'code' => $code,
					'net_price'=>$net_price
				);

				//2. New products for charateristics tables that load Sku module

				
				$this->addProduct ($okt_product, $chars_array, $newProducts, $i, $f, 'oktabit');
			}//if $c!=$cat
				
			$f=0;
		
		}//end foreach

		//echo '<p style="color:green">Finished</p><br />';
		//redirect(base_url());
		
		if (!empty($newProducts))//Send Mail Check
		{
			$message='<h2>Νέα προϊόντα</h2>';
		
			foreach ($newProducts as $newProduct){
				$message .= $newProduct['Κατηγορία'].' : '.$newProduct['Νέα προϊόντα'].' (<a href="'.base_url().'/extract/xml/'.$newProduct['Κατηγορία'].'/new">Προβολή XML)</a><br>';
			}

			Modules::run('emails/send','Νέα προϊόντα',$message);
		}

		 echo "Finnished Oktabit";

    }

    public function logicom(){

    	$this->load->view('upload_xml', array('error' => ' ' ));
    }

	public function import_logicom($path){

		if($xml = $this->xml($path)){
			
			$images = array();
			
			$this->updateLive('logicom');

		}

		$newProducts = array();
		$i=0;
		$k=0;
		//$f=0;
		

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
					break;
				case 'Consumables- / Laserjet Cartridges- / HP':
					$c = 'toners';
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


					$availability = $this->makeAvailability((string) trim($prd["Availability"]), 'logicom');

						if(!$availability){
							continue;
						}


	    		$pn = (string) trim($prd["SKU"]);
	    		$title = (string) trim($prd["Name"]);
	    		$description = (string) trim($prd["Description"]);
	    		$net_price = $prd["Price"];
	    		$availability = $availability;
	    		$recycle_tax = $prd["RT"];
	    		$ManufacturerList = (string) trim($product->Details->ManufacturerList->attributes()->{'Name'});


	    		if(strpos($title, 'Ent') && $c == 'multifunction_printers'){
	    			continue;	
	    		}
	    		 

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
						'supplier' =>'logicom',
						'status' => 'published',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', 'logicom');
					//$this->db->delete('live', $live);

					//$this->db->insert('live', $live);
					
					$this->db->replace('live', $live);
					unset($live);
				}

				$log_product = array(
					'category' => $c,
					'product_number' => $pn,
					'description' => $description,
					'brand' => $brand,
					'title' => $title,
					'product_url' => $product_url,
					'net_price'=>$net_price
				);

				//2. New products for charateristics tables that load Sku module
				//$this->AddProduct ($c, $pn, $description, $brand, $title, $product_url, $newProducts, $i, $imageUrl, 'logicom');
				$this->addProduct ($log_product, array(), $newProducts, $i, $imageUrl, 'logicom');

			}//if $c==$cat
		
		}//end foreach

		//echo '<p style="color:green">Finished</p><br />';
		//redirect(base_url());

		if (!empty($newProducts))//Send Mail Check
		{
			$message='<h2>Νέα προϊόντα</h2>';
		
			foreach ($newProducts as $newProduct){
				$message .= $newProduct['Κατηγορία'].' : '.$newProduct['Νέα προϊόντα'].' (<a href="'.base_url().'/extract/xml/'.$newProduct['Κατηγορία'].'/new">Προβολή XML)</a><br>';
			}

			Modules::run('emails/send','Νέα προϊόντα',$message);
		}

		echo "Finnished updating Logicom-Enet.";
		
    }


    private function checkLiveProduct($pn, $price){

    	$query = $this->db->get_where('live', array('product_number' => $pn), 1, 0);

    	if($query->num_rows()>0){

    		foreach ($query->result() as $row)
				{
					$price = (float) $price;
				        
				    if($row->net_price >= $price ){
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


	/*private function insertProductCategory($table, $pn, $data){

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
    }//private function insertProductCategory(){*/


    private function addProduct($product, $chars_array, $newProducts, $i, $f ,$supplier){

    	$c = $product['category'];

		$skuArray = array(
			'category'=> $c,
			'product_number' => $product['product_number'],
			);

				
		// Only For Update

		/*
		if($c == "carrying_cases" || $c == "external_hard_drives" ||
				 $c == "sata_hard_drives" || $c == "ssd" || $c == "speakers" || 
				 $c == "power_banks" || $c == "keyboard_mouse"  || 
				 $c == "routers"  || $c == "switches"  || $c == "laptops"  || $c == "tablets"  || $c == "smartphones" )
		{
			
		if(!$chars_array){
			$chars_array=array();
}


			$shipping_class = Modules::run('categories/makeShippingClass', $chars_array, $c);
			$chars_array = array_merge($chars_array, array("shipping_class"=>$shipping_class));
			$chars_array = array_merge($chars_array, array("description"=>$product['description']));
			$this->updateProduct($c, $chars_array, $product['product_number']);
			
		}
		*/
		// End Only for Update
		
		$newSku = Modules::run('sku/checkSku',$skuArray);
		$sku = $newSku['sku'];





		if($newSku['new']){

			
			if($c == 'cartridges' || $c == 'toners'){
				$shipping_class = Modules::run('categories/makeShippingClass', $chars_array, $c);

				$categoryData = array(
				'brand'=> $product['brand'], 
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $shipping_class

				);
			}elseif($c == 'printers' || $c == 'multifunction_printers'){
				
				$price = array('price'=>$product['net_price']);
				
				$shipping_class  = Modules::run('categories/makeShippingClass',$price, $c, true);
				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'description'=> strip_tags($product['description']),
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $shipping_class
				);
			}
			else
			{
				$shipping_class = '';
				if($c == "carrying_cases" || $c == "external_hard_drives" ||
				 $c == "sata_hard_drives" || $c == "ssd" || $c == "speakers" || 
				 $c == "power_banks" || $c == "keyboard_mouse"  || 
				 $c == "routers"  || $c == "switches"  || $c == "laptops"  || $c == "tablets"  || $c == "smartphones")		
				$shipping_class = Modules::run('categories/makeShippingClass', $chars_array, $c);


				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'description'=> strip_tags($product['description']),
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $shipping_class
				);
				if($chars_array)
				{
					$categoryData = array_merge($categoryData, $chars_array);
				}

				
				
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
			$this->AddProductImages($product, $f, $supplier, $sku);
			
		}//if($sku = Modules::run('sku/checkSku',$skuArray)){


		else
		{

			if($c == 'printers' || $c == 'multifunction_printers'){
				$price = array('price'=>(float)$product['net_price']);
				//print_r($price['price']);
				$shipping_class  = Modules::run('categories/makeShippingClass',$price, $c, true);
					
				
					

				$this->db->set('shipping_class',$shipping_class);
				$this->db->where('sku',$sku);
				$this->db->update($c);

		}	
    }


    }


    private function AddProductImages($product, $f, $supplier, $sku){

    	if ($supplier == 'oktabit')
    	{
    		while($f < 5){ // because we want to get max 5 images

				if($f=="0"){
					$tail='';
				}else{
					$tail = '_'.$f;
				}

				$imageData = array(
					'src' => "http://oktabit.gr/images/photos/".$product['code']."".$tail.".jpg",
					'sku' => $sku ,
					'brand' => $product['brand'] ,
					'part_number' => $product['product_number'] ,
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
					'brand' => $product['brand'] ,
					'part_number' => $product['product_number'] ,
					'tail' => ""
				);
				
				Modules::run('images/getImage',$imageData);
    	}
    }


    private function addProductChars($category, $product_code, $char_xml){

    	$is_found = false;

		if ($category == 'carrying_cases'){

			$chars_array = array(
				'type'=>"",
				'size'=>"",
				'material'=>"",
				'colour'=>"",
				'dimensions'=>""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
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
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'power_bank'){

			$chars_array = array(
				'battery_capacity' => "",
				'recharge_time' => "",
				'charging_output' => "",
				'dimensions' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Χωρητικότητα Μπαταρίας':
							$chars_array['battery_capacity']=$chars_value;
							break;
						case 'Χρόνος επαναφόρτισης ως το 90-100% (ώρες)':
							$chars_array['recharge_time']=$chars_value;
							break;
						case 'Έξοδος φόρτισης':
							$chars_array['charging_output']=$chars_value;
							break;
						case 'Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)':
							$chars_array['dimensions']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'routers'){

			$chars_array = array(
				'type' => "",
				'wireless' => "",
				'line_type' => "",
				'lan_ports' => "",
				'wan_ports' => "",
				'supported_protocols' => "",
				'vpn' => "",
				'removable_antenna' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Τύπος':
							$chars_array['type']=$chars_value;
							break;
						case 'Wireless':
							$chars_array['wireless']=$chars_value;
							break;
						case 'Τύπος γραμμής':
							$chars_array['line_type']=$chars_value;
							break;
						case 'Θύρες LAN':
							$chars_array['lan_ports']=$chars_value;
							break;
						case 'Θύρες WAN':
							$chars_array['wan_ports']=$chars_value;
							break;
						case 'Υποστηριζόμενα πρωτόκολλα':
							$chars_array['supported_protocols']=$chars_value;
							break;
						case 'VPN':
							$chars_array['vpn']=$chars_value;
							break;
						case 'Αποσπώμενη κεραία':
							$chars_array['removable_antenna']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'switches'){

			$chars_array = array(
				'ports' => "",
				'network_speed' => "",
				'sfp_ports' => "",
				'manage' => "",
				'poe' => "",
				'layer' => "",
				'rackmount' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Θύρες':
							$chars_array['ports']=$chars_value;
							break;
						case 'Ταχύτητα Δικτύου':
							$chars_array['network_speed']=$chars_value;
							break;
						case 'SFP θύρες':
							$chars_array['sfp_ports']=$chars_value;
							break;
						case 'Manage':
							$chars_array['manage']=$chars_value;
							break;
						case 'POE':
							$chars_array['poe']=$chars_value;
							break;
						case 'Layer':
							$chars_array['layer']=$chars_value;
							break;
						case 'RackMount':
							$chars_array['rackmount']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'speakers'){

			$chars_array = array(
				'type' => "",
				'watt' => "",
				'dolby_digital_decoding' => "",
				'headphones_input' => "",
				'input' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Τύπος ηχείων':
							$chars_array['type']=$chars_value;
							break;
						case 'Ισχύς Watt (RMS)':
							$chars_array['watt']=$chars_value;
							break;
						case 'Dolby Digital Decoding':
							$chars_array['dolby_digital_decoding']=$chars_value;
							break;
						case 'Είσοδος ακουστικών':
							$chars_array['headphones_input']=$chars_value;
							break;
						case 'Είσοδος':
							$chars_array['input']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'external_hard_drives'){

			$chars_array = array(
				'capacity' => "",
				'connection' => "",
				'size' => "",
				'cache' => "",
				'rpm' => "",
				'colour' => "",
				'weight' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Χωρητικότητα':
							$chars_array['capacity']=$chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection']=$chars_value;
							break;
						case 'Διάσταση':
							$chars_array['size']=$chars_value;
							break;
						case 'Cache':
							$chars_array['cache']=$chars_value;
							break;
						case 'Στροφές λειτουργίας':
							$chars_array['rpm']=$chars_value;
							break;
						case 'Χρώμα':
							$chars_array['colour']=$chars_value;
							break;
						case 'Βάρος (γραμμάρια)':
							$chars_array['weight']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'sata_hard_drives'){

			$chars_array = array(
				'capacity' => "",
				'connection' => "",
				'size' => "",
				'cache' => "",
				'rpm' => "",
				'colour' => "",
				'weight' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Χωρητικότητα':
							$chars_array['capacity']=$chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection']=$chars_value;
							break;
						case 'Διάσταση':
							$chars_array['size']=$chars_value;
							break;
						case 'Cache':
							$chars_array['cache']=$chars_value;
							break;
						case 'Στροφές λειτουργίας':
							$chars_array['rpm']=$chars_value;
							break;
						case 'Συσκευασία':
							$chars_array['packaging']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'ssd'){

			$chars_array = array(
				'capacity_from_to' => "",
				'capacity' => "",
				'connection' => "",
				'size' => "",
				'read_speed' => "",
				'write_speed' => "",
				'manufacture_technology' => "",
				'mtbf' => "",
				'packaging' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Χωρητικότητα (Από εώς)':
							$chars_array['capacity_from_to']=$chars_value;
							break;
						case 'Χωρητικότητα':
							$chars_array['capacity']=$chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection']=$chars_value;
							break;
						case 'Διάσταση':
							$chars_array['size']=$chars_value;
							break;
						case 'Ταχύτητα ανάγνωσης (MB/s)':
							$chars_array['read_speed']=$chars_value;
							break;
						case 'Ταχύτητα εγγραφής (MB/s)':
							$chars_array['write_speed']=$chars_value;
							break;
						case 'Τεχνολογία κατασκευής':
							$chars_array['manufacture_technology']=$chars_value;
							break;
						case 'MTBF (ώρες)':
							$chars_array['mtbf']=$chars_value;
							break;
						case 'Συσκευασία':
							$chars_array['packaging']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'keyboard_mouse'){

			$chars_array = array(
				'type' => "",
				'usage' => "",
				'connection' => "",
				'dpi' => "",
				'mouse_buttons' => "",
				'technology' => "",
				'language' => "",
				'programmable_buttons' => "",
				'multimedia_buttons' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Τύπος συσκευής':
							$chars_array['type']=$chars_value;
							break;
						case 'Χρήση':
							$chars_array['usage']=$chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection']=$chars_value;
							break;
						case 'Ανάλυση DPI':
							$chars_array['dpi']=$chars_value;
							break;
						case 'Κουμπιά mouse':
							$chars_array['mouse_buttons']=$chars_value;
							break;
						case 'Τεχνολογία':
							$chars_array['technology']=$chars_value;
							break;
						case 'Γλώσσα πληκτρολογίου':
							$chars_array['language']=$chars_value;
							break;
						case 'Προγραμματιζόμενα πλήκτρα':
							$chars_array['programmable_buttons']=$chars_value;
							break;
						case 'Multimedia πλήκτρα':
							$chars_array['multimedia_buttons']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}

    }

    private function updateProduct($table, $data, $product_number){

    	$this->db->where('product_number', $product_number);
    	$this->db->set($data);
    	$this->db->update($table);
    	echo 'update '.$product_number.' in table: '.$table.'<br>';
    }


    private function makeAvailability($availability, $supplier){

    	if($supplier == 'oktabit'){

/*
1.Διαθεσιμο 
2.Οριακο (λιγα κομματια ακομη στις αποθηκες Oktabit) 
3 και 4   Αναμενεται    (μηδεν διαθεσιμο αλλα το εχουμε παραγγειλει στον προμηθευτη) 
5. Κατοπιν παραγγελιας (μηδεν διαθεσιμο και το παραγγελνουμε στον προμηθευτη εφοσον το παραγγειλετε σε εμας) 
6. NOT AVAILABLE
*/

	    	switch ($availability) {
	    		case '1':
	    			$av = 'Κατόπιν παραγγελίας σε 1 εργάσιμη';
	    			break;
	    		case '2':
	    			$av = 'Κατόπιν παραγγελίας σε 1 εργάσιμη';
	    			break;
	    		case '3':
	    			$av = 'Αναμονή παραλαβής';
	    			break;
	    		case '4':
	    			$av = 'Αναμονή παραλαβής';
	    			break;
	    		case '5':
	    			$av = 'Κατόπιν παραγγελίας χωρίς διαθεσιμότητα';
	    			break;
	    		case '6':
	    			return false;
	    			break;

	    		
	    		default:
	    			return false;
	    			break;
	    	}

	    	return $av;

    	}elseif($supplier == 'logicom'){

    		switch ($availability) {
    			case 'Normal':
    			case 'Limited':
    				
    				$av="Κατόπιν παραγγελίας σε 1 εργάσιμη";
    				
    				break;
    			case 'PreOrder':
    				$av="Αναμονή παραλαβής";
    				break;
    			default:
    				return false;
    				break;
    		}

    		return $av;

    	}
    }

	private function updateLive($supplier){

		
		//1. Delete old trashed entries
		
		$this->db->where('supplier', $supplier);
		$this->db->where('status', 'trash');
		$this->db->where('delete_flag', 3);
		$this->db->delete('live');


		//2. Update all entries to set status=trash and delete_flag +1

		$this->db->where('supplier', $supplier);
		$products = $this->db->get('live');

			foreach ($products->result() as $row) {

				$id = $row->id;
				$flag = $row->delete_flag;
				$flag ++;

				$this->db->where('id', $id);
				$this->db->set('delete_flag',$flag);
				$this->db->set('status','trash');
				$this->db->update('live');

				
				
			}


		return true;
	}






}

?>