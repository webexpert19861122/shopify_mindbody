<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends MY_Controller {
    
  public function __construct() {
    parent::__construct();
    $this->load->model( 'Customer_model' );
    
    // Define the search values
    $this->_searchConf  = array(
      'shop' => $this->_default_store,
      'first_name' => '',
      'last_name' => '',
      'client_id' => '',
      'email' => '',
      'page_size' => '80',
      'sort_field' => 'created_at',
      'sort_direction' => 'DESC',
    );
    $this->_searchSession = 'customer_sel';
  }
  
  public function index(){
    $this->is_logged_in();
    
    $this->manage();
  }
  
  public function manage( $page =  0 ){
    // Check the login
    $this->is_logged_in();

    // Init the search value
    $this->initSearchValue();

    // Get data
    $arrCondition =  array(
       'first_name' => $this->_searchVal['first_name'],
       'last_name' => $this->_searchVal['last_name'],
       'client_id' => $this->_searchVal['client_id'],
       'email' => $this->_searchVal['email'],
       'page_size' => $this->_searchVal['page_size'],              
       'page_number' => $page,              
       'sort' => $this->_searchVal['sort_field'] . ' ' . $this->_searchVal['sort_direction'],
    );
    $this->Customer_model->rewriteParam($this->_searchVal['shop']);
    $data['query'] =  $this->Customer_model->getList( $arrCondition );
    $data['total_count'] = $this->Customer_model->getTotalCount();
    $data['page'] = $page;
    
    // Define the rendering data
    $data = $data + $this->setRenderData();

    // Store List    
    $arr = array();
    foreach( $this->_arrStoreList as $shop => $row ) $arr[ $shop ] = $shop;
    $data['arrStoreList'] = $arr;
    
    // Load Pagenation
    $this->load->library('pagination');

    $this->load->view('view_header');
    $this->load->view('view_customer', $data );
    $this->load->view('view_footer');
  }
  
  public function sync($shop) {
    $this->Customer_model->rewriteParam($shop);

    // Intialize Mindbody Credential
    $this->load->library('MBClientService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $usercreds = new UserCredentials($this->config->item('MINDBODY_USERNAME'), $this->config->item('MINDBODY_USERPASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    
    $this->mbclientservice->SetDefaultCredentials($creds);
    $this->mbclientservice->SetDefaultUserCredentials($usercreds);

    $pageNum = 0;
    $pageSize = 1000;
    $error = '';
    
    do {
      $result = $this->mbclientservice->GetClients(array(), '', $pageSize, $pageNum);
      
      if ($result['error'] == '') {
        foreach ($result['data'] as $client) {
          $this->Customer_model->add($client);
        }
      } else {
        $error = $result['error'];
      }
      
      $pageNum ++;
    } while ( $error == '' && count($result['data']) > 0);
    
    if ($error == '')
      echo 'success';
    else
      echo $error;
  }
  
  public function clear($shop) {
    $this->Customer_model->clear();
    
    echo 'success';
  }  
}            

