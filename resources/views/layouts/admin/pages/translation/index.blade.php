<form class="form-horizontal" role="form" id="formIndexTranslation" method="POST" action="{{ url('/dashboard/translations') }}">
    {!! csrf_field() !!}
    <div class="form-group">
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-btn fa-sign-in"></i> Save
            </button>                                
        </div>
    </div>
    <div class="form-group{{ $errors->has('files') ? ' has-error' : '' }}">
        <label class="col-md-1 control-label">Select lang</label>
        <div class="col-md-4">
            @if(isset($pol))
            {!! Form::select('files', array_combine(array_values($files), array_values($files)), $pol, ['class' => 'form-control','id'=>'files']) !!}
            @else
            {!! Form::select('files', array_combine(array_values($files), array_values($files)), isset($flFiles) ? $flFiles : NULL, ['class' => 'form-control','id'=>'files']) !!}
            @endif
            @if ($errors->has('files'))
                <span class="help-block">
                    <strong>{{ $errors->first('files') }}</strong>
                </span>
            @endif
        </div>
        <div class="col-md-4">
            <div class="input-group pull-right">
                {{ Form::text('addFile', '', array_merge(['class' => 'form-control','style'=>'display:none;','placeholder'=>'File name'], array())) }}    
            </div>
        </div>
        <div class="col-md-3">
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-primary" id="addFile">Add file</button>
                <button type="button" class="btn btn-info" id="addLine">Add line</button>                
            </div>
        </div>
    </div>    
    
    <table class="table table-bordered center">
        <thead>
            <tr>
                <th>Keys</th>
                @foreach($folders as $key => $val)
                <th>{{ $val }}</th>
                @endforeach
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr data-id="0" class="items" style="display:none;">
                <td>{{ Form::text('keys[0]', '', array_merge(['class' => 'form-control keys','placeholder'=>'Keys'], array())) }}</td>
                @for($i = 0;$i < count($folders) ; $i++)
                    <td>{{ Form::text('values_'.$folders[$i].'[0]', '', array_merge(['class' => 'form-control keys','placeholder'=>$folders[$i].' values'], array())) }}</td>
                @endfor
                <td><button type="button" class="btn btn-warning removeLine" ><i class="fa fa-remove"></i></button></td>
            </tr>
            @if(isset($firsts))
            @foreach($firsts as $key => $val)
            <tr>
                <td>{{ $key }}</td>
                @foreach($val as $ke => $va)

                    <td>{{ Form::text($ke."[".$key."]", $va, array_merge(['class' => 'form-control'], array())) }}</td>

                @endforeach
                <td>
                    {{ Form::checkbox("checkbox[]", $key) }}
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</form>