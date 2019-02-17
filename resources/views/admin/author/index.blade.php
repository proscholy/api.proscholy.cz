@extends('admin.layout')

@section('content')
    <div class="content-padding">
        <h2>Seznam autorů (seřazeno od nejnověji přidaných)</h2>
        <a class="btn btn-outline-primary" href="{{route('admin.author.create')}}">+ Nový autor</a>
        <div class="row">
            <div class="col-sm-6">
                <table class="table table-bordered">
                    @foreach($authors as $author)
                    <tr>
                        <td><a href="{{route('admin.author.edit',['id'=>$author->id])}}">{{$author->name}}</a></td>
                        </tr>
                    @endforeach
                </table>

                {!! $authors->links() !!}
            </div>
        </div>
    </div>
@endsection