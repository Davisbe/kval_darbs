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
        <h1>Visi lietotāji</h1>
    </div>
    <div class="places-list-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vārds</th>
                    <th>E-pasts</th>
                    <th>Reģistrējies</th>
                    <th>Apturēt profila darbību</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><a href="{{ route('profile.show', ['name'=>$user->name]) }}">{{ $user->name }}</a></td>
                        <td>{{ $user['created_at'] }}</td>
                        <td>{{ $user->email }}</td>
                        @if (!$user->suspended_profile)
                            <td><button user_name="{{ $user->name }}" class="user_suspend_toggle_button" type="button">Apturēt</button></td>
                        @else
                            <td><button user_name="{{ $user->name }}" class="user_suspend_toggle_button" type="button">Atsākt</button></td>
                        @endif
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