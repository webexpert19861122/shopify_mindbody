<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Program extends MY_Controller {
  
  public function __construct() {
    parent::__construct();
    $this->load->model( 'Program_model' );
    
    // Define the search values
    $this->_searchConf  = array(
      'shop' => $this->_default_store,
      'page_size' => $this->config->item('PAGE_SIZE'),
      'sort_field' => 'program_id',
      'sort_direction' => 'ASC',
    );
    $this->_searchSession = 'program';
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
    $this->Program_model->rewriteParam($this->_searchVal['shop']);
    $data['query'] =  $this->Program_model->getList( $arrCondition );
    $data['total_count'] = $this->Program_model->getTotalCount();
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
    $this->load->view('view_program', $data );
    $this->load->view('view_footer');
  }
  
  public function sync($shop) {
    
    // Intialize Mindbody Credential
    $this->load->library('MBSiteService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbsiteservice->SetDefaultCredentials($creds);
    
    $result = $this->mbsiteservice->getPrograms('Appointment');

    if ($result['error'] == '') {
      foreach ($result['data'] as $program) {
        $this->Program_model->add($program);
      }
      echo 'success';
    } else {
      echo 'error';
    }
  }
}                                                                