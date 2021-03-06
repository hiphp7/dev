<?php

namespace Api\Bus\Products;

use Api\Bus\AbstractBus;

/**
 * Get list categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Homepage extends AbstractBus {
    
    protected $_required = array(       
    );   
    
    public function operateDB($model, $param) {
        try {          
            $this->_response = $model->getForHomepage($param);       
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
