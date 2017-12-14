<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Endpoint extends MY_Controller {

  private $_shop = '';
  private $_inputInfo = array();
  private $_message = '';
  private $_shopifydelay = 0.9;
  
  private $_log_file = false;
  private $_log_message = true;
      
  public function __construct() {
    parent::__construct();
    
    ini_set( 'max_execution_time', '40000' );
    
    // Shopify Delay
    $this->_shopifydelay = $this->_shopifydelay * 1000000;
    
    // Load Model    
    $this->load->model( 'Log_model' );
    
    // Define a header
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: *");
    header('Content-Type: application/json');
    
    // Get the shop from the HTTP Header or private shop  
    $this->_shop = isset( $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'] ) ? $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'] : $this->config->item('PRIVATE_SHOP');

    // Get the Input Stream
    $this->_inputInfo = json_decode( file_get_contents('php://input') );

    if( !isset($this->_inputInfo->id ) )
    {
      $strTemp = '{"id":50239340555,"email":"jan.schmidt@outlook.com","closed_at":"2017-11-24T00:49:34-05:00","created_at":"2017-11-22T22:27:45-05:00","updated_at":"2017-11-24T00:49:34-05:00","number":1,"note":null,"token":"bbddf10cfc6ac4e8885cad7ce14c0262","gateway":null,"test":false,"total_price":"0.00","subtotal_price":"0.00","total_weight":0,"total_tax":"0.00","taxes_included":false,"currency":"USD","financial_status":"paid","confirmed":true,"total_discounts":"0.00","total_line_items_price":"0.00","cart_token":"71ac5d8af3a096f74bc7eb57363b58b3","buyer_accepts_marketing":false,"name":"#1001","referring_site":"","landing_site":"\/","cancelled_at":null,"cancel_reason":null,"total_price_usd":"0.00","checkout_token":"4e70e6ee5577953ef5608c776dd063a0","reference":null,"user_id":null,"location_id":null,"source_identifier":null,"source_url":null,"processed_at":"2017-11-22T22:27:45-05:00","device_id":null,"phone":null,"customer_locale":"en","app_id":580111,"browser_ip":null,"landing_site_ref":null,"order_number":1001,"discount_codes":[],"note_attributes":[],"payment_gateway_names":[],"processing_method":"free","checkout_id":119874584587,"source_name":"web","fulfillment_status":"fulfilled","tax_lines":[],"tags":"","contact_email":"jan.schmidt@outlook.com","order_status_url":"https:\/\/bodyfactoryskincare.com\/22460313\/orders\/bbddf10cfc6ac4e8885cad7ce14c0262\/authenticate?key=1a5f092ed8a428b318f95ccba18254aa","line_items":[{"id":92112453643,"variant_id":1582637973515,"title":"Intro Consultation","quantity":1,"price":"0.00","grams":0,"sku":"","variant_title":"Botox + Dermal Fillers","vendor":"BodyFactorySkinCare","fulfillment_service":"manual","product_id":182702931979,"requires_shipping":false,"taxable":true,"gift_card":false,"name":"Intro Consultation - Botox + Dermal Fillers","variant_inventory_management":null,"properties":[{"name":"client_id","value":"100013143"}],"product_exists":true,"fulfillable_quantity":0,"total_discount":"0.00","fulfillment_status":"fulfilled","tax_lines":[],"origin_location":{"id":3730921227,"country_code":"US","province_code":"NY","name":"BodyFactorySkinCare","address1":"726 9th Ave","address2":"","city":"New York","zip":"10019"}},{"id":92112486411,"variant_id":1582638039051,"title":"Intro Consultation","quantity":1,"price":"0.00","grams":0,"sku":"","variant_title":"Laser Treatments","vendor":"BodyFactorySkinCare","fulfillment_service":"manual","product_id":182702931979,"requires_shipping":false,"taxable":true,"gift_card":false,"name":"Intro Consultation - Laser Treatments","variant_inventory_management":null,"properties":[{"name":"client_id","value":"100013143"}],"product_exists":true,"fulfillable_quantity":0,"total_discount":"0.00","fulfillment_status":"fulfilled","tax_lines":[],"origin_location":{"id":3730921227,"country_code":"US","province_code":"NY","name":"BodyFactorySkinCare","address1":"726 9th Ave","address2":"","city":"New York","zip":"10019"}}],"shipping_lines":[],"billing_address":{"first_name":"schmidt","address1":"Werneuchener str","phone":null,"city":"Berlin","zip":"13055","province":null,"country":"Germany","last_name":"jan","address2":"","company":null,"latitude":52.540791,"longitude":13.4944867,"name":"schmidt jan","country_code":"DE","province_code":null},"fulfillments":[{"id":47198830603,"order_id":50239340555,"status":"success","created_at":"2017-11-24T00:49:33-05:00","service":"manual","updated_at":"2017-11-24T00:49:33-05:00","tracking_company":null,"shipment_status":null,"tracking_number":null,"tracking_numbers":[],"tracking_url":null,"tracking_urls":[],"receipt":{},"line_items":[{"id":92112453643,"variant_id":1582637973515,"title":"Intro Consultation","quantity":1,"price":"0.00","grams":0,"sku":"","variant_title":"Botox + Dermal Fillers","vendor":"BodyFactorySkinCare","fulfillment_service":"manual","product_id":182702931979,"requires_shipping":false,"taxable":true,"gift_card":false,"name":"Intro Consultation - Botox + Dermal Fillers","variant_inventory_management":null,"properties":[{"name":"client_id","value":"100013143"}],"product_exists":true,"fulfillable_quantity":0,"total_discount":"0.00","fulfillment_status":"fulfilled","tax_lines":[],"origin_location":{"id":3730921227,"country_code":"US","province_code":"NY","name":"BodyFactorySkinCare","address1":"726 9th Ave","address2":"","city":"New York","zip":"10019"}},{"id":92112486411,"variant_id":1582638039051,"title":"Intro Consultation","quantity":1,"price":"0.00","grams":0,"sku":"","variant_title":"Laser Treatments","vendor":"BodyFactorySkinCare","fulfillment_service":"manual","product_id":182702931979,"requires_shipping":false,"taxable":true,"gift_card":false,"name":"Intro Consultation - Laser Treatments","variant_inventory_management":null,"properties":[{"name":"client_id","value":"100013143"}],"product_exists":true,"fulfillable_quantity":0,"total_discount":"0.00","fulfillment_status":"fulfilled","tax_lines":[],"origin_location":{"id":3730921227,"country_code":"US","province_code":"NY","name":"BodyFactorySkinCare","address1":"726 9th Ave","address2":"","city":"New York","zip":"10019"}}]}],"client_details":{"browser_ip":"45.56.159.152","accept_language":"en-US,en;q=0.9","user_agent":"Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/62.0.3202.75 Safari\/537.36","session_hash":"1b122a5a7aff0f4df215762d1e41ec99","browser_width":1663,"browser_height":619},"refunds":[],"customer":{"id":106051174411,"email":"jan.schmidt@outlook.com","accepts_marketing":false,"created_at":"2017-11-22T22:27:37-05:00","updated_at":"2017-11-22T22:27:45-05:00","first_name":"schmidt","last_name":"jan","orders_count":1,"state":"disabled","total_spent":"0.00","last_order_id":50239340555,"note":null,"verified_email":true,"multipass_identifier":null,"tax_exempt":false,"phone":null,"tags":"","last_order_name":"#1001","default_address":{"id":117929508875,"customer_id":106051174411,"first_name":"schmidt","last_name":"jan","company":null,"address1":"Werneuchener str","address2":"","city":"Berlin","province":null,"country":"Germany","zip":"13055","phone":null,"name":"schmidt jan","province_code":null,"country_code":"DE","country_name":"Germany","default":true}}}';
//      $strTemp = '{"rate":{"origin":{"country":"AU","postal_code":"4213","province":"QLD","city":"Mudgeeraba","name":null,"address1":"P.O. Box 278","address2":"","address3":null,"phone":"61755227340","fax":null,"address_type":null,"company_name":"KevShop"},"destination":{"country":"US","postal_code":"7000","province":"TAS","city":"Hobart","name":"Maree Anne  Davis","address1":"98 Argyle Street","address2":"","address3":null,"phone":"","fax":null,"address_type":null,"company_name":""},"items":[{"name":"Default","sku":"YO0625501","quantity":2,"grams":35,"price":1000,"vendor":"KevShop","requires_shipping":true,"taxable":true,"fulfillment_service":"manual","properties":null,"product_id":5309944903,"variant_id":16435836679},{"name":"Large","sku":"YO0625502","quantity":1,"grams":50,"price":2000,"vendor":"KevShop","requires_shipping":true,"taxable":true,"fulfillment_service":"manual","properties":null,"product_id":5309940615,"variant_id":16435812679}],"currency":"AUD"}}';
      
      $this->_inputInfo = json_decode( $strTemp );
    }

    // Log the request 
    if( $this->_log_file )   
    {
      $this->Log_model->add( 'Webhook', $this->_shop, $_SERVER['REQUEST_URI'] . json_encode( $this->_inputInfo ), '' );
    }
    
  }
  
  public function __destruct()
  {
  }
  
  // Load shopify model  
  private function _loadShopify()
  {
    // Define the model
    $this->load->model( 'Shopify_model' );
    $this->Shopify_model->setStore( $this->_shop, $this->_arrStoreList[$this->_shop]->app_id, $this->_arrStoreList[$this->_shop]->app_secret );
  }      
  
  // Get the Shop information
  private function _getShopInfo()
  {
    // Load the shopify model
    $this->_loadShopify();

    return $this->Shopify_model->accessAPI( 'shop.json' );
  }

  
  public function index(){
  }
  
  /** 
  * Checkout popup
  * 
  */
  public function order_create( $method = 'Order Created' )
  {
    // Load Model
    $this->load->model( 'Process_model' );
    
    // Log the system
//    $this->Log_model->add( 'Webhook', $method, trim( $this->_inputInfo->name, '#'), $this->_shop );        

    // Access the Process
    $this->Process_model->order_create( $this->_inputInfo, $this->_arrStoreList[ $this->_shop ], $method );    
  }
  
  public function order_paid()
  {
    $this->order_create( 'Order Paid' );
  }
  
  public function order_update()
  {
    usleep( 10000000 );
    
    // Skip blank update within 10 seconds
    if( $this->_inputInfo->created_at == $this->_inputInfo->updated_at)  return;
    
    $created_at = strtotime( $this->_inputInfo->created_at ) + 0;
    $updated_at = strtotime( $this->_inputInfo->updated_at ) + 0;
    if( $updated_at - $created_at < 10 )  return;
    
    $this->order_create( 'Order Updated' );
  }
    
  public function order_cancel()
  {
    $this->Log_model->add( 'Webhook', 'Order Cancelled', $this->_inputInfo->name, '' );
    
    // Update the order status
    $this->load->model( 'Order_model' );
    $this->Order_model->rewriteParam( $this->_shop );
    $this->Order_model->updateStatus( $this->_inputInfo->id, array( 'status' => 'cancelled' ) );
  }

  public function product_create()
  {
    // Log
//    $this->Log_model->add( 'Webhook', 'Product Create', $this->_inputInfo->id, '' );
        
    $this->load->model( 'Process_model' );
    $this->Process_model->product_create( $this->_inputInfo, $this->_arrStoreList[ $this->_shop ], 'Product Create' );        

  }
  
  public function product_update()
  {
    $this->load->model( 'Process_model' );
    $this->Process_model->product_create( $this->_inputInfo, $this->_arrStoreList[ $this->_shop ], 'Product Update' );        
  }

  public function product_delete()
  {
    // Log
    $this->Log_model->add( 'Webhook', 'Product Delete', $this->_inputInfo, '' );
    
    // Define the product model
    $this->load->model( 'Product_model' );
    $this->Product_model->rewriteParam( $this->_shop );
    
    // Delete Product
    $this->Product_model->deleteProduct( $this->_inputInfo );
  }
 
  public function getLocations() {
    header("Content-type: application/json");
    
    $shop = $this->input->post('shop');
    if ($shop == '') $shop = $this->_shop;

    $this->load->model('Location_model');
    $this->Location_model->rewriteParam($shop);

    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    $query = $this->Location_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) {
      if (strtolower($row->name) == 'online store') continue;
      $obj = json_decode(base64_decode($row->data));
      
      $arrReturn['data'][] = array(
        'id' => $row->location_id,
        'title' => $row->name,
        'address1' => $obj->Address,
        'address2' => $obj->Address2,
        'image' => $row->image_url
      );
    }
    
    echo json_encode($arrReturn);
  }
  
  public function getCategories() {
    header("Content-type: application/json");
    $shop = $this->input->post('shop');
    if ($shop == '') $shop = $this->_shop;

    $this->load->model('Collection_model');
    $this->Collection_model->rewriteParam($shop);

    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    $query = $this->Collection_model->getList(array('is_available' => '1', 'sort' => 'order ASC'));
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) {
      $arrReturn['data'][] = array(
        'id' => $row->id,
        'title' => $row->title,
        'image' => $row->image_url
      );
    }
    
    echo json_encode($arrReturn);
  }
  
  public function getProducts() {
    header("Content-type: application/json");
    
    $shop = $this->input->post('shop');
    if ($shop == '') $shop = $this->_shop;
    $collectionId = $this->input->post('collection_id');
    
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );
    
    // Get the collection info
    $this->load->model('Collection_model');
    $this->Collection_model->rewriteParam($shop);
    $collectionInfo = $this->Collection_model->getInfo($collectionId);
    $arrProductId = json_decode($collectionInfo->product_ids);
    
    if (count($arrProductId) > 0) {
      // Get the product info
      $this->load->model('Product_model');
      $this->Product_model->rewriteParam($shop);
      $query = $this->Product_model->getList(array('product_id+' => implode(',', $arrProductId)));
      
      $prevProductId = '';
      if ($query->num_rows() > 0)
      foreach ($query->result() as $row) {
        if ($prevProductId == $row->product_id) continue;
        $prevProductId = $row->product_id;
        
        $arrReturn['data'][] = array(
          'id' => $row->product_id,
          'title' => $row->title,
          'image' => $row->image_url
        );
      }
    }
    
    echo json_encode($arrReturn);
  }
  
  public function getVariants() {
    header("Content-type: application/json");
    
    $shop = $this->input->post('shop');
    if ($shop == '') $shop = $this->_shop;
    $product_id = $this->input->post('product_id');
    
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );
    
    // Get the product info
    $this->load->model('Product_model');
    $this->Product_model->rewriteParam($shop);
    $query = $this->Product_model->getList(array('product_id' => $product_id));

    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) {
      if ($row->sessiontype_id == 0) continue;
      
      $arrReturn['data'][] = array(
        'variant_id' => $row->variant_id,
        'title' => $row->variant_title,
        'image' => $row->image_url,
        'sessiontype_id' => $row->sessiontype_id
      );
    }
    
    echo json_encode($arrReturn);    
  }
  
  public function getPrograms() {
    header("Content-type: application/json");

    // Intialize Mindbody Credential
    $this->load->library('MBSiteService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbsiteservice->SetDefaultCredentials($creds);
    
    $result = $this->mbsiteservice->getPrograms('Appointment');
    
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );
    if ($result['error'] == '') {
      foreach ($result['data'] as $program) {
        $arrReturn['data'][] = array(
          'id' => $program->ID,
          'name' => $program->Name
        );
      }
    } else {
      $arrReturn['error'] = 'Error on get Programs';
      $arrReturn['message'] = json_encode($result);
    }
    
    echo json_encode($arrReturn);
  }
  
  public function getSessionTypes() {
    header("Content-type: application/json");

    $shop = $this->input->post('shop');
    $programId = $this->input->post('program_id');
    
    // Get the available session type ids
    $this->load->model('Product_model');
    $this->Product_model->rewriteParam($shop);
    $arrSessiontypeId = $this->Product_model->getSessionTypeIds();
    
    // Intialize Mindbody Credential
    $this->load->library('MBSiteService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbsiteservice->SetDefaultCredentials($creds);
    
    $result = $this->mbsiteservice->GetSessionTypes(array($programId));
    
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    if ($result['error'] == '') {
      foreach ($result['data'] as $sessionType) {
        
        // Check it's available
        if (!in_array($sessionType->ID, $arrSessiontypeId)) continue;
        
        $arrReturn['data'][] = array(
          'id' => $sessionType->ID,
          'name' => $sessionType->Name
        );
      }
    } else {
      $arrReturn['error'] = $result['error'];
      $arrReturn['message'] = json_encode($result);
    }
    
    echo json_encode($arrReturn);
  }
  
  public function getStaff() {
    header("Content-type: application/json");

    $shop = $this->input->post('shop');
    $sessiontypeId = $this->input->post('sessiontype_id');
    $locationId = $this->input->post('location_id');
    
//    $sessiontypeId = '160';
//    $locationId = '2';
    
    // Intialize Mindbody Credential
    $this->load->library('MBStaffService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbstaffservice->SetDefaultCredentials($creds);
    
    $result = $this->mbstaffservice->GetStaff(array(), $sessiontypeId, $locationId);
    
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    if ($result['error'] == '') {
      foreach ($result['data'] as $staff) {
        
        $arrReturn['data'][] = array(
          'id' => $staff->ID,
          'name' => $staff->Name,
          'image' => isset($staff->ImageURL) ? $staff->ImageURL : ''
        );
      }
      
      function cmp($a, $b) {
        if ($a['name'] == $b['name']) {
            return 0;
        }
        return ($a['name'] < $b['name']) ? -1 : 1;
      }
      
      usort($arrReturn['data'], 'cmp');
    } else {
      $arrReturn['error'] = $result['error'];
      $arrReturn['message'] = json_encode($result);
    }
    
    echo json_encode($arrReturn);
  }  
  
  public function getBookableItems() {
    header("Content-type: application/json");

    // Intialize Mindbody Credential
    $this->load->library('MBAppointmentService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbappointmentservice->SetDefaultCredentials($creds);
    
    $dt = explode('/', $this->input->post('date_from')); $dateFrom = $dt[2] . '-' . $dt[0] . '-' . $dt[1];
    $dt = explode('/', $this->input->post('date_to')); $dateTo = $dt[2] . '-' . $dt[0] . '-' . $dt[1];
    $staffIDs = array();
    if ($this->input->post('staff_id') != '') $staffIDs[] = $this->input->post('staff_id');
    $result = $this->mbappointmentservice->GetBookableItems(array($this->input->post('sessiontype_id')), array($this->input->post('location_id')), $staffIDs, $dateFrom, $dateTo);

    $error = '';
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    $arrTemp = array();
    $arrTemp1 = array();
    if (isset($result->GetBookableItemsResult->ScheduleItems) && $result->GetBookableItemsResult->ErrorCode == 200) {
      if ($result->GetBookableItemsResult->ResultCount == 1) {
        $arrTemp[] = $result->GetBookableItemsResult->ScheduleItems->ScheduleItem;
      } elseif ($result->GetBookableItemsResult->ResultCount > 1) {
        $arrTemp = $result->GetBookableItemsResult->ScheduleItems->ScheduleItem;
      }

      foreach ($arrTemp as $scheduleItem) {
        if (isset($arrTemp1[substr($scheduleItem->StartDateTime, 0, 10)]['_' . $scheduleItem->Staff->ID])) {
          $arrTemp1[substr($scheduleItem->StartDateTime, 0, 10)]['_' . $scheduleItem->Staff->ID]['Times'][] = array (
            'StartDateTime' => $scheduleItem->StartDateTime,
            'StartDateTimeInt' => strtotime($scheduleItem->StartDateTime),
            'Mark' => date('g:i A', strtotime($scheduleItem->StartDateTime)),
            'EndDateTime' => $scheduleItem->EndDateTime,
            'BookableEndDateTime' => $scheduleItem->BookableEndDateTime
          );
        } else {
          $arrTemp1[substr($scheduleItem->StartDateTime, 0, 10)]['_' . $scheduleItem->Staff->ID] = array(
            'Date' => strtoupper(date('l F j, Y', strtotime($scheduleItem->StartDateTime))),
            'DateStr' => strtoupper(date('m-d-Y', strtotime($scheduleItem->StartDateTime))),
            'staff' => array(
              'ID' => $scheduleItem->Staff->ID,
              'Name' => $scheduleItem->Staff->Name,
              'Email' => $scheduleItem->Staff->Email,
              'City' => isset($scheduleItem->Staff->City) ? $scheduleItem->Staff->City : '',
              'State' => $scheduleItem->Staff->State,
              'Country' => $scheduleItem->Staff->Country,
              'Phone' => $scheduleItem->Staff->MobilePhone,
              'isMale' => $scheduleItem->Staff->isMale ? 1 : 0,
            ),
            'Times' => array(
              array(
                'StartDateTime' => $scheduleItem->StartDateTime,
                'StartDateTimeInt' => strtotime($scheduleItem->StartDateTime),
                'Mark' => date('g:i A', strtotime($scheduleItem->StartDateTime)),
                'EndDateTime' => $scheduleItem->EndDateTime,
                'BookableEndDateTime' => $scheduleItem->BookableEndDateTime
              )
            ),
          );
        }
      }
      
      // Key sort
      ksort($arrTemp1);
      
      foreach ($arrTemp1 as $item1) {
        $temp = array();
        foreach ($item1 as $item2) $temp[] = $item2;
        $arrReturn['data'][] = $temp;
      } 
    } else {
      $arrReturn['error'] = 'Error on get Programs';
      $arrReturn['message'] = json_encode($result);
    }
    
    echo json_encode($arrReturn);
  }  
  
  public function addAppointment() {
    header("Content-type: application/json");
    
    // Get parameters
    $shop = $this->input->post('shop');
    $locationId = $this->input->post('location_id');
    $sessiontypeId = $this->input->post('sessiontype_id');
    $staffId = $this->input->post('staff_id');
    $staffName = $this->input->post('staff_name');
    $staffGender = $this->input->post('staff_gender');
    $clientId = $this->input->post('client_id');
    $startDateTime = $this->input->post('start_at');
    $notes = $this->input->post('notes');
    $appointmentId = $this->input->post('appointment_id');
    
    // Cancel Appointment
    $this->load->model('Process_model');
    $arrReturn = $this->Process_model->cancelAppointment($shop, $appointmentId);

    // Add Appointment
    if ($arrReturn['error'] == '') $arrReturn = $this->Process_model->addAppointment($shop, $locationId, $sessiontypeId, $staffId, $staffName, $staffGender, $clientId, $startDateTime, $notes);

    // If success, fill the success message
    if ($arrReturn['error'] == '') $arrReturn['error'] = 'Your appointment schedule is changed successfully.<br>Please check your appointments <a href = "/pages/my-appointment">here</a>.';
    
    echo json_encode($arrReturn);
  }
  
  public function cancelAppointment() {
    header("Content-type: application/json");
    
    // Get parameters
    $shop = $this->input->post('shop');
    $appointmentId = $this->input->post('appointment_id');
    $clientId = $this->input->post('client_id');
    
    // Cancel Appointment
    $this->load->model('Process_model');
    $arrReturn = $this->Process_model->cancelAppointment($shop, $appointmentId);

    // If success, fill the success message
    if ($arrReturn['error'] == '') $arrReturn['error'] = 'Your appointment has been canceled successfully. <br>You have not been charged for this cancelation.';
    
    echo json_encode($arrReturn);
  }
  
  public function addClient() {
    header("Content-type: application/json");

    // Intialize Mindbody Credential
    $this->load->library('MBClientService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbclientservice->SetDefaultCredentials($creds);

    $client = array(
      'FirstName' => $this->input->post('first_name'),
      'LastName' => $this->input->post('last_name'),
      'Email' => $this->input->post('email'),
      'Username' => $this->input->post('user_name'),
      'Password' => $this->input->post('password'),
      'BirthDate' => $this->input->post('birthday'),
      'AddressLine1' => $this->input->post('address1'),
      'AddressLine2' => $this->input->post('address2'),
      'City' => $this->input->post('city'),
      'State' => $this->input->post('state'),
      'PostalCode' => $this->input->post('postalcode'),
      'Country' => $this->input->post('country'),
      'MobilePhone' => $this->input->post('phone'),
      'Gender' => $this->input->post('gender'),
      'EmailOptIn' => true,
      'PromotionalEmailOptIn' => true,
      'SendEmail' => true
    );

    $result = $this->mbclientservice->AddOrUpdateClients(array($client));

    $error = '';
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    if (isset($result->AddOrUpdateClientsResult->Clients->Client)) {
      $client = $result->AddOrUpdateClientsResult->Clients->Client;
      if ($result->AddOrUpdateClientsResult->ErrorCode != '200') {
        $arrReturn['error'] = $client->Messages->string;
      } else {
        $arrReturn['data'] = array(
          'id' => $client->ID
        );
        
        // Add Client to database
        $this->load->model('Customer_model');
        $this->Customer_model->rewriteParam($this->input->post('shop'));
        
        $this->Customer_model->add($client);
      }
    } else {
      $arrReturn['error'] = 'Error on get Add Appointment';
      $arrReturn['message'] = json_encode($result);
    }
    
    echo json_encode($arrReturn);
  }
  
  public function loginClient() {
    header("Content-type: application/json");

    // Intialize Mindbody Credential
    $this->load->library('MBClientService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbclientservice->SetDefaultCredentials($creds);

    $result = $this->mbclientservice->ValidateLogin($this->input->post('user_name'), $this->input->post('password'));

    $error = '';
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    if (isset($result->ValidateLoginResult)) {
      if ($result->ValidateLoginResult->ErrorCode != '200') {
        $arrReturn['error'] = $result->ValidateLoginResult->Message;
      } else {
        $arrReturn['data'] = array(
          'id' => $result->ValidateLoginResult->Client->ID
        );
      }
    } else {
      $arrReturn['error'] = 'Error on Login';
      $arrReturn['message'] = json_encode($result);
    }
    
    echo json_encode($arrReturn);
  }
  
  public function changePassword() {
    header("Content-type: application/json");

    // Intialize Mindbody Credential
    $this->load->library('MBClientService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbclientservice->SetDefaultCredentials($creds);

    $result = $this->mbclientservice->SendUserNewPassword($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'));

    $error = '';
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    if (isset($result->SendUserNewPasswordResult)) {
      if ($result->SendUserNewPasswordResult->ErrorCode != '200') {
        $arrReturn['error'] = $result->SendUserNewPasswordResult->Message;
      } else {
        $arrReturn['data'] = array(
          'Message' => 'The email is sent successfully'
        );
      }
    } else {
      $arrReturn['error'] = 'Error on Change Password';
      $arrReturn['message'] = json_encode($result);
    }
    
    echo json_encode($arrReturn);
  }
  
  public function checkAppointmentPossibility() {
    
    $shop = $this->input->post('shop');
    $clientId = $this->input->post('client_id');
    $sessiontypeId = $this->input->post('sessiontype_id');
    
    // Get the Total Purchase
    $this->load->model('Purchase_model');
    $this->Purchase_model->rewriteParam($shop);
    $totalPurchase = $this->Purchase_model->getPurchaseTotal($clientId, $sessiontypeId);
    
    // Get the Total Appointment 
    $this->load->model('Appointment_model');
    $this->Appointment_model->rewriteParam($shop);
    $totalAppointment = $this->Appointment_model->getAppointmentTotal($clientId, $sessiontypeId);
    
    $arrReturn = array(
      'possible' => '1',
      'variant_id' => ''
    );

    if ($totalPurchase <= $totalAppointment) { // If its possible, get the variant id
      $this->load->model('Product_model');
      $this->Product_model->rewriteParam($shop);
      
      $query = $this->Product_model->getList(array('sessiontype_id' => $sessiontypeId));
      if ($query->num_rows() > 0) {
        $res = $query->result();
        $arrReturn['possible'] = '0';
        $arrReturn['variant_id'] = $res[0]->variant_id;
      }
    }
    
    echo json_encode($arrReturn);
  }
  
  public function getAppointments() {
    $shop = $this->input->post('shop');
    $clientId = $this->input->post('client_id');
    
    // Get the Location List
    $arrLocation = array();
    $this->load->model('Location_model');
    $this->Location_model->rewriteParam($shop);
    $query = $this->Location_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) $arrLocation[$row->location_id] = $row->name; 
    
    // Get the Sessiontype List
    $arrSessiontype = array();
    $this->load->model('Sessiontype_model');
    $this->Sessiontype_model->rewriteParam($shop);
    $query = $this->Sessiontype_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) $arrSessiontype[$row->sessiontype_id] = $row->name; 

    $arrProduct = array();
    $this->load->model('Product_model');
    $this->Product_model->rewriteParam($shop);
    $query = $this->Product_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) $arrProduct[$row->variant_id] = $row->title . ' - ' . $row->variant_title; 
    
    // Make the returns
    $arrReturn = array(
      'purchase' => array(),
      'appointment' => array(),
      'availability' => array()
    );
    
    // Get Appointments
    $this->load->model('Appointment_model');
    $this->Appointment_model->rewriteParam($shop);
    $query = $this->Appointment_model->getList(array('client_id' => $clientId, 'sort' => 'start_at ASC'));
    
    $arrTemp1 = array();
    
    if ($query->num_rows() > 0)    
    foreach ($query->result() as $row) {
      $arrReturn['appointment'][] = array(
        'appointment_id' => $row->appointment_id,
        'session_type' => $arrSessiontype[$row->sessiontype_id],
        'sessiontype_id' => $row->sessiontype_id,
        'location' => $arrLocation[$row->location_id],
        'location_id' => $row->location_id,
        'staff' => $row->staff_name,
        'staff_id' => $row->staff_id,
        'start_at' => (date('D. M j, Y g:i A', strtotime($row->start_at))),
        'created_at' => (date('D. M j, Y g:i A', strtotime($row->created_at))),
        'active' => strtotime($row->start_at) > time() ? 1 : 0,
        'status' => $row->status,
        'cancel_at' => (date('D. M j, Y g:i A', strtotime($row->cancel_at))),
        'appointment_date' => strtoupper(date('m-d-Y', strtotime($row->start_at))),
        'appointment_time' => date('g:i A', strtotime($row->start_at))
      );
      
      // Sum the appointments (not canceled)
      if ($row->status != '2') {
        if (isset($arrTemp1[$row->sessiontype_id]))
          $arrTemp1[$row->sessiontype_id] ++;
        else
          $arrTemp1[$row->sessiontype_id] = 1;
      }
    }
    
    // Get Purchase
    $this->load->model('Purchase_model');
    $this->Purchase_model->rewriteParam($shop);
    $query = $this->Purchase_model->getList(array('client_id' => $clientId, 'sort' => 'created_at DESC'));
    
    $arrTemp2 = array();
    
    if ($query->num_rows() > 0)    
    foreach ($query->result() as $row) {
      $arrReturn['purchase'][] = array(
        'session_type' => $arrSessiontype[$row->sessiontype_id],
        'product' => $arrProduct[$row->variant_id],
        'qty' => $row->qty,
        'order' => $row->order_name,
        'created_at' => (date('D. M j, Y g:i A', strtotime($row->created_at))),
      );
      
      if (isset($arrTemp2[$row->sessiontype_id]))
        $arrTemp2[$row->sessiontype_id] += $row->qty;
      else
        $arrTemp2[$row->sessiontype_id] = $row->qty;
    }
    
    foreach ($arrTemp2 as $sessiontypeId => $qty) {
      // If the available quantity = 0, skip it
      $qtyReal = $qty - (!isset($arrTemp1[$sessiontypeId]) ? 0 : $arrTemp1[$sessiontypeId]);
      if ($qtyReal == 0) continue;
      
      $arrReturn['availability'][] = array(
        'session_type' => $arrSessiontype[$sessiontypeId],
        'qty' => $qtyReal
      );
    }

    echo json_encode($arrReturn);
  }
}
    
