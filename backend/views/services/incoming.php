<?php
$this->title = 'Outgoing Profiles';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$saveURL = \Yii::$app->getUrlManager()->createUrl('services/incoming-save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('services/incoming-delete');
$this->registerJs( <<< EOT_JS
       $(document).on('click', '#btnSubmit',
       function(ev) {  
        
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
                location.href='?r=services/incoming';
            }
        )
        ev.preventDefault();
  }); 
  
  $(document).on('click', '#btnNew',
       function(ev) {   
        location.href='?r=services/incoming';
        
        
  }); 
            
  $('.delete_event').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete - ' + event_name)){
                        $.get(  
                            '{$deleteURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
                            
$("#datalisting").DataTable();   
EOT_JS
);  
?>

<style type="text/css" media="screen">
    #yumpee_widget_content { 
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0px;
        min-width:1000px;
        height:500px;
     
    }
</style>

      
<div class="container-fluid">
<div id="addCategories">
     <form action="index.php?r=services/incoming" method="post" name="frm1" id="frm1">
    <table class="table">
        <tr><td>Client Name<td><input name="name" id="name" value="<?=$rs['name']?>" class="form-control" type="text" />
        <tr><td>Client ID<td><input name="client_id" id="client_id" value="<?=$rs['client_id']?>" class="form-control" type="text" />
        <tr><td>Client Key<td><input name="client_key" id="client_key" value="<?=$rs['client_key']?>" class="form-control" type="text" />
        <tr><td>IP Address Authentication<td><input name="ip_address" id="ip_address" value="<?=$rs['ip_address']?>" class="form-control" type="text" />       
        <tr><td>Rate Limits<td><input name="rate_limit" id="rate_limit" value="<?=$rs['rate_limit']?>" class="form-control" type="text" />
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$rs["id"]?>" />
            
            </td>
        
        
    </table>
    </form>
</div>


<div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-hover"><thead><tr><th>Client Name</th><th>Client ID</th><th>Rate<th>Action</thead>
        <tbody>
<?php
    foreach($records as $record):
                
?>
    <tr><td><?=$record['name']?></td><td><?=$record['client_id']?><td><?=$record['rate_limit']?></td><td><a href='?actions=edit&id=<?=$record['id']?>&r=services/incoming'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$record['id']?>' event_name='<?=$record['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a> </td>
        
 <?php
        endforeach;
?>
        </tbody>
</table>
</div>
</div>
</div>
