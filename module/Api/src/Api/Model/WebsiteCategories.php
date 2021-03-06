<?php

namespace Api\Model;

use Application\Lib\Log;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class WebsiteCategories extends AbstractModel {
    
    protected static $properties = array(
        'category_id',
        '_id',
        'sort',
        'is_locale',
        'locale',
        'name',
        'short',
        'content',
        'created',
        'updated',
        'active',
        'image_id',
        'parent_id'
    );
    
    protected static $primaryKey = 'category_id';
    
    protected static $tableName = 'website_categories';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'website_category_locales', 
                static::$tableName . '.category_id = website_category_locales.category_id',
                array('name', 'short')
            )
            ->join(
                'website_category_images', 
                static::$tableName . '.image_id = website_category_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where("website_category_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        if (isset($param['parent_id']) && $param['parent_id'] !== '') {            
            $select->where(static::$tableName . '.parent_id = '. $param['parent_id']);  
        }
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("website_category_locales.name LIKE '%{$param['name']}%'"));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(name|sort)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("website_category_locales.{$match[1]} " . $match[2]);
                        break;
                    case 'sort':
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.sort ASC');
        }         
        $selectString = $sql->getSqlStringForSqlObject($select);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    public function getAll($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'category_id', 
                '_id', 
                'sort',
                'image_id',
                'parent_id'
            ))
            ->join(               
                array(
                    'website_category_locales' => 
                    $sql->select()
                        ->from('website_category_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.category_id = website_category_locales.category_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.active = 1')     
            ->order('sort');     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    {
        $_id = mongo_id();  // website_categories._id
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $image = new Images();
                $param['image_id'] = $image->add(array(
                    'src' => 'website_categories',
                    'src_id' => 0,
                    'url_image' => $uploadResult['url_image'],
                    'is_main' => 1,
                ));
            }          
        }        
        $values = array(
            '_id' => $_id,
            'sort' => self::max(array('field' => 'sort')) + 1,
        );
        if (isset($param['sort'])) {
            $values['sort'] = $param['sort'];
        }  
        if (isset($param['image_id'])) {
            $values['image_id'] = $param['image_id'];
        }
        if (isset($param['parent_id'])) {
            $values['parent_id'] = $param['parent_id'];
        }
        if ($id = self::insert($values)) {
            $localeValues = array(
                'category_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            } 
            if (isset($param['content'])) {
                $localeValues['content'] = $param['content'];
            }
            self::$tableName = 'website_category_locales';
            self::insert($localeValues);
            if (empty(self::error()) && !empty($param['image_id'])) {
                $image = new Images();
                $image->updateInfo(array(
                    'src' => 'website_categories',
                    'src_id' => $id,
                    'id' => $param['image_id']
                ));
            }
            return $_id;
        }        
        return false;
    }

    public function updateInfo($param)
    {
        $self = self::find(
            array(            
                'where' => array('_id' => $param['_id'])
            ),
            self::RETURN_TYPE_ONE
        );   
        if (empty($self)) {
            self::errorNotExist('_id');
            return false;
        }        
        $set = array();
        if (isset($param['sort'])) {
            $set['sort'] = $param['sort'];
        }
        if (isset($param['parent_id'])) {
            $set['parent_id'] = $param['parent_id'];
        }
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'website_categories',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'website_categories',
                        'src_id' => $self['category_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'website_categories'
                ));
            }
        }
        if (isset($param['image_id'])) {
            $set['image_id'] = $param['image_id'];
        }
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['_id']
                ),
            )
        )) {
            $locales = \Application\Module::getConfig('general.locales');
            if (count($locales) == 1) {
                $param['locale'] = array_keys($locales)[0];
                self::addUpdateLocale($param);
            }
            return true;
        } 
        return false;
    }

    public function addUpdateLocale($param)
    {
        $detail = self::getDetail(array(
            '_id' => $param['_id'],
            'locale' => $param['locale'],
        ));
        if (empty($detail)) {
            self::errorNotExist('_id');
            return false;
        }
        
        static::$tableName = 'website_category_locales';
        $values = array();
        if (isset($param['name'])) {
            $values['name'] = $param['name'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        } 
        if (isset($param['content'])) {
            $values['content'] = $param['content'];
        }
        if (empty($detail['locale'])) {
            $values['category_id'] = $detail['category_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        return self::update(
            array(
                'set' => $values,
                'where' => array(
                    'category_id' => $detail['category_id'],
                    'locale' => $param['locale'],
                ),
            )
        );
    }

    public function getDetail($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'category_id', 
                '_id', 
                'sort',
                'image_id',
                'parent_id'
            ))
            ->join(               
                array(
                    'website_category_locales' => 
                    $sql->select()
                        ->from('website_category_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.category_id = website_category_locales.category_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'content',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where("_id = ". self::quote($param['_id']));                      
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
    }
    
    public function updateSort($param) {  
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
    
}
