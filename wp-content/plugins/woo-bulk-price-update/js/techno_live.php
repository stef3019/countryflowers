<?php
ob_start();
class techno_wc_bulk_price_update_lic_class
{  
    public $err;
    private $wp_option  = 'techno_wc_bulk_price_update_wp_plugin';
    public function is_techno_wc_bulk_price_update_act_lic()
    {
        $lic = get_option($this->wp_option);
        if(!empty( $lic )){
           $var_res=unserialize(base64_decode($lic));
            if( $var_res['d'] == strtotime(date('d-m-Y'))){
                return true;               
            } else {
                return $this->chack_lic_status($var_res['l']);
            }
        } else {
            delete_option($this->wp_option);
            return false;
        }
    }
    public function techno_wc_bulk_price_update_act_call($lic_key)
    {        
        return $this->chack_lic_status($lic_key);
    }    
    public function chack_lic_status($key)
    {
        $tc_site_url = preg_replace( "#^[^:/.]*[:/]+#i", "", get_site_url());
        $lic_src = 'https://technocrackers.com/?registered_domain='.$tc_site_url.'&slm_action=slm_activate&item_reference=woo_bulk_update&license_key='.$key;
        
        $lic_res = wp_remote_get($lic_src, array('timeout' => 20, 'sslverify' => false));
        if(is_array($lic_res))
        {
            $lic_res = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', utf8_encode($lic_res['body']));
            $lic_res_data = json_decode($lic_res);
            if($lic_res_data->result == 'success' || $lic_res_data->error_code == 40 || $lic_res_data->error_code ==110)
            {
                $lic_key = base64_encode(serialize(array('l'=>$key,'d'=>strtotime(date('d-m-Y')),'s'=>((isset($lic_res_data->error_code)) ? $lic_res_data->error_code : ''))));
                update_option($this->wp_option, $lic_key);
                return true;
            }
            else
            {
                $this->err = $lic_res_data->message;
                delete_option($this->wp_option);
                return false;
            }
        }
    } 
    public function techno_wc_bulk_price_update_deactive(){
        $lic_data = unserialize(base64_decode(get_option($this->wp_option)));
        $tc_site_url = preg_replace( "#^[^:/.]*[:/]+#i", "", get_site_url());        
        $deact_url = 'https://technocrackers.com/?registered_domain='.$tc_site_url.'&slm_action=slm_deactivate&license_key=' . $lic_data['l'];
        $response = wp_remote_get($deact_url, array('timeout' => 20, 'sslverify' => false));
        if(is_array($response))
        {
            $json = $response['body']; 
            $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', utf8_encode($json));
            $license_data = json_decode($json);
            delete_option($this->wp_option);
            if($license_data->result == 'success')
            {
                return true;
            }
            else
            {
                $this->err = $license_data->message;
                return false;
            }
        }
    }  
}