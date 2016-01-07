<form class="form-horizontal" role="form" id="formIndexImage" method="POST" action="{{ url('/dashboard/image') }}">
    {!! csrf_field() !!}
    <div class="form-group">
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary" id="save">
                <i class="fa fa-btn fa-sign-in"></i> Save
            </button>                                
        </div>
        <div class="col-md-2">
            {{ Form::file('images', ['id'=>'images','class' => 'filestyle','data-input'=> 'false' , 'multiple']) }}
		    <input id="file_image" name="file_image" type="hidden" value="">                           
        </div>
    </div>

	
	<div class="alert alert-success" style="display:none;">
		<strong>Title!</strong> Alert body ...
	</div>
	<div class="form-group" id="template-upload">
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg" style="display:none;"></button>
		<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"><div class="modal-dialog modal-lg"><div class="modal-content"><img src="" class="img-responsive" alt=""></div></div></div>
		<script>
		var template = '<div class="col-md-2 col-xs-4 marginimage" data-id="0"><button class="btn btn-primary btn-xs btncopy" type="button" title="" data-clipboard-text="http://placehold.it/230x182"><i class="fa fa-copy"></i></button><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-remove"></i></button><button class="btn btn-success btn-xs" type="button"><i class="fa fa-search-plus"></i></button><img src="http://placehold.it/230x182" class="img-responsive" alt="Image"></div>';		
		</script>	
		@if(isset($files))
			{{-- */$i = 0;/* --}}
			@foreach($files as $key => $val)
				<div class="col-md-2 col-xs-4 marginimage" data-id="{{ $i }}">
				<button class="btn btn-primary btn-xs btncopy" type="button" title="" data-clipboard-text="{{ $val }}"><i class="fa fa-copy"></i></button>
				<button class="btn btn-danger btn-xs" type="button"><i class="fa fa-remove"></i></button>
				<button class="btn btn-success btn-xs" type="button"><i class="fa fa-search-plus"></i></button>
				<img src="{{ $key }}" class="img-responsive" alt="Image">
				</div>
				{{-- */$i++/* --}}
			@endforeach
		@endif
	</div>	
</form>
