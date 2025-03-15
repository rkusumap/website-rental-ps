

<nav class="bg-light navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row default-layout-navbar">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
    <a class="navbar-brand brand-logo" href="{{url('')}}"><img style="height: 50px;width:auto"  src="{{asset('static/'.$option['logo'])}}" alt="logo"/></a>
    <a class="navbar-brand brand-logo-mini" href="{{url('')}}"><img src="{{asset('static/'.$option['logo'])}}" alt="logo"/></a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-stretch">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="ti-align-left"></span>
    </button>
    <ul class="navbar-nav navbar-collapse navbar-select2">
      <div class="col-md-12 col-xs-6 mt-2" >
        <div class="form-group">
          <div class="input-group">
            <select class="smt-select2 w-100" id="smt_navigation">
              @foreach ($moduleAppServiceProvider as $module)
                @php
                    //role config
                    $role_access = isAccess('read',$module->id_module,auth()->user()->level_user);
                    if(!$role_access){continue;}
                @endphp
                @if ($module->modules->count() > 0)
                  <optgroup label="{{$module->name_module}}">
                    <option value="{{$module->link_module}}">Dashboard {{$module->name_module}}</option>
                    @foreach ($module->modules as $submodule)
                      @php
                          //role config
                          $role_access = isAccess('read',$submodule->id_module,auth()->user()->level_user);
                          if(!$role_access){continue;}
                      @endphp
                      <option value="{{url($submodule->link_module)}}" {{ isSelected($submodule->link_module,request()->segment(1)) }}>{{$submodule->name_module}}</option>
                    @endforeach
                  </optgroup>
                @else
                <option value="{{url($module->link_module)}}" {{ isSelected($module->link_module,request()->segment(1)) }}>{{$module->name_module}}</option>
                @endif
              @endforeach
            </select>
          </div>
      </div>
    </ul>
    <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item nav-profile dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
            <img src="{{ show_image('images/user_thub/profile-user.png') }}" alt="image" />
            <span class="respon-android">{{Auth::user()->name}}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
            <a href="/biodata" class="dropdown-item">
              <i class="fa fa-user text-primary"></i>
              Profile & Account
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                class="dropdown-item">
                <i class="fas fa-power-off text-primary"></i>
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
        <span class="ti-align-right"></span>
      </button>
    </div>
  </nav>

