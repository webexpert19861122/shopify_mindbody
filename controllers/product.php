<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends MY_Controller {
    
  public function __construct() {
    parent::__construct();
    $this->load->model( 'Product_model' );
    
    // Define the search values
    $this->_searchConf  = array(
      'name' => '',
      'sku' => '',
      'shop' => $this->_default_store,
      'collection_id' => '',
      'page_size' => $this->config->item('PAGE_SIZE'),
      'sort_field' => 'product_id',
      'sort_direction' => 'DESC',
    );
    $this->_searchSession = 'product_app_page';
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

    // Get the Collections
    $arrCollection = array();
    $collectionIn = array();
    $this->load->model('Collection_model');
    $this->Collection_model->rewriteParam($this->_searchVal['shop']);
    $query = $this->Collection_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) {
      $data['arrCollection'][$row->collection_id] = $row->title;
      if ($row->collection_id == $this->_searchVal['collection_id']) $collectionIn = json_decode($row->product_ids);
    }

    // Get data
    $this->Product_model->rewriteParam($this->_searchVal['shop']);
    $arrCondition =  array(
      'name' => $this->_searchVal['name'],
      'sku' => $this->_searchVal['sku'],
      'sort' => $this->_searchVal['sort_field'] . ' ' . $this->_searchVal['sort_direction'],
      'page_number' => $page,
      'page_size' => $this->_searchVal['page_size'],              
    );
    if (count($collectionIn) > 0) $arrCondition['product_id+'] = implode(',', $collectionIn);
    $data['query'] =  $this->Product_model->getList( $arrCondition );
    $data['total_count'] = $this->Product_model->getTotalCount();
    $data['page'] = $page;
    
    // Store List    
    $arr = array();
    foreach( $this->_arrStoreList as $shop => $row ) $arr[ $shop ] = $shop;
    $data['arrStoreList'] = $arr;
    
    // Get Session Types
    $data['arrSessiontype'] = array(
      0 => ''
    );
    $this->load->model('Program_model');
    $this->load->model('Sessiontype_model');

    $arrProgram = array();
    $query = $this->Program_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) $arrProgram[$row->program_id] = $row->name;
    
    $query = $this->Sessiontype_model->getList(array());
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) $data['arrSessiontype'][$row->sessiontype_id] = $arrProgram[$row->program_id] . ' - ' . $row->name;
    
    // Define the rendering data
    $data = $data + $this->setRenderData();
    
    // Load Pagenation
    $this->load->library('pagination');

    $this->load->view('view_header');
    $this->load->view('view_product', $data );
    $this->load->view('view_footer');
  }
  
  public function update( $type, $pk )
  {
    $data = array(
      $type => $this->input->post('value')
    );
    
    $this->Product_model->update( $pk, $data );
  }
  
  public function sync( $shop, $page = 1 )
  {
    $this->load->model( 'Process_model' );
    
    // Set the store information
    $this->Product_model->rewriteParam( $shop );
    
    $this->load->model( 'Shopify_model' );
    $this->Shopify_model->setStore( $shop, $this->_arrStoreList[$shop]->app_id, $this->_arrStoreList[$shop]->app_secret );
    
    // Get the lastest day
    $last_day = $this->Product_model->getLastUpdateDate();
    $last_date = $this->config->item('CONST_EMPTY_DATE');
    
    // Retrive Data from Shop
    $count = 0;

    // Make the action with update date or page
    $action = 'products.json?';
    if( $last_day != '' && $last_day != $this->config->item('CONST_EMPTY_DATE') && $page == 1 )
    {
      $action .= 'limit=250&updated_at_min=' . urlencode( $last_day );
    }
    else
    {
      $action .= 'limit=20&page=' . $page;
    } 

    // Retrive Data from Shop
    $productInfo = $this->Shopify_model->accessAPI( $action );

    // Store to database
    if( isset($productInfo->products) && is_array($productInfo->products) )
    {
      foreach( $productInfo->products as $product )
      {
        $this->Process_model->product_create( $product, $this->_arrStoreList[$shop] );        
      }
    }
    
    // Get the count of product
    if( $last_day != '' && $last_day != $this->config->item('CONST_EMPTY_DATE') && $page == 1 )
    {
      $count = 0;
    }
    else
    {
      if( isset( $productInfo->products )) $count = count( $productInfo->products );
      $page ++;  
    }

    if( $count == 0 )
      echo 'success';
    else
      echo $page . '_' . $count;
  }
  
  public function clear($shop) {
    $this->load->model('Product_model');
    
    $this->Product_model->clear();
    
    echo 'success';
  }
}            

