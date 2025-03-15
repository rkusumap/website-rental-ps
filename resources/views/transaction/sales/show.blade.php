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
                    Detail Transaksi Penjualan
                </h3>
                <nav>
                    <a href="/sales">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                    </a>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action=""  class="form-sample" id="form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Tipe Transaksi</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: {{reference('type_transaction',$get_data->type_trx)}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Kode Transaksi</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: {{($get_data->code_trx)}}</label>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Metode Pembayaran</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: {{($get_data->method_payment->name_mp)}}</label>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Customer</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: {{($get_data->customer->name_customer)}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Diskon</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: {{$get_data->discount_persen_trx}}% (Rp. {{rupiah_format($get_data->discount_nominal_trx)}})</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Total Transaksi Tanpa Diskon</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: Rp. {{rupiah_format($get_data->total_trx)}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Total Transaksi Dengan Diskon</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: Rp. {{rupiah_format($get_data->grand_total_trx)}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Uang Diterima</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: Rp. {{rupiah_format($get_data->amount_received_trx)}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Uang Kembalian</label>
                                            <div class="col-sm-8">
                                                <label class="col-form-label">: Rp. {{rupiah_format($get_data->change_given_trx)}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label">Detail Transaksi</label>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-vcenter table-mobile-md card-table dt-responsive nowrap" id="set-table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Produk</th>
                                                    <th>Jumlah</th>
                                                    <th>Harga Beli</th>
                                                    <th>Harga Jual</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        var table = $('#set-table').DataTable( {
                    processing: true,
                    serverSide: true,
                    stateSave : true,
                    ajax: '/sales/json-show/{{$get_data->id_trx}}',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},

                        { data: 'product.name_product', name: 'product.name_product', defaultContent: '' },
                        { data : 'qty_trd',name : 'qty_trd' },
                        { data : 'harga_beli',name : 'harga_beli' },
                        { data : 'harga_jual',name : 'harga_jual' },
                        { data : 'total',name : 'total' },


                    ]
                });
        table.on( 'draw', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    })
</script>
@endsection
