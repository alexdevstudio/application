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


    public function updateItem($table, $categoryData){

///Continue here

    }




   

}