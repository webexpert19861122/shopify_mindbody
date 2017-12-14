<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends MY_Controller {
  
  public function __construct() {
    parent::__construct();
    $this->load->model( 'Order_model' );
    
    // Define the search values
    $this->_searchConf  = array(
      'customer_name' => '',
      'order_name' => '',
      'shop' => $this->_default_store,
      'page_size' => $this->config->item('PAGE_SIZE'),
      'is_map' => '0',
      'is_download' => '0',
      'created_at' => '',
      'sort_field' => 'created_at',
      'sort_direction' => 'DESC',
    );
    $this->_searchSession = 'order_sels';
  }
  
  private function _checkDispatchCode( $code1, $code2 )
  {
    // if the first code is empty or both are same, return code2
    if( $code1 == '' || $code1 == $code2 ) return $code2;
    
    // If the second code is empty, return code1
    if( $code2 == '' ) return $code1;
    
    $arrRule = array( 'HH', 'YH', 'GM', 'SU', 'SF', 'FR', 'AP', 'JM', 'AO', 'AJ', 'NO' );
    
    $pos1 = array_search( $code1, $arrRule );
    $pos2 = array_search( $code2, $arrRule );
    
    if( $pos2 !== false && $pos1 < $pos2 ) return $code1;
    
    return $code2;
  }
  
  public function index(){
      $this->is_logged_in();
      
      $this->manage();
  }
  
  public function manage( $page =  0 ){
    
    $this->_searchVal['shop'] = trim( $this->_searchVal['shop'], 'http://' );
    $this->_searchVal['shop'] = trim( $this->_searchVal['shop'], 'https://' );
    
    // Check the login
    $this->is_logged_in();

    // Init the search value
    $this->initSearchValue();

    // Get data
    $arrCondition =  array(
       'customer_name' => $this->_searchVal['customer_name'],
       'order_name' => $this->_searchVal['order_name'],
       'page_number' => $page,
       'page_size' => $this->_searchVal['page_size'],              
       'is_map' => $this->_searchVal['is_map'],         
       'is_download' => $this->_searchVal['is_download'],         
       'created_at' => $this->_searchVal['created_at'],         
       'sort' => $this->_searchVal['sort_field'] . ' ' . $this->_searchVal['sort_direction'],
    );
    $this->Order_model->rewriteParam($this->_searchVal['shop']);
    $data['query'] =  $this->Order_model->getList( $arrCondition );
    $data['total_count'] = $this->Order_model->getTotalCount();
    $data['page'] = $page;
    
    // Define the rendering data
    $data = $data + $this->setRenderData();

    // Store List    
    $arr = array();
    foreach( $this->_arrStoreList as $shop => $row ) $arr[ $shop ] = $shop;
    $data['arrStoreList'] = $arr;
    
    // Load Pagenation
    $this->load->library('pagination');

    // Renter to view
    $this->load->view('view_header');
    $this->load->view('view_order', $data );
    $this->load->view('view_footer');
  }
  
  public function sync( $shop )  {
    $this->load->model( 'Process_model' );
    $this->Process_model->order_sync($shop, $this->_arrStoreList[$shop]);
        
    echo 'success';
  }    
}                                                                