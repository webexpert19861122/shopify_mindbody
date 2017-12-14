<?php
class Sessiontype_model extends Master_model
{
  protected $_tablename = 'sessiontype';
  protected $_arrCondition = array(
    'sessiontype_id' => '=',
    'program_id' => '=',
  );
  
  public function add($location) {
    
    // Check Exist
    $existId = '';
    $query = parent::getList(array('sessiontype_id' => $location->ID));
    if ($query->num_rows() > 0) {
      $result = $query->result();
      $existId = $result[0]->id;
    }
    
    $arrParam = array(
      'sessiontype_id' => $location->ID,
      'program_id' => $location->ProgramID,
      'name' => $location->Name,
      'length' => $location->DefaultTimeLength,
      'created_at' => date($this->config->item('CONST_DATE_FORMAT')),
    );
    
    if ($existId == '')
      parent::add($arrParam);
    else
      parent::update($existId, $arrParam);
  }
}  
?>
