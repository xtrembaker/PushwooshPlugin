<?php
class PushwooshGroup extends PushwooshAppModel {
  
  /**
   * Class name
   * 
   * @var string 
   * @author arnaudbusson
   */
	var $name = 'PushwooshGroup';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
  
  /**
   * Validate
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
      'PushwooshDevicesConsumer' => array(
          'className' => 'Pushwoosh.PushwooshDevicesConsumer',
          'joinTable' => 'pushwoosh_groups_devices',
          'foreignKey' => 'pushwoosh_group_id',
          'associationForeignKey' => 'pushwoosh_devices_consumer_id',
          'with' => 'PushwooshGroupsDevice',
      ),
      'PushwooshMessage' => array(
          'className' => 'Pushwoosh.PushwooshMessage',
          'joinTable' => 'pushwoosh_groups_messages',
          'foreignKey' => 'pushwoosh_group_id',
          'associationForeignKey' => 'pushwoosh_message_id',
          'with' => 'PushwooshGroupsMessage',
      ),
  );
  
  /**
   * Class constructor
   * 
   * @param type $id
   * @param type $table
   * @param type $ds
   * @author arnaudbusson
   */
  public function __construct($id = false, $table = NULL, $ds = NULL) {
    parent::__construct($id, $table, $ds);
    $this->validate = array(
        'name' => array(
            'rule' => 'notEmpty',
            'message' => __d('PushwooshPlugin','Model.PushwooshGroup.validate.name', true)
        ),
        'PushwooshGroup' => array(
            'rule' => 'validatePushwooshGroup',
            'message' => __d('PushwooshPlugin','Model.PushwooshMessage.validate.PushwooshGroup', true)
        )
    );
  }
  
  /**
   * check validation for "PushwooshGroup" key
   * 
   * @return boolean
   * @author arnaudbusson
   */
  public function validatePushwooshGroup(){
    if(!empty($this->data['PushwooshGroup']['PushwooshGroup'])) {
         return true;
     }
     return false;
  }
  
  /**
   * Return a list of all PushwooshGroup created
   * 
   * @author arnaudbusson
   */
  public function getAllPushwooshGroup(){
    return $this->find('all', array(
        'fields' => array(
            'COUNT(PushwooshGroupsDevice.pushwoosh_devices_consumer_id) AS count',
            'PushwooshGroup.id',
            'PushwooshGroup.name',
            'PushwooshGroup.created'
        ),
        'joins' => array(
            array(
                'table' => 'pushwoosh_groups_devices',
                'alias' => 'PushwooshGroupsDevice',
                'type' => 'left',
                'conditions' => array(
                    'PushwooshGroup.id = PushwooshGroupsDevice.pushwoosh_group_id'
                )
            )
        ),
        'group' => array('PushwooshGroup.id'),
        'recursive' => -1
    ));
  }
  
  /**
   * Return (partially) necessary data to edit a group
   * 
   * @param int $id
   * @author arnaudbusson
   */
  public function getPushwooshGroupDataForEdit($id = null){
    if(isset($id)){
      $group = $this->find('first', array(
          'conditions' => array('PushwooshGroup.id' => $id),
          'recursive' => 2
      ));
      if(!empty($group)){
        return $group;
      }
    }
    return false;
  }
  
  /**
   * Return a list of all groups
   * 
   * @return array
   * @author arnaudbusson
   */
  public function listAllGroups(){
    return $this->find('list', array(
        'fields' => array('PushwooshGroup.id', 'PushwooshGroup.name')
    ));
  }
  
  /**
   * Prepare Data for pagination
   * 
   * @return type
   * @author arnaudbusson
   */
  public function prepareDataForPagination(){
    return array('fields' => array(
            'COUNT(PushwooshGroupsDevice.pushwoosh_devices_consumer_id) AS count',
            'PushwooshGroup.id',
            'PushwooshGroup.name',
            'PushwooshGroup.created'
        ),
        'joins' => array(
            array(
                'table' => 'pushwoosh_groups_devices',
                'alias' => 'PushwooshGroupsDevice',
                'type' => 'left',
                'conditions' => array(
                    'PushwooshGroup.id = PushwooshGroupsDevice.pushwoosh_group_id'
                )
            )
        ),
        'group' => array('PushwooshGroup.id'),
        'recursive' => -1
    );
  }
  
  /**
   * Override's paginateCount
   * 
   * @param type $conditions
   * @param type $recursive
   * @param type $extra
   * @return type
   * @author arnaudbusson
   */
  public function paginateCount($conditions = null, $recursive = 0, $extra = array()){
    return count($this->find('all', $this->prepareDataForPagination()));
  }
  
}
