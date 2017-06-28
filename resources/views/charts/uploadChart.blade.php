@extends('layouts.layout')
@section('content')
<form action="/uploadExcel" method="post" enctype="multipart/form-data">
	{!! csrf_field() !!}
	<input type="file" name="myfile" />
    <input type="submit" name="submit" value="上传" />
</form>

@if(!empty(session('success')))
{{session('success')}}
@endif
<br>
<a href="/readExcel">查看文件</a>

@endsection