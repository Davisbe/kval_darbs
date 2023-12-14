<div class="padding-lr-15 bottom-nav">
    <nav>
        <div class="row-lmr-1">
            <div>
                <button onclick="">
                    <x-svg.settings_icon />
                </button>
            </div>
        </div>
        <div class="row-lmr-2">
            <div>
                <button onclick="window.location.href='{{ route('games_index') }}'">
                    <x-svg.games_list_icon />
                </button>
            </div>
        </div>
        <div class="row-lmr-3">
            <div>
                <button onclick="window.location.href='{{ route('profile.show', ['name' => Auth::user()->name,]) }}'">
                <img src="{{ asset(Auth::user()->profile_picture) }}" alt="Profile Picture">
                </button>
            </div>
        </div>
    </nav>
</div>