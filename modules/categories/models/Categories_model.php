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


    		$xml=simplexml_load_file("./files/uploads/".$filename) or die("Error: Cannot find uploaded XML file");
    		$message = '';
$asd = 0;
    		foreach ($xml->children() as $product) {
    			$data=array();
    			foreach ($product as $key => $value) {
    				
    				
    				if($key != 'description' && $key!='shipping_class'){

    					$data[$key]=$value;

    				}


    			}
				

    			$sku = $data['sku'];

    			$data['new_item']=0;
    			if($table=="desktops" || $table == "monitors" || $table == "ups")
    			$data['shipping_class'] = $this->makeShippingClass($data, $table);
					
				$this->db->where('sku', $sku );
				$query = $this->db->get($table);

				if($query->num_rows()>0){
					$this->db->where('sku',$sku);
					$this->db->update($table, $data);

				}else{
					$message.="SKU: $sku δεν βρέθηκε στην κατηγορία $table \n";
					exit($message);
				}
    		}

    		$message = 'Η ενημέρωση των '.$table.' ολοκληρώθηκε με επιτυχία.';
    		echo $message;


    }



public function makeShippingClass($data, $cat, $dynamic = null){

	if($dynamic){
		
		switch ($cat) {

			case 'printers':
						
						$price = (float) $data['price'];
						if($price >= 600)
							$shipping_class= 4654;
						elseif($price >= 320)
							$shipping_class= 4653;
						elseif($price >= 200)
							$shipping_class= 4652;
						elseif($price >= 120)
							$shipping_class= 4651;
						else
							$shipping_class= 4650;
						break;
				
			case 'multifunction_printers':
					
						$price = (float) $data['price'];
						if($price >= 600)
							$shipping_class= 4682;
						elseif($price >= 320)
							$shipping_class= 4681;
						elseif($price >= 200)
							$shipping_class= 4680;
						elseif($price >= 120)
							$shipping_class= 4679;
						else
							$shipping_class= 4678;
						break;
				default:
						return false;
						break; 
			
		}								
	}else{

		switch ($cat) {

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
					if($size >= 42)
						$shipping_class= 4667;
					elseif($size >= 32)
						$shipping_class= 4666;
					elseif($size >= 25)
						$shipping_class= 4665;
					else
						$shipping_class= 4664;
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
						$shipping_class= 4682;
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
					$shipping_class= 4682;
					break;
			default:
				return false;
				break;
	
			}
	
		}
     
      return $shipping_class;
    }
   

}