@extends('admin.master')
@section('title', 'MAUC - admin')
@section('content')
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
    <div class="admin-dashboard-selection col-section">
        <div class="admin-selection-heading">
            Izveidot jaunu
        </div>

        <div class="admin-selection-content row-section">
            <a href="{{ route('admin.create_game') }}">
                <div class="admin-selection-button">
                    Spēli
                </div>
            </a>
            <a href="{{ route('admin.create_place') }}">
                <div class="admin-selection-button">
                    Vietu
                </div>
            </a>
        </div>
    </div>
    <div class="admin-dashboard-selection col-section">
        <div class="admin-selection-heading">
            Apskatīt/rediģēt
        </div>

        <div class="admin-selection-content row-section">
            <a href="{{ route('admin.list_games') }}">
                <div class="admin-selection-button">
                    Spēles
                </div>
            </a>

            <a href="{{ route('admin.list_users') }}">
                <div class="admin-selection-button">
                    Lietotājus
                </div>
            </a>

            <a href="{{ route('admin.list_vietas') }}">
                <div class="admin-selection-button">
                    Vietas
                </div>
            </a>

        </div>
    </div>

@endsection