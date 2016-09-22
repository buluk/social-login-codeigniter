<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
set_include_path(APPPATH . 'third_party/' . PATH_SEPARATOR . get_include_path());
session_start();

class Googleplus {
  
  public function __construct() {
    
    $CI =& get_instance();
    $CI->config->load('googleplus');
    
    require APPPATH .'third_party/src/Google_Client.php';
    require APPPATH .'third_party/src/contrib/Google_Oauth2Service.php';

    $cache_path = $CI->config->item('cache_path');
    $GLOBALS['apiConfig']['ioFileCache_directory'] = ($cache_path == '') ? APPPATH .'cache/' : $cache_path;
  }


  public function getUser($code) {
    
    $gClient = new Google_Client();
    $gClient->setApplicationName($CI->config->item('application_name', 'googleplus'));
    $gClient->setClientId($CI->config->item('client_id', 'googleplus'));
    $gClient->setClientSecret($CI->config->item('client_secret', 'googleplus'));
    $gClient->setRedirectUri($CI->config->item('redirect_uri', 'googleplus'));
    $google_oauthV2 = new Google_Oauth2Service($gClient);
    
    if(!empty($code)){
        $gClient->authenticate($code);
        $_SESSION['token'] = $gClient->getAccessToken();
        $gClient->setAccessToken($_SESSION['token']);
        $gClient->getAccessToken();
        if ($gClient->getAccessToken()) {
        $userProfile = $google_oauthV2->userinfo->get();
        return $userProfile;
        }
    }else{
        $gClient->authenticate();
    }
  }
  
}
?>