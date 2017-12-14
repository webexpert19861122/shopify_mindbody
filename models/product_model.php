<?php
class Product_model extends Master_model
{
  protected $_tablename = 'product';
  protected $_arrCondition = array(
    'title' => '%LIKE%',
    'sku' => '%LIKE%',
    'variant_id' => '=',
    'sessiontype_id' => '=',
    'product_type' => '=%',
    'product_id+' => 'IN+',
    'product_id' => '=',
  );
  
  // Get last updated date
  public function getLastUpdateDate()
  {
    $return = '';
    
    $this->db->select( 'updated_at' );
    $this->db->order_by( 'updated_at DESC' );
    $this->db->limit( 1 );
    $this->db->where( 'shop', $this->_shop );
    
    $query = $this->db->get( $this->_tablename );
    
    if( $query->num_rows() > 0 )
    {
      $res = $query->result();
      
      $return = $res[0]->updated_at;
    }
    
    return $return;
  }    
  
  // Add product to database
  public function add( $product )
  {
    // Get the images as array
    $arrImage = array();
    foreach( $product->images as $item ) $arrImage[ $item->id ] = $item->src;
    
    foreach( $product->variants as $variant )
    {
      $existId = '';
      
      // Check Exist
      $isUpdate = false;
      $query = $this->getList( array('variant_id' => $variant->id));
      if( $query->num_rows() > 0 ) {
        $isUpdate = true;
        $result = $query->result();
        $existId = $result[0]->id;
      }
      
      // Get image id
      $image_url = '';
      if( !empty($variant->image_id) ) $image_url = $arrImage[$variant->image_id];
      if( $image_url == '' && isset( $product->image->src ))
      {
        $image_url = $product->image->src;
      } 
      
      // Build Values
      $newProductInfo = array(
        'title' => $product->title,
        'variant_title' => $variant->title,
        'product_id' => $product->id,
        'variant_id' => $variant->id,
        'sku' => $variant->sku,
        'body_html' => base64_encode($product->body_html),
        'categories' => implode( ',', $product->categories ),
        'product_type' => $product->product_type,
        'handle' => $product->handle,
        'price' => $variant->price,
        'position' => $variant->position,
        'updated_at' => date( $this->config->item('CONST_DATE_FORMAT'), strtotime($variant->updated_at)),
        'is_exist' => 1,
        'image_url' => $image_url,
        'data' => base64_encode( json_encode( $variant ) ),
      );
      
      // Apply to DB
      if(!$isUpdate) {
        $newProductInfo['is_exist'] = 1;
        
        parent::add( $newProductInfo );
      } else {
        parent::update($existId, $newProductInfo );
      }
    }      
  }
  
  // Delete the product from product_id
  public function deleteProduct( $product_id )
  {
    $this->db->delete( $this->_tablename, array( 'product_id' => $product_id, 'shop' => $this->_shop ) );
    if( $this->db->affected_rows() > 0 )
        return true;
    else
        return false;
    
  }
  
  // Get the variant object from variant_id
  public function getVariant( $variant_id )
  {
    $returnObj = '';
    
    $query = $this->getList( array( 'variant_id' => $variant_id ) );
    
    if( $query->num_rows() > 0 )
    foreach( $query->result() as $row )
    {
      $row->data = json_decode( base64_decode($row->data));
      $returnObj = $row;
    }
    
    return $returnObj;
  }
  
  // Get variant ID from SKU
  public function getVariantIdFromSku( $sku )
  {
    $return = '';
    
    $query = $this->getList( array( 'sku' => $sku ) );
    
    if( $query->num_rows() > 0 )
    foreach( $query->result() as $row )
    {
      $return = $row->variant_id;
    }
    
    return $return;
  }
  
  public function getSessionTypeIds() {
    $this->db->select('sessiontype_id');
    $this->db->where('shop', $this->_shop);
    $this->db->group_by('sessiontype_id');
    
    $arrReturn = array();
    $query = $this->db->get($this->_tablename);
    
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) {
      if ($row->sessiontype_id == 0) continue;
      
      $arrReturn[] = $row->sessiontype_id;
    }
    
    return $arrReturn;
  }
  
  public function getProductTypes() {
    $this->db->select('product_type');
    $this->db->where('shop', $this->_shop);
    $this->db->group_by('product_type');
    
    $arrReturn = array();
    $query = $this->db->get($this->_tablename);
    
    if ($query->num_rows() > 0)
    foreach ($query->result() as $row) {
      if ($row->product_type == '') continue;
      
      $arrReturn[$row->product_type] = $row->product_type;
    }
    
    return $arrReturn;
  }
  // ********************** //
}  
?>
