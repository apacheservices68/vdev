@extends($extend)
@section('content')
	<div class="wrapper">    
    	@include('layouts.home.partials.header')
		@include('layouts.home.partials.nav')
		@include($body)    
	</div><!--//wrapper-->
	@include('layouts.home.partials.footer')
@endsection
