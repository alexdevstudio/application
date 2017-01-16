<?php

defined('BASEPATH') OR exit('No direct script access allowed');

 
class Skroutz_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function getItems(){
    	return Modules::run('crud/get', 'skroutz_urls');
    }
    
  

}