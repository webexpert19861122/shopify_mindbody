<?php
class Purchase_model extends Master_model
{
  protected $_tablename = 'purchase';
  protected $_arrCondition = array(
    'client_id' => '=',
    'variant_id' => '=',
    'sessiontype_id' => '=',
    'order_id' => '=',
  );
  
  public function add($item) {
    
    // Check Exist
    $existId = '';
    $query = parent::getList(array('order_id' => $item['order_id'], 'variant_id' => $item['variant_id']));
    if ($query->num_rows() > 0) {
      return;
    }
    
    $item['created_at'] = date($this->config->item('CONST_DATE_FORMAT'));
    
    parent::add($item);
  }
  
  public function getPurchaseTotal($clientId, $sessiontypeId) {
    $this->db->select('SUM(qty) AS amount');
    $this->db->where('client_id', $clientId);
    $this->db->where('sessiontype_id', $sessiontypeId);
    $this->db->where('shop', $this->_shop);
    
    $query = $this->db->get($this->_tablename);
    if ($query->num_rows() == 0) return 0;

    $res = $query->result();

    return (int)$res[0]->amount;
  }
}  
?>
