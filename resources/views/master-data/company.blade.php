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
                    Data Perusahaan
                </h3>
                <nav>

                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('company.update',[$get_data->id_company])}}" method="POST" class="form-sample" id="form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Perusahaan</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name_company" class="form-control"
                                        placeholder="Contoh : Toko A"
                                        value="{{$get_data->name_company}}"
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Phone</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="phone_company" class="form-control"
                                        value="{{$get_data->phone_company}}"
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Alamat</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="address_company" class="form-control"
                                        value="{{$get_data->address_company}}"
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Deskripsi</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="description_company" class="form-control"
                                        value="{{$get_data->description_company}}"
                                        >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Logo Website</label>
                                        <div class="col-sm-10">
                                        @php
                                            if (!empty($get_data->logo_company)) {
                                                $url_logo = asset('static/'.$get_data->logo_company);
                                            } else {
                                                $url_logo = null;
                                            }
                                        @endphp
                                        <input type="file"  name="logo" id="logo" class="dropify" data-default-file="{{ $url_logo }}"/>
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
                                    document.location='/company';
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
