<?php
class PushwooshMessage extends PushwooshAppModel {
  
  /**
   * Class name
   * 
   * @var string
   * @author arnaudbusson
   */
	var $name = 'PushwooshMessage';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
  
  /**
   * Validator
   * 
   * @var array
   * @author arnaudbusson
   */
  var $validate = array();
  
  /**
   * HABTM
   * 
   * @var array
   * @author arnaudbusson
   */
  var $hasAndBelongsToMany = array(
      'PushwooshGroup' => array(
          'className' => 'Pushwoosh.PushwooshGroup',
          'joinTable' => 'pushwoosh_groups_messages',
          'foreignKey' => 'pushwoosh_message_id',
          'associationForeignKey' => 'pushwoosh_group_id',
          'with' => 'PushwooshGroupsMessage',
      ),
      'PushwooshDevicesConsumer' => array(
          'className' => 'Pushwoosh.PushwooshDevicesConsumer',
          'joinTable' => 'pushwoosh_consumers_messages',
          'foreignKey' => 'pushwoosh_message_id',
          'associationForeignKey' => 'pushwoosh_devices_consumer_id',
          'with' => 'PushwooshConsumersMessage',
      )
  );
  
  /**
   * Constructor class
   * 
   * @param type $id
   * @param type $table
   * @param type $ds
   * @author arnaudbusson
   */
  public function __construct($id = false, $table = NULL, $ds = NULL) {
    parent::__construct($id, $table, $ds);
    $this->validate = array(
        'content' => array(
            'rule' => 'notEmpty',
            'message' => __d('PushwooshPlugin','Model.PushwooshMessage.validate.content', true)
        )
    );
  }
  
  /**
   * Before's validate callback
   * 
   * @param type $options
   * @return boolean
   * @author arnaudbusson
   */
  public function beforeValidate($options = array()) {
    parent::beforeValidate($options);
    // If PushwooshGroup is filled, disable validation rule on PushwooshDevicesConsumer
    if(!empty($this->data['PushwooshGroup']['PushwooshGroup'])){
      unset($this->PushwooshDevicesConsumer->validate['PushwooshDevicesConsumer']);
    }
    // If PushwooshDevicesConsumer is filled, disable validation rule on PushwooshGroup
    if(!empty($this->data['PushwooshDevicesConsumer']['PushwooshDevicesConsumer'])){
      unset($this->PushwooshGroup->validate['PushwooshGroup']);
    }
    return true;
  }
  
  /**
   * Return a list of all PushWoosh Messages
   * 
   * @author arnaudbusson
   */
  public function getAllPushwooshMessages(){
    return $this->find('all', array(
        'fields' => array(
            'COUNT(PushwooshConsumersMessage.pushwoosh_devices_consumer_id) as consumers_reached',
            'COUNT(PushwooshGroupsMessage.pushwoosh_group_id) as groups_reached',
            'PushwooshMessage.content',
            'PushwooshMessage.sent_date',
            'PushwooshMessage.created',
            'PushwooshMessage.id'
            
        ),
        'joins' => array(
            array(
                'table' => 'pushwoosh_groups_messages',
                'alias' => 'PushwooshGroupsMessage',
                'type' => 'left',
                'conditions' => array(
                    'PushwooshGroupsMessage.pushwoosh_message_id = PushwooshMessage.id'
                )
            ),
            array(
                'table' => 'pushwoosh_consumers_messages',
                'alias' => 'PushwooshConsumersMessage',
                'type' => 'left',
                'conditions' => array(
                    'PushwooshConsumersMessage.pushwoosh_message_id = PushwooshMessage.id'
                )
            )
        ),
        'recursive' => -1,
        'group' => array('PushwooshMessage.id')
    ));
  }
  
  /**
   * Return a list of all push_token according to the message id passed in
   * parameter
   * 
   * @param int $id
   * @author arnaudbusson
   */
  public function getAllPushTokenByMessageId($id = null){
    if(!isset($id)){
      $id = $this->id;
    }
    $results = $this->find('all', array(
        'fields' => array(
            'PushwooshConsumersMessage.pushwoosh_devices_consumer_id',
            'PushwooshGroupsDevice.pushwoosh_devices_consumer_id',
        ),
        'conditions' => array('PushwooshMessage.id' => $id),
        'joins' => array(
            array(
                'table' => 'pushwoosh_consumers_messages',
                'alias' => 'PushwooshConsumersMessage',
                'type' => 'left',
                'conditions' => array(
                    'PushwooshConsumersMessage.pushwoosh_message_id = PushwooshMessage.id'
                )
            ),
            array(
                'table' => 'pushwoosh_groups_messages',
                'alias' => 'PushwooshGroupsMessage',
                'type' => 'left',
                'conditions' => array(
                    'PushwooshGroupsMessage.pushwoosh_message_id = PushwooshMessage.id'
                )
            ),
            array(
                'table' => 'pushwoosh_groups_devices',
                'alias' => 'PushwooshGroupsDevice',
                'type' => 'left',
                'conditions' => array(
                    'PushwooshGroupsDevice.pushwoosh_group_id = PushwooshGroupsMessage.pushwoosh_group_id'
                )
            )
        ),
        'recursive' => 2
    ));
    $devices_consumer_id = array();
    
    // Loop through results
    foreach($results as $result){
      if(!empty($result['PushwooshConsumersMessage']['pushwoosh_devices_consumer_id'])){
        $devices_consumer_id[] = $result['PushwooshConsumersMessage']['pushwoosh_devices_consumer_id'];
      }
      if(!empty($result['PushwooshGroupsDevice']['pushwoosh_devices_consumer_id'])){
        $devices_consumer_id[] = $result['PushwooshGroupsDevice']['pushwoosh_devices_consumer_id'];
      }
    }
    $devices_consumer_id = array_unique($devices_consumer_id);
    // Return all push_tokens
    return $this->PushwooshDevicesConsumer->getAllPushTokenByDeviceConsumerId($devices_consumer_id);
  }
}
