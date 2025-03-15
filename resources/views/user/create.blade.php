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
                    Tambah User
                </h3>
                <nav>
                    <a href="/user">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                    </a>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('user.store')}}" method="POST" class="form-sample" id="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Customer</label>
                                    <div class="col-sm-10">
                                        <select name="customer_user" id="customer_user" class="select2">
                                            <option value="">Bukan Customer</option>
                                            @foreach ($dataCustomer as $customer)
                                                <option value="{{$customer->id_customer}}" data-name="{{$customer->name_customer}}">{{$customer->name_customer}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" name="email" class="form-control"
                                        placeholder="Contoh : testing@mail.com">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="username" class="form-control"
                                        placeholder="Contoh : admin">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Contoh : Raffi">
                                    </div>
                                </div>



                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="password" class="form-control"
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Konfirmasi Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="password-confirmation" class="form-control"
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Akses</label>
                                    <div class="col-sm-10">
                                        <select name="level_user" id="level_user" class="form-control">
                                            <option value="">-Pilih Akses-</option>
                                            @foreach($dataLevel as $level)
                                                <option value="{{$level->id_level}}">{{$level->name_level}}</option>
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
        $('.select2').select2();
        $(document).on('change','#customer_user',function () {
            $('#name').val($(this).find(':selected').attr('data-name'));
        });
        $('.btn-simpan').on('click',function () {
            $('#form').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Menyimpan Data", icon: "success"})
                                .then(function(){
                                    document.location='/user';
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
