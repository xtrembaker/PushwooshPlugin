<?php
class PushwooshDevicesConsumer extends PushwooshAppModel {
  
  /**
   * Class name
   * 
   * @var string
   * @author arnaudbusson
   */
	var $name = 'PushwooshDevicesConsumer';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
  
  /**
   * Validator
   * 
   * @var array
   * @author arnaudbusson
   */
  var $validate = array();
  
  /**
   * $hasAndBelongsToMany
   * 
   * @var array 
   * @author arnaudbusson
   */
  var $hasAndBelongsToMany = array(
      'PushwooshGroup' => array(
          'className' => 'Pushwoosh.PushwooshGroup',
          'joinTable' => 'pushwoosh_groups_devices',
          'foreignKey' => 'pushwoosh_devices_consumer_id',
          'associationForeignKey' => 'pushwoosh_group_id',
          'with' => 'PushwooshGroupsDevice'
      )
  );
  
  /**
   * Return true if one record was found using those keys in parameters
   * 
   * @param type $push_token
   * @param type $hw_id
   * @return boolean
   * @author arnaudbusson
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
        ),
        // Used when validating HABTM from PushwooshGroup model
        'PushwooshDevicesConsumer' => array(
            'rule' => 'validatePushwooshDevicesConsumer',
            'message' => __d('PushwooshPlugin','Model.PushwooshDeviceConsumer.validate.habtm', true)
        )
    );
  }
  
  /**
   * Check validation for "PushwooshDevicesConsumer"
   * 
   * @return boolean
   * @author arnaudbusson
   */
  public function validatePushwooshDevicesConsumer(){
    if(!empty($this->data['PushwooshDevicesConsumer']['PushwooshDevicesConsumer'])) {
         return true;
     }
     return false;
  }
  
  /**
   * Return a PushwooshDevicesConsumer record according to parameters
   *  
   * @param type $push_token
   * @param type $hwid
   * @return boolean
   * @author arnaudbusson
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
   * Return all push_token associated with the $devices_consumers_id key
   * 
   * @param type $devices_consumers_id
   * @return array
   * @author arnaudbusson
   */
  public function getAllPushTokenByDeviceConsumerId($devices_consumers_id){
    $push_token = $this->find('all', array(
        'fields' => array('PushwooshDevicesConsumer.push_token'),
        'conditions' => array('PushwooshDevicesConsumer.id' => $devices_consumers_id),
        'recursive' => -1
    ));
    if(!empty($push_token)){
      return Set::extract('/PushwooshDevicesConsumer/push_token', $push_token);
    }
    return array();
  }
  
}
