<!DOCTYPE html>
<html>
<head>
	@include('layouts.header')

</head>
<body>
	<div class="container">
		@yield('content')
	</div>

	@if($flash = session('message'))
	<div class="alert alert-success" role="alert">
		{{$flash}}
	</div>
	@endif
	
</body>
</html>