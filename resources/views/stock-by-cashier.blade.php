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
                Data Stok Dari Kasir
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
                                <th>Nama Produk</th>
                                <th>Qty</th>

                                <th>Tanggal</th>
                                <th>Status</th>
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
    <div class="modal-dialog" style="margin-top:30px;max-width: 1000px;" role="document">
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

                <div class="form-group row  row-product">
                    <div class="col-sm-12">
                        <table class="table table-striped table-vcenter table-mobile-md card-table dt-responsive nowrap" id="set-table">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-product">
                                <tr>
                                    <td>
                                        <select class="form-control select2 product_sbc" name="product_sbc[]" id="product_sbc">
                                            <option value="">Pilih Produk</option>
                                            @foreach ($dataProduct as $product)
                                                <option
                                                    data-hpp="{{$product->hpp_product}}"
                                                    value="{{$product->id_product}}"
                                                >{{$product->name_product}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="qty_td">
                                        <input type="text" name="qty_sbc[]" id="qty_sbc" class="form-control rupiahNumber">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-remove-product">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success btn-add-product"><i class="fa fa-plus"></i></button>
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


<div class="modal fade" id="formmodalEdit" tabindex="-1" role="dialog" aria-labelledby="formmodalEdit" aria-hidden="true">
    <div class="modal-dialog" style="margin-top:30px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formmodalEditlabel">Label Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-sample" id="formUpdate">
            <div class="modal-body">
                @csrf

                <div class="form-group row  row-product">
                    <label class="col-sm-12 ">Nama Produk</label>
                    <div class="col-sm-12">
                        <select class="form-control select2" name="product_sbc" id="product_sbc_edit">
                            <option value="">Pilih Produk</option>
                            @foreach ($dataProduct as $product)
                                <option
                                    data-hpp="{{$product->hpp_product}}"
                                    value="{{$product->id_product}}"
                                >{{$product->name_product}}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="error-product_sbc"></div>
                    </div>
                </div>

                <div class="form-group row  row-qty">
                    <label class="col-sm-12 ">Qty</label>
                    <div class="col-sm-12">
                        <input type="text" name="qty_sbc" id="qty_sbc_edit" class="form-control rupiahNumber">
                        <div class="invalid-feedback" id="error-qty_sbc"></div>
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

{{-- modal approve --}}
<div class="modal fade" id="formApproveModal" tabindex="-1" role="dialog" aria-labelledby="formApproveModal" aria-hidden="true">
    <div class="modal-dialog" style="margin-top:30px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formApproveModallabel">Approve Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-sample" id="formApprove">
            <div class="modal-body">
                @csrf

                <div class="form-group row  row-approve-tidak-sesuai">
                    <label class="col-sm-12 ">Nama Produk</label>
                    <div class="col-sm-12">
                        <input type="hidden" name="stock_sesuai" id="stock_sesuai">
                        <input type="hidden" name="input_id_sbc" id="input_id_sbc">
                        <input type="hidden" name="product_approve" id="input_product_approve">
                        <input type="text" readonly name="" id="name_product_approve" class="form-control">
                        <div class="invalid-feedback" id="error-product_approve"></div>
                    </div>
                </div>

                <div class="form-group row  row-approve-tidak-sesuai">
                    <label class="col-sm-12 ">Qty Dari Kasir</label>
                    <div class="col-sm-12">
                        <input readonly type="text" name="qty_dari_kasir" id="qty_dari_kasir" class="form-control rupiahNumber">
                        <div class="invalid-feedback" id="error-qty_dari_kasir"></div>
                    </div>
                </div>

                <div class="form-group row  row-approve-tidak-sesuai">
                    <label class="col-sm-12 ">Qty Dari Sistem</label>
                    <div class="col-sm-12">
                        <input readonly type="text" name="qty_dari_sistem" id="qty_dari_sistem" class="form-control rupiahNumber">
                        <div class="invalid-feedback" id="error-qty_dari_sistem"></div>
                    </div>
                </div>

                <div class="form-group row  row-approve-tidak-sesuai">
                    <label class="col-sm-12 ">Jenis Stock Opname</label>
                    <div class="col-sm-12">
                        <input  type="hidden" name="jenis_stock_opname" id="input_jenis_stock_opname" class="form-control rupiahNumber">
                        <input readonly type="text" name="" id="name_jenis_stock_opname" class="form-control ">
                        <div class="invalid-feedback" id="error-jenis_stock_opname"></div>
                    </div>
                </div>

                <div class="form-group row  row-approve-tidak-sesuai">
                    <label class="col-sm-12 ">Qty Stock Opname</label>
                    <div class="col-sm-12">
                        <input readonly type="text" name="qty_stock_opname" id="qty_stock_opname" class="form-control rupiahNumber">
                        <div class="invalid-feedback" id="error-qty_stock_opname"></div>
                    </div>
                </div>

                <div class="form-group row  row-approve-tidak-sesuai">
                    <label class="col-sm-12 ">Harga Beli Stock Opname</label>
                    <div class="col-sm-12">
                        <input  type="text" name="hpp_stock_opname" id="hpp_stock_opname" class="form-control rupiahNumber">
                        <div class="invalid-feedback" id="error-hpp_stock_opname"></div>
                    </div>
                </div>

                <div class="form-group row  row-approve-tidak-sesuai">
                    <label class="col-sm-12 ">Deskripsi</label>
                    <div class="col-sm-12">
                        <input  type="text" name="description_so" id="description_so" class="form-control">
                        <div class="invalid-feedback" id="error-description_so"></div>
                    </div>
                </div>

                <div class="form-group row  row-approve-sesuai">
                    <h4 class="col-sm-12 ">Stock Sudah Sesuai dengan Sistem</h4>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary btn-simpan-approve">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    var indexProduct = 1;
    $(function () {
        $(document).on('click','.btn-remove-product',function () {
            $(this).parents('tr').remove()
        })

        $(document).on('click','.btn-add-product',function () {
            var html = `
                <tr>
                    <td>
                        <select class="form-control select2 product_sbc" name="product_sbc[]" id="product_sbc${indexProduct}">
                            <option value="">Pilih Produk</option>
                            @foreach ($dataProduct as $product)
                                <option
                                    data-hpp="{{$product->hpp_product}}"
                                    value="{{$product->id_product}}"
                                >{{$product->name_product}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="qty_td">
                        <input type="text" name="qty_sbc[]" id="qty_sbc" class="form-control rupiahNumber">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-remove-product">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#tabel-product').append(html)
            $('#product_sbc'+indexProduct).select2();
            indexProduct++;
        })

        $(document).on('change','.product_sbc', function() {
            if ($(this).val() != '') {
                $(this).parent().parent().find('.qty_td').find('input').val(1)
            }
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
            $('#tabel-product').html('');
            var html = `
                    <tr>
                        <td>
                            <select class="form-control select2 product_sbc" name="product_sbc[]" id="product_sbc">
                                <option value="">Pilih Produk</option>
                                @foreach ($dataProduct as $product)
                                    <option
                                        data-hpp="{{$product->hpp_product}}"
                                        value="{{$product->id_product}}"
                                    >{{$product->name_product}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="qty_td">
                            <input type="text" name="qty_sbc[]" id="qty_sbc" class="form-control rupiahNumber">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-remove-product">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
            `
            $('#tabel-product').append(html)
            $('#product_sbc').select2();

        });

        $(document).on('click', '.btn-tambah', function(){
            $('#formmodal').modal('show');
            $('#formmodallabel').html('Tambah Stok Dari Kasir');
            $('.btn-simpan').val('tambah')
        })

        $(document).on('click', '.btn-approve', function(){

            $('#formApproveModal').modal('show');

            var id = $(this).data('id');
            // Send an AJAX request
            $.ajax({
                url: '/stock-by-cashier-get-approve/'+id, // Replace with your route
                type: 'get',
                success: function (response) {
                    console.log(response);

                    if (response.data.stock_sesuai == 0) {
                        $('.row-approve-tidak-sesuai').show();
                        $('.row-approve-sesuai').hide();
                    }
                    else{
                        $('.row-approve-tidak-sesuai').hide();
                        $('.row-approve-sesuai').show();
                    }

                    $('#hpp_stock_opname').val(funcFormatRupiahNumbers(response.data.hpp_stock_opname.toString()))
                    $('#qty_dari_kasir').val(funcFormatRupiahNumbers(response.data.qty_dari_kasir.toString()))
                    $('#qty_dari_sistem').val(funcFormatRupiahNumbers(response.data.qty_dari_sistem.toString()))
                    $('#qty_stock_opname').val(funcFormatRupiahNumbers(response.data.qty_stock_opname.toString()))

                    $('#stock_sesuai').val(response.data.stock_sesuai)
                    $('#input_id_sbc').val(response.data.input_id_sbc)
                    $('#input_jenis_stock_opname').val(response.data.input_jenis_stock_opname)
                    $('#input_product_approve').val(response.data.input_product_approve)
                    $('#name_jenis_stock_opname').val(response.data.name_jenis_stock_opname)
                    $('#name_product_approve').val(response.data.name_product_approve)


                    // Handle success response
                    $('#formApproveModal').modal('show');


                },
                error: function (xhr, status, error) {
                    // Handle error response
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        })

        $('#formApprove').on('submit', function (e) {
            e.preventDefault(); // Stop the default form submission


            $('.btn-simpan-approve').prop('disabled', true);

            let formData = $('#formApprove').serialize();

            var url = "/stock-by-cashier-post-approve";
            var type = 'POST';

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
                        $('#formApproveModal').modal('hide'); // Close the modal
                        $('#formApprove')[0].reset(); // Reset the form
                        $('.btn-simpan-approve').val("")
                        $('.btn-simpan-approve').data('id', '')
                        $('.btn-simpan-approve').prop('disabled', false);

                        $('#set-table').DataTable().ajax.reload(null, false); // Reload without resetting pagination

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

        $(document).on('click', '.btn-edit', function(){

            $('#formmodalEdit').modal('show');
            $('#formmodalEditlabel').html('Edit Stok Dari Kasir');
            $('.btn-simpan').val('update')


            var id = $(this).data('id');
            // Send an AJAX request
            $.ajax({
                url: '/stock-by-cashier/'+id, // Replace with your route
                type: 'get',
                success: function (response) {
                    console.log(response);

                    $('#product_sbc_edit').val(response.data.product_sbc).trigger('change')
                    $('#qty_sbc_edit').val(funcFormatRupiahNumbers(response.data.qty_sbc.toString()))

                    // Handle success response
                    $('#formmodalEdit').modal('show');
                    $('.btn-simpan').val('update')
                    $('.btn-simpan').data('id', response.data.id_sbc)
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        })

        $('#formUpdate').on('submit', function (e) {
            e.preventDefault(); // Stop the default form submission
            var id = $('.btn-simpan').data('id');
            var tipe_submit = $('.btn-simpan').val();

            $('.btn-simpan').prop('disabled', true);

            let formData = $('#formUpdate').serialize();

            var url = "/stock-by-cashier/"+id;
            var type = 'PUT';


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
                        $('#formmodalEdit').modal('hide'); // Close the modal
                        $('#formUpdate')[0].reset(); // Reset the form
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

        $('#form').on('submit', function (e) {
            e.preventDefault(); // Stop the default form submission
            var id = $('.btn-simpan').data('id');
            var tipe_submit = $('.btn-simpan').val();

            $('.btn-simpan').prop('disabled', true);

            let formData = $('#form').serialize();

            var url = "/stock-by-cashier";
            var type = 'POST';

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
                        ajax: '/stock-by-cashier/json',
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},

                            { data: 'product.name_product', name: 'product.name_product' },
                            { data: 'qty_sbc', name: 'qty_sbc' },


                            { data: 'tgl', name: 'tgl' },
                            { data: 'status', name: 'status' },
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
                        url: 		'/stock-by-cashier/delete/' + kode,
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
