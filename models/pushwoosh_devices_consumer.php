<?php
class PushwooshDevicesConsumer extends PushwooshAppModel {
	var $name = 'PushwooshDevicesConsumer';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
  
  var $validate = array();
  
  /**
   * BeforeValidate callback
   * 
   * @param type $options
   * @return boolean
   */
//  public function beforeValidate($options = array()) {
//    parent::beforeValidate($options);
//    if(true === $this->checkDeviceAlreadyExists()){
//      return false;
//    }
//    return true;
//  }
  
  /**
   * Return true if one record was found using those keys in parameters
   * 
   * @param type $push_token
   * @param type $hw_id
   * @return boolean
   */
  public function checkDeviceAlreadyExists($push_token = null, $hwid = null){
    if(!isset($push_token) 
        && !isset($hwid) 
        && array_key_exists('push_token', $this->data[$this->alias])
        && array_key_exists('hwid', $this->data[$this->alias])){
      $push_token = $this->data[$this->alias]['push_token'];
      $hwid = $this->data[$this->alias]['hwid'];
    }
    $count = $this->find('count', array(
        'conditions' => array(
            $this->alias.'.push_token' => $push_token,
            $this->alias.'.hwid' => $hwid
        ))
    );
    if($count > 0){
      return true;
    }
    return false;
  }
  
  /**
   * Constructor's class
   * 
   * @param type $id
   * @param type $table
   * @param type $ds
   */
  public function __construct($id = false, $table = NULL, $ds = NULL) {
    parent::__construct($id, $table, $ds);
    $this->validate = array(
        'push_token' => array(
            'rule' => 'notEmpty',
            'message' => __d('PushwooshPlugin','Model.PushwooshDeviceConsumer.validate.push_token', true)
        ),
        'hw_id' => array(
            'rule' => 'notEmpty',
            'message' => __d('PushwooshPlugin','Model.PushwooshDeviceConsumer.validate.hw_id', true)
        )
    );
  }
  
  /**
   * 
   * 
   * @param type $foreign_key
   * @param type $model
   */
  public function checkConsumerHasAlreadyDevice($foreign_key, $model){
    $count = $this->find('count', array(
        'conditions' => array(
            'PushwooshDevice.foreign_key' => $foreign_key,
            'PushwooshDevice.model' => $model
          )
    ));
    if($count > 0){
      return true;
    }
    return false;
  }
  
  public function deleteConsumerDevice($foreign_key, $model){
    $conditions = array(
            'PushwooshDeviceConsumer.foreign_key' => $id,
            'PushwooshDeviceConsumer.model' => 'Consumer'
          );
    $this->deleteAll($conditions);
  }
  
  /**
   * 
   * @param type $push_token
   * @param type $hwid
   * @return boolean
   */
  public function getDeviceConsumerByDeviceData($push_token, $hwid){
    $record = $this->find('first', array(
        'conditions' => array(
            $this->alias.'.push_token' => $push_token,
            $this->alias.'.hwid' => $hwid
        )
    ));
    if(!empty($record)){
      return $record;
    }
    return false;
  }
  
  /**
   * 
   * @param type $foreign_key
   * @param type $model
   * @return boolean
   */
  public function getDeviceConsumerByConsumerData($foreign_key, $model){
    $record = $this->find('first', array(
        'conditions' => array(
            $this->alias.'.foreign_key' => $foreign_key,
            $this->alias.'.model' => $model
        )
    ));
    if(!empty($record)){
      return $record;
    }
    return false;
  }
  
}
