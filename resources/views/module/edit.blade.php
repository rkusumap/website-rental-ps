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
                Edit Module
            </h3>
            <nav>
                <a href="/module">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                </a>
            </nav>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{route('module.update',[$data->id_module])}}" method="POST" class="form-sample" id="form">
                    @csrf
                    <input type="hidden" value="PUT" name="_method">
                    <div class="row">
                        <div class="col-md-12">
                          <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Parent Module</label>
                              <div class="col-sm-10">
                                <select name="induk_module" id="induk_module" class="form-control smt-select2">
                                    <option value="">parent</option>
                                    @foreach ($modules as $mdl)
                                        <option value="{{$mdl->id_module}}" {{($data->induk_module == $mdl->id_module) ? "selected" : ""}}>{{$mdl->name_module}}</option>
                                    @endforeach
                                </select>
                              </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kode Module</label>
                                <div class="col-sm-10">
                                    <input type="text" name="code_module" class="form-control" aria-describedby="code_module" value="{{$data->code_module}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nama Module</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name_module" class="form-control" aria-describedby="name_module" value="{{$data->name_module}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Link Module</label>
                                <div class="col-sm-10">
                                    <input type="text" name="link_module" class="form-control" aria-describedby="name_module" value="{{$data->link_module}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Icon Module</label>
                                <div class="col-sm-10">
                                    <input type="text" name="icon_module" class="form-control" aria-describedby="icon_module" value="{{$data->icon_module}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Urutan Module</label>
                                <div class="col-sm-10">
                                    <input type="text" name="order_module" class="form-control" aria-describedby="order_module" value="{{$data->order_module}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Aksi Module</label>
                                <div class="col-sm-10">
                                    <input type="text" name="action_module" class="form-control smt-tags" aria-describedby="action_module" value="{{$data->action_module}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Deskripsi Module</label>
                                <div class="col-sm-10">
                                    <textarea name="description_module" id="description_module" cols="30" rows="10" class="form-control">{{$data->description_module}}</textarea>
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
        $('.btn-simpan').on('click',function () {
            $('#form').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Menyimpan Data", icon: "success"})
                                .then(function(){
                                    document.location='/module';
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
        $('.smt-tags').tagsInput({
            'width': '100%',
            // 'height': '75%',
            'interactive': true,
            'defaultText': 'gunakan koma',
            'removeWithBackspace': true,
            'placeholderColor': '#666666'
        });
    })
</script>
@endsection
