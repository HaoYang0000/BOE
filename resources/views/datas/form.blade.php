@extends('layouts.layout')
@section('content')

<div class="wrapper">
    <div class="fresh-table full-color-azure full-screen-table">
    <!--    Available colors for the full background: full-color-blue, full-color-azure, full-color-green, full-color-red, full-color-orange                  
            Available colors only for the toolbar: toolbar-color-blue, toolbar-color-azure, toolbar-color-green, toolbar-color-red, toolbar-color-orange
    -->
        
        <div class="toolbar">
            <button id="alertBtn" class="btn btn-default">返回</button>
            {{-- 从<input id="rangeForm">到<input id="rangeTo" > --}}
        </div>
        <h3>文件名：{{$file_name}}</h3>
        <table id="fresh-table" class="table">
            <thead>
                <th data-field="id">ID</th>
            	<th data-field="name" data-sortable="true">项目名称</th>
            	<th data-field="state" data-sortable="true">状态</th>
            	<th data-field="goal" data-sortable="true">目标</th>
            	<th data-field="achieve" data-sortable="true">实际</th>
            	<th data-field="month" data-sortable="true">月份</th>
            	{{-- <th data-field="actions" data-formatter="operateFormatter" data-events="operateEvents">Actions</th> --}}
            </thead>
            <tbody>
            	@foreach($all_projects as $project)
					@foreach($project as $all_details)
							@foreach($all_details as $detail)
								<tr>
									<td>{{$detail->id}}</td>
									<td>{{$detail->name}}</td>
									@if($detail->goal == 0||$detail->achieve == 0)
									<td>数据不全</td>
									@elseif($detail->achieve/$detail->goal>=0.8)
									<td><div style="background-color: green;">良好</div></td>
									@elseif($detail->achieve/$detail->goal>=0.6)
									<td><div style="background-color: orange;">需要关注</div></td>
									@else
									<td><div style="background-color: red;">需要重点关注</div></td>
									@endif
									<td>{{$detail->goal}}</td>
									<td>{{$detail->achieve}}</td>
									<td>{{$detail->month}}</td>
									{{-- <td></td> --}}
								</tr>
							@endforeach
					@endforeach	
				@endforeach
            </tbody>
        </table>
    </div>
    
</div>
<form id="range" action="/table/{{$file_id}}" method="get">
    <input type="hidden" name="from" id="from">
    <input type="hidden" name="to" id="to">
</form>
    <script type="text/javascript">
        function showTotal(){
            var temp = "{{$total}}";
            if(temp == ""){
                window.location.href = 'http://localhost:8000/table/{{$file_id}}?'+'total=1';
            }
            else{
                window.location.href = 'http://localhost:8000/table/{{$file_id}}';
            }      
        }

        function update(){
            var from = document.getElementById("rangeFrom").value;
            var to = document.getElementById("rangeTo").value;

            if(from == "" || to == "" ){
                alert("请填写完整数据！");
            }
            else if(from > to){
                alert("起始日期应小于或等于结束日期！");
            }
            else{
                document.getElementById("from").value = from;
                document.getElementById("to").value = to;
                document.getElementById("range").submit();
            }
        }

        var $table = $('#fresh-table'),
            $alertBtn = $('#alertBtn'), 
            full_screen = true,
            window_height;
            
        $().ready(function(){
            
            window_height = $(window).height();
            table_height = window_height - 20;
            
            
            $table.bootstrapTable({
                toolbar: ".toolbar",
                showRange: true,
                showRefresh: true,
                showTotal: true,
                search: true,
                showToggle: false,
                showColumns: true,
                pagination: true,
                striped: true,
                sortable: true,
                height: table_height,
                pageSize: 50,
                pageList: [25,50,100],
                
                formatShowingRows: function(pageFrom, pageTo, totalRows){
                    //do nothing here, we don't want to show the text "showing x of y from..." 
                },
                formatRecordsPerPage: function(pageNumber){
                    return pageNumber + " rows visible";
                },
                icons: {
                    refresh: 'fa fa-refresh',
                    toggle: 'fa fa-th-list',
                    columns: 'fa fa-columns',
                    detailOpen: 'fa fa-plus-circle',
                    detailClose: 'fa fa-minus-circle'
                }
            });
            var temp = "{{$total}}";
            if(temp == 1){
                document.getElementById("totalDiv").style="background-color:green;";
            }
            
            window.operateEvents = {
                'click .like': function (e, value, row, index) {
                    alert('You click like icon, row: ' + JSON.stringify(row));
                    console.log(value, row, index);
                },
                'click .edit': function (e, value, row, index) {
                    alert('You click edit icon, row: ' + JSON.stringify(row));
                    console.log(value, row, index);    
                },
                'click .remove': function (e, value, row, index) {
                    $table.bootstrapTable('remove', {
                        field: 'id',
                        values: [row.id]
                    });
                }
            };
            
            $alertBtn.click(function () {
            	window.location.href = 'http://localhost:8000/'
                //alert("You pressed on Alert");
            });
        
            
            $(window).resize(function () {
                $table.bootstrapTable('resetView');
            });    
        });
        

        function operateFormatter(value, row, index) {
            return [
                '<a rel="tooltip" title="Like" class="table-action like" href="javascript:void(0)" title="Like">',
                    '<i class="fa fa-heart"></i>',
                '</a>',
                '<a rel="tooltip" title="Edit" class="table-action edit" href="javascript:void(0)" title="Edit">',
                    '<i class="fa fa-edit"></i>',
                '</a>',
                '<a rel="tooltip" title="Remove" class="table-action remove" href="javascript:void(0)" title="Remove">',
                    '<i class="fa fa-remove"></i>',
                '</a>'
            ].join('');
        }
       
    </script>
@endsection
