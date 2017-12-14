<?php
class Program_model extends Master_model
{
  protected $_tablename = 'program';
  protected $_arrCondition = array(
    'program_id' => '='
  );
  
  public function add($location) {
    
    // Check Exist
    $existId = '';
    $query = parent::getList(array('program_id' => $location->ID));
    if ($query->num_rows() > 0) {
      $result = $query->result();
      $existId = $result[0]->id;
    }
    
    $arrParam = array(
      'program_id' => $location->ID,
      'name' => $location->Name,
      'created_at' => date($this->config->item('CONST_DATE_FORMAT')),
      'data' => base64_encode(json_encode($location)),
    );
    
    if ($existId == '')
      parent::add($arrParam);
    else
      parent::update($existId, $arrParam);
  }
}  
?>
