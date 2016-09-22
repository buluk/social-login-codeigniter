<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH .'third_party/src/Google_Client.php';
require APPPATH .'third_party/src/contrib/Google_Oauth2Service.php';
class Login extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('facebook'); // Automatically picks appId and secret from config
    }

	
	public function facebook()
	{
		
        $user = $this->facebook->getUser();
        if ($user) {
            try {
                    $data['user_profile'] = $this->facebook->api('/me?fields=name,email,first_name,last_name');
                    $profile = $this->facebook->api('/me?fields=name,email,first_name,last_name');
                    $data['url'] = site_url('login/logout'); // Logs off application
                    
    	            $sess_array['fb_id'] = $profile['id'];
    	            $sess_array['profilepic'] = 'https://graph.facebook.com/'.$profile['id'].'/picture?type=large';
    	            $sess_array['first_name'] = $profile['first_name'];
                	$sess_array['last_name'] = $profile['last_name'];
    	            $this->session->set_userdata('logged_in', $sess_array);
                } 
                catch (FacebookApiException $e) {
                    $user = null;
                }
            } 
            else {
	            $data['url'] = $this->facebook->getLoginUrl(array(
	                'redirect_uri' => base_url().'login', 
	                'scope' => array("email","publish_actions") // permissions here
	            ));
                
        	}
		    //print_r($data); die;
			$this->load->view('view_page', $data);
           
	}
	

	public function google(){

        /*google login*/
        $clientId = '299544866433-0ki6k82l9mb1iskgr830eq2c9vgl65p8.apps.googleusercontent.com';
        $clientSecret = 'TAS5fSbko6tQQWPjQzfqFZMa';
        $redirectUrl = base_url().'login/google'; #Add redirect url which you add in google console.

        $gClient = new Google_Client();
        $gClient->setApplicationName('MINDLER');
        $gClient->setClientId($clientId);
        $gClient->setClientSecret($clientSecret);
        $gClient->setRedirectUri($redirectUrl);
        $gClient->setApprovalPrompt('auto') ;

        $google_oauthV2 = new Google_Oauth2Service($gClient);
        
        if(!empty($_GET['code'])){
            $gClient->authenticate($_GET['code']);
            $_SESSION['token'] = $gClient->getAccessToken();
            $gClient->setAccessToken($_SESSION['token']);
            $gClient->getAccessToken();
            if ($gClient->getAccessToken()) {
            $profile = $google_oauthV2->userinfo->get();

            $sess_array['google_id'] = $profile['id'];
            $sess_array['profilepic'] = $profile['picture'];
            $sess_array['first_name'] = $profile['given_name'];
            $sess_array['last_name'] = $profile['family_name'];
            $this->session->set_userdata('logged_in', $sess_array);
        }else{
            $gClient->authenticate();
        }
        
    }

}
