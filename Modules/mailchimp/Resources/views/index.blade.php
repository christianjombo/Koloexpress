@extends('panel::layouts.master')

@section('content')
    <h2>MailChimp settings</h2>

    @role('admin')
    <ul class="nav nav- mb-4">
        <li class="nav-item {{ active(['panel.addons.mailchimp.index'])  }}">
            <a class="nav-link pl-0" href="{{route('panel.addons.mailchimp.index')}}">Settings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link pl-0" @if(!$mailchimp_url)style="cursor:not-allowed" href="#" @else: href="{{ $mailchimp_url }}"  target="_blank"@endif >Subscribers</a>
        </li>
    </ul>
    @endrole

    <div class="row mb-5 pb-5">

        <div class="col-sm-10">
    @include('alert::bootstrap')
            <br />

    {!! form($form)  !!}


</div>
</div>
@stop
