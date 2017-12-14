<?php
class Order_model extends Master_model
{
    protected $_tablename = 'orderlist';
    protected $_getlistFields = "id, order_id, order_name, created_at, customer_name, customer_id, amount, fulfillment_status, coupon_code, num_products, country";
    protected $_arrCondition = array(
      'customer_name' => '%LIKE%',
      'order_name' => '%LIKE%',
      'created_at' => 'LIKE%',
      'customer_id' => '=',
      'order_id' => '=',
      'id' => 'IN',
    );
    
    function __construct() {
      parent::__construct();
    }

    // Get the lastest order date
    public function getLastOrderDate()
    {
        $return = '';
        
        $this->db->select( 'created_at' );
        $this->db->order_by( 'created_at DESC' );
        $this->db->limit( 1 );
        $this->db->where( 'shop', $this->_shop );
        
        $query = $this->db->get( $this->_tablename );
        
        if( $query->num_rows() > 0 )
        {
            $res = $query->result();
            
            $return = $res[0]->created_at;
        }
        
        return $return;
    }
    
    /**
    * Add order and check whether it's exist already
    * 
    * @param mixed $order
    */
    public function add( $order )
    {
      // Check the order is exist already
      $query = $this->getList( array('order_id' => $order->id));
      if( $query->num_rows() > 0 ) return false;
      
      $customer_name = '';
      if( isset( $order->customer)) $customer_name = $order->customer->first_name . ' ' . $order->customer->last_name;
      
      $country = '';
      if( isset($order->shipping_address->country_code)) $country = $order->shipping_address->country_code;
      
      // Insert data
      $data = array(
        'order_id' => $order->id,
        'customer_id' => isset($order->customer) ? $order->customer->id : '',
        'customer_name' => $customer_name,
        'order_name' => $order->name,
        'created_at' => date( $this->config->item('CONST_DATE_FORMAT'), strtotime( $order->created_at )) ,
        'created_at_m' => date('Y-m', strtotime( $order->created_at )) ,
        'amount' => $order->total_price,
        'country' => $country,
        'num_products' => count($order->line_items),
        'fulfillment_status' => empty($order->fulfillment_status) ? '' :  $order->fulfillment_status,
        'coupon_code' => count($order->discount_codes) > 0 ? $order->discount_codes[0]->code : '',
        'data' => base64_encode( json_encode( $order ) ),
      );

      parent::add( $data );
      
      return true;
    }
    
    public function getCustomerTotal($customerId) {
      $arrReturn = array(
        'prev' => array( '0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => '0' ),
        'cur' => array( '0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => '0' ),
      );
      
      $this->db->where('shop' , $this->_shop);
      $this->db->where('customer_id' , $customerId);
      $this->db->where('created_at >=\'' . (date('Y') - 1) . '-01-01\'');
      $this->db->group_by('created_at_m');
      $this->db->select('SUM(amount) AS amount, created_at_m');
      $query = $this->db->get($this->_tablename);
      
      foreach ($query->result() as $row) {
        // Get quarter number
        $idxQuarter = 1;
        switch (substr($row->created_at_m, 5)) {
          case '01':
          case '02':
          case '03':
            $idxQuarter = 1;
            break;
          case '04':
          case '05':
          case '06':
            $idxQuarter = 2;
            break;
          case '07':
          case '08':
          case '09':
            $idxQuarter = 3;
            break;
          case '10':
          case '11':
          case '12':
            $idxQuarter = 4;
            break;
        }
        
        if (substr($row->created_at_m, 0, 4) == date('Y')) {
          $arrReturn['cur'][$idxQuarter] += $row->amount;
          $arrReturn['cur'][0] += $row->amount;
        } else {
          $arrReturn['prev'][$idxQuarter] += $row->amount;
          $arrReturn['prev'][0] += $row->amount;
        }
      }
      
      return $arrReturn;
    }
    
    public function getCustomerRebate($year, $q) {
      // Make the Quarter array
      $arrQ = array();
      for ($i = 1; $i <= 3; $i ++) $arrQ[] = $year . '-' . (($q - 1) * 3 + $i);
      
      $this->db->select('customer_name, SUM(amount) AS spend');
      $this->db->where('shop' , $this->_shop);
      $this->db->where('created_at_m IN ' . str_replace(']', ')', str_replace('[', '(', json_encode($arrQ))));
      $this->db->group_by('customer_id');
      $query = $this->db->get($this->_tablename);
      
      return $query;
    }
    // ********************** //
}  
?>
