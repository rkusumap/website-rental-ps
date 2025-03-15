<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title>{{$option['name']}}</title>
        <meta name="author" content="My Website">
        <meta name="copyright" content="My Website" />
        <meta name="description" content="{{ $option['description'] }}">
        <link rel="icon" type="image/png" href="{{asset('static/'.$option['logo'])}}">

        {{--  Css Wajib  --}}
        <link rel="stylesheet" href="{{asset('vendors/iconfonts/font-awesome/css/all.min.css')}}">
        <link rel="stylesheet" href="{{asset('vendors/iconfonts/flag-icon-css/css/flag-icon.min.css')}}" />
        <link rel="stylesheet" href="{{asset('vendors/iconfonts/ti-icons/css/themify-icons.css')}}">
        <link rel="stylesheet" href="{{asset('vendors/iconfonts/simple-line-icon/css/simple-line-icons.css')}}">
        <link rel="stylesheet" href="{{asset('css/result_combine.css')}}">

        {{--  JS Wajib  --}}
        <script defer src="{{ asset('js/result_combine.js') }}"></script>
        <script src="{{asset('vendors/js/vendor.bundle.base.js')}}"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        <style>
            .ih250 {
                height: 250px !important;
            }
        </style>

        @yield('css')
    </head>
    <body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth" style="margin-top:0px !important">
                <div class="row w-100">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo">

                            </div>
                            <h2>Welcome back!</h2>
                            <h5 class="font-weight-light">Happy to see you again!</h5>
                            <div class="pt-3" autocomplete="off">
                                <form action="" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="exampleInputUsername">Username</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fa fa-user color-icon-login"></i>
                                            </span>
                                        </div>
                                        <input type="text" value="{{ old('username') }}" class="form-control form-control-lg border-left-0" placeholder="Enter username" required autocomplete="username" name="username" id="username">
                                        @error('username')
                                            <span class="invalid-feedback" role="alert"></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputPassword">Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fa fa-lock color-icon-login"></i>
                                            </span>
                                        </div>
                                        <input type="password" class="form-control form-control-lg border-left-0" name="password" id="password" placeholder="Password" required autocomplete="current-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert"></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="my-3 text-center">
                                    <button type="submit" class="btn btn-block btn-color-smt btn-lg font-weight-medium auth-form-btn btn-login">Login</button>
                                    {{-- <a href="#" class="d-block mt-3 btn-lupa-password">Lupa Password</a> --}}
                                    <a href="#" class="d-block mt-3 btn-register-form">Register</a>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="forgetpass" tabindex="-1" role="dialog" aria-labelledby="forgetpass" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgetpasslabel">Form Lupa Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Masukan Email" name="email" id="email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary btn-simpan-password">Kirim</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="registerform" tabindex="-1" role="dialog" aria-labelledby="registerform" aria-hidden="true">
            <div class="modal-dialog" role="document" style="margin-top:50px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerformlabel">Register</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Nama</label>
                            <input type="text" class="form-control" placeholder="Masukan Nama" name="name_register" id="name_register">
                        </div>

                        <div class="form-group">
                            <label for="">No Hp</label>
                            <input type="text" class="form-control" placeholder="Masukan No Hp" name="nohp_register" id="nohp_register">
                        </div>

                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="text" class="form-control" placeholder="Masukan Email" name="email_register" id="email_register">
                        </div>



                        <div class="form-group">
                            <label for="">Username</label>
                            <input type="text" class="form-control" placeholder="Masukan Username" name="username_register" id="username_register">
                        </div>



                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="password" class="form-control" placeholder="Masukan Password" name="password_register" id="password_register">
                        </div>



                        <div class="form-group">
                            <label for="">Konfirmasi Password</label>
                            <input type="password" class="form-control" placeholder="Konfirmasi Password" name="confirm_password_register" id="confirm_password_register">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary btn-register">Register</button>
                    </div>
                </div>
            </div>
        </div>


    </body>
  </html>
  <script>
    $(function () {
        $('.btn-lupa-password').on('click',function () {
            $('#forgetpass').modal('show');
        })

        $('.btn-register-form').on('click',function () {
            $('#registerform').modal('show');
        })

        $('.btn-register').on('click',function (e) {
            $(this).attr('disabled',true);
            e.preventDefault();
            if (
                $('#name_register').val().length==0
                || $('#username_register').val().length==0
                || $('#email_register').val().length==0
                || $('#nohp_register').val().length==0
                || $('#password_register').val().length==0
                || $('#confirm_password_register').val().length==0
            ) {
                swal("Error!", 'Lengkapi Data', "error");
            } else {
                if ($('#password_register').val() != $('#confirm_password_register').val()) {
                    swal("Error!", 'Password Tidak Sama', "error");
                }
                else{
                    $.ajax({
                        url:"{{ route('register') }}",
                        type:"post",
                        data:{
                        _token :  $('input[name="_token"]').val(),
                        username : $('#username_register').val(),
                        password: $('#password_register').val(),
                        name : $('#name_register').val(),
                        email : $('#email_register').val(),
                        nohp : $('#nohp_register').val(),

                        },
                        dataType: "json",
                        success:function(response){
                            console.log(response);

                            $('.btn-register').attr('disabled',false);
                            if (response.status==true) {
                                    swal({title: "Berhasil!", text: "Berhasil Melakukan Registrasi", icon: "success"})
                                            .then(function(){
                                                document.location='{{url('home')}}';
                                        });
                            } else {
                                var pesan = "";
                                jQuery.each(response.pesan,function (key,value) {
                                    pesan +=value+'. ';
                                });
                                swal("Error!", pesan, "error");
                            }
                        }
                    })
                }
            }
        });


        $('.btn-login').on('click',function (e) {
            $(this).attr('disabled',true);
            e.preventDefault();
            if ($('#username').val().length==0 || $('#password').val().length==0) {
                swal("Error!", 'username atau password tidak boleh kosong', "error");
            } else {
                $.ajax({
                    url:"{{ route('login') }}",
                    type:"post",
                    data:{
                    _token :  $('input[name="_token"]').val(),
                    username : $('#username').val(),
                    password: $('#password').val(),

                    },
                    dataType: "json",
                    success:function(response){
                        $('.btn-login').attr('disabled',false);
                        if (response.status==true) {
                                swal({title: "Berhasil!", text: "Berhasil Melakukan Login", icon: "success"})
                                        .then(function(){
                                            document.location='{{url('home')}}';
                                    });
                        } else {
                            swal("Error!", response.pesan, "error");
                        }
                    }
                })
            }
        });
    })
  </script>
