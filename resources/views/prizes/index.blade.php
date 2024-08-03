@extends('default')

@section('content')
    @include('prob-notice')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if ($totalProbability != 100)
                    <div class="alert alert-danger">
                        Sum of all prizes probability must be 100%. Currently it's {{ $totalProbability }}%. You have yet to {{ $totalProbability < 100 ? 'add' : 'remove' }} {{ abs(100 - $totalProbability) }}% to the prize.
                    </div>
                @endif
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('prizes.create') }}" class="btn btn-info">Create</a>
                </div>
                <h1>Prizes</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Probability</th>
                            <th>Awarded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prizes as $prize)
                            <tr>
                                <td>{{ $prize->id }}</td>
                                <td>{{ $prize->title }}</td>
                                <td>{{ $prize->probability }}</td>
                                <td>{{ $results[$prize->title] ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary">Edit</a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['prizes.destroy', $prize->id]]) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Simulate</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['simulate']]) !!}
                        <div class="form-group">
                            {!! Form::label('number_of_prizes', 'Number of Prizes') !!}
                            {!! Form::number('number_of_prizes', 50, ['class' => 'form-control']) !!}
                        </div>
                        {!! Form::submit('Simulate', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>

                    <br>

                    <div class="card-body">
                        <button type="button" class="btn btn-primary" id="resetButton">Reset</button>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div class="container  mb-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Probability Settings</h2>
                <canvas id="probabilityChart"></canvas>
            </div>
            <div class="col-md-6">
                <h2>Actual Rewards</h2>
                <canvas id="awardedChart"></canvas>
            </div>
        </div>
    </div>


@stop


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        const data = {
            labels: @json($prizes->pluck('title')),
            datasets: [{
                label: 'Probability',
                data: @json($prizes->pluck('probability')),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(201, 203, 207, 1)'
                ],
                borderWidth: 1
            }]
        };

        const config = {
            type: 'doughnut',
            data: data,
        };

        new Chart(document.getElementById('probabilityChart'), config);
    </script>

<script>
    var ctx = document.getElementById('awardedChart').getContext('2d');
    var awardedChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($results)) !!},
            datasets: [{
                label: 'Rewards Distribution',
                data: {!! json_encode(array_values($results)) !!},
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(231, 233, 237, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(231, 233, 237, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        }
    });
</script>
<script>
    document.getElementById('resetButton').addEventListener('click', function() {
        awardedChart.data.labels = [];
        awardedChart.data.datasets[0].data = [];
        awardedChart.update();
    });
</script>
@endpush
