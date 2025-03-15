@extends('admin.layout')

@section('css')

@endsection

@section('page-title')

@endsection

@section('content')
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Biodata</h4>
                <p class="card-description"></p>

                <form action="{{route('biodata.update',$get_data->id)}}" method="POST" enctype="multipart/form-data" id="form">
                    @csrf
                    <input type="hidden" value="PUT" name="_method">
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">Name</label>
                        <div class="col-9">
                            <input type="text" name="name" value="{{ $get_data->name }}" class="form-control" aria-describedby="nameHelp"
                                placeholder="Enter name">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">Email</label>
                        <div class="col-9">
                            <input type="email" name="email" value="{{ $get_data->email }}" class="form-control" aria-describedby="emailHelp"
                                placeholder="Enter email">
                            <small class="form-hint"></small>
                        </div>
                    </div>

                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">Username</label>
                        <div class="col-9">
                            <input type="text" name="username" value="{{ $get_data->username }}" class="form-control" aria-describedby="usernameHelp"
                                placeholder="Enter username">
                            <small class="form-hint"></small>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="{{url('/')}}">
                            <button type="button" class="btn btn-default">Kembali</button>
                        </a>
                        <button class="btn btn-primary btn-simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Ganti Kata Sandi</h4>
                <p class="card-description"></p>

                <form action="{{url('biodata-reset-password/'.$get_data->id)}}" method="post" id="form-password">
                    @csrf
                    <div class="form-group mb-3 row">
                        <label for="" class="col-3">Password Baru</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label for="" class="col-3">Password Baru Konfirmasi</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                        </div>
                    </div>
                    <div class="form-footer">
                        <a href="{{url('/')}}">
                            <button type="button" class="btn btn-default">Kembali</button>
                        </a>
                        <button class="btn btn-primary btn-simpan-password">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>

    $(function () {
        $('.btn-simpan').on('click',function () {
            $('#form').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Merubah Biodata", icon: "success"})
                                .then(function(){
                                    location.reload();
                            });
                    } else {
                        var pesan = "";
                        jQuery.each(response.pesan,function (key,value) {
                            pesan +=value+'. ';
                        });
                        swal("Error!", pesan, "error");
                    }
                },
                error: function(){
	                swal("Error!", "Proses Gagal", "error");
	            }
            })
        })
        $('.btn-simpan-password').on('click',function () {
            $('#form-password').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Merubah Password", icon: "success"})
                                .then(function(){
                                    location.reload();
                            });
                    } else {
                        var pesan = "";
                        jQuery.each(response.pesan,function (key,value) {
                            pesan +=value+'. ';
                        });
                        swal("Error!", pesan, "error");
                    }
                },
                error: function(){
	                swal("Error!", "Proses Gagal", "error");
	            }
            })
        })
    })
</script>
@endsection
