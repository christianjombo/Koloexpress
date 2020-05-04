@extends('panel::layouts.master')


@section('content')
    <a href="{{ route('panel.addons.memberships.index') }}" class="mb-1"><i class="fa fa-angle-left" aria-hidden="true"></i> back</a>

    @if(!$form->getModel())
        <h2  class="mt-xxs">Creating membership plan</h2>
    @else
        <h2  class="mt-xxs">Editing membership plan</h2>
    @endif
    <br />
    @include('alert::bootstrap')

    {!! form($form) !!}

@stop
