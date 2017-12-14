<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location extends MY_Controller {
  
  public function __construct() {
    parent::__construct();
    $this->load->model( 'Location_model' );
    
    // Define the search values
    $this->_searchConf  = array(
      'shop' => $this->_default_store,
      'page_size' => $this->config->item('PAGE_SIZE'),
      'sort_field' => 'location_id',
      'sort_direction' => 'ASC',
    );
    $this->_searchSession = 'location_sel';
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
     'page_number' => $page,
     'page_size' => $this->_searchVal['page_size'],              
     'sort' => $this->_searchVal['sort_field'] . ' ' . $this->_searchVal['sort_direction'],
    );
    $this->Location_model->rewriteParam($this->_searchVal['shop']);
    $data['query'] =  $this->Location_model->getList( $arrCondition );
    $data['total_count'] = $this->Location_model->getTotalCount();
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
    $this->load->view('view_location', $data );
    $this->load->view('view_footer');
  }
  
  public function sync($shop) {
    // Get the data
    $this->load->model('Process_model');
    $result = $this->Process_model->getLocations();
    
    if ($result['error'] == '') {
      foreach ($result['data'] as $location) {
        $this->Location_model->add($location);
      }
      echo 'success';
    } else {
      echo 'error';
    }
  }
  
  public function clear($shop) {
    $this->load->model('Program_model');
    $this->load->model('Sessiontype_model');
    
    $this->Location_model->clear();
    $this->Program_model->clear();
    $this->Sessiontype_model->clear();
    
    echo 'success';
  }
  public function update( $type, $pk )
  {
    $data = array(
      $type => $this->input->post('value')
    );
    $this->Location_model->update( $pk, $data );
  }
}                                                                