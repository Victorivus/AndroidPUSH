<?php
/*
Plugin Name: Android PUSH notifications
Plugin URI: https://gist.github.com/Victorivus/d0fc52464948c18b66fb2a54d77437a3
Description: send an Android PUSH notification when new post or page is published
Version: 1.0
Author: Victor N
Author URI: festicidas.com
*/

function send_notification($new_status, $old_status, $post){ 
    if ( $new_status == 'publish' && ($old_status == 'draft' || $old_status == 'new' ||$old_status == 'pending' ||$old_status == 'auto-draft' ||$old_status == 'private') ) {
		$post_id = $post->ID;
		$post_title = $post->post_title;
		$post_excerpt = $post->post_excerpt;

		$url = 'https://fcm.googleapis.com/fcm/send';
		###########################################
		#       YOUR API KEY GOES BELOW           #
		###########################################
		$server_key = 'AIzaSyBs_Sc6BxA-lr94txtXUCIiejA44H-iui8';

		$notification = array();
		$notification['body'] = $post_excerpt;
		$notification['title'] = $post_title;
		$notification['icon'] = "ic_launcher";

		$data = array();
		$data['id'] = $post_id;

		$fields = array();
		$fields['data'] = $data;
		###########################################
		# WHO YOU WANNA SEND THE NOTIFICATION TO  #
		###########################################
		$fields['to'] = '/topics/news'; #Users subscribed to news
		$fields['notification'] = $notification;

		//header with content_type api key
		$headers = array(
			'Content-Type:application/json',
		  	'Authorization:key='.$server_key
		);
					
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		if ($result === FALSE) {
			die('FCM Send Error: ' . curl_error($ch));
		}
		curl_close($ch);
		return $result;
	}
}

add_action( 'transition_post_status', 'send_notification', 10, 3 );
add_action( 'transition_page_status', 'send_notification', 10, 3 );

?>
