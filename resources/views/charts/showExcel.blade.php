@extends('layouts.layout')
@section('content')
<table id="table">
	<tbody>
		<th>图表名</th>
		<th>是否被写入数据库</th>
		<th>创建时间</th>
		<th>更新时间</th>
		<th>操作</th>
		@foreach($files as $file)
		<tr>
			@if($file->used == true)
				<td><a href="/table/{{$file->id}}">{{$file->title}}</a></td>
			@else
				<td>{{$file->title}}</td>
			@endif
			
			@if($file->used == false)
				<td>未被写入数据库</td>
				
			@else
				<td>已被写入数据库</td>
			@endif
			<td>创建时间：{{$file->created_at}}</td>
			<td>更新时间：{{$file->updated_at}}</td>
			<td>
				@if($file->used == false)
					<a href="/updateExcel/{{$file->id}}">写入数据库</a>/
				@else
					<a href="/downloadForm/{{$file->id}}">下载图表</a>/
					<a href="/showChart/{{$file->id}}">生成线上数据图</a>/
					<a href="/generateChart/{{$file->id}}">生成excel图表</a>/
				@endif
				

				@if($file->used == true)
					<a href="/deleteForm/{{$file->id}}">删除图表数据</a>
				@else
					<a href="/deleteFile/{{$file->id}}">删除服务器excel文件</a>
				@endif
				
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
<br>
<form action="/uploadExcel" method="post" enctype="multipart/form-data">
	{!! csrf_field() !!}
	<input type="file" name="myfile" />
    <input type="submit" name="submit" value="上传" />
</form>

@if(!empty(session('success')))
{{session('success')}}
@endif
<br>

<style type="text/css">

#table td,#table th,#table tbody,#table tr{
	border:1px solid black;
	text-align:center;
}
</style>

@endsection