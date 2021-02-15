<html>
 <head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laravel 8 - DataTables Server Side Processing using Ajax</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>  
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
 </head>
 <body>
  <div class="container">    
     <br />
     <h3 align="center">Userlist</h3>
     <br />
     <div align="right">
      <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm" onclick="exportAll()">Export Data</button>
     </div>
     <br />    
     <input type="hidden" name="_token2" id="token2" value="{{ csrf_token() }}">
   <div class="table-responsive">
    <table class="table table-bordered table-striped" id="user_table">
           <thead>
            <tr>
                <th width="10%">Select all <input type="checkbox" name="select-all" id="select-all" /></th>
                <th width="10%">Id</th>
                <th width="35%">user Id</th>
                <th width="35%">title</th>
                <th width="30%">Body</th>
                <th  width="30%">Action</th>
            </tr>
           </thead>
           @foreach($displaydata as $value)
           <tbody>
            <td><input type="checkbox" name="checkbox-<?php echo $value->id ?>" id="checkbox-<?php echo $value->id ?>" value="<?php echo $value->id ?>"/></td>
             <td>{{$value->id}}</td>
             <td>{{$value->userid}}</td>
             <td>{{$value->title}}</td>
             <td>{{$value->body}}</td>
             <td><a href="#" class="btn btn-primary a-btn-slide-text" style="width: 100%;
    margin-top: 10%;"  onclick="edit_user('<?php echo $value->id ?>')" >
        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
        <span><strong>Edit</strong></span>            
    </a>
    <a href="#" class="btn btn-primary a-btn-slide-text delete" style="width: 100%;
    margin-top: 10%;" id="delete_user" user_id="<?php echo $value->id ?>">
       <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
        <span><strong>Delete</strong></span>            
    </a>
  </td>
           </tbody>
           @endforeach
       </table>
   </div>
   <br />
   <br />
  </div>
 </body>
</html>

<div id="formModal" class="modal fade" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Record <span id="edit_id"></span></h4>
        </div>
        <div class="modal-body">
         <span id="form_result"></span>
         <form  id="edit_form" class="form-horizontal">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <div class="form-group">
            <label class="control-label col-md-4" >Title</label>
            <div class="col-md-8">
             <input type="text" name="usertitle" id="usertitle" class="form-control" />
            </div>
           </div>
           <div class="form-group">
            <label class="control-label col-md-4">Body</label>
            <div class="col-md-8">
             <input type="text" name="userbody" id="userbody" class="form-control" />
            </div>
           </div>
          
           <br />
           <div class="form-group" align="center">
            
            <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Edit" />
           </div>
         </form>
        </div>
     </div>
    </div>
</div>

<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">Confirmation</h2>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function(){

// $('#user_table').DataTable();
    
 $('#user_table').DataTable( {
       // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
       "bPaginate": false,
    } );

 


$('#edit_form').on('submit', function(event){
  event.preventDefault();
  let token = $('#token').val();
   let id = $('#edit_id').html();
   let title = $('#usertitle').val();
  let body =     $('#userbody').val();
  $.ajax({
    url:"{{ url('user/update')}}",
    method:"POST",
    data: {id:id,title:title,body:body,_token: token},    
    dataType:"json",
    success:function(data)
    {
     var html = '';
     if(data.errors)
     {
      html = '<div class="alert alert-danger">';
      for(var count = 0; count < data.errors.length; count++)
      {
       html += '<p>' + data.errors[count] + '</p>';
      }
      html += '</div>';
     }
     if(data.success)
     {
      html = '<div class="alert alert-success">' + data.success + '</div>';
     }
     $('#form_result').html(html);

    }

   })
});


var user_id;
$(document).on('click', '#delete_user', function(){
  user_id = $(this).attr('user_id');
  $('#confirmModal').modal('show');
 });


 $('#ok_button').click(function(){
  $.ajax({
   url:"{{ url('user/destroy')}}"+'/'+user_id,
    method:"GET",
   beforeSend:function(){
    $('#ok_button').text('Deleting...');
   },
   success:function(data)
   {
    location.reload();
   }
  })
 });



$('#select-all').click(function(event) {   
    if(this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = true;                        
        });
    } else {
        $(':checkbox').each(function() {
            this.checked = false;                       
        });
    }
});



 });


function edit_user(id)
{
     $('#formModal').modal('show');
     
     $('#edit_id').html(id);
      $.ajax({
       url:"{{ url('user/edit')}}"+'/'+id,
       dataType:"json",
       success:function(jsonData){
        $('#usertitle').val(jsonData.edit_data.title);
        $('#userbody').val(jsonData.edit_data.body);
      }
    })
}


function exportAll() {
    var userlist = $('input:checkbox:checked').map(function () {
        return this.value;
    }).get();
    //let token = $('#token2').val();
    $.ajax({
        type: "POST",
        url: "{{ url('user/exportData')}}" ,
        data: {
            list: userlist,_token: "{{ csrf_token() }}",
        },
        success: function (result) {
            if (result == 'export') {
               window.location.href = "/smtp";
            }
        }
    });
}



</script>