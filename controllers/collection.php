<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Collection extends MY_Controller {
    
  public function __construct() {
    parent::__construct();
    $this->load->model( 'Collection_model' );
    
    // Define the search values
    $this->_searchConf  = array(
      'shop' => $this->_default_store,
      'page_size' => $this->config->item('PAGE_SIZE'),
      'sort_field' => 'title',
      'sort_direction' => 'ASC',
    );
    $this->_searchSession = 'collection';
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
    
    $this->Collection_model->rewriteParam($this->_searchVal['shop']);
    $arrCondition =  array(
       'page_number' => $page,
       'page_size' => $this->_searchVal['page_size'],              
       'sort' => $this->_searchVal['sort_field'] . ' ' . $this->_searchVal['sort_direction'],
    );
    $data['query'] =  $this->Collection_model->getList( $arrCondition );
    $data['total_count'] = $this->Collection_model->getTotalCount();
    $data['page'] = $page;
    
    $arr = array();
    foreach( $this->_arrStoreList as $shop => $row ) $arr[ $shop ] = $shop;
    $data['arrStoreList'] = $arr;

    // Define the rendering data
    $data = $data + $this->setRenderData();
    
    // Load Pagenation
    $this->load->library('pagination');

    $this->load->view('view_header');
    $this->load->view('view_collection', $data );
    $this->load->view('view_footer');
  }
  
  public function sync($shop)
  {
    $this->Collection_model->rewriteParam( $shop );

    $this->load->model( 'Shopify_model' );
    $this->Shopify_model->setStore( $shop, $this->_arrStoreList[$shop]->app_id, $this->_arrStoreList[$shop]->app_secret );
    
    // Retrive Data from Shop
    $action = 'custom_collections.json?';
    $collectionInfo = $this->Shopify_model->accessAPI( $action );
    
    $arrProduct = array(); // Array of product->collection
    
    // Store to database
    if( isset($collectionInfo->custom_collections) && is_array($collectionInfo->custom_collections) )
    {
      foreach( $collectionInfo->custom_collections as $collection )
      {
        // Get the list of collects for each collection
        $arrProductId = array();
        $action = 'collects.json?collection_id=' . $collection->id;

        // Retrive Data from Shop
        $connectInfo = $this->Shopify_model->accessAPI( $action );
        if( isset($connectInfo->collects) && is_array($connectInfo->collects) )
        foreach ($connectInfo->collects as $collect){
          $arrProductId[] = $collect->product_id;
          $arrProduct['_' . $collect->product_id][] = $collection->id;
        } 
        
        $collection->productIds = $arrProductId;
        $this->Collection_model->add( $collection );
      }
    }
    
    // Update products
    foreach ($arrProduct as $productId => $arrCollectionId) {
      $this->db->where('product_id', ltrim($productId, '_'));
      $this->db->where('shop', $shop);
      $this->db->update('product', array('categories' => json_encode($arrCollectionId)));
    }
    
    echo 'success';
  }  
  
  public function clear($shop) {
    
    $this->Collection_model->clear();
    
    echo 'success';
  }

  public function update( $type, $pk )
  {
    $data = array(
      $type => $this->input->post('value')
    );
    
    $this->Collection_model->update( $pk, $data );
  }
}            

