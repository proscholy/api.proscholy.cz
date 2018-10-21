@extends('layout')

@section('content')
    <h2 style="margin-bottom: 5px;">{{$song_l->name}}</h2>

    @if($song_l->authors()->count() == 0)
        <i>Neznámý autor</i>
    @elseif($song_l->authors()->count() == 1)
        Autor písně: <a
                href="{{route('author.single', ['id'=> $authors->first()->id])}}">{{$authors->first()->name}}</a>
    @else
        Autoři písně:<br>
        @foreach($song_l->authors as $author)
            <a href="{{route('author.single', ['id'=> $author->id])}}">{{$author->name}}</a><br>
        @endforeach
    @endif

    <br>
    <div id="lyrics">
    </div>

    @if($song_l->videos()->count() != 0)
        <h4>Videa</h4>

        <div class="row">
            @foreach($song_l->videos as $video)
                <div class="col-sm-4">
                    {!! $video->getHtml() !!}
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
    <script
            src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>

    @include('scripts.chordpro_parse')

    <script>
        $(document).ready(function () {
            let lyrics = document.getElementById('lyrics');
            lyrics.innerHTML = parseChordPro('{{$song_l->lyrics}}', 0);
        });
    </script>


@endsection