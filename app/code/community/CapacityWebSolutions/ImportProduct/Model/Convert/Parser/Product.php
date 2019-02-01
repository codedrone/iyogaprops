<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
ini_set("memory_limit","1024M");
class CapacityWebSolutions_ImportProduct_Model_Convert_Parser_Product extends Mage_Catalog_Model_Convert_Parser_Product
{
    const MULTI_DELIMITER = ' , ';
    protected $_resource;

    /**
     * Product collections per store
     *
     * @var array
     */
    protected $_collections;

    /**
     * Product Type Instances object cache
     *
     * @var array
     */
    protected $_productTypeInstances = array();

    /**
     * Product Type cache
     *
     * @var array
     */
    protected $_productTypes;

    protected $_inventoryFields = array();

    protected $_imageFields = array();

    protected $_systemFields = array();
    protected $_internalFields = array();
    protected $_externalFields = array();

    protected $_inventoryItems = array();

    protected $_productModel;

    protected $_setInstances = array();

    protected $_store;
    protected $_storeId;
    protected $_attributes = array();

    public function __construct()
    {
        foreach (Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin') as $code=>$node) {
            if ($node->is('inventory')) {
                $this->_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $this->_inventoryFields[] = 'use_config_'.$code;
                }
            }
            if ($node->is('internal')) {
                $this->_internalFields[] = $code;
            }
            if ($node->is('system')) {
                $this->_systemFields[] = $code;
            }
            if ($node->is('external')) {
                $this->_externalFields[$code] = $code;
            }
            if ($node->is('img')) {
                $this->_imageFields[] = $code;
            }
        }
    }

    /**
     * @return Mage_Catalog_Model_Mysql4_Convert
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceSingleton('catalog_entity/convert');
                #->loadStores()
                #->loadProducts()
                #->loadAttributeSets()
                #->loadAttributeOptions();
        }
        return $this->_resource;
    }

    public function getCollection($storeId)
    {
        if (!isset($this->_collections[$storeId])) {
            $this->_collections[$storeId] = Mage::getResourceModel('catalog/product_collection');
            $this->_collections[$storeId]->getEntity()->setStore($storeId);
        }
        return $this->_collections[$storeId];
    }

    /**
     * Retrieve product type options
     *
     * @return array
     */
    public function getProductTypes()
    {
        if (is_null($this->_productTypes)) {
            $this->_productTypes = Mage::getSingleton('catalog/product_type')
                ->getOptionArray();
        }
        return $this->_productTypes;
    }

    /**
     * Retrieve Product type name by code
     *
     * @param string $code
     * @return string
     */
    public function getProductTypeName($code)
    {
        $productTypes = $this->getProductTypes();
        if (isset($productTypes[$code])) {
            return $productTypes[$code];
        }
        return false;
    }

    /**
     * Retrieve product type code by name
     *
     * @param string $name
     * @return string
     */
    public function getProductTypeId($name)
    {
        $productTypes = $this->getProductTypes();
        if ($code = array_search($name, $productTypes)) {
            return $code;
        }
        return false;
    }

    /**
     * Retrieve product model cache
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel()
    {
        if (is_null($this->_productModel)) {
            $productModel = Mage::getModel('catalog/product');
            $this->_productModel = Mage::objects()->save($productModel);
        }
        return Mage::objects()->load($this->_productModel);
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            try {
                $store = Mage::app()->getStore($this->getVar('store'));
            } catch (Exception $e) {
                $this->addException(
                    Mage::helper('catalog')->__('Invalid store specified'),
                    Varien_Convert_Exception::FATAL
                );
                throw $e;
            }
            $this->_store = $store;
        }
        return $this->_store;
    }

    /**
     * Retrieve store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->_storeId = $this->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * ReDefine Product Type Instance to Product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function setProductTypeInstance(Mage_Catalog_Model_Product $product)
    {
        $type = $product->getTypeId();
        if (!isset($this->_productTypeInstances[$type])) {
            $this->_productTypeInstances[$type] = Mage::getSingleton('catalog/product_type')
                ->factory($product, true);
        }
        $product->setTypeInstance($this->_productTypeInstances[$type], true);
        return $this;
    }

    public function getAttributeSetInstance()
    {
        $productType = $this->getProductModel()->getType();
        $attributeSetId = $this->getProductModel()->getAttributeSetId();

        if (!isset($this->_setInstances[$productType][$attributeSetId])) {
            $this->_setInstances[$productType][$attributeSetId] =
                Mage::getSingleton('catalog/product_type')->factory($this->getProductModel());
        }

        return $this->_setInstances[$productType][$attributeSetId];
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getProductModel()->getResource()->getAttribute($code);
        }
        return $this->_attributes[$code];
    }

    /**
     * @deprecated not used anymore
     */
    public function parse()
    {
        $data            = $this->getData();
        $entityTypeId    = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getId();
        $inventoryFields = array();

        foreach ($data as $i=>$row) {
            $this->setPosition('Line: '.($i+1));
            try {
                // validate SKU
                if (empty($row['sku'])) {
                    $this->addException(
                        Mage::helper('catalog')->__('Missing SKU, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                }
                $this->setPosition('Line: '.($i+1).', SKU: '.$row['sku']);

                // try to get entity_id by sku if not set
                if (empty($row['entity_id'])) {
                    $row['entity_id'] = $this->getResource()->getProductIdBySku($row['sku']);
                }

                // if attribute_set not set use default
                if (empty($row['attribute_set'])) {
                    $row['attribute_set'] = 'Default';
                }
                // get attribute_set_id, if not throw error
                $row['attribute_set_id'] = $this->getAttributeSetId($entityTypeId, $row['attribute_set']);
                if (!$row['attribute_set_id']) {
                    $this->addException(
                        Mage::helper('catalog')->__('Invalid attribute set specified, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                }

                if (empty($row['type'])) {
                    $row['type'] = 'Simple';
                }
                // get product type_id, if not throw error
                $row['type_id'] = $this->getProductTypeId($row['type']);
                if (!$row['type_id']) {
                    $this->addException(
                        Mage::helper('catalog')->__('Invalid product type specified, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                }

                // get store ids
                $storeIds = $this->getStoreIds(isset($row['store']) ? $row['store'] : $this->getVar('store'));
                if (!$storeIds) {
                    $this->addException(
                        Mage::helper('catalog')->__('Invalid store specified, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                }

                // import data
                $rowError = false;
                foreach ($storeIds as $storeId) {
                    $collection = $this->getCollection($storeId);
                    $entity = $collection->getEntity();

                    $model = Mage::getModel('catalog/product');
                    $model->setStoreId($storeId);
                    if (!empty($row['entity_id'])) {
                        $model->load($row['entity_id']);
                    }
                    foreach ($row as $field=>$value) {
                        $attribute = $entity->getAttribute($field);

                        if (!$attribute) {
                            //$inventoryFields[$row['sku']][$field] = $value;

                            if (in_array($field, $this->_inventoryFields)) {
                                $inventoryFields[$row['sku']][$field] = $value;
                            }
                            continue;
//                            $this->addException(
//                                Mage::helper('catalog')->__('Unknown attribute: %s.', $field),
//                                Mage_Dataflow_Model_Convert_Exception::ERROR
//                            );
                        }
                        if ($attribute->usesSource()) {
                            $source = $attribute->getSource();
                            $optionId = $this->getSourceOptionId($source, $value);
                            if (is_null($optionId)) {
                                $rowError = true;
                                $this->addException(
                                    Mage::helper('catalog')->__('Invalid attribute option specified for attribute %s (%s), skipping the record.', $field, $value),
                                    Mage_Dataflow_Model_Convert_Exception::ERROR
                                );
                                continue;
                            }
                            $value = $optionId;
                        }
                        $model->setData($field, $value);

                    }//foreach ($row as $field=>$value)

                    //echo 'Before **********************<br/><pre>';
                    //print_r($model->getData());
                    if (!$rowError) {
                        $collection->addItem($model);
                    }
                    unset($model);
                } //foreach ($storeIds as $storeId)
            } catch (Exception $e) {
                if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                    $this->addException(
                        Mage::helper('catalog')->__('Error during retrieval of option value: %s', $e->getMessage()),
                        Mage_Dataflow_Model_Convert_Exception::FATAL
                    );
                }
            }
        }

        // set importinted to adaptor
        if (sizeof($inventoryFields) > 0) {
            Mage::register('current_imported_inventory', $inventoryFields);
            //$this->setInventoryItems($inventoryFields);
        } // end setting imported to adaptor

        $this->setData($this->_collections);
        return $this;
    }

    public function setInventoryItems($items)
    {
        $this->_inventoryItems = $items;
    }

    public function getInventoryItems()
    {
        return $this->_inventoryItems;
    }

    /**
     * Unparse (prepare data) loaded products
     *
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function unparse()
    {
		$i = 0;
		$entityIds = $this->getData();
		$category_model = Mage::getModel('catalog/category');  //get category model
		$product_model = Mage::getModel('catalog/product'); //get product model 	
		
        foreach ($entityIds as $i => $entityId) {
            $product = $this->getProductModel()
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setProductTypeInstance($product);
            /* @var $product Mage_Catalog_Model_Product */
            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => $this->getStore()->getCode(),
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                                        $product->getAttributeSetId()),
                'type'          => $product->getTypeId(),
                'category_ids'  => join(',', $product->getCategoryIds())
            );

            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $product->getProductUrl(false);
                }
            }


            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }

                $row[$field] = $value;
            }

            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }
			
            foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    $row[$field] = null;
                }
            }
			
			/* Code is added by jalpesh*/
			/*This code is use for the get product category like ParentCategory/ChildCategory/SubchildCategory*/
			
			$all_cats = $product->getCategoryIds(); // all the categories
			$main_cnt = count($all_cats);
			$cat_str_main = ''; 
			$j = 0;
			foreach($all_cats as $ac)
			{
				$category = $category_model->load($ac);
				$pathIds = explode("/",$category->getPath());
				$coll = $category->getResourceCollection()->addAttributeToSelect('name')->addAttributeToFilter('entity_id', array('in' => $pathIds));
				$cat_str = '';
				$i=0;
				foreach($coll as $cat)
				{
					if($i == 2)
					{
						$cat_str = $cat->getName();
					}
					else if($i > 2)
					{
						$cat_str = $cat_str."/".$cat->getName();
					}
					$i = $i+1;
				}
				if($j < 1)
				{
					$cat_str_main = $cat_str;
				}
				else 
				{			
					$cat_str_main = $cat_str_main .",".$cat_str;
				}
				$j = $j+1;
			}
			$row['categories'] = $cat_str_main;
			unset($cat_str_main);
			$field_value = '';
			foreach ($product->getOptions() as $o) 
			{
				$optionType = $o->getType();
				$optionTitle = $o->getTitle(); 
				$is_required = $o->getIsRequire();
				$price = $o->getPrice();
				
				$values = $o->getValues();
				$price_type = $o->getPriceType();
				$sku =	$o->getSku();
				$max_characters = $o->getMaxCharacters();
				
				$title='cws!'.$optionTitle.':'.$optionType.':'.$is_required;
				
				switch($optionType)
				{
					case 'area':
						$field_value = "|:".$price_type.":".$price.":".$sku.":".$max_characters;
						break;
					case 'date':
						$field_value = "|:".$price_type.":".$price.":".$sku;
						break;
					case 'date_time':
						$field_value = "|:".$price_type.":".$price.":".$sku;
						break;
					case 'field':
						$field_value = "|:".$price_type.":".$price.":".$sku.":".$max_characters;
						break;
					case 'time':
						$field_value = "|:".$price_type.":".$price.":".$sku;
						break;
					case 'drop_down':
					case 'radio':
					case 'multiple':
					case 'checkbox':
					default:
						$values = $o->getValues();
						$cnt = count($values);
						$i=0;			
						foreach ($values as $k => $v)
						{
							$price = $v->getPrice();
							$price_type = $v->getPriceType();
							$sku = $v->getSku();
							$sort_order = $v->getSortOrder();
							if($cnt == 0)
							{
								
								$field_value = $v->getData('default_title').":".$price_type.":".$price.":".$sku.":".$sort_order;
							}
							else
							{
								if($cnt - 1  > $i)
								{
									$field_value = $field_value.''.$v->getData('default_title').":".$price_type.":".$price.":".$sku.":".$sort_order."|";
								}
								else
								{
									$field_value = $field_value.''.$v->getData('default_title').":".$price_type.":".$price.":".$sku.":".$sort_order;
								}
							}
							$i++;
						}
				}
				$row[$title] = $field_value;
				unset($field_value);	
			}
			
			/*This code is use for export limited field in CSV*/
			/*$removeKeys = array('category_ids','meta_title','meta_description','url_key','url_path','custom_design','page_layout','image_label','small_image_label','thumbnail_label','country_of_manufacture','msrp_enabled','msrp_display_actual_price_type','gift_message_available','special_price','msrp','is_recurring','enable_googlecheckout','meta_keyword','custom_layout_update','special_from_date','special_to_date','news_from_date','news_to_date','custom_design_from','custom_design_to','min_qty','use_config_min_qty','is_qty_decimal','backorders','use_config_backorders','min_sale_qty','use_config_min_sale_qty','max_sale_qty','use_config_max_sale_qty','low_stock_date','notify_stock_qty','use_config_notify_stock_qty','manage_stock','use_config_manage_stock','stock_status_changed_auto','use_config_qty_increments','qty_increments','use_config_enable_qty_inc','enable_qty_increments','is_decimal_divided','stock_status_changed_automatically','use_config_enable_qty_increments','store_id','product_type_id','product_status_changed','product_changed_websites');

			foreach($removeKeys as $key) {
			   unset($row[$key]);
			}*/
			
			/* Remove comment when required all the field*/
			unset($row["category_ids"]);	
			
			
            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
            $product->reset();
        }
        return $this;
		
    }

    /**
     * Retrieve accessible external product attributes
     *
     * @return array
     */
    public function getExternalAttributes()
    {
        $productAttributes  = Mage::getResourceModel('catalog/product_attribute_collection')->load();
        $attributes         = $this->_externalFields;

        foreach ($productAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $this->_internalFields) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }

        foreach ($this->_inventoryFields as $field) {
            $attributes[$field] = $field;
        }

        return $attributes;
    }
}
