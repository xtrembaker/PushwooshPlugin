<?php

class PushwooshApi{
  
  /**
   * API Token
   * 
   * @var string
   * @author arnaudbusson
   */
  public $pw_auth;
  
  /**
   * Application Code
   * 
   * @var string
   * @author arnaudbusson
   */
  public $pw_application;
  
  /**
   * Devices (push_token) you'd like to send the message
   * 
   * @var array
   * @author arnaudbusson
   */
  public $devices = array();
  
  /**
   * Message to send
   * 
   * @var array
   * @author arnaudbusson
   */
  public $content = array('fr' => '', 'en' => '');
  
  public function __construct($api_key, $pw_application) {
    $this->pw_auth = $api_key;
    $this->pw_application = $pw_application;
  }
  
  /**
   * doPostRequest
   * 
   * @param type $url
   * @param type $data
   * @param type $optional_headers
   * @return boolean
   * @throws Exception
   * @author arnaudbusson
   */
  public function doPostRequest($url, $data, $optional_headers = null) {
      $params = array(
          'http' => array(
              'method' => 'POST',
              'content' => $data
          ));
      if ($optional_headers !== null)
          $params['http']['header'] = $optional_headers;

      $ctx = stream_context_create($params);
      $fp = fopen($url, 'rb', false, $ctx);
      if (!$fp)
          throw new Exception("Problem with $url, $php_errmsg");

      $response = @stream_get_contents($fp);
      if ($response === false)
          return false;
      return $response;
  }
    
  /**
   * pwCall
   * 
   * @param type $action
   * @param type $data
   * @author arnaudbusson
   */
  public function pwCall( $action, $data = array() ) {
      $url = 'https://cp.pushwoosh.com/json/1.3/' . $action;
      $json = json_encode( array( 'request' => $data ) );
      $res = $this->doPostRequest( $url, $json, 'Content-Type: application/json' );
      return @json_decode( $res, true );
  }


  /**
   * Create Message API Method
   * 
   * @return string
   * @author arnaudbusson
   */
  public function createMessage(){
    $data = array(
        'application' => $this->pw_application,
        'auth' => $this->pw_auth,
        'notifications' => array(
            array(
              //'send_date' => '2013-02-04 15:17:00',
              'send_date' => 'now',
              'content' => $this->content,
              'platforms' => array(1,2,3,4,5,6,7),
              'ios_badges' => 1,
              'devices' => $this->devices
          )
        )
    );
    return $this->pwCall('createMessage', $data);
  }
}
?>