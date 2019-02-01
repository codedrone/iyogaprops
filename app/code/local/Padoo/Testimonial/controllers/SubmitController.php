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
 
class Padoo_Testimonial_SubmitController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		if (!Mage::getStoreConfig('padoo_testimonial/general/enable')) $this->_redirect('no-route');
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction() {
		if (!Mage::getStoreConfig('padoo_testimonial/general/enable')) $this->_redirect('no-route');
		$data = $this->getRequest()->getPost();
		$testimonialData = $data['testimonial'];
		$media = $_FILES['testimonialmedia']['name'];
        if (!empty($data)) {
            $session = Mage::getSingleton('core/session', array('name'=>'frontend'));
            /* @var $session Mage_Core_Model_Session */
			$testimonial = Mage::getModel('padoo_testimonial/testimonial');
			$validate = $testimonial->validate();
			if ($validate === true) {
				$formId = 'padoo_testimonial';
				$magentoVersion = Mage::getVersion();
				if (version_compare($magentoVersion, '1.7', '>=')){
					//version is 1.7 or greater
					$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
					if ($captchaModel->isRequired()) {
						$word = $this->_getCaptchaString($this->getRequest(), $formId);
						if (!$captchaModel->isCorrect($word)) {
							Mage::getSingleton('core/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
							$this->_redirectReferer('');
							return;
						}
					}
				}
			
				try {
					 if(isset($_FILES['testimonialmedia']['name']) && ($_FILES['testimonialmedia']['tmp_name'] != NULL)){
						$uploader = new Varien_File_Uploader('testimonialmedia');
						$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','mp4','flv')); 
						$uploader->setAllowRenameFiles(true);
						$uploader->setFilesDispersion(true);        
						//$path = Mage::getBaseDir('media') . DS ;
						//$path= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);     

						$path = Mage::getBaseDir('media') . DS . 'testimonials';
						$img = $uploader->save($path, 'testimonials/' . $_FILES['testimonialmedia']['name']);
					}
					$imgPath = 'testimonials'. $img['file'];
					$testimonial->setTestimonialName($testimonialData['name']);
					$testimonial->setTestimonialEmail($testimonialData['email']);
					$testimonial->setTestimonialText($testimonialData['content']);
					$testimonial->setTestimonialImg($imgPath);
					$testimonial->save();
					$itemId = $testimonial->getTestimonialId(); 
					//send email to store 
					if(Mage::getStoreConfig('padoo_testimonial/options/enable_notification')){
						Mage::helper('padoo_testimonial')->sendMailToStore($itemId);
					}
								
					$session->addSuccess($this->__('Your testimonial has been accepted'));
				}catch (Exception $e) {
					$session->setFormData($data);
					$session->addError($this->__('Unable to post testimonial. Please, try again later !'));
				}
			}else {
				try{
					$session->setFormData($data);
				}catch(Exception $e){
					Mage::log($e->getMessage());
				}                  
				if (is_array($validate)) {                   
					foreach ($validate as $errorMessage) {
						$session->addError($errorMessage);
					}                 
				}
				else {
					$session->addError($this->__('Unable to post testimonial. Please, try again later !'));
				}
			}	
        }

        if ($redirectUrl = Mage::getSingleton('core/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();		
	}
	
	protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }
}