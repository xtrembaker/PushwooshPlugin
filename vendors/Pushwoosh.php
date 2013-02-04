<?php

class Pushwoosh{
  
  /**
   * API Token
   * @var type 
   */
  public $pw_auth;
  
  /**
   * Application Code
   * @var type 
   */
  public $pw_application;
  
  /**
   * doPostRequest
   * 
   * @param type $url
   * @param type $data
   * @param type $optional_headers
   * @return boolean
   * @throws Exception
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
     */
    public function pwCall( $action, $data = array() ) {
        $url = 'https://cp.pushwoosh.com/json/1.3/' . $action;
        $json = json_encode( array( 'request' => $data ) );
        pr($json);
        $res = $this->doPostRequest( $url, $json, 'Content-Type: application/json' );
        return @json_decode( $res, true );
    }
    
    public function test(){
      $data = array(
          'application' => 'B477A-5ED44',
          'auth' => 'MJidGxXMjorkwqldaqH2vktvVXZQfMYDButpxPDIRCFh4Tx2rcsAko2p8PdL57aWVgwNp4nTqo++FFOu3JfP',
          'notifications' => array(
              array(
                //'send_date' => '2013-02-04 15:17:00',
                'send_date' => 'now',
                'content' => array(
                    //'en' => 'English message to both mobile',
                    'fr' => 'Message a Arnaud'
                ),
                'platforms' => array(1,2,3,4,5,6,7),
                'ios_badges' => 1,
                'devices' => array(
                    //'58597ecffd86696dea76e924302e696188d5074fcdd0dc067bead9060691fb25',
                    'acb82950fe088a5d111b21bf62fd195819299ed992c63952202e6d3a810f9e6a'
                )
            )
          )
      );
      
      return $this->pwCall('createMessage', $data);
    }
}
?>