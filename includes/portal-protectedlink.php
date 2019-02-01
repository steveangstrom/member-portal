
<?php
/*  this is removed for now, to make the portal more atomic
what this code does is allow the download via push of any file attached via the mtas in


/**** protected link display ********/

function cmb2_getprotectedlink(){

	$theID=get_the_ID();
		$link ='';
	$thelinks = get_post_meta( $theID, 'linkgroup', true );

	$thepubliclinks = get_post_meta( $theID, 'publiclinkgroup', true );

	if (empty($thelinks[0])&& empty($thepubliclinks[0])){return ;} // nothing here, so dont bother

	$link .= '<div class="link-list-wrap"><h4>Links</h4>';

	//$protection = get_post_meta( $theID, 'resource_protection', 1 );

	$protection = get_post_meta( $theID, 'resource_protection', 1 ); // kept for potential reinclusion depending on client thoughts
	$check_if_saved_since =  get_post_meta( $theID, 'resource_protection_override', 1 );

	if(isset($protection) && $protection == ''){
		$protection = 'public';
		}

	if(isset($protection) && $protection != 'public'){
		$protection = 'protected'; // protection hard set for unsassigned meta on 1dec 2016, because public and private files are now in separate metaboxes
	}

	if(isset($check_if_saved_since)&& ($check_if_saved_since=='protected')){
		$protection = 'protected'; // protection hard set for items resaved since dec 2016
	}



	//$protection = 'protected'; // protection hard set on Dec 1 2016, because protected items are in their own metabox

	$user_status = is_user_logged_in ();
	$hasaccess = false;
	$class="locked";

		if (!empty($thelinks[0])){
			foreach ( (array) $thelinks as $key => $entry ) {
				$thelabel = $entry['protectable_link_text'];
					if(empty($thelabel)){$label='Visit Protected link ';}else{
					$label =$thelabel;
				}

				if(($protection=='protected' && $user_status==1) || empty($protection) || $protection=='public'){
					$hasaccess = true;
					$class="unlocked";
				}

				if($hasaccess == true){
					$linkurl = $entry['protectable_link'];
				}else{
					$linkurl=home_url('/sign-in'); // new
					$usermessage='<div class="usermessage">Member-only resource. Join for Access</div>';
				}
				if(!empty ($entry['protectable_link'])){	$link .= '<a href="'.$linkurl.'" class="protected-link '.$class.'" target="_blank">'.$label.$usermessage.'</a>';}


			}
		}// empty protected check

	/******* NOW LETS DO ANY PUBLIC LINKS IN THE SYSTEM.********/

	if (!empty($thepubliclinks[0])){
		foreach ( (array) $thepubliclinks as $key => $entry ) {

			$thelabel = $entry['public_link_text'];
				if(empty($thelabel)){$label='Visit link ';}else{
				$label =$thelabel;
			}

			$linkurl = $entry['public_link'];
				if(empty($linkurl)){$linkurl ='';}else{
				$linkurl=$entry['public_link'];
				}
			if (!is_user_logged_in()){	$class="unlocked public";	}else{$class="unlocked";}

			if(!empty ($entry['public_link'])){
				$link .= '<a href="'.$linkurl.'" class="protected-link '.$class.'" target="_blank">'.$label.'</a>';
			}


		}

	}
	$link.='</div>';

	// empty public links check

		/*echo('<pre>');
		print_r($thepubliclinks);
		echo('</pre>');*/


	return $link;

}

/*******file list display *****/

function cmb2_output_file_list( $file_list_meta_key, $img_size = 'medium' ) {

    // Get the list of files
	$theID=get_the_ID();
    $files = get_post_meta($theID, $file_list_meta_key, 1 );
	$publicfiles = get_post_meta($theID, 'public_file_list', 1 );


	if (empty($files)&&empty($publicfiles)){return ;} // IF nothing here, so dont bother

	$protection = get_post_meta( $theID, 'resource_protection', 1 ); // kept for potential reinclusion depending on client thoughts
	$check_if_saved_since =  get_post_meta( $theID, 'resource_protection_override', 1 );

	if(isset($protection) && $protection == ''){
		$protection = 'public'; // some legacy items have an empty value, so assume public.
	}
	if(isset($protection) && $protection != 'public'){
		$protection = 'protected'; // protection hard set for unsassigned meta on 1dec 2016, because public and private files are now in separate metaboxes
		}

	if(isset($check_if_saved_since)&& ($check_if_saved_since=='protected')){
		$protection = 'protected'; // protection hard set for items resaved since dec 2016
	}



	$user_status = is_user_logged_in ();
	$hasaccess = false;
	$class="locked";

	if(($protection=='protected' && $user_status==1) || empty($protection) || $protection=='public'){
		$hasaccess = true;
		$class="unlocked";
	}
		$out= '<div class="file-list-wrap"><h4>Downloads</h4>';

		// Loop through the Memberfiles and output
		foreach ((array) $files as $attachment_id => $attachment_url ) {
			$attachment_data = wp_prepare_attachment_for_js($attachment_id );

			if (!empty($files)){
				if($hasaccess == true){
					$dload_nonce=wp_create_nonce('download'.$attachment_id );
					#	$out.= '<a class="file-list '.$class.'" href="'.wp_get_attachment_url($attachment_id).'" target="blank">';//UNPROTECTED DIRECT LINK
					$out.= '<a class="file-list '.$class.'" href="?lpf='.$attachment_id.'&_wpnonce='.$dload_nonce.'">';
						$usermessage='';
				}else{
						$linkurl=home_url('/sign-in'); // new
						$out.= '<a class="file-list '.$class.'" href="'.$linkurl.'">';
						$usermessage='<div class="usermessage">Member-only resource. Join for Access</div>';
				}

				$out.=get_the_title($attachment_id);

				$out.=' ' .$attachment_data['subtype']. ' '. $attachment_data['filesizeHumanReadable'];
				$out.=$usermessage;
				$out.= '</a>';
			}
		}

	/******* NOW LETS DO ANY PUBLIC DOWNLOADS  IN THE SYSTEM.********/

		foreach ((array) $publicfiles as $attachment_id => $attachment_url ) {
			if (!is_user_logged_in() ){	$class="unlocked public";	}else{$class="unlocked";}

			if(!empty($attachment_url)){
				$out.= '<a class="file-list '.$class.'" href="'.$attachment_url.'">';
				$out.=get_the_title($attachment_id);

				$out.($attachment_id);
				$out.= '</a>';
			}
		}

    $out.='</div>';

	return $out;
}

 /********* log hook ***********/

add_action('pher_resdown','pher_resdown_log_trigger',10,3);

function pher_resdown_log_trigger($user_id , $status){
	return 'a returned var';
}

 /********** Librarian *****************/

add_action( 'init', 'lpf_librarian', 15 );
function lpf_librarian(){
	if(!isset($_REQUEST['lpf'])){return 0;}// abandon this function if there's no download request

	// theres a download requested ?
	$attachment_id=isset($_REQUEST['lpf'])?$_REQUEST['lpf']:null;

	//force download and obscure the real download link of a protected library file
	$mime=get_post_mime_type( $attachment_id);
	if (empty($mime)){$mime='application/octet-stream';}//or 'application/force-download'
	$filepath=get_attached_file( $attachment_id);
	$filename = basename($filepath);
	if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {$filename = preg_replace('/\./', '%2e', $filename, substr_count($filename, '.') - 1);}
	// workaround for IE filename bug with multiple periods / multiple dots in filename ??is this even still a bug??
	$filesize=@filesize( $filepath);
	clearstatcache();

	//TO DO user activity   log the download and user id
	//do some checking - download nonce and is  user logged in?

	$user_status = is_user_logged_in ();
	$protection = get_post_meta( $attachment_id, 'resource_protection', 1 );

	if(($protection=='protected' && $user_status==1) || empty($protection) || $protection=='public'){
		$hasaccess = true;
	}

	$nonce = isset($_REQUEST['_wpnonce'])?$_REQUEST['_wpnonce']:'';
	if (wp_verify_nonce( $nonce, 'download'.$attachment_id  ) ) {

	$userID = get_current_user_id();
	do_action('pher_resdown',$userID, $filename );// steves PLAINVIEW download watching new hook, defined in phua_logging / hooks

 	#force download  // condition check, it should not be empty, not a directory, and should be readable.
 	$forcedownload = empty($filepath) ? false : !is_file($filepath) ? false : is_readable($filepath);
	if ($forcedownload && file_exists($filepath) && $hasaccess==true) {  // user stat conditional updated  some files are PUBLIC !!!
    header("Content-Description: File Transfer");
    header("Content-Type:".$mime);
    header("Content-Disposition: attachment; filename=".$filename   );
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");
    header("Content-Length:".$filesize );
		readfile($filepath);}
		}else{
	// bounce them because they tried to link to a protected resource.  It would be nice if we could hook an error msg, but so far I havent./
			wp_redirect( home_url('/sign-in') );
		 	exit;
		}
}//end lpf_librarian
