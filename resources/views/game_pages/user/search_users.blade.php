@extends('game_pages.master')
@section('title', 'MAUC - Meklēt lietotājus')
@section('header_title', 'Meklēt lietotājus')
@section('content')
    <div class="padding-lr-15 col-section">
        <div class="col-section">
            <div class="search-section col-section">
                <div class="searchbar">
                    <form id="searchbar-users" action="{{ route('users.return') }}">
                        @csrf
                        <input type="text" id="searchUser">
                        <label>
                            <input type="submit" />
                            <x-svg.search_icon />
                        </label>
                    </form>
                    
                </div>

                <div id="searchResults" class="search-results col-section">
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
       var RETURN_USERS_URL = "{{ route('users.return') }}";
   </script>
@endsection