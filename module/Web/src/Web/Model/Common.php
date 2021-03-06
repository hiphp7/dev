<?php
namespace Web\Model;

use Application\Lib\Api;
use Application\Lib\Cache;
use Exception;
class Common extends Model{    
    
    public static function getCities($params ) 
	{
        try{
            $key = SCENNICS_LIST;     
            if ( ! ($data = Cache::get($key) ) ) 
            {
                $data = Api::call('url_scennics_lists', $params );
                if (!empty($data)) {
                    Cache::set($key, $data);
                }
            }
            return $data;
        }
        catch(Exception $e) {
            return array();
        }
        
    }
    public static function getStates($params ) 
    {
        try
        {
            $key = SCENNICS_DETAIL. $params['scennic_id'];     
            if ( ! ($data = Cache::get($key) ) ) 
            {
                $data = Api::call('url_scennics_detail', $params );
                if ( ! empty($data) ) {
                    Cache::set($key, $data);
                }
            }
            return $data;
        }
        catch(Exception $e) {
            return null;
        }
        
    }
    public static function getCountries($params ) 
    {
        try
        {
            $data = Api::call('url_scennics_add', $params );
            return $data;
        }
        catch(Exception $e) {
            return null;
        }
    }
    public static function update($params ) 
    {
        try
        {
            $data = Api::call('url_scennics_update', $params );
            return $data;
        }
        catch(Exception $e) {
            return null;
        }
    }
}
