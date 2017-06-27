@extends('layouts.layout')
@section('content')
	@foreach($all_projects as $project)
		@foreach($project as $all_details)
			<script>
				var data=[];
				@foreach($all_details as $detail)
					data.push({"month":'{{$detail->month}}',"mubiao":'{{$detail->goal}}',"shiji":'{{$detail->achieve}}'});
				@endforeach
			</script>
			
				<h1>{{$detail->name}}</h1>
				<div id="{{$detail->id}}" style="height: 250px;">
			
			<script>
				Morris.Area({
					element: '{{$detail->id}}',
					data: data,
					xkey: 'month',
			        ykeys: ['mubiao','shiji'],//纵坐标数值变量名
			        parseTime: false,
					//axes: true, //底标
					grid: true, //网格横线
					labels: ['目标', '实际'],
					barColors: ['#901D1D','#1D4990'],
					//stacked: false //是否重叠
					hideHover: 'false',//是否显示底标数据
					behaveLikeLine: true,
					//   hoverCallback: function (index, options, content, row) {
					//     return "sin(" + row.x + ") = " + row;
					//   }, //自定义callback 方法
					//gridTextColor: 'black',//底标字颜色	
				 });
			</script>	
		@endforeach	
	@endforeach
	<h1><a href="/">返回</a></h1>	
@endsection