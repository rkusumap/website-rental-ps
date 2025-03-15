@extends('admin.layout')

@section('css')

@endsection

@section('page-title')
@endsection

@section('content')

<div class="row">
    <div class="col-12 grid-margin">
        <div class="page-header">
            <h3 class="page-title">
                Keranjang
            </h3>
            <nav aria-label="breadcrumb">

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
                                <th>Nama</th>
                                <th>Jenis PS</th>
                                <th>Tanggal</th>
                                <th>Biaya</th>
                                <th>Status Bayar</th>
                                <th>Status Barang</th>
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

<!-- Modal -->
<div class="modal fade" id="formmodal" tabindex="-1" role="dialog" aria-labelledby="formmodal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formmodallabel">Detail Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-sample" id="form">
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="">Jenis PS</label>
                    </div>
                    <div class="col-sm-1">
                        <p>:</p>
                    </div>
                    <div class="col-sm-8">
                        <p id="jenis_ps"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="">User</label>
                    </div>
                    <div class="col-sm-1">
                        <p>:</p>
                    </div>
                    <div class="col-sm-8">
                        <p id="user_detail"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="">Tanggal</label>
                    </div>
                    <div class="col-sm-1">
                        <p>:</p>
                    </div>
                    <div class="col-sm-8">
                        <p id="tgl_detail"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="">Total Biaya</label>
                    </div>
                    <div class="col-sm-1">
                        <p>:</p>
                    </div>
                    <div class="col-sm-8">
                        <p id="biaya_detail"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="">Metode Bayar</label>
                    </div>
                    <div class="col-sm-1">
                        <p>:</p>
                    </div>
                    <div class="col-sm-8">
                        <p id="metode_detail"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
  $(function () {

    $(document).on('click','.btn-detail',function () {
        var id = $(this).data('id');
        $.ajax({
            url: "/keranjang/"+id,
            method: 'get',
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('#jenis_ps').html(response.data.rental_detail_one.product.name_product);
                $('#user_detail').html(response.data.user.name);
                $('#tgl_detail').html(response.formated_date);
                $('#biaya_detail').html("Rp "+response.total);
                $('#metode_detail').html(response.status.payment_type.toUpperCase());
                $('#formmodal').modal('show');
            },
            error: function (xhr, status, error) {
                console.error(error);
                swal("Error!", "Proses Gagal", "error");
            }
        })

    })

    $(document).on('click','.btn-bayar',function () {
        var snap_token = $(this).data('snap');
        var id = $(this).data('id');
        snap.pay(snap_token, {
          // Optional
          onSuccess: function(result){
            $.ajax({
                url: "{{ route('keranjang.store') }}",
                method: 'POST',
                data: {
                    id:id,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    swal("Berhasil!", "Pembayaran selesai! Terimakasih", "success").then(function () {
                        window.location.href = "{{ route('keranjang.index') }}";
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    swal("Error!", "Proses Gagal", "error");
                }
            })
          },
          // Optional
          onPending: function(result){

          },
          // Optional
          onError: function(result){

          }
        });
    })

    var table = $('#set-table').DataTable( {
                    processing: true,
                    serverSide: true,
                    stateSave : true,
                    ajax: '/keranjang/json',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'user.name', name: 'user.name' }, // User's name
                        { data: 'rental_detail_one.product.name_product', name: 'rental_detail_one.product.name_product' }, // Product name
                        { data: 'tanggal', name: 'tanggal' }, // Rental date
                        { data: 'biaya', name: 'biaya' }, // Cost
                        { data: 'status_bayar', name: 'status_bayar' },
                        { data: 'status_rental', name: 'status_rental' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

     $('#set-table').each(function() {
        var datatable = $(this);
        // SEARCH - Add the placeholder for Search and Turn this into in-line form control
        var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
        search_input.attr('placeholder', 'Search');
        search_input.removeClass('form-control-sm');
        // LENGTH - Inline-Form control
        var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
        length_sel.removeClass('form-control-sm');
    });


    table.on( 'draw', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

  })
</script>


@endsection
