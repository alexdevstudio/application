<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Categories_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function insertItem($c, $categoryData){

    	$query = $this->db->get_where($c,array('product_number'=>$categoryData['product_number']));
    	if($query->num_rows()<1){


	    	if($this->db->insert($c, $categoryData)){
	    		return true;
	    	}
	    }
	    else
	    {
	    	

			$this->db->set('sku',$categoryData['sku']);
			$this->db->where('id',$query->row()->id);
			if($this->db->update($c)){

			$message = "Το SKU που προστέθηκε δεν ταιριάζει με το SKU που είναι ήδη
			 καταχωρημένο στο ERP για αυτό το Προϊόν:<br /> Κατηγορία: ".$c."<br />Παλιό SKU: ".$query->row()->sku."<br />
			 Τίτλος: ".$query->row()->title."<br/> <p style='color:red;'>Παρακαλούμε όπως προβείτε άμεσα στην αλλαγή του SKU στο ERP ή 
			 διαφορετικά αγνοείστε αυτό το μήνυμα.</p><br />Το νέο SKU είναι: ".$categoryData['sku'];
				
				Modules::run('emails/send','Διπλό SKU',$message);
				return true;
			}

	    }


    	return false;
 
    }


    public function updateItem($filename, $table){

$fa = 0;
    		$xml=simplexml_load_file("./files/uploads/".$filename) or die("Error: Cannot find uploaded XML file");
    		$message = '';
//$asd = 0;
    		foreach ($xml->children() as $product) {
    			$data=array();

    			foreach ($product as $key => $value) {
    				
    				
    				if($key != 'description' && $key!='shipping_class'){

    					$data[$key]=$value;

    				}


    			}
				


    			$sku = $data['sku'];
    				/*if($sku == '1313329'){
    					echo "<pre />";
    						print_r($data);
    						exit();
    				}*/
    			$data['new_item']=0;
    			if($table=="desktops" || $table == "monitors" || $table == "ups" ){
    			    $data['shipping_class'] = $this->makeShippingClass($data, $table);
    			}
    			$vw = $data['volumetric_weight'];

    			if($vw=='' || $vw == 0 ){

    				if($table == "printers" || $table == "multifunction_printers") {

    					$dimensions = $data['dimensions'];
						$data['volumetric_weight'] = $this->volumeWeight($dimensions);
    			   		

						if(!$data['volumetric_weight']){
    						
    						$data['new_item']=1;

						}
    				}
    				else
    				{

    					$data['volumetric_weight'] = $this->getWeight($data['shipping_class']);
    				}
    			}


    			if($table == "printers" || $table == "multifunction_printers"){
    				$data['shipping_class'] = $this->makeShippingClass($data, $table);
    			}


				
				
				$this->db->where('sku', $sku );
				$query = $this->db->get($table);

				if($query->num_rows()>0){
					$this->db->where('sku',$sku);
					$this->db->update($table, $data);
					//unset($data);

				}else{
					$message.="SKU:$fa $sku δεν βρέθηκε στην κατηγορία $table \n";
					exit($message);
				}

				$fa++;
    		}

    		$message = 'Η ενημέρωση των '.$table.' ολοκληρώθηκε με επιτυχία.';
    		return $message;


    }

public function volumeWeight($dimensions){



	//String sanitize
	$dimensions = str_replace(' ','', $dimensions);
	$dimensions = str_replace('mm','', $dimensions);
	$dimensions = str_replace('.',',', $dimensions);
	$dimensions = str_replace('X','x', $dimensions);

	//Create an array
	$da = explode("x",$dimensions);
	
	if(count($da) != 3){

		return false;
	}

	$vweight = (((int) $da[0]/10+10)*((int) $da[1]/10+10)*((int) $da[2]/10+10))/5000;

	$vweight = ceil($vweight);
	

	return $vweight;
	





}

public function makeShippingClass($data, $cat, $dynamic = null){

	

		switch ($cat) {

			case 'printers':
			case 'multifunction_printers':
						
						$vweight = (int) $data['volumetric_weight'];



						if($vweight > 35)
							$shipping_class= 9974;
						elseif($vweight > 30)
							$shipping_class= 4682;
						elseif($vweight > 26)
							$shipping_class= 4681;
						elseif($vweight > 23)
							$shipping_class= 4680;
						elseif($vweight > 20)
							$shipping_class= 4679;
						elseif($vweight > 17)
							$shipping_class= 4678;
						elseif($vweight > 14)
							$shipping_class= 4654;
						elseif($vweight > 11)
							$shipping_class= 4653;
						elseif($vweight > 8)
							$shipping_class= 4652;
						elseif($vweight > 5)
							$shipping_class= 4651;
						else
							$shipping_class= 4650;
						break;

			case 'laptops':
					$shipping_class= 4636;
					break;
			case 'software':
					$shipping_class= 4644;
					break;
			case 'carrying_cases':
					$shipping_class= 4636;
					break;
			case 'desktops':
					$shipping_class= 4661;
					if($data['type']=='Mini Pc')
						$shipping_class= 4660;
					else
						$shipping_class= 4661;

					break;
			case 'power_bank':
					$shipping_class= 4663;
					break;
			case 'monitors':

					$size = (float) $data['screen_size'];
					$pn = $data['product_number'];
					if($data['brand']=='DELL' && $size >= 24 && $size <= 25 && (substr($pn, 0, 1) === 'U' || substr($pn, 0, 1) === 'u') ){
									
						$shipping_class= 9393;
						
					}
						
					else
					{
						if($size >= 42)
							$shipping_class= 4667;
						elseif($size >= 32)
							$shipping_class= 4666;
						elseif($size >= 25)
							$shipping_class= 4665;
						else
							$shipping_class= 4664;
					}
					break;

			case 'routers':
					$shipping_class= 4671;
					break;
			case 'switches':
					
					$ports=(int) $data['ports'];
					
					if( $ports >= 16)
						$shipping_class= 4636;
					else
						$shipping_class= 4671;
					break;
			case 'ups':
					$strength=(int) $data['strength'];
					
					if( $strength >= 3001)
						$shipping_class= 9974;
					elseif( $strength >= 1501)
						$shipping_class = 4669;
					else
						$shipping_class = 4661;
					break;
			
			case 'servers':
					$shipping_class= 4669;
					break;
			case 'speakers':
					$shipping_class= 4636;
					break;
			case 'external_hard_drives':
					$shipping_class= 4671;
					break;
			case 'sata_hard_drives':
					$shipping_class= 4662;
					break;
			case 'ssd':
					$shipping_class= 4672;
					break;
			case 'keyboard_mouse':
					if($data['type']=='Mouse')
						$shipping_class = 4671;
					else
						$shipping_class = 4636;
					break;
			case 'tablets':
					$shipping_class= 4662;
					break;
			case 'cartridges':
					$shipping_class= 4677;
					break;
			case 'toners':
					$title = $data['title'];
					if(strpos($title, 'Tri-Pack'))
						$shipping_class = 4686;
					elseif(strpos($title, 'Dual Pack') || strpos($title, '2-pack'))
						$shipping_class = 4676;
					else
						$shipping_class = 4675;
					break;
			case 'smartphones':
					$shipping_class= 4663;
					break;

			case 'cables':
					$shipping_class= 4644;
					break;
			case 'patch_panels':
					$shipping_class= 4675;
					break;
			case 'racks':
					$shipping_class= 9974;
					break;
			case 'optical_drives':
					$shipping_class = 4662;
					break;
			case 'card_readers':
					$shipping_class = 4671;
					break;
			case 'flash_drives':
					$shipping_class = 4644;
					break;
			case 'power_supplies':
					$shipping_class = 4636;
					break;
			case 'cases':
					$shipping_class = 4661;
					break;
			case 'fans':
					$shipping_class = 4671;
					break;
			case 'motherboards':
					$shipping_class = 4636;
					break;
			case 'graphic_cards':
					$shipping_class = 4662;
					break;
			case 'cpu':
					$shipping_class = 4636;
					break;
			case 'memories':
					$shipping_class = 4644;
					break;
			case 'hoverboards':
					$shipping_class = 4661;
					break;
			case 'ip_cards':
					$shipping_class = 4672;
					break;
			case 'ip_gateways':
					$shipping_class = 4636;
					break;
		    case 'ip_phones':
					$shipping_class = 4676;
					break;
			case 'ip_pbx':
					$shipping_class = 4636;
					break;
			default:
				return false;
				break;
	
			}
	
		
     
      return $shipping_class;
    }

    public function getWeight($shipping_class){
      $sc_array = array('4636' =>5,
						'4644' =>2,
						'4660' =>9,
						'4661' =>13,
						'4662' =>2,
						'4663' =>2,
						'4664' =>5,
						'4665' =>19,
						'4666' =>32,
						'4667' =>46,
						'4668' =>13,
						'4669' =>40,
						'4670' =>2,
						'4671' =>2,
						'4672' =>2,
						'4673' =>3,
						'4674' =>2,
						'4675' =>2,
						'4676' =>2,
						'4677' =>2,
						'4686' =>4,
						'9393' =>16
						);

    	return $sc_array[$shipping_class];
    }
   

}