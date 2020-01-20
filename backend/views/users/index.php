<?php
/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

 */
$this->title = 'Users';

$saveURL = \Yii::$app->getUrlManager()->createUrl('users/save');
$deleteURL = \Yii::$app->getUrlManager()->createUrl('users/delete');
$saveRoleURL = \Yii::$app->getUrlManager()->createUrl('users/save-role');
$deleteRoleURL = \Yii::$app->getUrlManager()->createUrl('users/delete-role');
$mediaURL =  \Yii::$app->getUrlManager()->createUrl('media/featured-media');
$profileURL = \Yii::$app->getUrlManager()->createUrl('users/details');
$fileURL = \Yii::$app->getUrlManager()->createUrl('users/files');

$this->registerJs( <<< EOT_JS
  $(document).on('click', '#btnSubmit',
       function(ev) {   
        $.post(
            '{$saveURL}',$( "#frm1" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
  }); 
  
  $(document).on('click', '#btnNew',
       function(ev) {         
        location.href="?r=users/index";
        ev.preventDefault();
  });
  
       $(document).on('click', '#btnSubmitRole',
       function(ev) {   
        $.post(
            '{$saveRoleURL}',$( "#frmRole" ).serialize(),
            function(data) {
                alert(data);
            }
        )
        ev.preventDefault();
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
  
  $('.delete_event_role').click(function (element) {                    
                    var id = $(this).attr('id');
                    var event_name = $(this).attr('event_name');
                    if(confirm('Are you sure you want to delete role - ' + event_name)){
                        $.get(  
                            '{$deleteRoleURL}',{id:id},
                            function(data) {
                                alert(data);
                            }
                        )
                    }            
  });
                            
  $('.media').click(function (element) {  
      localStorage.setItem("image_caller",$(this).attr('id')); //store who is calling this dialog 
      $.get(
                '{$mediaURL}',{search:'featured',exempt_the_headers_in_yumpee:'true'},
                function(data) {                    
                    $('#yumpee_media_content').html(data);
                    $('#myModal').modal();
                }    
            )
     ev.preventDefault(); 
  });
             
   $(document).on('click','#btnInsertMedia',
       function(ev){
       var myradio = $("input[name='my_images']:checked").val();   
       var my_radio_info = myradio.split("|");
       var img_src = my_radio_info[0];
       var my_id = my_radio_info[1];
       if(localStorage.image_caller=="set_feature"){
                $("#display_image_id").val(my_id);
                $("#my_display_image").attr("src","../../uploads/" + img_src);
                localStorage.removeItem("image_caller");
       }
       
                
       $('#myModal').modal('toggle');     
   });
 
 if($("#edit_roles").val()=="edit_roles"){
  $('#role_tab').trigger('click')        
 }
                
 $("#lnkDetails").click(function(){
   $.get(
                '{$profileURL}',{user:$(this).attr("account_id")},
                function(data) {                    
                    $("#details-content").html(data);
                }    
            )  
 })
  
 $("#sel_check_page").click(function(){     
     $(".rolep").each(function(){    
                $(this).attr("checked","checked");
     })
 })
 $("#sel_uncheck_page").click(function(){     
     $(".rolep").each(function(){    
                $(this).removeAttr("checked");
     })
 })               
 $("#sel_check_widget").click(function(){     
     $(".rolew").each(function(){    
                $(this).attr("checked","checked");
     })
 })
 $("#sel_uncheck_widget").click(function(){     
     $(".rolew").each(function(){    
                $(this).removeAttr("checked");
     })
 })               
                
                
                
                
                
 $("#lnkFiles").click(function(){
   $.get(
                '{$fileURL}',{user:$(this).attr("account_id")},
                function(data) {                    
                    $("#details-content").html(data);
                }    
            )  
 })

$("#btnCustomAdd").click(function(){                
                $("#custom_form_header").append("<tr><td>" + $("#yumpee_custom_field").val() + "<td><input class='form-control' type='text' name='" + $("#yumpee_custom_field").val() + "' id='" + $("#yumpee_custom_field").val() + "'>");    
})
                
 $("#datalisting").DataTable();
EOT_JS
);
?>
<style>
    .modal-dialog{
    position: relative;
    display: table; /* This is important */ 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 300px;   
}



 /* The sidepanel menu */
.sidepanel {
  height: 1000px; /* Specify a height */
  width: 0; /* 0 width - change this with JavaScript */
  position: fixed; /* Stay in place */
  z-index: 1; /* Stay on top */
  top: 200;
  left: 200;
  background-color: #ffffff; /* Black*/
  overflow-x: hidden; /* Disable horizontal scroll */
  padding-top: 60px; /* Place content 60px from the top */
  transition: 0.5s; /* 0.5 second transition effect to slide in the sidepanel */
}

/* The sidepanel links */
.sidepanel a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  color: #818181;
  display: block;
  transition: 0.3s;
}

/* When you mouse over the navigation links, change their color */
.sidepanel a:hover {
  color: #f1f1f1;
}

/* Position and style the close button (top right corner) */
.sidepanel .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

/* Style the button that is used to open the sidepanel */
.openbtn {
  font-size: 20px;
  cursor: pointer;
  background-color: #111;
  color: white;
  padding: 10px 15px;
  border: none;
}

.openbtn:hover {
  background-color: #444;
} 
</style>
<?php
$display_image_path="";
if(isset($rs->displayImage->path)):
    $display_image_path=$rs->displayImage->path;
endif;

?>

<div id="mySidepanel" class="sidepanel">
    <div class="container">
     <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
     <div id="details-content"></div>
    </div>
</div>

<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#users">Manage Users</a></li>
        <li><a data-toggle="tab" href="#roles" id="role_tab">Roles</a></li>
  
    </ul>
    <div class="tab-content">
        <div id="users" class="tab-pane fade in active">

<div id="addUser">
    <form action="index.php?r=events/index" method="post" id="frm1"><br>
        <?php
        if(Yii::$app->request->get("id")!=null):
        ?>
         <div class="pull-right"><a href='#' onclick="openNav()" id="lnkDetails" account_id="<?=Yii::$app->request->get("id")?>"><i class="fa fa-info-circle"></i> Additional Details</a> | <a href='#' onclick="openNav()" id="lnkFiles" account_id="<?=Yii::$app->request->get("id")?>"> <i class="fa fa-file"></i> Attached Files</a> | <a href='#' data-toggle="modal" data-dismiss="modal" data-target="#addfield">+ Add Field</a>
             
         </div>
        <?php
        endif;
        ?>
    <table class="table">
        <thead id="custom_form_header"></thead>
        <tr><td>Username<td><input name="usrname" id="usrname" class="form-control" type="text" value="<?=$rs['username']?>"/>
        <tr><td>Password<td><input name="passwd" id="passwd" class="form-control" type="password" value="<?=$rs['password_hash']?>"/>
        <tr><td>First Name<td><input name="first_name" id="first_name" value="<?=$rs['first_name']?>" class="form-control"type="text" />
        <tr><td>Last Name<td><input name="last_name" value="<?=$rs['last_name']?>" id="last_name" class="form-control" type="text" />
        <tr><td>Title<td><input name="title" value="<?=$rs['title']?>" id="title" class="form-control" type="text" />  
        <tr><td>Role<td><?=$role_dropdown?>
        <tr><td>Email<td><input name="email" value="<?=$rs['email']?>" id="email" class="form-control" type="text" /> 
        <tr><td>About<td><textarea name="about" id="about" class="form-control"><?=$rs['about']?></textarea>
        <tr><td>Feature Image<td><img id='my_display_image' src='<?=Yii::getAlias("@image_dir")?>/<?=$display_image_path?>' height='100px' align='top' width='200px' style='border:1px solid #233388' HSPACE='20' VSPACE='20'/> <a href='#' class='media' id='set_feature'>Set Feature Image</a><input type="hidden" name="display_image_id" id="display_image_id" value="<?=$rs['display_image_id']?>"/>
        <tr><td colspan="2"><button type="submit" id="btnSubmit" class="btn btn-success">Save</button> <button type="reset" id="btnNew" class="btn btn-primary">New</button><input type="hidden" name="processor" value="true" /><input type="hidden" name="id" value="<?=$id?>" /></td>
    </table>
    </form>
</div>


<div class="box">
<div class="box-body">
<table id="datalisting" class="table table-bordered table-striped">
    <thead><tr><th><th>Username</th><th>Full Name<th>Email<th>Assigned Role<th>Actions</thead>
    <tbody>
            <?php
            foreach ($records as $rec):
                $di="empty";
                if($rec->displayImage!=null):
                    $di = $rec->displayImage->path;
                endif;
            ?>
    <tr><td><img src='<?=Yii::getAlias("@image_dir")?>/<?=$di?>' height='70px'></img><td><?=$rec['username']?></td><td><?=$rec['first_name']?> <?=$rec['last_name']?></td><td><?=$rec['email']?></td><td><?=$rec['role']['name']?></td><td><a href='?actions=edit&id=<?=$rec['id']?>&r=users/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event' id='<?=$rec['id']?>' event_name='{{name}}'><small><i class="glyphicon glyphicon-trash"></i></small></a> <a href='?actions=edit&id={{id}}&r=users/region'><small><i class="fa fa-globe"></i></small></a></td>
        <?php
            endforeach
        ?>
    </tbody>
</table>
</div>
</div>
        </div>
<div id="roles" class="tab-pane fade in">
            <form action="index.php?r=events/index" method="post" id="frmRole">
        <table class="table">
        <tr><td width="20%">Role Name<td><input name="name" id="name" value="<?=$role_rs["name"]?>" class="form-control" type="text" />
        <tr><td>Parent Role<td><?=$role_parent_dropdown?>
        <tr><td>Access Type<td><?=\yii\helpers\Html::dropDownList("access_type",$access_type,['F'=>'Front End','B'=>'Back End'],['class'=>'form-control'])?></td>
        <tr><td>Default Menu(frontend)</td><td><?=$menu_list?></td>
        <tr><td>Home Page</td><td><?=$home_page_dropdown?></td>
        <tr><td>Default Theme(frontend)</td><td><?=$theme_dropdown?></td>
        <tr><td>Description<td><textarea name="description" id="description" rows="3" cols="30" class="form-control"><?=$role_rs["description"]?></textarea>  
        <?php
        if(Yii::$app->request->get("actions")=="edit_roles"):
        ?>
        <tr><td valign="top">Pages </td><td><div class="pull-right"><a href='#' id='sel_check_page'>Check </a>/  <a href='#' id='sel_uncheck_page'>Uncheck All</a></div><br><div><?=$page_checkbox?></div></td>
        <tr><td valign="top">Widgets</td><td><div class="pull-right"><a href='#' id='sel_check_widget'>Check </a> / <a href='#' id='sel_uncheck_widget'>Uncheck All</a></div><br><div><?=$page_custom_widget?></div></td>
                
        <?php
        endif;
        ?>
        
        <tr><td colspan="2"><button type="submit" id="btnSubmitRole" class="btn btn-success">Save</button> <button type="button" id="btnNew" class="btn btn-primary">New</button> <input type="hidden" name="processor" value="true" /><input type="hidden" name="role_id" value="<?=$role_rs["id"]?>" />
            
        </td>
    </table>
    </form><input id="edit_roles" type="hidden" value="<?=Yii::$app->request->get("actions")?>"/>
            <div class="box">
<div class="box-body">
    <table id="datalisting" class="table table-bordered table-hover"><thead><tr><th>Name</th><th>Description<th>Action</thead>
        <tbody>
<?php
    foreach($roles as $record):
                
?>
    <tr><td><?=$record['name']?></td></td><td><?=$record['description']?><td><a href='?actions=edit_roles&role_id=<?=$record['id']?>&r=users/index'><small><i class="glyphicon glyphicon-pencil"></i></small></a> <a href='#' class='delete_event_role' id='<?=$record['id']?>' event_name='<?=$record['name']?>'><small><i class="glyphicon glyphicon-trash"></i></small></a> </td>
        
 <?php
        endforeach;
?>
        </tbody>
</table>
</div>
</div>
        </div>
</div>




<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Insert Media</h4>
      </div>
      <div class="modal-body">
          <div id="yumpee_media_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnInsertMedia">Insert Media</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    
<div id="addfield" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add field to user</h4>
      </div>
      <div class="modal-body">
        <p>Enter field name</p>
        <input type="text" class="form-control" id="yumpee_custom_field" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="btnCustomAdd">Add</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script>
    /* Set the width of the sidebar to 250px (show it) */
function openNav() {
  document.getElementById("mySidepanel").style.width = "1000px";
}

/* Set the width of the sidebar to 0 (hide it) */
function closeNav() {
  document.getElementById("mySidepanel").style.width = "0";
}
</script>