@extends('admin.layout')

@section('css')

@endsection

@section('page-title')
@endsection

@section('content')
@include('admin.menu.master-data')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="page-header">
            <h3 class="page-title">
                Data Stok Opanme
            </h3>
            <nav aria-label="breadcrumb">
                {{-- akses craete --}}
                @if (isAccess('create', $get_module, auth()->user()->level_user))
                    <button type="button" class="btn btn-light btn-icon-text btn-tambah">
                        <i class="fa fa-plus btn-icon-prepend"></i>
                        Tambah
                    </button>
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
                                <th>Tipe</th>
                                <th>Nama Produk</th>
                                <th>Qty</th>
                                <th>Harga Beli</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
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
    <div class="modal-dialog" style="margin-top:30px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formmodallabel">Label Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-sample" id="form">
            <div class="modal-body">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-12 ">Tipe Stok Opname</label>
                    <div class="col-sm-12">
                        <select class="form-control select2" name="type_so" id="type_so">
                            <option value="">Pilih Tipe</option>
                            <option value="S">Keluar</option>
                            <option value="P">Masuk</option>
                        </select>
                        <div class="invalid-feedback" id="error-type_so"></div>
                    </div>
                </div>

                <div class="form-group row d-none row-product">
                    <label class="col-sm-12 ">Nama Produk</label>
                    <div class="col-sm-12">
                        <select class="form-control select2" name="product_so" id="product_so">
                            <option value="">Pilih Produk</option>
                            @foreach ($dataProduct as $product)
                                <option
                                    data-hpp="{{$product->hpp_product}}"
                                    value="{{$product->id_product}}"
                                >{{$product->name_product}}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="error-product_so"></div>
                    </div>
                </div>



                <div class="form-group row d-none row-qty">
                    <label class="col-sm-12 ">Qty</label>
                    <div class="col-sm-12">
                        <input type="text" name="qty_so" id="qty_so" class="form-control rupiahNumber">
                        <div class="invalid-feedback" id="error-qty_so"></div>
                    </div>
                </div>

                <div class="form-group row d-none row-hpp">
                    <label class="col-sm-12 ">Harga Beli</label>
                    <div class="col-sm-12">
                        <input type="text" name="hpp_so" id="hpp_so" class="form-control rupiahNumber">
                        <div class="invalid-feedback" id="error-hpp_so"></div>
                    </div>
                </div>

                <div class="form-group row d-none row-total">
                    <label class="col-sm-12 ">Total</label>
                    <div class="col-sm-12">
                        <input type="text" name="grand_total_so" id="grand_total_so" class="form-control rupiahNumber" readonly>
                        <div class="invalid-feedback" id="error-grand_total_so"></div>
                    </div>
                </div>

                <div class="form-group row d-none row-total">
                    <label class="col-sm-12 ">Deskripsi</label>
                    <div class="col-sm-12">
                        <input type="text" name="description_so" id="description_so" class="form-control " >
                        <div class="invalid-feedback" id="error-description_so"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary btn-simpan" data-id="" value="">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function totalBill() {
        var total = 0;
        var type_so = $('#type_so').val()
        var qty_so = formatNumberVal($('#qty_so').val())
        var hpp_so = formatNumberVal($('#hpp_so').val())

        total = qty_so * hpp_so

        $('#grand_total_so').val(funcFormatRupiahNumbers(total.toString()))
    }
    $(function () {
        $('#product_so').on('change', function() {
            if ($(this).val() != '') {
                var type_so = $('#type_so').val()
                var hpp = $(this).find(':selected').data('hpp')
                var price = $(this).find(':selected').data('price')

                $('#qty_so').val(1)
                $('#hpp_so').val(funcFormatRupiahNumbers(hpp.toString()))
                $('#grand_total_so').val(funcFormatRupiahNumbers((hpp * $('#qty_so').val()).toString()))

            }
        })
        $('#qty_so').on('input', function() {
            totalBill()
        })
        $('#hpp_so').on('input', function() {
            totalBill()
        })

        $('#type_so').on('change', function() {

            $('.row-product').addClass('d-none')
            $('.row-qty').addClass('d-none')
            $('.row-hpp').addClass('d-none')
            $('.row-total').addClass('d-none')

            $('#product_so').val('').trigger('change')
            $('#qty_so').val('')
            $('#hpp_so').val('')

            $('#grand_total_so').val('')


            $('.row-product').removeClass('d-none')
            $('.row-qty').removeClass('d-none')
            $('.row-hpp').removeClass('d-none')
            $('.row-total').removeClass('d-none')

        })
        $('.select2').select2()
        // Close the modal when Esc is pressed
        // $(document).on('keydown', function (e) {

        //     if (e.key === "Escape" || e.keyCode === 27) {
        //         $('#form')[0].reset(); // Reset all form fields
        //         $('.btn-simpan').val("")
        //         $('.form-control').removeClass('is-invalid');
        //         $('.invalid-feedback').text('');
        //         $('.btn-simpan').prop('disabled', false);
        //         $('.btn-simpan').data('id', '')

        //         $('#formmodal').modal('hide'); // Close the modal
        //     }
        // });

        // Reset the form when the modal is closed
        $('#formmodal').on('hidden.bs.modal', function () {
            $('#form')[0].reset(); // Reset all form fields
            $('.btn-simpan').val("")
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('.btn-simpan').data('id', '')
            $('.btn-simpan').prop('disabled', false);
        });

        $(document).on('click', '.btn-tambah', function(){
            $('#formmodal').modal('show');
            $('#formmodallabel').html('Tambah Stok Opname');
            $('.btn-simpan').val('tambah')
        })

        $(document).on('click', '.btn-edit', function(){


            $('.row-product').addClass('d-none')
            $('.row-qty').addClass('d-none')
            $('.row-hpp').addClass('d-none')

            $('.row-total').addClass('d-none')

            $('#type_so').val('').trigger('change')
            $('#product_so').val('').trigger('change')
            $('#qty_so').val('')
            $('#hpp_so').val('')

            $('#grand_total_so').val('')

            $('#formmodal').modal('show');
            $('#formmodallabel').html('Edit Stok Opname');
            $('.btn-simpan').val('update')


            var id = $(this).data('id');
            // Send an AJAX request
            $.ajax({
                url: '/stock-opname/'+id, // Replace with your route
                type: 'get',
                success: function (response) {
                    console.log(response);

                    $('#type_so').val(response.data.type_so).trigger('change')


                    $('.row-product').removeClass('d-none')
                    $('.row-qty').removeClass('d-none')
                    $('.row-hpp').removeClass('d-none')
                    $('.row-total').removeClass('d-none')

                    $('#product_so').val(response.data.product_so).trigger('change')
                    $('#qty_so').val(funcFormatRupiahNumbers(response.data.qty_so.toString()))
                    $('#hpp_so').val(funcFormatRupiahNumbers(response.data.hpp_so.toString()))
                    $('#grand_total_so').val(funcFormatRupiahNumbers(response.data.grand_total_so.toString()))

                    $('#description_so').val((response.data.description_so))

                    // Handle success response
                    $('#formmodal').modal('show');
                    $('.btn-simpan').val('update')
                    $('.btn-simpan').data('id', response.data.id_so)
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        })

        $('#form').on('submit', function (e) {
            e.preventDefault(); // Stop the default form submission
            var id = $('.btn-simpan').data('id');
            var tipe_submit = $('.btn-simpan').val();

            $('.btn-simpan').prop('disabled', true);

            let formData = $('#form').serialize();

            if (tipe_submit == 'tambah') {
                var url = "/stock-opname";
                var type = 'POST';
            } else if (tipe_submit == 'update') {
                var url = "/stock-opname/"+id;
                var type = 'PUT';
            }

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            $.ajax({
                url: url, // Replace with your route
                type: type,
                data: formData,
                success: function (response) {
                    // Handle success response
                    if (response.status == true) {
                        swal({
                                title: "Success!",
                                text: "Berhasil Menyimpan Data",
                                icon: "success"
                            })
                        $('#formmodal').modal('hide'); // Close the modal
                        $('#form')[0].reset(); // Reset the form
                        $('.btn-simpan').val("")
                        $('.btn-simpan').data('id', '')
                        $('.btn-simpan').prop('disabled', false);

                        if (response.type == 'add') {
                            $('#set-table').DataTable().ajax.reload();
                        }
                        else{
                            // Reload the DataTable
                            $('#set-table').DataTable().ajax.reload(null, false); // Reload without resetting pagination
                        }

                        setTimeout(function() {
                            swal.close();
                        }, 1500);
                    }
                    else {
                        // Handle validation errors
                        if (response.pesan) {
                            $.each(response.pesan, function (key, value) {
                                $('#' + key).addClass('is-invalid'); // Highlight input
                                $('#error-' + key).text(value[0]); // Show error message
                            });
                        }
                        $('.btn-simpan').prop('disabled', false);
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    swal("Error!", "Proses Gagal", "error");
                }
            })
        })

        var table = $('#set-table').DataTable( {
                        processing: true,
                        serverSide: true,
                        stateSave : true,
                        ajax: '/stock-opname/json',
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                            { data: 'tipe', name: 'tipe' },
                            { data: 'product.name_product', name: 'product.name_product' },
                            { data: 'qty_so', name: 'qty_so' },
                            { data: 'hpp_so', name: 'hpp_so' },
                            { data: 'grand_total_so', name: 'grand_total_so' },
                            { data: 'tgl', name: 'tgl' },
                            { data: 'description_so', name: 'description_so' },
                            { data: 'action', name: 'action', orderable: false, searchable: false}
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
                        url: 		'/stock-opname/delete/' + kode,
                        async: 		true,
                        dataType: 	'json',
                        success: 	function(response){
                            if(response.status==true){
                                swal({title: "Success!", text: "Berhasil Menghapus Data", icon: "success"})
                                    .then(function(){
                                    // Reload the DataTable
                                    $('#set-table').DataTable().ajax.reload(null, false); // Reload without resetting pagination
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
                    swal("Cancelled", "Hapus Data Dibatalkan.", "error");
                }
            });
        });
    })
</script>
@endsection
