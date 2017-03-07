<?php 
/**
 * @package        Arastta eCommerce
 * @copyright      Copyright (C) 2015-2016 Arastta Association. All rights reserved. (arastta.org)
 * @license        GNU General Public License version 3; see LICENSE.txt
 */

class ModelPaymentPaytrCheckout extends Model
{
    
    public function getMethod($address, $total)
    {
        $this->load->language('payment/paytr_checkout');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('paytr_checkout_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('paytr_checkout_total') > 0 && $this->config->get('paytr_checkout_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('paytr_checkout_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => 'paytr_checkout',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => 1
            );
        }

        return $method_data;
    }
}