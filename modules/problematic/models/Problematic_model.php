<?php

defined('BASEPATH') OR exit('No direct script access allowed');

 
class Problematic_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function weight(){
    	return Modules::run("crud/problematicWeight", array("volumetric_weight"=>''), $this->problematicCategory(1), false);
    }

    public function problematicCategory($id){
        $categories = array(1=>'Δέν έχει οριστεί το βάρος',
                            2=>'Δεν υπάρχουν φωτογραφίες');
		return $categories[$id];
    }

    public function noImages($tables=null){
        //$this->load->model('problematic_model');

        return Modules::run("crud/noImages", array("sku >"=>'1'), $tables, $this->problematicCategory(2), false);


    }

}