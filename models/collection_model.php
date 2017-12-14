<?php
class Collection_model extends Master_model
{
  protected $_tablename = 'collection';
  protected $_arrCondition = array(
    'collection_id' => '=',
    'is_available' => '='
  );
  
  // Add product to database
  public function add( $collection )
  {
    $existId = '';
    
    // Check Exist
    $isUpdate = false;
    $query = $this->getList( array('collection_id' => $collection->id));
    if( $query->num_rows() > 0 ) {
      $isUpdate = true;
      $result = $query->result();
      $existId = $result[0]->id;
    }
    
    // Build Values
    $info = array(
      'collection_id' => $collection->id,
      'title' => $collection->title,
      'image_url' => isset($collection->image) ? $collection->image->src : '',
      'product_ids' => json_encode($collection->productIds),
      'created_at' => $collection->updated_at,
    );
    
    // Apply to DB
    if(!$isUpdate) {
      parent::add( $info );
    } else {
      parent::update($existId, $info );
    }
  }
  
  // ********************** //
}  
?>
