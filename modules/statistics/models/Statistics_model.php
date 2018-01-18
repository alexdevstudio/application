<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics_model extends CI_Model {
 
    public function __construct()
    {
    	parent::__construct();
    }

    public function getSupplierProducts($supplier)
    {
        /*
        $this->db->select('live.*');
        $this->db->select('accessories.*');
        $this->db->select('cables.*');
        $this->db->select('cable_accessories.*');
        $this->db->select('card_readers.*');
        $this->db->select('carrying_cases.*');
        $this->db->select('cartridges.*');
        $this->db->select('cases.*');
        $this->db->select('cooling_pads.*');
        $this->db->select('copiers.*');
        $this->db->select('cpu.*');
        $this->db->select('desktops.*');
        $this->db->select('docking_stations.*');
        $this->db->select('external_hard_drives.*');
        $this->db->select('fans.*');
        $this->db->select('flash_drives.*');
        $this->db->select('graphic_cards.*');
        $this->db->select('hoverboards.*');
        $this->db->select('ip_phones.*');
        $this->db->select('ip_cards.*');
        $this->db->select('ip_gateways.*');
        $this->db->select('ip_pbx.*');
        $this->db->select('keyboard_mouse.*');
        $this->db->select('laptops.*');
        $this->db->select('memories.*');
        $this->db->select('monitors.*');
        $this->db->select('motherboards.*');
        $this->db->select('multifunction_printers.*');
        $this->db->select('optical_drives.*');
        $this->db->select('patch_panels.*');
        $this->db->select('powerlines.*');
        $this->db->select('power_bank.*');
        $this->db->select('power_supplies.*');
        $this->db->select('printers.*');
        $this->db->select('printer_drums.*');
        $this->db->select('printer_fusers.*');
        $this->db->select('printer_belts.*');
        $this->db->select('projectors.*');
        $this->db->select('racks.*');
        $this->db->select('routers.*');
        $this->db->select('sata_hard_drives.*');
        $this->db->select('servers.*');
        $this->db->select('smartphones.*');
        $this->db->select('software.*');
        $this->db->select('speakers.*');
        $this->db->select('ssd.*');
        $this->db->select('switches.*');
        $this->db->select('tablets.*');
        $this->db->select('toners.*');
        $this->db->select('tv.*');
        $this->db->select('ups.*');

        $this->db->from('live');

        $this->db->where('status', 'publish');
        $this->db->where('supplier', $supplier);

        $this->db->join('accessories', 'live.product_number = accessories.product_number', 'left');
        $this->db->join('cables', 'live.product_number = cables.product_number', 'left');
        $this->db->join('cable_accessories', 'live.product_number = cable_accessories.product_number', 'left');
        $this->db->join('card_readers', 'live.product_number = card_readers.product_number', 'left');
        $this->db->join('carrying_cases', 'live.product_number = carrying_cases.product_number', 'left');
        $this->db->join('cartridges', 'live.product_number = cartridges.product_number', 'left');
        $this->db->join('cases', 'live.product_number = cases.product_number', 'left');
        $this->db->join('cooling_pads', 'live.product_number = cooling_pads.product_number', 'left');
        $this->db->join('copiers', 'live.product_number = copiers.product_number', 'left');
        $this->db->join('cpu', 'live.product_number = cpu.product_number', 'left');
        $this->db->join('desktops', 'live.product_number = desktops.product_number', 'left');
        $this->db->join('docking_stations', 'live.product_number = docking_stations.product_number', 'left');
        $this->db->join('external_hard_drives', 'live.product_number = external_hard_drives.product_number', 'left');
        $this->db->join('fans', 'live.product_number = fans.product_number', 'left');
        $this->db->join('flash_drives', 'live.product_number = flash_drives.product_number', 'left');
        $this->db->join('graphic_cards', 'live.product_number = graphic_cards.product_number', 'left');
        $this->db->join('hoverboards', 'live.product_number = hoverboards.product_number', 'left');
        $this->db->join('ip_phones', 'live.product_number = ip_phones.product_number', 'left');
        $this->db->join('ip_cards', 'live.product_number = ip_cards.product_number', 'left');
        $this->db->join('ip_gateways', 'live.product_number = ip_gateways.product_number', 'left');
        $this->db->join('ip_pbx', 'live.product_number = ip_pbx.product_number', 'left');
        $this->db->join('keyboard_mouse', 'live.product_number = keyboard_mouse.product_number', 'left');
        $this->db->join('laptops', 'live.product_number = laptops.product_number', 'left');
        $this->db->join('memories', 'live.product_number = memories.product_number', 'left');
        $this->db->join('monitors', 'live.product_number = monitors.product_number', 'left');
        $this->db->join('motherboards', 'live.product_number = motherboards.product_number', 'left');
        $this->db->join('multifunction_printers', 'live.product_number = multifunction_printers.product_number', 'left');
        $this->db->join('optical_drives', 'live.product_number = optical_drives.product_number', 'left');
        $this->db->join('patch_panels', 'live.product_number = patch_panels.product_number', 'left');
        $this->db->join('powerlines', 'live.product_number = powerlines.product_number', 'left');
        $this->db->join('power_bank', 'live.product_number = power_bank.product_number', 'left');
        $this->db->join('power_supplies', 'live.product_number = power_supplies.product_number', 'left');
        $this->db->join('printers', 'live.product_number = printers.product_number', 'left');
        $this->db->join('printer_drums', 'live.product_number = printer_drums.product_number', 'left');
        $this->db->join('printer_fusers', 'live.product_number = printer_fusers.product_number', 'left');
        $this->db->join('printer_belts', 'live.product_number = printer_belts.product_number', 'left');
        $this->db->join('projectors', 'live.product_number = projectors.product_number', 'left');
        $this->db->join('racks', 'live.product_number = racks.product_number', 'left');
        $this->db->join('routers', 'live.product_number = routers.product_number', 'left');
        $this->db->join('sata_hard_drives', 'live.product_number = sata_hard_drives.product_number', 'left');
        $this->db->join('servers', 'live.product_number = servers.product_number', 'left');
        $this->db->join('smartphones', 'live.product_number = smartphones.product_number', 'left');
        $this->db->join('software', 'live.product_number = software.product_number', 'left');
        $this->db->join('speakers', 'live.product_number = speakers.product_number', 'left');
        $this->db->join('ssd', 'live.product_number = ssd.product_number', 'left');
        $this->db->join('switches', 'live.product_number = switches.product_number', 'left');
        $this->db->join('tablets', 'live.product_number = tablets.product_number', 'left');
        $this->db->join('toners', 'live.product_number = toners.product_number', 'left');
        $this->db->join('tv', 'live.product_number = tv.product_number', 'left');
        $this->db->join('ups', 'live.product_number = ups.product_number', 'left');

        $this->db->order_by('live.category', 'asc');
        //$this->db->order_by('brand', 'asc');

        $query = $this->db->get();

        return $query->result();
        */

        $this->db->select('live.*');
        $this->db->from('live');

        $this->db->where('status', 'publish');
        $this->db->where('supplier', $supplier);

        $this->db->order_by('live.category', 'asc');

        $query = $this->db->get();
        $all_products = $query->result();
        

        foreach($all_products as $product){
            
            $this->db->select($product->category.'.*');
            $this->db->from($product->category);

            $this->db->where($product->category.'.product_number', $product->product_number);

            $product_query = $this->db->get();
            $product->product = $product_query->row();
        }

        return $all_products;

    }
}