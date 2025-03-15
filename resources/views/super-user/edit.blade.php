@extends('admin.layout')

@section('css')

@endsection

@section('page-title')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="page-header">
                <h3 class="page-title">
                    Super Edit User
                </h3>
                <nav>
                    <a href="/super-user">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                    </a>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('super-user.update',[$get_data->id])}}" method="POST" class="form-sample" id="form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" name="email" class="form-control"
                                        placeholder="Contoh : testing@mail.com"
                                        value="{{$get_data->email}}"
                                        >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="username" class="form-control"
                                        placeholder="Contoh : admin"
                                        value="{{$get_data->username}}"
                                        >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name" class="form-control"
                                        placeholder="Contoh : Raffi"
                                        value="{{$get_data->name}}"
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="password" class="form-control">
                                        <label class="text-danger">*Kosongkan apabila tidak ingin ganti password</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Konfirmasi Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="password-confirmation" class="form-control">
                                        <label class="text-danger">*Kosongkan apabila tidak ingin ganti password</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Akses</label>
                                    <div class="col-sm-10">
                                        <select name="level_user" id="level_user" class="form-control select2">
                                            <option value="">-Pilih Akses-</option>
                                            @foreach($dataLevel as $level)
                                                <option value="{{$level->id_level}}|{{$level->id_company}}"
                                                    {{isSelected($level->id_level,$get_data->level_user)}}
                                                    >{{$level->name_level}} - {{$level->name_company}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <button type="submit" class="btn btn-simpan btn-primary mr-2">Simpan</button>
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
        $('.select2').select2()
        $('.btn-simpan').on('click',function () {
            $('#form').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Menyimpan Data", icon: "success"})
                                .then(function(){
                                    document.location='/super-user';
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
