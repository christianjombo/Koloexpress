@extends('panel::layouts.master')

@section('content')
    <h2>Sitemap settings</h2>


    <div class="row mb-5 pb-5">

        <div class="col-sm-10">
            @include('alert::bootstrap')
            <br />
            <h4>Your XML sitemap</h4>
            <p>You can find your XML sitemap here <a href="/sitemap.xml" target="_blank">XML Sitemap</a></p>
            <p class="font-italic">Note: You do not need to generate your sitemap</p>
            <br />

            {!! Form::open(['route' => 'panel.addons.sitemap.store']) !!}

            <h4>Entries per sitemap</h4>
            <p>Please enter the number of entries per sitemap. (defaults to 100)</p>
            {{  Form::number('sitemap_entries', setting('sitemap_entries', 100), ['class' => 'form-control']) }}
<br />
            <h4>Caching</h4>
            <p>Please enter the number minutes to cache the sitemap for. (defaults to 60)</p>
            {{  Form::number('sitemap_cache', setting('sitemap_cache', 60), ['class' => 'form-control']) }}
            <br />            
			
            <button type="submit" class="btn btn-primary">Save settings</button>

            {!! Form::close() !!}
        </div>
    </div>
@stop
