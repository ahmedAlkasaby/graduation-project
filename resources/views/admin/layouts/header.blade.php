<header class="main-header">
    <!-- Logo -->
    <a href="https://systemira.com" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">{{ __('Systemira') }}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">{{ __('Systemira') }}</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @php
                    $route_name = getRouteName($route_type ?? 'admin');
                @endphp
                @if ($route_name == 'admin' && auth()->user()->isAbleTo(['notifications.index']))
                    <li class="dropdown messages-menu">
                        <a data-toggle="tooltip" title="{{ __('Notifications') }}" data-placement="bottom"
                            href="{{ route('admin.notifications.index', ['user_id' => auth()->user()->id]) }}">
                            <i class="fa fa-bell"></i>
                            @if ($notification_count > 0)
                                <span class="label label-danger">
                                    @if ($notification_count < 100)
                                        {{ $notification_count }}
                                    @elseif($notification_count >= 100)
                                        +99
                                    @endif
                                </span>
                            @endif
                        </a>
                    </li>
                @endif
                @if ($route_name == 'admin' && auth()->user()->isAbleTo(['contacts.index']))
                    <li class="dropdown messages-menu">
                        <a data-toggle="tooltip" title="{{ __('Contact Us') }}" data-placement="bottom"
                            href="{{ route('admin.contacts.index') }}">
                            <i class="fa fa-envelope"></i>
                            @if ($contact_count > 0)
                                <span class="label label-danger">
                                    @if ($contact_count < 100)
                                        {{ $contact_count }}
                                    @elseif($contact_count >= 100)
                                        +99
                                    @endif
                                </span>
                            @endif
                        </a>
                    </li>
                @endif
                @if ($route_name == 'admin' && auth()->user()->isAbleTo(['orders.index']))
                    <li class="dropdown messages-menu">
                        <a data-toggle="tooltip" title="{{ __('Orders') }}" data-placement="bottom"
                            href="{{ route('admin.orders.index') }}">
                            <i class="fa fa-cart-plus"></i>
                            @if ($order_count > 0)
                                <span class="label label-danger">
                                    @if ($order_count < 100)
                                        {{ $order_count }}
                                    @elseif($order_count >= 100)
                                        +99
                                    @endif
                                </span>
                            @endif
                        </a>
                    </li>
                @endif
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        @if (isset($user_account) && $user_account->image != null)
                            <img src="{{ $user_account->image }}" class="user-image" alt="User Image">
                        @else
                            <img src="{{ asset('css/img/user2-160x160.jpg') }}" class="user-image" alt="User Image">
                        @endif

                        <span class="hidden-xs">
                            @if (isset($user_account))
                                {{ $user_account->name }}
                            @endif
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            @if (isset($user_account) && $user_account->image != null)
                                <img src="{{ $user_account->image }}" class="img-circle" alt="User Image">
                            @else
                                <img src="{{ asset('css/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                            @endif

                            <p>
                                @if (isset($user_account))
                                    {{ $user_account->name }}
                                @endif
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="{{ route('admin.users.edit',$user_account->id) }}"
                                    class="btn btn-default btn-flat">{{ __('Profile') }}</a>
                            </div>
                            <div class="pull-left">
                                <a class="btn btn-default btn-flat"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Sign out') }}</a>
                                <form id="logout-form" action="{{ route($route_name . '.logout') }}" method="POST"
                                    style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
