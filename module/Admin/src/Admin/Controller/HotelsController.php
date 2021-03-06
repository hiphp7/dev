<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Application\Lib\Cache;
use Application\Lib\Log;
use Admin\Lib\Api;
use Admin\Form\Hotel\HotelSearchForm;
use Admin\Form\Hotel\HotelListForm;
use Admin\Form\Hotel\HotelForm;
use Admin\Model\Hotels;

class HotelsController extends AppController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function indexAction()
    {
        $param = $this->getParams(array(
            'limit' => \Application\Module::getConfig('general.default_limit')
        ));
        
        // create search form
        $searchForm = new HotelSearchForm();
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);

        // process update on list form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = (array) $request->getPost();
            $data['iseq'] = \Zend\Json\Encoder::encode($data['iseq']);
            Api::call('url_hotels_updateiseq', $data);
            if (Api::error()) {
                $this->addErrorMessage($this->getErrorMessage());
            } else {
                $this->addSuccessMessage('Data saved successfully');
            }
        }
        $data = Api::call('url_hotels_lists', $param);

        $listForm = new HotelListForm();
        $listForm   ->setController($this)
                    ->setDataset($data)
                    ->create();
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,
            )
        );
    }
    
    public function updateAction()
    {
        $param = $this->getParams(array(
            'locale' => ''
        ));
        $id = $this->params()->fromRoute('id', 0);
                
        // change breadcrumb
        $this->setBreadcrumbLabel('admin_hotels_update', $id > 0 ? 'Edit Hotel' : 'Add Hotel');
        
        // create add/edit form
        $form = new HotelForm();
        $form->setAttribute('enctype','multipart/form-data')
             ->setController($this)
             ->create();
        if (!empty($id)) {
            $data = Hotels::hotels_detail($id, isset($param['locale']) ? $param['locale'] : '' );
            if (empty($data)) {
                return $this->notFoundAction();
            }
            $form->bindData($data);
        }
        
        // process save form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = (array) $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                if (!empty($id)) {
                    Api::call('url_hotels_update', $data);
                } else {
                    $id = Api::call('url_hotels_add', $data);
                }
               
                if (Api::error()) {
                    $error = array(
                        array('field' => 'artist', 'code' => 1002, 'message' => $this->translate('The input is less than %s characters long', 100)),
                    );
                    $this->addErrorMessage($this->getErrorMessage($error));
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute('admin/hotels', array('action' => 'update', 'id' => $id));
                }
            }
        }
        
        return $this->getViewModel(array(
                'form' => $form
            )
        );
    }
    
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->notFoundAction();
        }
        Api::call('url_hotels_delete', array('hotel_id' => $id));
        if (Api::error()) {
            $this->addErrorMessage($this->getErrorMessage());
        } else {
            $this->addSuccessMessage('Data deleted successfully');
            return $this->redirect()->toRoute('admin/Hotels', array('action' => 'index'));
        }
    }
    
}
