<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sessiontype extends MY_Controller {
  
  public function __construct() {
    parent::__construct();
    $this->load->model( 'Sessiontype_model' );
    
    // Define the search values
    $this->_searchConf  = array(
      'shop' => $this->_default_store,
      'page_size' => $this->config->item('PAGE_SIZE'),
      'sort_field' => 'program_id',
      'sort_direction' => 'ASC',
    );
    $this->_searchSession = 'sessiontype';
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
    $this->Sessiontype_model->rewriteParam($this->_searchVal['shop']);
    $data['query'] =  $this->Sessiontype_model->getList( $arrCondition );
    $data['total_count'] = $this->Sessiontype_model->getTotalCount();
    $data['page'] = $page;
    
    // Get programs
    $data['arrProgram'] = array();
    $this->load->model('Program_model');
    $query = $this->Program_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) $data['arrProgram'][$row->program_id] = $row->name;
    
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
    $this->load->view('view_sessiontype', $data );
    $this->load->view('view_footer');
  }
  
  public function sync($shop) {
    
    // Intialize Mindbody Credential
    $this->load->library('MBSiteService');
    $creds = new SourceCredentials($this->config->item('MINDBODY_SOURCENAME'), $this->config->item('MINDBODY_PASSWORD'), array($this->config->item('MINDBODY_SITEID')));
    $this->mbsiteservice->SetDefaultCredentials($creds);
    
    $this->load->model('Program_model');
    $query = $this->Program_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) {
      $result = $this->mbsiteservice->GetSessionTypes(array($row->program_id));
      
      if ($result['error'] == '') {
        foreach ($result['data'] as $sessionType) {
          $this->Sessiontype_model->add($sessionType);
        }
      }      
    }

    echo 'success';
  }
}                                                                