<nav class="navbar navbar-expand-lg navbar-dark justify-content-between absolute-top">
    <div class="container">
    <a class="navbar-brand" href="{{url("")}}"><img src="{{asset('img/logo_v2.png')}}" style="padding: 0 10px 0 0;" width="60">Zpěvník pro scholy</a>
    @if (Auth::check())
            <a class="navbar-text px-3" href="{{route('admin.dashboard')}}">
            Přihlášený uživatel: {{ Auth::user()->name }}
            @if (Auth::user()->roles()->count() > 0)
                ({{Auth::user()->roles()->first()->name}})
            @endif
        </a>
    @endif
        <div>
            <a href="{{url("")}}" class="btn btn-secondary"><i class="fas fa-search"></i> Vyhledávání</a>
            <a href="#" class="btn btn-secondary"><i class="fas fa-book"></i> Zpěvníky</a>
            <a href="#" class="btn btn-secondary"><i class="fas fa-star"></i> Oblíbené</a>
            <a href="{{route("client.about")}}" class="btn btn-secondary"><i class="fas fa-info"></i> O zpěvníku</a>
            <a href="#" class="btn btn-secondary"><i class="fas fa-cog"></i> Nastavení</a>
            <dark-mode-button text=" Tmavý mód"></dark-mode-button>
        </div>
    </div>
</nav>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a href="{{url("")}}" class="btn"><img src="{{asset('img/logo_v2.png')}}" height="20"></a>
        <a href="{{url("")}}" class="btn btn-secondary"><i class="fas fa-search"></i></a>
        <a href="#" class="btn btn-secondary"><i class="fas fa-book"></i></a>
        <a href="#" class="btn btn-secondary"><i class="fas fa-star"></i></a>
        <a href="{{route("client.about")}}" class="btn btn-secondary"><i class="fas fa-info"></i></a>
        <a href="#" class="btn btn-secondary"><i class="fas fa-cog"></i></a>
        <dark-mode-button></dark-mode-button>
    </div>
</nav>