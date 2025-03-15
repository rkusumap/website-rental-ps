<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card card-menu">
            <div class="">
                <div class="d-md-flex justify-content-between align-items-center">
                    <nav class="navbar navbar-expand-lg navbar-light ">
                        <button class="navbar-toggler navbar-toggler-right border-0" type="button" data-toggle="collapse" data-target="#navbar4">
                            <span class="ti-align-left font-white"></span>
                        </button>

                        <div class="container">
                            <div class="collapse navbar-collapse" id="navbar4">
                                <ul class="navbar-nav mr-auto">
                                    @foreach ($moduleAppServiceProvider->where('code_module','MST') as $module)
                                        @if (count($module->modules) > 0)
                                        @foreach ($module->modules as $submodule)
                                        @php
                                            if (!isAccess('read', $submodule->id_module, auth()->user()->level_user)) {
                                                continue;
                                            }
                                        @endphp
                                            <li class="jarak-menu">
                                                <span class="font-menu-icon" style="font-size:14px;">
                                                    <i class="fa fa-circle "></i> <a href="{{url($submodule->link_module)}}" class="font-white">{{$submodule->name_module}}</a>
                                                </span>
                                            </li>
                                        @endforeach
                                        @endif
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
