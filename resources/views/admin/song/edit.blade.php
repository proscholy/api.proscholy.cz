@extends('admin.layout')

@section('content')
    <div class="content-padding">
        <h2>Úprava písně</h2>

        <div class="row">
            <div class="col-sm-6">
                <form action="{{ route('admin.song.update', ['song_lyric' => $song_lyric->id]) }}" method="post">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="id" value="{{$song_lyric->id}}">

                    <label>Název</label>
                    <input class="form-control" required autofocus name="name" placeholder="Název písně" value="{{$song_lyric->name}}"><br>

                    @if ($assigned_song_disabled)
                        <p>Píseň je označena jako originál následujících písní: </p>
                        @foreach ($song_lyric->getSiblings()->get() as $item)
                            {{ $item->name }}<br/>
                        @endforeach
                        <br/>
                    @else
                    <label>Jedná se o překlad následující písně:</label>
                        @include('admin.components.magicsuggest', [
                            'field_name' => 'assigned_song_lyrics',
                            'value_field' => 'id',
                            'display_field' => 'name',
                            'list_all' => $all_song_lyrics,
                            'list_selected' => $assigned_song_lyrics,
                            'is_single' => true,
                            'disabled' => $assigned_song_disabled
                        ])
                    @endif

                    <label>Autoři</label><br>

                    @include('admin.components.magicsuggest', [
                        'field_name' => 'authors',
                        'value_field' => 'id',
                        'display_field' => 'name',
                        'list_all' => $all_authors,
                        'list_selected' => $assigned_authors,
                        'is_single' => false,
                        'disabled' => false
                    ])

                    <br><br>

                    <label>Text</label>
                    <textarea rows="20" name="lyrics" class="form-control" title="">{{$song_lyric->lyrics}}</textarea>

                    <br>
                    <label>Typ</label>
                    <select class="form-control" name="is_original" title="">
                        <option value="1" @if($song_lyric->is_original)
                        selected
                                @endif>Originál
                        </option>
                        <option value="0" @if(!$song_lyric->is_original)
                        selected
                                @endif>Překlad
                        </option>
                    </select>

                    <br>
                    <label>Autorizovaný překlad</label>
                    <select class="form-control" name="is_authorized" title="">
                        <option value="1" @if($song_lyric->is_authorized)
                        selected
                                @endif>Ano
                        </option>
                        <option value="0" @if(!$song_lyric->is_authorized)
                        selected
                                @endif>Ne
                        </option>
                    </select>

                    <br>

                    {{-- <input class="btn btn-outline-primary" type="submit" value="create"> --}}

                    <button type="submit" class="btn btn-outline-primary" name="redirect" value="save">Uložit</button>
                    <button type="submit" class="btn btn-outline-primary" name="redirect" value="save_show">Uložit a zobrazit ve zpěvníku</button>
                    <button type="submit" class="btn btn-outline-primary" name="redirect" value="add_external">Uložit a přidat externí odkaz</button>
                </form>

                @include('admin.components.deletebutton', [
                    'url' => route('admin.song.delete', ['song_lyric' => $song_lyric->id]),
                    'class' => 'btn btn-outline-warning',
                    'redirect' => route('admin.song.index')
                ])
            </div>
        </div>
    </div>
@endsection

@include('admin.components.magicsuggest_includes')
@include('admin.components.deletebutton_includes')