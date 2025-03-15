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
                    Pengaturan Informasi Website
                </h3>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('option.store')}}" method="POST" class="form-sample" id="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Website</label>
                                    <div class="col-sm-10">
                                        <input type="name" name="name" class="form-control" value="{{ $get_data->name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Deskripsi Website</label>
                                    <div class="col-sm-10">
                                        <textarea name="description" class="form-control" id="description" cols="30" rows="5">{{ $get_data->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Logo Admin</label>
                                    <div class="col-sm-10">
                                        @php
                                            if (!empty($get_data->logo)) {
                                                $url_logo = asset('static/'.$get_data->logo);
                                            } else {
                                                $url_logo = null;
                                            }
                                        @endphp
                                        <input type="file"  name="logo" id="logo" class="dropify" data-default-file="{{ $url_logo }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (isAccess('create', $get_module, auth()->user()->level_user))
                        <div class="col-md-12 row">
                            <button type="submit" class="btn btn-simpan btn-primary mr-2">Simpan</button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $(function () {
        $('.dropify').dropify();
        $('.js-example-basic-single').select2();

        $('.btn-simpan').on('click',function () {
            $('#form').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Menyimpan Data", icon: "success"})
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
