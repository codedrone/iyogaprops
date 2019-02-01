<?php

class Mageguru_Onestepcheckout_Block_Sales_Order_View extends Mage_Sales_Block_Order_View
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('onestepcheckout/sales/order/view.phtml');
    }
}
