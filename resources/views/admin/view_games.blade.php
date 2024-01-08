@extends('admin.master')
@section('title', 'MAUC - admin')
@section('content')

<div class="col-section padding-lr-15">
    @if(Session::get('success'))
        <div class="success-msg">
            {{ Session::get('success') }}
        </div>
    @endif
    @if(Session::get('fail'))
        <div class="error-msg">
            {{ Session::get('fail') }}
        </div>
    @endif
    <div class="col-section-header">
        <h1>Visas spēles</h1>
    </div>
    <div class="places-list-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nosaukums</th>
                    <th>Sākums</th>
                    <th>Beigas</th>
                    <th>Rediģēt/skatīt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($games as $game)
                    <tr>
                        <td>{{ $game->id }}</td>
                        <td>{{ $game->name }}</td>
                        <td>{{ $game->start_time }}</td>
                        <td>{{ $game->end_time }}</td>
                        <td><a href="{{ route('admin.edit_game', ['id'=>$game->id]) }}">skat.</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

    <script>
        var ADMIN_USER_SUSPEND_URL = "{{ route('admin.suspend_user') }}";
    </script>

@endsection