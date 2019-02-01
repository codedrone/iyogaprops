<?php
/**
 * Padoosoft Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0).
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you are unable to obtain it through the world-wide-web, please send
 * an email to support@mage-addons.com so we can send you a copy immediately.
 *
 * @category   Padoo
 * @package    Padoo_Testimonial
 * @author     PadooSoft Team
 * @copyright  Copyright (c) 2010-2012 Padoo Co. (http://mage-addons.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Padoo_Testimonial_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Get name of the extension
     *
     * @return string - name
     */
    public function getTranslatedExtensionName()
    {
        return $this->__('Testimonials');
    }

    /**
     * Get url for check updates
     *
     * @return string - URL
     */
    public function getCheckUpdateUrl()
    {
        $_t = explode('_', __CLASS__);
        $module_name = $_t[0] . '_' . $_t[1];
        $version = (string)Mage::getConfig()->getModuleConfig($module_name)->version;
        $check_url = 'http://padoo.com/extension.html?name=' . $module_name . '&version=' . $version;
        return $check_url;
    }
	
	/* public function sendMailToStore($email_sender,$name_sender,$message){
		$templateId = Mage::getStoreConfig('testimonials/mail/mail_to_store_template');        

        $mailSubject = $this->__('Notification');
        $sender = Array(
            'name' 	=> $name_sender,
            'email' => $email_sender
        );

        $email = Mage::getStoreConfig('padoo_testimonial/options/recipient_email');
		$title_sender = 'New testimonial was send !';
        $vars = Array(
			'subject'			=> $title_sender,
			'customer_name'		=> $name_sender,
			'message'			=> $message,
		); 
        $storeId = Mage::app()->getStore()->getId();

        $translate = Mage::getSingleton('core/translate');
        Mage::getModel('core/email_template')
            ->setTemplateSubject($mailSubject)
            ->sendTransactional($templateId, $sender, $email, null, $vars, $storeId);
        $translate->setTranslateInline(true);
    } */
	
	public function sendMailToStore($itemId){
		$object = Mage::getModel('padoo_testimonial/testimonial')->load($itemId);
		$customer_email = $object->getTestimonialEmail();
		$customer_name  = $object->getTestimonialName();
		$message 		= $object->getTestimonialText();
		$templateId = Mage::getStoreConfig('padoo_testimonial/options/mail_to_store_template');        

        $mailSubject = $this->__('Testimonial Notification !');

        /**
         * $sender can be of type string or array. You can set identity of
         * diffrent Store emails (like 'support', 'sales', etc.) found
         * in "System->Configuration->General->Store Email Addresses"
         */
        $sender = Array(
            'name' 	=> $customer_name,
            'email' => $customer_email
        );

        /**
         * In case of multiple recipient use array here.
         */
        $email = Mage::getStoreConfig('padoo_testimonial/options/recipient_email');
		
        $vars = Array(
			'subject'			=> 'New Testimonial was sended.',
			'customer_name'		=> $customer_name,
			'message'			=> $message,
		 ); 
        
        /* This is optional */
        $storeId = Mage::app()->getStore()->getId();

        $translate = Mage::getSingleton('core/translate');
        Mage::getModel('core/email_template')
            ->setTemplateSubject($mailSubject)
            ->sendTransactional($templateId, $sender, $email, null, $vars, $storeId);
        $translate->setTranslateInline(true);
    }
	
	
	
	/* public function sendMailToStore($itemId){
		$object = Mage::getModel('padoo_testimonial/testimonial')->load($itemId);
		$customer_email = $object->getTestimonialEmail();
		$customer_name  = $object->getTestimonialName();
		$message 		= $object->getTestimonialText();

		$templateId = Mage::getStoreConfig('padoo_testimonial/options/mail_to_store_template');        

			$mailSubject = $this->__('Testimonial Notification !');

			$sender = Array(
				'name' => $customer_name,
				'email' => $customer_email
			);

			$email = Mage::getStoreConfig('padoo_testimonial/options/recipient_email');

			$vars = Array(
				'subject' => 'New Testimonial was sended.',
				'name'   => $customer_name,
				'email'  => $customer_email,
				'message'   => $message,
			); 

        $translate = Mage::getSingleton('core/translate');
        Mage::getModel('core/email_template')
            ->setTemplateSubject($mailSubject)
            ->sendTransactional($templateId, $sender, $email, null, $vars);
        $translate->setTranslateInline(true);
    } */
	
	public function getTestimonialUrl(){
		return $this->_getUrl('testimonials');
	}

}