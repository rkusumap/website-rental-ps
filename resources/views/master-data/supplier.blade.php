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
                Daftar Supplier
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
                                <th>Nama Supplier</th>
                                <th>No. Telp</th>
                                <th>Alamat</th>
                                <th>Keterangan</th>
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
                <h5 class="modal-title" id="formmodallabel">Label Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-sample" id="form">
            <div class="modal-body">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-12 ">Nama Supplier</label>
                    <div class="col-sm-12">
                        <input type="text" name="name_supplier" id="name_supplier" class="form-control"
                        >
                        <div class="invalid-feedback" id="error-name_supplier"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-12 ">No. Telp Supplier</label>
                    <div class="col-sm-12">
                        <input type="text" name="phone_supplier" id="phone_supplier" class="form-control"
                        >
                        <div class="invalid-feedback" id="error-phone_supplier"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-12 ">Alamat Supplier</label>
                    <div class="col-sm-12">
                        <input type="text" name="address_supplier" id="address_supplier" class="form-control"
                        >
                        <div class="invalid-feedback" id="error-address_supplier"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-12 ">Keterangan Supplier</label>
                    <div class="col-sm-12">
                        <input type="text" name="description_supplier" id="description_supplier" class="form-control"
                        >
                        <div class="invalid-feedback" id="error-description_supplier"></div>
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
  $(function () {
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
        $('#formmodallabel').html('Tambah Supplier');
        $('.btn-simpan').val('tambah')
    })

    $(document).on('click', '.btn-edit', function(){
        $('#formmodal').modal('show');
        $('#formmodallabel').html('Edit Supplier');
        $('.btn-simpan').val('update')
        var id = $(this).data('id');
        // Send an AJAX request
        $.ajax({
            url: '/supplier/'+id, // Replace with your route
            type: 'get',
            success: function (response) {
                // Handle success response
                $('#name_supplier').val(response.data.name_supplier)
                $('#phone_supplier').val(response.data.phone_supplier)
                $('#address_supplier').val(response.data.address_supplier)
                $('#description_supplier').val(response.data.description_supplier)
                $('.btn-simpan').data('id', response.data.id_supplier)
                $('#formmodal').modal('show');
                $('.btn-simpan').val('update')
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
            var url = "/supplier";
            var type = 'POST';
        } else if (tipe_submit == 'update') {
            var url = "/supplier/"+id;
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
                    ajax: '/supplier/json',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                        { data: 'name_supplier', name: 'name_supplier' },
                        { data: 'phone_supplier', name: 'phone_supplier' },
                        { data: 'address_supplier', name: 'address_supplier' },
                        { data: 'description_supplier', name: 'description_supplier' },
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
                    url: 		'/supplier/delete/' + kode,
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
