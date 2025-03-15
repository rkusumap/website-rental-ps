<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="nav-link">
                <div class="profile-image">
                    <img src="{{ show_image('images/user_thub/profile-user.png') }}" alt="image" />
                </div>
                <div class="profile-name">
                    <p class="name">
                        {{Auth::user()->name}}
                    </p>
                    <p class="designation">
                        {{Auth::user()->role->name_level}}
                    </p>
                </div>
            </div>
        </li>
        @foreach ($moduleAppServiceProvider as $module)
            @php
                //role config
                $role_access = isAccess('read',$module->id_module,auth()->user()->level_user);
                if(!$role_access){continue;}
            @endphp
            @if (count($module->modules) > 0)
            {{-- menu jika punya child --}}
            <li class="nav-item">
                <a class="nav-link " data-toggle="collapse" href="#{{$module->code_module}}" aria-expanded="false"
                aria-controls="sidebar-layouts">
                    <i class="{{$module->icon_module}} menu-icon"></i>
                    <span class="menu-title">{{$module->name_module}}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse " id="{{$module->code_module}}">
                    <ul class="nav flex-column sub-menu">

                        @foreach ($module->modules as $chmod)
                        @php
                            //role config
                            $role_access = isAccess('read',$chmod->id_module,auth()->user()->level_user);
                            if(!$role_access){continue;}
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link" href="{{url($chmod->link_module)}}">{{$chmod->name_module}}</a>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </li>
            {{-- end menu jika punya child --}}
            @endif

            @if (count($module->modules) == 0)
            {{-- menu jika tidak punya child --}}
            <li class="nav-item">
                <a class="nav-link" href="{{url($module->link_module)}}">
                    <i class="{{$module->icon_module}} menu-icon"></i>
                    <span class="menu-title" style="margin-left:-5px">{{$module->name_module}}</span>
                </a>
            </li>
            {{-- end menu jika tidak punya child --}}
            @endif
        @endforeach
    </ul>
</nav>
