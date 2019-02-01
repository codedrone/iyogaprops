<?php

class Ost_Instagramslider_Block_Instagramslider extends Mage_Core_Block_Template
{
    public function getModelInstagram()
    {
		return Mage::getModel('instagramslider/instagramslider')->getDetailData();
    }
}
