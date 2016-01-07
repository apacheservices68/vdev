@extends($extend)
@section('content')
	<span id="top-link-block" class="hidden">
	    <a href="#top" class="btn btn-danger" onclick="$('html,body').animate({scrollTop:0},'slow');return false;">
	        <i class="glyphicon glyphicon-chevron-up"></i>
	    </a>
	</span><!-- /top-link-block -->
	<div id="loading">
	&nbsp;
	</div>
	@if($body !== 'auth.admin.login')
	@include('layouts.admin.partials.nav')
	@endif
	@include($body)
@endsection