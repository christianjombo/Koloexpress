@extends('panel::layouts.master')


@section('content')

    <h2>Moderate listings</h2>
    @include('alert::bootstrap')
    @role('admin')
    <ul class="nav nav- mb-4">
        <li class="nav-item {{ active(['panel.addons.moderatelistings.index'])  }}">
            <a class="nav-link pl-0" href="{{route('panel.addons.moderatelistings.index')}}">Flagged listings</a>
        </li>
        <li class="nav-item {{ active(['panel.addons.moderatelistings.create'])  }}">
            <a class="nav-link pl-0" href="{{route('panel.addons.moderatelistings.report-types.index')}}">Report types</a>
        </li>
    </ul>
    @endrole

    <p>The following listings were flagged by users and require attention.</p>

    <table class="table table-sm  ">
        <thead class="thead- border-0">
        <tr>
            <th>Reason</th>
            <th>Notes</th>
            <th>Reported by</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($listings as $i => $listing)
            <tr>
                <td>{{$listing->reason}}</td>
                <td>{{$listing->notes}}</td>
                <td>{{$listing->user->display_name}}</td>

                <td>
                    @if($listing->active)
                        <a href="{{route('panel.addons.moderatelistings.edit', $listing->id)}}" class="text-muted float-right">Review <i class="fa fa-chevron-right"></i></a>
                    @else
                        <a href="{{route('panel.addons.moderatelistings.edit', $listing->id)}}" class="text-muted float-right">Solved <i class="text-success fa fa-check"></i></a>
                    @endif
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

    {{ $listings->links() }}



@stop
