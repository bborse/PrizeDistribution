@extends('default')

@section('content')

@include('prob-notice')
	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'prizes.store']) !!}

		<div class="mb-3">
			{{ Form::label('title', 'Title', ['class'=>'form-label']) }}
			{{ Form::text('title', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('probability', 'Probability', ['class'=>'form-label']) }}
			{{ Form::number('probability', null, array('class' => 'form-control','min' => '0','max' => '100', 'placeholder' => '0 - 100','step' => '0.01')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
<script>
    document.getElementById('prizeForm').addEventListener('submit', function(event) {
        let totalProbability = {{ $totalProbability }};
        let newProbability = parseFloat(document.querySelector('input[name="probability"]').value);

        if (totalProbability + newProbability > 100 || totalProbability + newProbability < 0) {
            event.preventDefault();
            alert('Probability sum must be exactly 100%.');
        }
    });
</script>

