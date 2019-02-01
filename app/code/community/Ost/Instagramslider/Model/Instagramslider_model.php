<?php
class Ost_Instagramslider_Model_Instagramslider extends Mage_Core_Model_Abstract{
	
	public function getDbConfigData(){
		$storeId = Mage::app()->getStore()->getId();
	
		return Mage::getStoreConfig('instagramslider/instagramslider_settings',$storeId);
			
	}
	
	public function getDetailData(){
		
	
			$configData = $this->getDbConfigData();
			
			$data = '';
			if($configData['instagramslider_status'] == 1){
				$url = "https://api.instagram.com/v1/users/".$configData['instagramslider_userid']."/media/recent?s=150&access_token=".$configData['instagramslider_token'];
				
				$ch = curl_init($url); 

				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
				$json = curl_exec($ch); 
				curl_close($ch);
				$result = json_decode($json);
				
				$width='';
				switch($configData['instagramslider_dimension'])
				{
					case '1': 
						$width=50;  
						$width_height='50x50'; 
					break;
					case '2': 
						$width=100; 
						$width_height='100x100'; 
					break;
					case '3': 
						$width=150; 
						$width_height='150x150'; 
					break;
					case '4': 
						$width=200; 
						$width_height='200x200'; 
					break;
					case '5': 
						$width=350; 
						$width_height='350x350'; 
					break;
					case '6': 
						$width=640; 
						$width_height='640x640'; 
					break;
					case '7': 
						$width=1080; 
						$width_height='1080x1080'; 
					break;
					
				}
				foreach ($result->data as $post) {
				$data['images'][] = array(
						'title' => ($post->caption)? (($post->caption->text) ? $post->caption->text :'') : '',
						'link'  => $post->link,
						'image' => str_replace('s320x320','s'.$width_height,$post->images->low_resolution->url)
					);
				}
				$data['title'] = $configData['instagramslider_title'];
				$data['item'] = $configData['instagramslider_item'];
				$data['width'] = $width;
				$data['autoplay'] = $configData['instagramslider_play'];
				$data['status'] = $configData['instagramslider_status'];
				$data['navigation'] = $configData['instagramslider_navigation'];
				$data['pagination'] = $configData['instagramslider_pagination'];
				$data['rewindnav'] = $configData['instagramslider_rewind'];
				
				return $data;
			}
			
			$storeId = Mage::app()->getStore()->getId();
		
			return Mage::getStoreConfig('instagramslider/instagramslider_settings',$storeId);
			
	}
}
