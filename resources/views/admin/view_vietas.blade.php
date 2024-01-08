@extends('admin.master')
@section('title', 'MAUC - admin')
@section('content')

<div class="padding-lr-15">
    <h1>Vietu saraksts</h1>
    <div class="col-section places-list-table-wrapper">
        
        <table>
            <tr>
                <th>Nosaukums</th>
                <th>Attēls</th>
                <th>Garums</th>
                <th>Platums</th>
                <th>Pielaujama kļūda</th>
                <th>Sarezģītība</th>
                <th>Izmantota reizes</th>
                <th>Rediģēt</th>
            </tr>
            @foreach ($vietas as $vieta)
                <tr>
                    <td>{{ $vieta->name }}</td>
                    <td><img src="{{ asset($vieta->picture) }}" alt=""></td>
                    <td>{{ $vieta->garums }}</td>
                    <td>{{ $vieta->platums }}</td>
                    <td>{{ $vieta->pielaujama_kluda }}</td>
                    <td>{{ $vieta->sarezgitiba }}</td>
                    <td>{{ $vieta->vieta_izmantota_count }}</td>
                    <td><a href="{{ route('admin.edit_vieta', ['id'=>$vieta->id]) }}">Rediģēt</a></td>
                </tr>
            @endforeach
        </table>

    </div>
</div>


@endsection