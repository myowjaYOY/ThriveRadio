<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex ">
                <div class="logo">
                    <a href="{{ url('home') }}">
                        <img src="{{ env('LOGO') ? url(Storage::url(env('LOGO'))) : url('assets/images/logo/logo.png') }}" alt="" class="h-auto w-px-40"
                            style="width:222px!important">

                    </a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">

                <li class="sidebar-item">
                    <a href="{{ route('home') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span class="menu-item">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                @if($city_mode == 1)
                    <li class="sidebar-item">
                        <a href="{{route('city.index')}}" class='sidebar-link'>
                            <i class="fas fa-building"></i>
                            <span class="menu-item">{{ __('City') }}</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-item">
                    <a href="{{ route('category.index')}}" class='sidebar-link'>
                        <i class="fas fa-align-justify"></i>
                        <span class="menu-item">{{ __('Category') }}</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('radio-station.index')}}" class='sidebar-link'>
                        <i class="fas fa-microphone"></i>
                        <span class="menu-item">{{ __('Radio-Station') }}</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('slider.index')}}" class='sidebar-link'>
                        <i class="fas fa-sliders-h"></i>
                        <span class="menu-item">{{ __('Sliders') }}</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('notification.index')}}" class='sidebar-link'>
                        <i class="bi bi-bell"></i>
                        <span class="menu-item">{{ __('Notification') }}</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('radio-station-report.index')}}" class='sidebar-link'>
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span class="menu-item">{{ __('User Reported Station') }}</span>
                    </a>
                </li>
                 <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-gear"></i>
                        <span class="menu-item">{{ __('Settings') }}</span>
                        <i class="fa fa-angle-down menu-arrow ml-4"></i>
                    </a>
                    <ul class="submenu" style="padding-left: 0rem">

                        <li class="submenu-item">
                            <a href="{{ route('about_us') }}">{{ __('About Us') }}</a>
                        </li>


                        <li class="submenu-item">
                            <a href="{{ route('privacy_policy') }}">{{ __('Privacy Policy') }}</a>
                        </li>


                        <li class="submenu-item">
                            <a href="{{ route('terms_condition') }}">{{ __('Terms & Condition') }}</a>
                        </li>


                        <li class="submenu-item">
                            <a href="{{ route('system_settings') }}">{{ __('System Settings') }}</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('system-update.index')}}" class='sidebar-link'>
                        <i class="bi bi-cloud-download"></i>
                        <span class="menu-item">{{ __('System Update') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
