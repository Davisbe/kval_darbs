@extends('homepage.master')
@section('title', 'MAUC - e-pasta verifikācija')

@section('content')
    <div class="text-section padding-lr-15">
        <h1>Verificē e-pastu</h1>
    </div>

    <div class="col-section padding-lr-15">
        @if(Session::get('success'))
            <div class="success-msg">
                {{ Session::get('success') }}
            </div>
        @endif

        <p>
        {{ __('Ja minūtes laikā nesaņēmi verifikācijas e-pastu, tad izmanto pogu, kas atrodama zemāk!') }}
        </p>

        <div class="padding-tb-30">
            <form class="" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class=""><span class="hyperlink-text">{{ __('spiediet šeit, lai nosūtītu jaunu verifikācijas saiti') }}</span></button>
            </form>
        </div>
        
    </div>
@endsection