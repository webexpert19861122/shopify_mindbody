<?php
class Process_model extends CI_Model
{
  function __construct() {
  }
  
  private function _checkRewardStatus($amount) {
    $level = 0;
    if ($amount > 1000) $level = 1;
    if ($amount > 2000) $level = 2;
    if ($amount > 5000) $level = 3;
    if ($amount > 10000) $level = 4;
    if ($amount > 20000) $level = 5;
    
    return $level;
  }
  
  public function order_create( $order, $shopInfo, $method = 'Order Created' )
  {
    $CI =& get_instance();

    // Load Models
    $CI->load->model( 'Order_model' );
    $CI->Order_model->rewriteParam( $shopInfo->shop );

    $CI->load->model( 'Purchase_model' );
    $CI->Purchase_model->rewriteParam($shopInfo->shop);

    $CI->load->model( 'Product_model' );
    $CI->Product_model->rewriteParam($shopInfo->shop);
    
    // Create Order
    $is_new = $CI->Order_model->add( $order );
    
    // Process with appointment
    foreach ($order->line_items as $line_item) {
      // ********** get the properties
        $clientId = 0;
        $locationId = '';
        $sessiontypeId = '';
        $staffId = '';
        $staffName = '';
        $staffGender = '';
        $startDateTime = '';
        $notes = '';
        
        // If no properties, skip it
        if (count($line_item->properties) == 0) continue;
        
        foreach ($line_item->properties as $property) {
          switch ($property->name) {
            case 'Transaction Key' :
              $temp = explode('_', base64_decode($property->value));
              $clientId = $temp[0];
              $locationId = $temp[1];
              $sessiontypeId = $temp[2];
              $staffId = $temp[3];
              $staffGender = $temp[4];
              $startDateTime = date('Y-m-d', $temp[5]) . 'T' . date('H:i:s', $temp[5]);
              break;
            case 'Specialist' : $staffName = $property->value; break;
            case 'Notes' : $notes = $property->value; break;
          }
        }
        
        // If the client id is not defined, skip it
        if ($clientId == 0) continue;
      
      // ********** Get the sessiontype_id
        $query = $CI->Product_model->getList(array('variant_id' => $line_item->variant_id));

        // If the variant is not registerd, skip it
        if ($query->num_rows() == 0) continue;
        
        $result = $query->result();
        $sessiontypeId = $result[0]->sessiontype_id;
        
        // If the sessiontype id is not defined, skip it
        if ($sessiontypeId == 0 || $sessiontypeId == '') continue;
      
      // ********** Add to purchse model
        $arrParam = array(
          'client_id' => $clientId,
          'variant_id' => $line_item->variant_id,
          'sessiontype_id' => $sessiontypeId,
          'order_id' => $order->id,
          'order_name' => $order->name,
          'qty' => $line_item->quantity
        );
        
        $this->Purchase_model->add($arrParam);
        
      // Book Appointment
      if ($startDateTime != '' && $clientId != 0 && $sessiontypeId != '') {
        $this->addAppointment($shopInfo->shop, $locationId, $sessiontypeId, $staffId, $staffName, $staffGender, $clientId, $startDateTime, $notes);
      }
    }
  }
  
  public function order_sync($shop, $storeInfo)
  {
    $CI =& get_instance();
    
    // Define the order models
    $CI->load->model( 'Order_model' );
    $CI->Order_model->rewriteParam($shop);
        
    $CI->load->model( 'Shopify_model' );
    $CI->Shopify_model->setStore( $shop, $storeInfo->app_id, $storeInfo->app_secret );
    
    // Get the lastest day
    $last_day = $CI->Order_model->getLastOrderDate();
    
    $param = 'status=any';
    if( $last_day != '' ) $param .= '&limit=250&processed_at_min=' . ( urlencode( $last_day ) );
    $action = 'orders.json?' . $param;

    // Retrive Data from Shop
    $orderInfo = $CI->Shopify_model->accessAPI( $action );
    foreach( $orderInfo->orders as $order )
    {
      $this->order_create($order, $storeInfo);
    }
  }
  
  public function product_create( $product, $shopInfo, $method = 'Product Create' )
  {
    $CI =& get_instance();
    
    // Define the order models
    $CI->load->model( 'Product_model' );
    $CI->Product_model->rewriteParam( $shopInfo->shop );
        
    // Define the Collections
    $product->categories = array();
    
    // Create Product
    $CI->Product_model->add( $product );
    
  }
  
  public function install( $shop, $app_id = '', $app_secret = '', $access_token = '' )
  {
    // ********* Register the Script Tags ********* //
    $CI =& get_instance();
    $CI->load->model( 'Shopify_model' );
    
    if( $app_id != '' ) $CI->Shopify_model->setStore( $shop, $app_id, $app_secret );
    if( $access_token != '' ) $CI->Shopify_model->rewriteParam( $shop, $access_token );

    // Define base url
    $base_url = $this->config->item('base_url') . $this->config->item('index_page');
    if( substr( $base_url, -1 ) != '/' ) $base_url .= '/';
    
    // Webhook
    foreach( $this->config->item('WEBHOOK_LIST') as $topic => $address )
    {
      $arrParam = array(
        'webhook' => array(
          'topic' => $topic,
          'address' => $base_url . 'endpoint/' . $address,
          'format' => 'json',
        ),
      );
      $return = $this->Shopify_model->accessAPI( 'webhooks.json', $arrParam, 'POST' );
    }
    
    $arrParam = array(
      'webhook' => array(
        'topic' => 'app/uninstalled',
        'address' => $base_url . 'install/uninstall',
        'format' => 'json',
      ),
    );
    
    $return = $this->Shopify_model->accessAPI( 'webhooks.json', $arrParam, 'POST' );
    
    // Script Tag
    foreach( $this->config->item('SCRIPT_TAG_LIST') as $script )
    {
      $arrParam = array(
        'script_tag' => array(
            'event' => 'onload',
            'display_scope' => 'all',
            'src' => base_url( $script ),
        ),
      );
      $return = $this->Shopify_model->accessAPI( 'script_tags.json', $arrParam, 'POST' );
    }
  }
      
  public function uninstall( $shop, $app_id = '', $app_secret = '', $access_token = '' )
  {
    // Get access token
    $CI =& get_instance();
    $CI->load->model( 'Shopify_model' );

    if( $app_id != '' ) $CI->Shopify_model->setStore( $shop, $app_id, $app_secret );
    if( $access_token != '' ) $CI->Shopify_model->rewriteParam( $shop, $access_token );
    
    // Delete webhooks
    $return = $CI->Shopify_model->accessAPI( 'webhooks.json' );

    if( isset( $return->webhooks ) && count( $return->webhooks ) > 0 )
    foreach( $return->webhooks as $webhook )
    {
      $returnDelete = $CI->Shopify_model->accessAPI( 'webhooks/' . $webhook->id . '.json', array(), 'DELETE' );
    }
    
    // Delete Script Tag
    $return = $CI->Shopify_model->accessAPI( 'script_tags.json' );
    
    if( isset( $return->script_tags ) && count( $return->script_tags ) > 0 )
    foreach( $return->script_tags as $tag )
    {
      $returnDelete = $CI->Shopify_model->accessAPI( 'script_tags/' . $tag->id . '.json', array(), 'DELETE' );
    }
  }     
  
  public function addAppointment($shop, $locationId, $sessiontypeId, $staffId, $staffName, $staffGender, $clientId, $startDateTime, $notes) {
        
    $CI =& get_instance();

    // Intialize Mindbody Credential
    $CI->load->library('MBAppointmentService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $CI->mbappointmentservice->SetDefaultCredentials($creds);
    
    $appointment = array(
      'Location' => array(
        'ID' => $locationId
      ),
      'SessionType' => array(
        'ID' => $sessiontypeId
      ),
      'Staff' => array(
        'ID' => $staffId,
        'isMale' => $staffGender
      ),
      'Client' => array(
        'ID' => $clientId
      ),
      'StartDateTime' => $startDateTime,
      'Notes' => $notes
    );
    
    $updateAction = 'AddNew';
    
    $result = $CI->mbappointmentservice->AddOrUpdateAppointments(array($appointment), $updateAction, false, true);

    $arrReturn = array(
      'error' => '',
      'message' => ''
    );

    if (isset($result->AddOrUpdateAppointmentsResult->Appointments->Appointment)) {
      $appointment = $result->AddOrUpdateAppointmentsResult->Appointments->Appointment;
      if ($result->AddOrUpdateAppointmentsResult->ErrorCode != '200' || !isset($appointment->ID)) {
        $arrReturn['error'] = $appointment->Messages->string;
      } else {
        // Add to Appointment
        $arrParam = array(
          'appointment_id' => $appointment->ID,
          'client_id' => $clientId,
          'sessiontype_id' => $sessiontypeId,
          'location_id' => $locationId,
          'staff_id' => $staffId,
          'staff_name' => $staffName,
          'start_at' => $startDateTime,
          'created_at' => date( $this->config->item('CONST_DATE_FORMAT')),
        );
        
        $CI->load->model('Appointment_model');
        $CI->Appointment_model->rewriteParam($shop);
        $CI->Appointment_model->add($arrParam);
      }
    } else {
      $arrReturn['error'] = 'Error on get Add Appointment';
      $arrReturn['message'] = json_encode($result);
    }
    
    return $arrReturn;    
  }
  
  public function cancelAppointment($shop, $appointmentId) {
    $CI =& get_instance();

    // Intialize Mindbody Credential
    $CI->load->library('MBAppointmentService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $CI->mbappointmentservice->SetDefaultCredentials($creds);
    
    $appointment = array(
      'ID' => $appointmentId,
      'Execute' => 'cancel'
    );
    
    $result = $CI->mbappointmentservice->AddOrUpdateAppointments(array($appointment), 'Update', false, true);

    $arrReturn = array(
      'error' => '',
      'message' => ''
    );

    if (isset($result->AddOrUpdateAppointmentsResult->Appointments)) {
      if ($result->AddOrUpdateAppointmentsResult->ErrorCode != '200') {
        $arrReturn['error'] = $result->AddOrUpdateAppointmentsResult->Appointments->Appointment->Messages->string;
      } else {
        // Delete Appointment
        $CI->load->model('Appointment_model');
        $CI->Appointment_model->rewriteParam($shop);
        $query = $CI->Appointment_model->getList(array('appointment_id' => $appointmentId));
        
        if ($query->num_rows() > 0) {
          $result = $query->result();
          $CI->Appointment_model->update($result[0]->id, array('status' => '2', 'cancel_at' => date( $this->config->item('CONST_DATE_FORMAT'))));
        }
      }
    } else {
      $arrReturn['error'] = 'Error on Delete Appointment';
      $arrReturn['message'] = json_encode($result);
    }
    
    return $arrReturn;
  }
  
  public function getLocations() {

    $CI =& get_instance();

    // Intialize Mindbody Credential
    $CI->load->library('MBSiteService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $CI->mbsiteservice->SetDefaultCredentials($creds);
    
    $result = $this->mbsiteservice->getLocations();
    
    // Build the return data
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );
    if ($result['error'] == '') {
      foreach ($result['data'] as $location) {
        $arrReturn['data'][] = array(
          'id' => $location->ID,
          'name' => $location->Name,
          'state' => isset($location->StateProvCode) ? $location->StateProvCode : '',
          'address1' => $location->Address,
          'address2' => $location->Address2,
          'city' => isset($location->City) ? $location->City : '',
          'postalcode' => isset($location->PostalCode) ? $location->PostalCode : '',
          'phone' => $location->Phone,
          'data' => base64_encode(json_encode($location))
        );
      }
    } else {
      $arrReturn['error'] = 'Error on get Locations';
      $arrReturn['message'] = json_encode($result);
    }
    
    return $arrReturn;
  }  
}  
?>
