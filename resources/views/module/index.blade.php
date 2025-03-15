@extends('admin.layout')

@section('css')

@endsection

@section('page-title')

@endsection

@section('content')
<div class="row justify-content-center">
        <div class="col-12 grid-margin">
            <div class="page-header">
                <h3 class="page-title">
                    Data Module
                </h3>
                <nav aria-label="breadcrumb">
                    @if (isAccess('create', $get_module, auth()->user()->level_user))
                    <a href="{{route('module.create')}}">
                        <button type="button" class="btn btn-light btn-icon-text">
                            <i class="fa fa-plus btn-icon-prepend"></i>
                            Tambah
                        </button>
                    </a>
                    @endif
                </nav>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <ul class="sortable list-unstyled" id="sortable">
                        @foreach ($data as $module)
                        @php
                            $id_module = get_module_id('module');

                            //selalu bisa
                            $detailButton = '<a class="" href="'.route('module.show',$module->id_module).'">Detail</a>';
                            $editButton = "";
                            if (isAccess('update',$id_module,auth()->user()->level_user)){
                                $editButton = '<a href="#" onclick="location.href='."'".route('module.edit',$module->id_module)."'".';" class="">Edit</a>';
                            }
                            $deleteButton = "";
                            if (isAccess('delete',$id_module,auth()->user()->level_user)){
                                $deleteButton = '<a class="btn-delete" href="#hapus" data-id="'.$module->id_module.'" data-nama="'.$module->name_module.'">Hapus</a>';
                            }
                            $action  =  '
                            '.$editButton.'
                            '.$detailButton.'
                            '.$deleteButton.'
                            ';
                        @endphp
                        <li id="mdl-{{$module->id_module}}">
                            <div class="block block-title">
                                <i class="fa fa-sort"></i>
                                {{$module->name_module}}
                                {!!$action!!}
                            </div>

                            <ul class="sortable list-unstyled">
                                @foreach ($module->modules as $submodule)
                                @php
                                    $id_module = get_module_id('module');

                                    //selalu bisa
                                    $detailButton = '<a class="" href="'.route('module.show',$submodule->id_module).'">Detail</a>';
                                    $editButton = "";
                                    if (isAccess('update',$id_module,auth()->user()->level_user)){
                                        $editButton = '<a href="#" onclick="location.href='."'".route('module.edit',$submodule->id_module)."'".';" class="">Edit</a>';
                                    }
                                    $deleteButton = "";
                                    if (isAccess('delete',$id_module,auth()->user()->level_user)){
                                        $deleteButton = '<a class="btn-delete" href="#hapus" data-id="'.$submodule->id_module.'" data-nama="'.$submodule->name_module.'">Hapus</a>';
                                    }
                                    $action  =  '
                                    '.$editButton.'
                                    '.$detailButton.'
                                    '.$deleteButton.'
                                    ';
                                @endphp
                                <li id="mdl-{{$submodule->id_module}}">
                                    <div class="block block-title"><i class="fa fa-sort"></i> {{$submodule->name_module}} {!!$action!!}</div>
                                    <ul class="sortable list-unstyled"></ul>
                                </li>
                                @endforeach
                            </ul><!-- /.menu-sortable -->

                        </li>
                        @endforeach
                    </ul><!-- /.menu-sortable -->

                </div>
            </div>
        </div>
    </div>
  <style>
        .sortable > li > div {
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .sortable, .sortable > li > div {
            display: block;
            width: 100%;
            float: left;
        }

        .sortable > li {
            display: block;
            width: 100%;
            margin-bottom: 5px;
            float: left;
            border: 1px solid #ddd;
            background : #fff;
            padding: 5px;
        }
        .sortable ul {
            padding: 5px;
        }
  </style>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        $('.sortable').sortable({
            connectWith: '.sortable',
            placeholder: 'placeholder',
            // sort: function(e) {
            //     console.log('Handled');
            //     $(".sortable").css("background", "yellow");
            // },
            update: function(event, ui) {
                var struct = [];
                var i = 0;
                $(".sortable").each(function(ind, el) {
                    struct[ind] = {
                    index: i,
                    class: $(el).attr("class"),
                    count: $(el).children().length,
                    parent: $(el).parent().is("li") ? $(el).parent().attr("id") : "",
                    parentIndex: $(el).parent().is("li") ? $(el).parent().index() : "",
                    array: $(el).sortable("toArray"),
                    serial: $(el).sortable("serialize")
                    };
                    i++;
                });

                var orderData = {};
                $(struct).each(function(k,v){
                    var main = v.array[0];
                    orderData[v.parent] = v.array;
                });
                // var myJsonString = JSON.stringify(orderData);
                // console.log(myJsonString);
                $.ajax({
                    url:"module/sort",
                    method:"POST",
                    data:{'main':orderData,'_token':'{{csrf_token()}}'},
                    success:function(data)
                    {
                    // alert('Data berhasil diperbarui');
                    }
                });
            }
        }).disableSelection();

        //delete
        $('#sortable').on('click', '.btn-delete', function(){
                var kode 	= $(this).data('id');
                var nama 	= $(this).data('nama');
                swal({
                    title: "Apakah anda yakin?",
                    text: "Untuk menghapus data : " + nama,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 		'ajax',
                            method: 	'get',
                            url: 		'/module/delete/' + kode,
                            async: 		true,
                            dataType: 	'json',
                            success: 	function(response){
                                if(response.status==true){
                                    swal({title: "Success!", text: "Berhasil Menghapus Data", icon: "success"})
                                        .then(function(){
                                        location.reload(true);
                                    });
                                }else{
                                    swal("Hapus Data Gagal !", {
                                        icon: "warning",
                                    });
                                }
                            },
                            error: function(){
                                swal("ERROR", "Hapus Data Gagal.", "error");
                            }
                        });
                    } else {
                        swal("Dibatalkan!", "Hapus Data Dibatalkan.", "error");
                    }
                });
            });
    });
</script>
@endsection
