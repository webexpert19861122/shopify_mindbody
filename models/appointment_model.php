<?php
class Appointment_model extends Master_model
{
  protected $_tablename = 'appointment';
  protected $_arrCondition = array(
    'client_id' => '=',
    'appointment_id' => '=',
    'sessiontype_id' => '=',
    'staff_id' => '=',
    'location_id' => '=',
  );
  
  public function getAppointmentTotal($clientId, $sessiontypeId) {
    $this->db->select('COUNT(id) AS amount');
    $this->db->where('client_id', $clientId);
    $this->db->where('sessiontype_id', $sessiontypeId);
    $this->db->where('shop', $this->_shop);
    $query = $this->db->get($this->_tablename);
    
    if ($query->num_rows() == 0) return 0;

    $res = $query->result();

    return (int)$res[0]->amount;
  }
  
  public function add($appointment) {
    $existId = '';
    
    // Check Exist
    $isUpdate = false;
    $query = $this->getList( array('appointment_id' => $appointment['appointment_id']));
    if( $query->num_rows() > 0 ) {
      $isUpdate = true;
      $result = $query->result();
      $existId = $result[0]->id;
    }
    
    // Apply to DB
    if(!$isUpdate) {
      parent::add( $appointment );
    } else {
      parent::update($existId, $appointment );
    }
  }  
}  
?>
