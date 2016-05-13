<?php

namespace Api\Controller;

class ProductsController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        return \Api\Bus\Products\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }

    public function updateAction()
    {
        return \Api\Bus\Products\Update::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function listsAction() 
    {
        return \Api\Bus\Products\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function searchAction() 
    {
        return \Api\Bus\Products\Search::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function allAction()
    {
        return \Api\Bus\Products\All::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function detailAction()
    {
        return \Api\Bus\Products\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function addUpdateLocaleAction()
    {
        return \Api\Bus\Products\addUpdateLocale::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function updatesortAction() 
    {
        return \Api\Bus\Products\UpdateSort::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }

    public function onoffAction()
    {
        return \Api\Bus\Products\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function saveattributeAction()
    {
        return \Api\Bus\Products\SaveAttribute::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function addfeaturedAction()
    {
        return \Api\Bus\Products\AddFeatured::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function removefeaturedAction()
    {
        return \Api\Bus\Products\RemoveFeatured::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function updatesortfeaturedAction()
    {
        return \Api\Bus\Products\UpdateSortFeatured::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function addtopsellerAction()
    {
        return \Api\Bus\Products\AddTopSeller::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function removetopsellerAction()
    {
        return \Api\Bus\Products\RemoveTopSeller::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function updatesorttopsellerAction()
    {
        return \Api\Bus\Products\UpdateSortTopSeller::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function addlatestarrivalAction()
    {
        return \Api\Bus\Products\AddLatestArrival::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function removelatestarrivalAction()
    {
        return \Api\Bus\Products\RemoveLatestArrival::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function updatesortlatestarrivalAction()
    {
        return \Api\Bus\Products\UpdateSortLatestArrival::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
    public function homepageAction()
    {
        return \Api\Bus\Products\Homepage::getInstance()->execute(
            $this->getServiceLocator()->get('Products'),
            $this->getParams()
        );
    }
    
}