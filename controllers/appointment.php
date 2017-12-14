<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends MY_Controller {
  
  public function __construct() {
    parent::__construct();
    $this->load->model( 'Appointment_model' );
    
    // Define the search values
    $this->_searchConf  = array(
      'shop' => $this->_default_store,
      'page_size' => $this->config->item('PAGE_SIZE'),
      'sort_field' => 'created_at',
      'sort_direction' => 'DESC',
    );
    $this->_searchSession = 'appointment';
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
    $this->Appointment_model->rewriteParam($this->_searchVal['shop']);
    $data['query'] =  $this->Appointment_model->getList( $arrCondition );
    $data['total_count'] = $this->Appointment_model->getTotalCount();
    $data['page'] = $page;
    
    // Get the Location List
    $arrLocation = array();
    $this->load->model('Location_model');
    $this->Location_model->rewriteParam($this->_searchVal['shop']);
    $query = $this->Location_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) $arrLocation[$row->location_id] = $row->name; 
    $data['arrLocation'] = $arrLocation;
    
    // Get the Sessiontype List
    $arrSessiontype = array();
    $this->load->model('Sessiontype_model');
    $this->Sessiontype_model->rewriteParam($this->_searchVal['shop']);
    $query = $this->Sessiontype_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) $arrSessiontype[$row->sessiontype_id] = $row->name; 
    $data['arrSessiontype'] = $arrSessiontype;
    
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
    $this->load->view('view_appointment', $data );
    $this->load->view('view_footer');
  }
}                                                                