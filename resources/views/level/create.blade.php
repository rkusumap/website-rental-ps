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
                    Tambah Level
                </h3>
                <nav>
                    <a href="/level">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                    </a>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('level.store')}}" method="POST" class="form-sample" id="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Level</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name_level" class="form-control"
                                        placeholder="Contoh : Admin, USER">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Kode Level</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="code_level" class="form-control"
                                        placeholder="Contoh : ADM, USR">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <p class="text-center">Pengaturan Menu</p>
                            </div>
                            <div class="col-md-12">
                                @foreach ($dataModule as $module)
                                    <div class="form-group row">
                                        <label class="col-sm-12 pb-0 col-form-label font-weight-bold">{{$module->name_module}} <span class="font-weight-normal">({{$module->action_module}})</span> </label>
                                        <div class="col-sm-10">
                                            <input type="text" value="" name="action_gmd[{{$module->id_module}}]" class="form-control smt-tags" aria-describedby="action_gmd">
                                        </div>
                                    </div>
                                    @if ($module->modules->count() > 0)
                                        @foreach ($module->modules as $mod)

                                            <div class="form-group row ml-3">
                                                <label class="col-sm-12 pb-0 col-form-label font-weight-bold">{{$mod->name_module}} <span class="font-weight-normal">({{$mod->action_module}})</span></label>
                                                <div class="col-sm-10">
                                                    <input type="text" value="" name="action_gmd[{{$mod->id_module}}]" class="form-control smt-tags" aria-describedby="action_gmd">
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
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
        $('.smt-tags').tagsInput({
            'width': '100%',
            // 'height': '75%',
            'interactive': true,
            'defaultText': 'Enter',
            'removeWithBackspace': true,
            'placeholderColor': '#666666'
        });

        $('.btn-simpan').on('click',function () {
            $('#form').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Menyimpan Data", icon: "success"})
                                .then(function(){
                                    document.location='/level';
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
