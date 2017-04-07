<?php

defined('BASEPATH') OR exit('No direct script access allowed');

 
class Keelpno_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function weight(){
    	return Modules::run("crud/problematic", array("volumetric_weight"=>''), $this->problematicCategory(1), false);
    }

    public function problematicCategory($id){
    	$categories = array(1=>'Δέν έχει οριστεί το βάρος');
		return $categories[$id];
    }

    public function categories($cat = null){
        if($cat){
            $this->db->where('category', $cat);
        }
        return $this->db->get('service_categories');
    }

}