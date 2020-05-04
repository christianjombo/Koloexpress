
<ul class="nav nav- mb-4">
    <li class="nav-item {{ active(['panel.addons.memberships.index'])  }}">
        <a class="nav-link pl-0" href="{{route('panel.addons.memberships.index')}}">Plans</a>
    </li>
    <li class="nav-item {{ active(['panel.addons.memberships.subscriptions'])  }}">
        <a class="nav-link" href="{{route('panel.addons.memberships.subscriptions')}}">Subscriptions</a>
    </li>
</ul>