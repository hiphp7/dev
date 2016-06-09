<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Web\Model\Products;

class IndexController extends AppController
{
    public function indexAction()
    {
        $this->setHead(array(
            'title' => 'Mua balo trực tuyến giá rẻ, đẹp, chất lượng'
        ));
        $param = $this->getParams(array(                      
            'force' => 0,            
        ));
        $blocks = Products::homepage($param);
        return $this->getViewModel(array(
                'blocks' => $blocks
            )
        );
    }    
    
}
