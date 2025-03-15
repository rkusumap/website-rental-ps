@extends('admin.layout')

@section('css')

@endsection

@section('page-title')
@endsection

@section('content')
@include('admin.menu.transaction')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="page-header">
            <h3 class="page-title">
                Daftar Transaksi Pembelian
            </h3>
            <nav aria-label="breadcrumb">
                {{-- akses craete --}}
                @if (isAccess('create', $get_module, auth()->user()->level_user))
                <a href="{{route('purchase.create')}}">
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

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-vcenter table-mobile-md card-table dt-responsive nowrap" id="set-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor</th>
                                <th>Supplier</th>
                                <th>Metode Bayar</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        {{-- akses read --}}
                        @if (isAccess('read', $get_module, auth()->user()->level_user))
                            <tbody id="tabel-body"></tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
  $(function () {
    var table = $('#set-table').DataTable( {
                processing: true,
                serverSide: true,
                stateSave : true,
                ajax: '/purchase/json',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                    { data: 'code_trx', name: 'code_trx' },
                    { data: 'supplier.name_supplier', name: 'supplier.name_supplier', defaultContent: '' },
                    { data : 'method_payment.name_mp',name : 'method_payment.name_mp' ,defaultContent: '' },
                    { data : 'total',name : 'total' },
                    { data : 'tanggal',name : 'tanggal' },
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
    table.on( 'draw', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    //delete
    $('#tabel-body').on('click', '.btn-hapus', function(){
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
                    url: 		'/purchase/delete/' + kode,
                    async: 		true,
                    dataType: 	'json',
                    success: 	function(response){
                        if(response.status==true){
                            swal({title: "Success!", text: "Berhasil Menghapus Data", icon: "success"})
                                .then(function(){
                                location.reload(true);
                            });
                        }else{
                            swal("Hapus Data Gagal ! "+response.pesan, {
                                icon: "warning",
                            });
                        }
                    },
                    error: function(){
                        swal("ERROR", "Hapus Data Gagal.", "error");
                    }
                });
            } else {
                swal("Cancelled", "Hapus Data Dibatalkan.", "error");
            }
        });
    });

  })
</script>
@endsection
