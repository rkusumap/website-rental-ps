@extends('admin.layout')

@section('css')

@endsection

@section('page-title')
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="page-header">
            <h3 class="page-title">
                Rental PS
            </h3>
            <nav aria-label="breadcrumb">
                {{-- akses craete --}}
                @if (isAccess('create', $get_module, auth()->user()->level_user))
                    <button type="button" class="btn btn-light btn-tambah btn-icon-text">
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
                <input type="hidden" name="" id="rental_minggu" value="{{$dataRentalMinggu->biaya_rm}}">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="detailmodal" tabindex="-1" role="dialog" aria-labelledby="detailmodal" aria-hidden="true">
    <div class="modal-dialog" role="document" style="margin-top:50px;max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailmodallabel">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="">
                <ul class="nav nav-tabs" id="myTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab1-link" data-bs-toggle="tab" href="#tab1">Detail</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2-link" data-bs-toggle="tab" href="#tab2">Rental PS</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab1">
                        <button type="button" class="btn btn-primary mb-3" id="pesanBtn">Pesan</button>
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter table-mobile-md card-table dt-responsive nowrap" id="set-table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Code</th>
                                        <th>Tanggal</th>
                                        <th>Biaya</th>
                                        <th>Status Bayar</th>
                                        <th>Status Barang</th>
                                        <th>Aksi</th>

                                    </tr>
                                </thead>
                                <tbody id="tbody_detail">

                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab2">
                        <form action="" id="form-rental">
                        <div class="form-group">
                            <label for="">Jenis PS</label>
                            <select name="jenis_ps" id="jenis_ps" class="form-control">
                                <option value="kosong">Pilih</option>
                                @foreach ($dataProduct as $prd)
                                    <option value="{{$prd->id_product}}" data-biaya="{{$prd->biaya_rental_product}}">{{$prd->name_product}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-1">
                                    <label for="">Tanggal</label>

                                </div>
                                <div class="col-md-11">
                                    <div>
                                        <input style="margin-bottom:.5rem; vertical-align: middle;" type="checkbox" name="satuhari" id="satuhari" value="1">
                                        <label for="satuhari" style="vertical-align: middle;">Satu Hari</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" type="date" name="tanggal_awal" id="tanggal_awal">
                                </div>
                                <div class="col-md-1">
                                    <p>s/d</p>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" type="date" name="tanggal_akhir" id="tanggal_akhir">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Biaya</label>
                            <input class="form-control rupiahNumber" readonly type="text" name="biaya" id="biaya">
                        </div>
                        <button class="btn btn-success btn-pesan-sekarang" type="button">Pesan Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light tutup-modal" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
    // // Handle "Pesan" button click
    // document.getElementById('pesanBtn').addEventListener('click', function () {
    //     const tab = new bootstrap.Tab(document.getElementById('tab2-link'));
    //     tab.show(); // Show the "Rental PS" tab
    // });
</script>
<script>
    function totalBiaya() {
        var jenis_ps = $('#jenis_ps').val();
        var biaya_rental = $('#jenis_ps').find(':selected').data('biaya') || 0;
        var rental_minggu = parseFloat($('#rental_minggu').val()) || 0;
        var tanggalAwal = $('#tanggal_awal').val();
        var tanggalAkhir = $('#tanggal_akhir').val();

        // Ensure dates are valid
        if (!tanggalAwal || !tanggalAkhir) {
            swal("Error!", 'Tanggal awal atau akhir tidak boleh kosong', "error");
            // console.error("Tanggal awal atau akhir tidak boleh kosong.");
            $('#biaya').val(0);
        }

        // Convert dates to Date objects
        var startDate = new Date(tanggalAwal);
        var endDate = new Date(tanggalAkhir);

        if (startDate > endDate) {
            swal("Error!", 'Tanggal awal tidak boleh lebih besar dari tanggal akhir', "error");
            // console.error("Tanggal awal tidak boleh lebih besar dari tanggal akhir.");
            $('#biaya').val(0);
        }

        // Calculate the number of days
        var totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;

        // Count the number of Saturdays and Sundays
        var weekendCount = 0;
        for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
            if (d.getDay() === 0 || d.getDay() === 6) { // 0 = Sunday, 6 = Saturday
                weekendCount++;
            }
        }

        // Calculate total biaya
        var total_biaya = (totalDays * biaya_rental) + (weekendCount * rental_minggu);

        // console.log("Biaya Rental:", biaya_rental);
        // console.log("Rental Minggu:", rental_minggu);
        // console.log("Total Hari:", totalDays);
        // console.log("Jumlah Sabtu/Minggu:", weekendCount);
        // console.log("Total Biaya:", total_biaya);
        total_biaya = funcFormatRupiahNumbers(total_biaya.toString())
        $('#biaya').val(total_biaya);

        if (jenis_ps === 'kosong') {
            swal("Error!", 'Pilih jenis PS terlebih dahulu', "error");
            $('#biaya').val(0);
        }

    }

    $(document).ready(function () {
        $('#jenis_ps').on('change', function () {
            var value = $(this).val();
            if (value !== 'kosong') {
                $.ajax({
                    url: '/rental/check-stock/' + value,
                    method: 'get',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status == false) {
                            swal("Error!", response.pesan, "error");
                            $('#biaya').val(0);
                            $('#jenis_ps').val('kosong').change();
                        }

                    },
                })
                totalBiaya()
            }
        })
        $('.btn-tambah').on('click', function () {
            $('#tbody_detail').empty();
            $('#tab2-link').tab('show'); // Show the "Rental PS" tab
            $('#detailmodal').modal('show');
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal_awal').val(today);
        });
        $('#detailmodal').on('hidden.bs.modal', function () {
            $('#form-rental')[0].reset(); // Clear all form inputs
        });
        $('.btn-pesan-sekarang').on('click', function () {
            var jenis_ps = $('#jenis_ps').val();
            var satuhari = $('#satuhari').is(':checked') ? $('#satuhari').val() : 0;
            var tanggal_awal = $('#tanggal_awal').val();
            var tanggal_akhir = $('#tanggal_akhir').val();
            var biaya = $('#biaya').val();
            var startDate = new Date(tanggal_awal);
            var endDate = new Date(tanggal_akhir);
            // Calculate the number of days
            var totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;

            if (biaya != 0 || biaya != '') {
                // console.log("Jenis PS:", jenis_ps);
                // console.log("Satu Hari:", satuhari);
                // console.log("Tanggal Awal:", tanggal_awal);
                // console.log("Tanggal Akhir:", tanggal_akhir);
                // console.log("Biaya:", biaya);
                $.ajax({
                    url: "{{ route('rental.store') }}",
                    method: 'POST',
                    data: {
                        jenis_ps: jenis_ps,
                        satuhari: satuhari,
                        tanggal_awal: tanggal_awal,
                        tanggal_akhir: tanggal_akhir,
                        biaya: biaya,
                        total_days: totalDays,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        // console.log(response);
                        $('#detailmodal').modal('hide');

                        swal("Berhasil!", "Data berhasil disimpan", "success").then(function () {
                            window.location.href = "{{ route('keranjang.index') }}";
                        });
                    },
                    error: function (xhr, status, error) {
                        // console.error(error);
                        swal("Error!", "Proses Gagal", "error");
                    }
                })
            }
            else{
                swal("Maaf!", "Lengkapi Inputan", "error");
            }

        })

        $('#satuhari').on('change', function () {
            if (this.checked) {
                $('#tanggal_akhir').val($('#tanggal_awal').val());
                $('#tanggal_akhir').attr('disabled', true);
            }
            else{
                $('#tanggal_akhir').attr('disabled', false);
            }
            totalBiaya()
        })
        $('#tanggal_awal').on('change', function () {
            if ($('#satuhari').is(':checked')) {
                $('#tanggal_akhir').val($('#tanggal_awal').val());
            }
            totalBiaya()
        });
        $('#tanggal_akhir').on('change', function () {
            totalBiaya()
        });

        $('#pesanBtn').on('click', function () {
            $('#tab2-link').tab('show'); // Show the "Rental PS" tab
        });

        $('.tutup-modal').on('click', function () {
            $('#detailmodal').modal('hide');
        })

        function fetchEvents(start, end, callback) {
            // AJAX request to load events from the database
            $.ajax({
                url: '/get-events', // Your endpoint to fetch events
                type: 'GET',
                data: {
                    start: start.format('YYYY-MM-DD'),
                    end: end.format('YYYY-MM-DD')
                },
                success: function (events) {
                    // console.log(events);

                    callback(events);
                },
                error: function () {
                    alert('Error fetching events');
                }
            });
        }

        function contentTableDetail (response){
            response.forEach(function(val) {
                $('#tbody_detail').append(`
                    <tr>
                        <td>${val.user}</td>
                        <td>${val.code_rental}</td>
                        <td>${val.tanggal}</td>
                        <td>${val.biaya}</td>
                        <td>
                            <span class="badge ${val.warna_bayar}">${val.status_bayar.toUpperCase()}</span>

                        </td>
                        <td>${val.status_rental}</td>
                        <td>
                            <button type="button" data-id="${val.id_rental}" data-snap="${val.snap_token}" class="btn btn-sm btn-success btn-bayar ${val.d_none_bayar}">Bayar</button>
                        </td>
                    </tr>
                `);
            });
            $('#detailmodal').modal('show');
        }

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
                            location.reload();
                        });
                    },
                    error: function (xhr, status, error) {
                        // console.error(error);
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

        function fetchEventDetailsByDate(startDate) {
            $('#tbody_detail').empty();
            return $.ajax({
                url: '/events-by-date',
                type: 'GET',
                data: {
                    start: startDate
                },
                dataType: 'json'
            });
        }

        $('#calendar').fullCalendar({
            // height: 900, // Calendar height in pixels
            header: {
                left: 'prev,next today',
                center: 'title',
                right: '' // Hide week and day buttons
            },
            defaultView: 'month', // Show only month view
            editable: true,
            displayEventTime: false, // Hide event time
            // eventLimit: true, // Group events when more than 3
            events: function(start, end, timezone, callback) {
                fetchEvents(start, end, callback);
            },
            eventRender: function(event, element) {
                // Check if the event has an end date
                if (!event.end) {
                    element.addClass('fc-not-end');
                } else {
                    element.addClass('fc-end');
                }

                // console.log('Event:', event.title, 'Class:', element.attr('class'));
            },
            dayClick: function(date) {

                const startDate = date.format()

                $('#tanggal_awal').val(startDate);
                $('#tab1-link').tab('show'); // Show the "Rental PS" tab

                fetchEventDetailsByDate(startDate)
                    .done(function(response) {
                        // console.log(response);
                        contentTableDetail(response);

                    })
                    .fail(function(xhr, status, error) {
                        // console.error('Error fetching event details:', error);
                        $('#tab2-link').tab('show'); // Show the "Rental PS" tab
                        $('#detailmodal').modal('show');
                    });
            },
            eventClick: function(event) {
                const startDate = event.start.format('YYYY-MM-DD');

                $('#tanggal_awal').val(startDate);
                $('#tab1-link').tab('show'); // Show the "Rental PS" tab

                fetchEventDetailsByDate(startDate)
                    .done(function(response) {

                        contentTableDetail(response);


                    })
                    .fail(function(xhr, status, error) {
                        // console.error('Error fetching event details:', error);
                        alert('Failed to load event details.');
                    });
            },
            eventLimitClick: function(cellInfo) {


                // Get the clicked date
                const clickedDate = cellInfo.date.format('YYYY-MM-DD');

                $('#tanggal_awal').val(clickedDate);
                $('#tab1-link').tab('show'); // Show the "Rental PS" tab

                fetchEventDetailsByDate(clickedDate)
                    .done(function(response) {

                        contentTableDetail(response);


                    })
                    .fail(function(xhr, status, error) {
                        // console.error('Error fetching event details:', error);
                        alert('Failed to load event details.');
                    });


                // Show modal
                $('#detailmodal').modal('show');
            }
        });


    });
</script>

@endsection
