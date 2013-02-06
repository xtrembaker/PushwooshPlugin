<?php
/**
 * @author arnaudbusson
 */
class PushwooshHelper extends AppHelper {
    
    /**
     * Return the name of the device, according to its type
     * 
     * @param int $device_type
     * @return string
     * @author arnaudbusson
     */
    public function getDeviceTypeById($device_type){
        $list = $this->listDeviceType();
        if(array_key_exists($device_type, $list)){
          return $list[$device_type];
        }
        return "-";
    }
    
    /**
     * List all device (int => name) used by Pushwoosh
     * 
     * @return array
     * @author arnaudbusson
     */
    public function listDeviceType(){
      return array(
          1 => 'iPhone',
          2 => 'BlackBerry',
          3 => 'Android',
          4 => 'Nokia',
          5 => 'Windows Phone',
          7 => 'Mac'
      );
    }
}

?>
