@extends('panel::layouts.master')


@section('content')
    <script src="https://cdn.jsdelivr.net/npm/jquery.repeater@1.2.1/jquery.repeater.min.js"></script>
    <h2>Moderate listings</h2>
    @include('alert::bootstrap')
    <ul class="nav nav- mb-4">
        <li class="nav-item {{ active(['panel.addons.moderatelistings.index'])  }}">
            <a class="nav-link pl-0" href="{{route('panel.addons.moderatelistings.index')}}">Flagged listings</a>
        </li>
        <li class="nav-item {{ active(['panel.addons.moderatelistings.report-types.index'])  }}">
            <a class="nav-link pl-0" href="{{route('panel.addons.moderatelistings.report-types.index')}}">Report types</a>
        </li>
    </ul>

    <p>Enter the different types of issues a user can report (e.g. spam, inappropriate, duplicate).</p>
    <form class="repeater" method="post" action="{{route('panel.addons.moderatelistings.report-types.store')}}">
        @csrf
        <!--
            The value given to the data-repeater-list attribute will be used as the
            base of rewritten name attributes.  In this example, the first
            data-repeater-item's name attribute would become group-a[0][text-input],
            and the second data-repeater-item would become group-a[1][text-input]
        -->
        <div data-repeater-list="report_name">

            @if(!$report_types)
            <div class="row mb-3" data-repeater-item>
                <div class="col">
                    <input type="text" name="value" class="form-control" value=""/>
                </div>
                <div class="col">
                    <input data-repeater-delete type="button" class="btn btn-default" value="Delete"/>
                </div>
            </div>
            @endif

            @if($report_types)
                @foreach($report_types as $report_type)
                    <div class="row mb-3" data-repeater-item>
                        <div class="col">
                            <input type="text" name="value" class="form-control" value="{{$report_type['value']}}"/>
                        </div>
                        <div class="col">
                            <input data-repeater-delete type="button" class="btn btn-default" value="Delete"/>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
        <input data-repeater-create type="button" class="btn btn-outline-primary" value="+ Add"/>
<br />
<br />
        <button type="submit" class="btn btn-primary" >Save form</button>
    </form>


    <script>
        $(document).ready(function () {
            $('.repeater').repeater({
                // (Optional)
                // start with an empty list of repeaters. Set your first (and only)
                // "data-repeater-item" with style="display:none;" and pass the
                // following configuration flag
                initEmpty: false,
                // (Optional)
                // "show" is called just after an item is added.  The item is hidden
                // at this point.  If a show callback is not given the item will
                // have $(this).show() called on it.
                show: function () {
                    $(this).show();
                },
                // (Optional)
                // "hide" is called when a user clicks on a data-repeater-delete
                // element.  The item is still visible.  "hide" is passed a function
                // as its first argument which will properly remove the item.
                // "hide" allows for a confirmation step, to send a delete request
                // to the server, etc.  If a hide callback is not given the item
                // will be deleted.
                hide: function (deleteElement) {
                    if(confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                // (Optional)
                // Removes the delete button from the first list item,
                // defaults to false.
                isFirstItemUndeletable: true
            })
        });
    </script>

@stop
