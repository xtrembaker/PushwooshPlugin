<?php
class PushwooshAppModel extends AppModel {
 
  public $recursive = -1;
  /**
   * 
   * @param type $queryData
   */
  public function beforeFind($queryData) {
    if(!array_key_exists('recursive', $queryData)){
      $queryData['recursive'] = -1;
    }
    parent::beforeFind($queryData);
  }
  
}