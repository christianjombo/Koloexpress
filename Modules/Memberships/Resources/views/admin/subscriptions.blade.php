@extends('panel::layouts.master')

@section('content')
    <a href="{{ route('panel.addons.memberships.index') }}" class="mb-1"><i class="fa fa-angle-left" aria-hidden="true"></i> back</a>

    <h2>Membership Subscriptions</h2>
    <br />
    @include("memberships::admin.menu")
    @include('alert::bootstrap')

    <table class="table table-sm ">
        <thead class=" ">
        <tr>
            <th scope="col"  class=""></th>
            <th scope="col"  >Title</th>
            <th scope="col"  >User</th>
            <th scope="col"  >Period</th>
            <th scope="col"  >Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($subscriptions as $item)
            <tr>
                <th scope="row">{{$item->id}}</th>
                <td>{{str_limit($item->membership_plan->name, 40)}}</td>
                <td>{{$item->user->email}}</td>
                <td>{{$item->period}}</td>
                <td>{{$item->created_at}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

    {{ $subscriptions->appends(app('request')->except('page'))->links() }}

@stop
