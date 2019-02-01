<?php
class Ost_Instagramslider_Helper_Data extends Mage_Core_Helper_Abstract 
{	
	public function getConfigData()
	{	
		$storeId = Mage::app()->getStore()->getId();
		
		$configData = Mage::getStoreConfig('instagramslider/instagramslider_settings',$storeId);
		
		$data = '';
		if($configData['instagramslider_status'] == 1){
			$url = "https://api.instagram.com/v1/users/".$configData['instagramslider_userid']."/media/recent?s=150&access_token=".$configData['instagramslider_token']."&count=".$configData['instagramslider_item'];
			
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
			$data['item'] = ($configData['instagramslider_item'] == '')?0:$configData['instagramslider_item'];
			$data['width'] = $width;
			$data['autoplay'] = ($configData['instagramslider_play']==0)?"false":"true";
			$data['autoplayspeed'] = ($configData['instagramslider_play']==0)?0:$configData['instagramslider_play_speed'];
			$data['status'] = $configData['instagramslider_status'];
			$data['navigation'] = ($configData['instagramslider_navigation']==0)?"false":"true";
			$data['pagination'] = ($configData['instagramslider_pagination']==0)?"false":"true";
			$data['rewindnav'] = ($configData['instagramslider_rewind']==0)?"false":"true";
			$data['margin']= ($configData['instagramslider_margin']== '')?10:$configData['instagramslider_margin'];;
			
			return $data;
		}
		else
		{
			return ;	
		}
	}
}
