<?php
class Ost_Instagramslider_Model_Animationin
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'bounce', 'label' => 'bounce'),
			array('value' => 'flash', 'label' => 'flash'),
			array('value' => 'pulse', 'label' => 'pulse'),
			array('value' => 'rubberBand', 'label' => 'rubberBand'),
			array('value' => 'shake', 'label' => 'shake'),
			array('value' => 'swing', 'label' => 'swing'),
			array('value' => 'tada', 'label' => 'tada'),
			array('value' => 'wobble', 'label' => 'wobble'),
			array('value' => 'jello', 'label' => 'jello'),
			array('value' => 'bounceIn', 'label' => 'bounceIn'),
			array('value' => 'bounceInDown', 'label' => 'bounceInDown'),
			array('value' => 'bounceInLeft', 'label' => 'bounceInLeft'),
			array('value' => 'bounceInRight', 'label' => 'bounceInRight'),
			array('value' => 'bounceInUp', 'label' => 'bounceInUp'),					
			array('value' => 'fadeIn', 'label' => 'fadeIn'),			
			array('value' => 'fadeInDown', 'label' => 'fadeInDown'),			
			array('value' => 'fadeInDownBig', 'label' => 'fadeInDownBig'),			
			array('value' => 'fadeInLeft', 'label' => 'fadeInLeft'),			
			array('value' => 'fadeInLeftBig', 'label' => 'fadeInLeftBig'),			
			array('value' => 'fadeInRight', 'label' => 'fadeInRight'),			
			array('value' => 'fadeInRightBig', 'label' => 'fadeInRightBig'),			
			array('value' => 'fadeInUp', 'label' => 'fadeInUp'),			
			array('value' => 'fadeInUpBig', 'label' => 'fadeInUpBig'),									
			array('value' => 'flipInX', 'label' => 'flipInX'),			
			array('value' => 'flipInY', 'label' => 'flipInY'),		
			array('value' => 'lightSpeedIn', 'label' => 'lightSpeedIn'),			
			array('value' => 'rotateIn', 'label' => 'rotateIn'),
			array('value' => 'rotateInDownLeft', 'label' => 'rotateInDownLeft'),
			array('value' => 'rotateInDownRight', 'label' => 'rotateInDownRight'),
			array('value' => 'rotateInUpLeft', 'label' => 'rotateInUpLeft'),
			array('value' => 'rotateInUpRight', 'label' => 'rotateInUpRight'),
			array('value' => 'hinge', 'label' => 'hinge'),
			array('value' => 'rollIn', 'label' => 'rollIn'),			
			array('value' => 'zoomIn', 'label' => 'zoomIn'),
			array('value' => 'zoomInDown', 'label' => 'zoomInDown'),
			array('value' => 'zoomInLeft', 'label' => 'zoomInLeft'),
			array('value' => 'zoomInRight', 'label' => 'zoomInRight'),
			array('value' => 'zoomInUp', 'label' => 'zoomInUp'),
			array('value' => 'slideInDown', 'label' => 'slideInDown'),
			array('value' => 'slideInLeft', 'label' => 'slideInLeft'),
			array('value' => 'slideInRight', 'label' => 'slideInRight'),
			array('value' => 'slideInUp', 'label' => 'slideInUp'),
		);
	}
	
	public function toArray()
    {
        return array(
            1 => Mage::helper('adminhtml')->__('50x50'),
            2 => Mage::helper('adminhtml')->__('100x100'),
            3 => Mage::helper('adminhtml')->__('150x150'),
            4 => Mage::helper('adminhtml')->__('200x200'),
            5 => Mage::helper('adminhtml')->__('350x350'),
            6 => Mage::helper('adminhtml')->__('640x640'),
            7 => Mage::helper('adminhtml')->__('1080x1080'),
        );
    }
}
