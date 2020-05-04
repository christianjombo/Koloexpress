@extends('panel::layouts.master')


@section('content')

    <h2>Membership Plans <a href="{{route('panel.addons.memberships.create')}}" class="btn btn-link btn-xs"><i class="mdi mdi-plus"></i> Add new plan</a></h2>

    <br />
    @include('alert::bootstrap')

    @if(count($plans))
        <table class="table table-sm  ">
            <thead class="thead- border-0">
            <tr>
                <th>Plan name</th>
                <th class="text-muted">Listings</th>
                <th class="text-muted">Images</th>
                <th class="text-muted">Featured</th>
                <th class="text-muted">Messages</th>
                <th class="text-muted">Monthly price</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($plans as $i => $item)
                <tr>
                    <td><a href="{{route('panel.addons.memberships.edit', $item->id)}}">{{$item->name}}</a></td>
                    <td class="text-muted">{{$item->getFeatureByCode('listings')['value']}}</td>
                    <td class="text-muted">{{$item->getFeatureByCode('images')['value']}}</td>
                    <td class="text-muted">{{$item->getFeatureByCode('featured_listings')['value']}}</td>
                    <td class="text-muted">{{$item->getFeatureByCode('messages')['value']}}</td>
                    <td class="text-muted">{{$item->price}}</td>

                    <td>
                        <a href="#" ic-target="#main" ic-select-from-response="#main" ic-delete-from="{{ route('panel.addons.memberships.destroy', $item) }}" ic-confirm="Are you sure?" class="text-muted float-right ml-2"><i class="fa fa-remove"></i></a>
                        <a href="{{route('panel.addons.memberships.edit', $item)}}" class="text-muted float-right"><i class="fa fa-pencil"></i></a>

                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @else
        <div class="alert alert-danger" role="alert">
            No membership plans have been set-up yet. <a href="{{route('panel.addons.memberships.create')}}" class="alert-link">(Add a plan)</a>
        </div>
    @endif


@stop
