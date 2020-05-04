@extends('panel::layouts.master')

@section('content')
    <h2>Ratings &amp; Reviews</h2>
    @role('admin')
    <ul class="nav nav- mb-4">
        <li class="nav-item {{ active(['panel.addons.ratings.index'])  }}">
            <a class="nav-link pl-0" href="{{route('panel.addons.ratings.index')}}">Settings</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link pl-0" href="{{route('panel.addons.ratings.comments')}}">Comments</a>
        </li>
    </ul>
    @endrole

    <div class="row mb-5 pb-5">

        <div class="col-sm-10">
    @include('alert::bootstrap')
            <br />
            <table class="table table-sm  ">
                <thead class="thead- border-0">
                <tr>
                    <th>Comment</th>
                    <th>Rating</th>
                    <th>Reported by</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($comments as $i => $comment)
                    <tr>
                        <td>{{$comment->comment}}</td>
                        <td>{{$comment->rating}} <i class="fa fa-star-o" aria-hidden="true"></i></td>
                        <td>{{$comment->commenter->display_name}}</td>

                        <td>
                            @if($comment->approved)
                                <a href="{{route('panel.addons.ratings.edit', $comment->id)}}" class="text-muted float-right">Edit/Hide comment</a>
                            @else
                                <a href="{{route('panel.addons.ratings.edit', $comment->id)}}" class="text-muted float-right">Approve comment comment</a>
                            @endif
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

            {{ $comments->links() }}
</div>
</div>
@stop
