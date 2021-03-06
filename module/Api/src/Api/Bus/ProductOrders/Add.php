<?php

namespace Api\Bus\ProductOrders;

use Application\Lib\Log;
use Zend\View\Model\ViewModel;
use Api\Bus\AbstractBus;

/**
 * Add categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Add extends AbstractBus {
    
    protected $_required = array(
        'website_id',        
    );
    
    public function operateDB($sm, $param) {
        try {    
            $model = $sm->get('ProductOrders');           
            $this->_response = $model->add($param); 
            if (empty($model->error()) && isset($param['send_email'])) { 
                $data = $model->getDetail(array(
                    'website_id' => $param['website_id'],
                    '_id' => $this->_response,
                ));          
                if (!empty($data['user_email'])) { 
                    try {                       
                        $mail = $sm->get("Mail");        
                        $viewModel = new ViewModel(array('data' => $data));
                        $viewModel->setTemplate('email/order');
                        $mail->setTo($data['user_email']);                                         
                        $mail->setSubject(sprintf('%s DA NHAN DUOC DON HANG %s', $data['website_url'], $data['code']));
                        $mail->setBody($viewModel);
                        $mail->send();
                    } catch (\Exception $e) {
                        Log::warning('ORDER: ' . $data['code'] . ' - ' . $e->getMessage());
                    }
                }
            }        
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
