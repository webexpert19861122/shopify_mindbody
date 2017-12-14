<?php
$config['base_url'] = base_url( 'location/manage/' );
$config['total_rows'] = $total_count;
$config['per_page'] = $sel_page_size; 
$config['num_links'] = 4;

$config['first_link'] = 'First';
$config['first_tag_open'] = '<li class="paginate_button previous" id="example1_previous">';
$config['first_tag_close'] = '</li>';

$config['last_link'] = 'Last';
$config['last_tag_open'] = '<li class="paginate_button next" id="example1_previous">';
$config['last_tag_close'] = '</li>';

$config['prev_link'] = '&lt;';
$config['prev_tag_open'] = '<li class="paginate_button ">';
$config['prev_tag_close'] = '</li>';

$config['next_link'] = '&gt;';
$config['next_tag_open'] = '<li class="paginate_button ">';
$config['next_tag_close'] = '</li>';

$config['num_tag_open'] = '<li class="paginate_button ">';
$config['num_tag_close'] = '</li>';

$config['cur_tag_open'] = '<li class="paginate_button active " disabled><a href = "#" disabled>';
$config['cur_tag_close'] = '</a></li>';

$this->pagination->initialize($config); 

$summary = 'Showing ' . ( $page + 1 ) . ' to ' . ( $page + $sel_page_size > $total_count ? $total_count : $page + $sel_page_size ) . ' of ' . $total_count . ' orders';

?>
<style>
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Location
    <small>List</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Order</li>
  </ol>
</section>

<!-- Main content -->

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <div class="col-md-12 column"  style = "border-bottom:solid 1px #ddd; margin-bottom:4px; padding-bottom: 5px;" >
          <form style="display: inline" class = 'form-inline' id = 'frmSearch' action="<?php echo base_url('location') ?>" method = "post" >
            <label>Store</label>&nbsp;:&nbsp;
            <?PHP echo form_dropdown('sel_shop', $arrStoreList, $sel_shop, 'id="sel_shop" class="form-control input-group-sm"' ); ?>
            <button type = "submit" class = "btn btn-info" ><i class="glyphicon glyphicon-search" ></i></button>
            
            <input type = hidden id = 'sel_sort_field' name = 'sel_sort_field' value = '<?PHP echo $sel_sort_field;?>' >
            <input type = hidden id = 'sel_sort_direction' name = 'sel_sort_direction' value = '<?PHP echo $sel_sort_direction;?>' >
          </form>
          &nbsp;&nbsp;|&nbsp;&nbsp;
          <button type = "button" class = "btn btn-info btn_sync" >Sync Locations</button>
          &nbsp;&nbsp;
          <button type = "button" class = "btn btn-success btn_clear" >Clear Mindbody Resource</button>
          </div>
          <div id = 'ret' class="col-md-12 column" ></div>
        </div><!-- /.box-header -->
        
        <!-- Pagenation -->
        <div class = 'box-body' style = "padding:0px 10px;">
            <div class="col-sm-5">
                <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">
                    <?php echo $summary ; ?>    
                </div>
            </div>
            <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                    <ul class="pagination">
                        <?php echo $this->pagination->create_links(); ?>
                    </ul>
              </div>
            </div>
        </div>
        <div class="box-body">
          <table id="example2" class="table table-bordered table-hover">
            <thead>
              <tr class = "text-center">
                <th class = "text-center" >No.</th>
                <th class = "text-center" ><a href = "javascript:sort('location_id');" >Location ID</a></th>
                <th class = "text-center" ><a href = "javascript:sort('name');" >Name</a></th>
                <th class = "text-center" >Description</th>
                <th class = "text-center" >Address</th>
                <th class = "text-center" ><a href = "javascript:sort('state');" >State</a></th>
                <th class = "text-center" ><a href = "javascript:sort('city');" >City</a></th>
                <th class = "text-center" >Postal Code</th>
                <th class = "text-center" >Phone</th>
                <th class = "text-center" >Latitude</th>
                <th class = "text-center" >Longitude</th>
                <th class = "text-center" >Image Url</th>
              </tr>
            </thead>
            <tbody>
            <?php $sno = $page;
            foreach ($query->result() as $row):
              $obj = json_decode(base64_decode($row->data));
              $sno ++;
              ?>
              <tr class="tbl_view text-center" >
                <td><?php echo $sno; ?></td>
                <td><?=$row->location_id ?></td>
                <td class = 'text-left' ><?=$row->name ?></td>
                <td><?=isset($obj->BusinessDescription) ? $obj->BusinessDescription : '' ?></td>
                <td><?=$obj->Address . ' ' . $obj->Address2 ?></td>
                <td><?=isset($obj->StateProvCode) ? $obj->StateProvCode : '' ?></td>
                <td><?=isset($obj->City) ? $obj->City : '' ?></td>
                <td><?=isset($obj->PostalCode) ? $obj->PostalCode : '' ?></td>
                <td><?=$obj->Phone ?></td>
                <td><?=$obj->Latitude ?></td>
                <td><?=$obj->Longitude ?></td>
                <td class = '' >
                  <a href="#" class="text" data-type="text" data-pk="<?= $row->id?>" data-url="<?php echo base_url( $this->config->item('index_page') . '/location/update/image_url/' . $row->id ) ?>" data-title="Enter Url">
                    <?php if (trim($row->image_url) != '') echo '<img src = "' . $row->image_url . '" style = "width: 70px" >'; ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div><!-- /.row -->
  
  <!-- Pagenation -->
  <div class="row">
    <div class="col-sm-5">
        <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">
            <?php echo $summary ; ?>    
        </div>
    </div>
    <div class="col-sm-7">
        <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
            <ul class="pagination">
                <?php echo $this->pagination->create_links(); ?>
            </ul>
      </div>
    </div>
  </div><!-- /.row -->  

<script>
var sel_product;

// Collect selected ids
function collect_sels()
{
  // Clear
  $('#sel_ids').val('');
  
  // Collect vals for variants
  $('.chk_order').each(function(){
     if( $(this).is(':checked') )
     {
         $('#sel_ids').val( $('#sel_ids').val() + '_' + $(this).val() );
     } 
  });
}

$(document).ready(function(){

  // Editable
  $('.text').editable();

  // Checkbox selection
  $('#chk_all').click( function(){
    if( $(this).is(':checked')) 
    {
      $('.chk_order').prop('checked', true );
    }
    else
    {
      $('.chk_order').prop('checked', false );
    }
  });
    
  // ********************************* //

  // Sync Button Config
  $('.btn_sync').btn_init(
    'sync',
    { class : 'btn-warning', caption : 'Sync' },
    { class : 'btn-default fa fa-spinner', caption : '' },
    { class : 'btn-success', caption : 'Done' },
    { class : 'btn-danger', caption : 'Error' }
  );

  // Sync category
  $('.btn_sync').click(function(){
    $(this).btn_action( 'sync', 'pending' );
    $.ajax({
      url: '<?php echo base_url($this->config->item('index_page') . '/location/sync') ?>'  + '/' + $('#sel_shop').val(),
      type: 'GET'
    }).done(function(data) {
      console.log( data );
      if( data == 'success' )
      {
        $('.btn_sync').btn_action( 'sync', 'success' );

        setTimeout( function(){
          window.location.reload();
          }, 1000
        );
      }
      else
      {
        $('.btn_sync').btn_action( 'sync', 'error' );  
      }
    });
    
    event.preventDefault();
  }); 
  
  // Sync Button Config
  $('.btn_clear').btn_init(
    'clear',
    { class : 'btn-success', caption : 'Sync' },
    { class : 'btn-default fa fa-spinner', caption : '' },
    { class : 'btn-success', caption : 'Done' },
    { class : 'btn-danger', caption : 'Error' }
  );

  // Sync category
  $('.btn_clear').click(function(){
    $(this).btn_action( 'clear', 'pending' );
    $.ajax({
      url: '<?php echo base_url($this->config->item('index_page') . '/location/clear') ?>'  + '/' + $('#sel_shop').val(),
      type: 'GET'
    }).done(function(data) {
      console.log( data );
      if( data == 'success' )
      {
        $('.btn_clear').btn_action( 'clear', 'success' );

        setTimeout( function(){
          window.location.reload();
          }, 1000
        );
      }
      else
      {
        $('.btn_clear').btn_action( 'clear', 'error' );  
      }
    });
    
    event.preventDefault();
  }); 
    
  $('#sel_shop').change( function(){
    $('#frmSearch').submit();
  });
});

function sort( field )
{
  $('#sel_sort_field').val( field );
  $('#sel_sort_direction').val( $('#sel_sort_direction').val() == 'ASC' ? 'DESC' : 'ASC' );
  
  $('#frmSearch').submit();
}

</script>