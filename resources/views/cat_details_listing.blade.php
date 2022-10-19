@extends('layouts.dynamic_pg')
@section('content_dynamic')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Category</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                @if(isset($breadcrumbs) && !empty($breadcrumbs))
                    @for($i = 0; $i < count($breadcrumbs); $i++)

                    @if(isset($breadcrumbs[$i]['url']) && !empty($breadcrumbs[$i]['url']))
                        <li class="breadcrumb-item"><a href="{{ $breadcrumbs[$i]['url'] }}">
                    @endif
                        {{ $breadcrumbs[$i]['name'] }}

                        @if(isset($breadcrumbs[$i]['url']) && !empty($breadcrumbs[$i]['url']))
                        </a> </li> > 
                        @endif
                    @endfor
                @endif
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-header text-right">
                     <a class="btn btn-success" href="{{ route('edit_user_master_view') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                <table id="cat_datatable_table" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="select_all" value="1" class="select_all_checkbox" id="select-all-checkbox"></th>
                            <th>Category Name</th>
                            <th>Category Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<script type="text/javascript">
	var oTable;
	$(function() {
		var rows_selected = [];
		oTable = $('#cat_datatable_table').DataTable({
			responsive:true,
			"order": [[1, 'desc']], //By default, order by descending 1st column
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	        	url: "{{ route('cat_ajax_list') }}",
	        	"dataType":"json",
				
	    	},
	    	"fnDrawCallback": function( oSettings ) {
	    		
		      	$("#select-all-schools").on("click", function(){
					var rows = oTable.rows({ "search": "applied" }).nodes();
					$("input[type='checkbox']", rows).prop("checked", this.checked);
				});
		    },
	        "columns": [
	        	{ "data": "checkbox", orderable: false, searchable: false},
	        	{ "data": "category_name"},
                { "data": "category_desc"},
	            { "data": "action", orderable: false, searchable: false  }
	        ]
		});
		
		// $(document).on('change','#tracker_type',function() {
		// 	oTable.ajax.reload();
		// });
		// Handle click on checkbox
        $('#cat_datatable_table tbody').on('click', 'input[type="checkbox"]', function(e){
          var $row = $(this).closest('tr');
          
          // Get row data
          var data = oTable.row($row).data();
          
          // Get row ID
          var rowId = data[0];
          
          
          // Determine whether row ID is in the list of selected row IDs
          var index = $.inArray(rowId, rows_selected);

          // If checkbox is checked and row ID is not in list of selected row IDs
          if(this.checked && index === -1){
             rows_selected.push(rowId);

          // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
          } else if (!this.checked && index !== -1){
             rows_selected.splice(index, 1);
          }

          if(this.checked){
             $row.addClass('selected');
          } else {
             $row.removeClass('selected');
          }

          // Update state of "Select all" control
          updateDataTableSelectAllCtrl(oTable);

          // Prevent click event from propagating to parent
          e.stopPropagation();
        });

        // Handle click on table cells with checkboxes
        $('#cat_datatable_table').on('click', 'tbody td, thead th:first-child', function(e){
          // $(this).parent().find('input[type="checkbox"]').trigger('click');
        });

        // Handle click on "Select all" control
        $('thead input[name="select_all"]', oTable.table().container()).on('click', function(e){
          if(this.checked){
             $('#cat_datatable_table tbody input[type="checkbox"]:not(:checked)').trigger('click');
          } else {
             $('#cat_datatable_table tbody input[type="checkbox"]:checked').trigger('click');
          }

          // Prevent click event from propagating to parent
          e.stopPropagation();
        });
});	


//Delete user
$(document).on('click', '.delete_user', function() {
    var u_id = $(this).attr("data-uid");
    var u_role = $(this).attr("u-role");
    if(u_role == 1){ // super admin cannot be deleted by anyone
        alert('Super Admin cannot be delete. Contact Administrator !!!');
    }
    else{
        if(confirm("Do you really want to delete this user?")){
            $this = $(this);
            $this.prop("disabled", true);
            $.ajax({
                type: "POST",
                url: "{{ route('delete_user') }}",
                data: {'u_id': u_id },
                headers: {
                    // 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                // dataType: 'json',
                success: function (result) {
                    $this.prop("disabled", false);
                    if(result.status == true){
                        oTable.draw();
                        showSweetAlertMessage(result.status,result.message);

                    
                    }
                    else{
                            showSweetAlertMessage(result.status,result.message);
                    }    
                    
                },
                error: function (data) {
                    // console.log('Error:', data);
                    $this.prop("disabled", false);
                    var parse_error = JSON.parse(data.responseText);
                    // console.log('parse_error:'+ parse_error.error);
                    if(typeof parse_error.error != 'undefined' && parse_error.error == 'Unauthenticated.'){
                        showSweetAlertMessage(false,'Your session has expired. Please login again.');
                        $(".close, .modal").click(function(){
                            window.location.reload();
                        });
                    }
                }
            });
        }
    }
    
});
//Delete user close
</script>

@endsection