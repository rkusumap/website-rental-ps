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
                    Edit Transaksi Penjualan
                </h3>
                <nav>
                    <a href="/sales">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                    </a>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('sales.update',$get_data->id_trx)}}" method="POST" class="form-sample" id="form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">

                                <select name="" id="firstProduct" class="form-control d-none">
                                    <option value="">-Pilih Produk-</option>
                                    @foreach ($dataProduct as $product)
                                        <option
                                        data-price="{{$product->price_product}}" value="{{$product->id_product}}">{{$product->name_product}}</option>
                                    @endforeach
                                </select>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4>Keranjang</h4> <h5>{{$get_data->code_trx}}</h5>
                                                <hr>
                                                <div class="table-responsive">
                                                    <table class="table table-striped" id="keranjang">
                                                        <thead>
                                                            <tr>
                                                                <th>Nama Produk</th>
                                                                <th>Harga</th>
                                                                <th>Qty</th>
                                                                <th>Total</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbody">
                                                            @foreach($dataTransactionDetail as $trd)
                                                                <tr>
                                                                    <td>
                                                                        <select name="product[]" id="" class="form-control product-select select2">
                                                                            <option value="">-Pilih Produk-</option>
                                                                            @foreach ($dataProduct as $product)
                                                                                <option
                                                                                {{isSelected($product->id_product,$trd->product_trd)}}
                                                                                data-price="{{$product->price_product}}" value="{{$product->id_product}}">{{$product->name_product}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td class="harga">
                                                                        <input value="{{$trd->price_trd}}" type="text" name="price[]" id="" class="form-control rupiahNumber" readonly>
                                                                    </td>
                                                                    <td class="qty">
                                                                        <input value="{{$trd->qty_trd}}" type="number" name="qty[]" class="form-control qtyInput" value="0">
                                                                    </td>
                                                                    <td class="total">
                                                                        <input value="{{$trd->total_trd}}" type="text" name="total[]" class="form-control totalInput rupiahNumber" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-danger btn-remove">Hapus</button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <h4 class="mt-3">Total Semua : Rp. <span id="grandTotal">{{rupiah_format($get_data->grand_total_trx)}}</span></h4>
                                                <input type="hidden" name="" id="grandTotalInput">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <button type="button" class="btn btn-add btn-success mr-2">Tambah Product</button>
                            <button type="button" class="btn btn-checkout btn-primary mr-2">Checkout</button>
                        </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="formmodal" tabindex="-1" role="dialog" aria-labelledby="formmodal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formmodallabel">Checkout (<span>{{$get_data->code_trx}}</span>)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="col-sm-12 ">Nama Customer</label>
                                <div class="col-sm-12">
                                    <select name="id_customer" id="id_customer" class="form-control select2">
                                        <option value="">-Tanpa Customer-</option>
                                        @foreach ($dataCustomer as $customer)
                                            <option
                                            {{isSelected($customer->id_customer,$get_data->customer_trx)}}
                                            value="{{$customer->id_customer}}">{{$customer->name_customer}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-sm-12 ">Metode Pembayaran</label>
                                    <div class="col-sm-12">
                                        <select name="id_method_payment" id="id_method_payment" class="form-control select2">
                                            <option value="">-Pilih Metode Pembayaran-</option>
                                            @foreach ($dataMethodPayment as $mp)
                                                <option
                                                {{isSelected($mp->id_mp,$get_data->method_payment_trx)}}
                                                value="{{$mp->id_mp}}">{{$mp->name_mp}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-12">Jenis Diskon</label>
                        <div class="col-sm-12">
                            <select name="jenis_diskon" id="jenis_diskon" class="form-control select2">
                                <option value="">-Tidak Pakai Diskon-</option>
                                <option {{isSelected('persen',$get_data->type_discount_trx)}} value="persen">Persen (%)</option>
                                <option {{isSelected('nominal',$get_data->type_discount_trx)}} value="nominal">Nominal (Rp.)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row form-diskon d-none">
                        <label class="col-sm-12 ">Diskon <span id="diskon_label"></span></label>
                        <div class="col-sm-12">
                            @php
                                $diskon_value = 0;
                                if ($get_data->type_discount_trx == 'persen') {
                                    $diskon_value = $get_data->discount_persen_trx;
                                }
                                else if($get_data->type_discount_trx == 'nominal') {
                                    $diskon_value = $get_data->discount_nominal_trx;
                                }
                            @endphp
                            <input type="text" name="diskon_value" id="diskon_value" class="form-control rupiahNumber"
                            placeholder="" value="{{$diskon_value}}">
                            <div class="invalid-feedback" id="error-diskon_value"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-12 ">Total Transaksi</label>
                        <div class="col-sm-12">
                            <input value="{{$get_data->grand_total_trx}}" type="text" name="total_transaksi" id="total_transaksi" class="form-control rupiahNumber"
                            placeholder="" readonly>
                            <div class="invalid-feedback" id="error-total_transaksi"></div>
                        </div>
                    </div>

                    <div class="form-group row d-none form-total_transaksi_dengan_diskon">
                        <label class="col-sm-12 ">Total Transaksi Dengan Diskon</label>
                        <div class="col-sm-12">
                            <input type="text" name="total_transaksi_dengan_diskon" id="total_transaksi_dengan_diskon" class="form-control"
                            placeholder="" readonly>
                            <div class="invalid-feedback" id="error-total_transaksi_dengan_diskon"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-12 ">Uang Diterima</label>
                        <div class="col-sm-12">
                            <input value="{{$get_data->amount_received_trx}}" type="text" name="uang_diterima" id="uang_diterima" class="form-control rupiahNumber"
                            placeholder="">
                            <div class="invalid-feedback" id="error-uang_diterima"></div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-12 ">Uang Kembalian</label>
                        <div class="col-sm-12">
                            <input value="{{$get_data->change_given_trx}}" type="text" name="uang_kembalian" id="uang_kembalian" class="form-control rupiahNumber"
                            placeholder="" readonly>
                            <div class="invalid-feedback" id="error-uang_kembalian"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-simpan">Simpan</button>
                </div>

            </div>
        </div>
    </div>
</form>
@endsection
@section('script')
<script>
    function totalBill(){
        var inputTotal = $('.totalInput');
        var grandTotal = 0;


        inputTotal.each(function(indexInputTotal, inputTotalval) {
            var classInputTotal = $(inputTotalval);  // Cache jQuery object of the input

            var inputValue = classInputTotal.val();

            var numberWithDots = inputValue
            var numberWithNull = numberWithDots.replace(/\./g, "");
            var result = parseInt(numberWithNull, 10);

            if (isNaN(result)) {
                result = 0;
            }
            if (result != 0) {
                grandTotal += Number(result); // Adjust the selector as needed
            }

        });

        var jenis_diskon = $('#jenis_diskon').val();
        var uang_diterima = formatNumberVal($('#uang_diterima').val());
        var grandTotalDenganDiskon = 0;
        var uang_kembalian = 0
        if (jenis_diskon == 'persen') {
            var numberWithDotsDiskon = $('#diskon_value').val()
            var numberWithNullDiskon = numberWithDotsDiskon.replace(/\./g, "");
            var resultDiskon = parseInt(numberWithNullDiskon, 10);

            if (isNaN(resultDiskon)) {
                resultDiskon = 0;
            }

            grandTotalDenganDiskon = grandTotal * (100-resultDiskon) / 100;
            uang_kembalian = uang_diterima - grandTotalDenganDiskon;
        }
        else if(jenis_diskon == 'nominal') {
            var numberWithDotsDiskon = $('#diskon_value').val()
            var numberWithNullDiskon = numberWithDotsDiskon.replace(/\./g, "");
            var resultDiskon = parseInt(numberWithNullDiskon, 10);

            if (isNaN(resultDiskon)) {
                resultDiskon = 0;
            }

            grandTotalDenganDiskon = grandTotal - resultDiskon;
            uang_kembalian = uang_diterima - grandTotalDenganDiskon;
        }
        else{
            uang_kembalian = uang_diterima - grandTotal;
        }


        $('#uang_kembalian').val(funcFormatRupiahNumbers(uang_kembalian.toString()));

        $('#grandTotalInput').val(grandTotal);
        var grandTotal = funcFormatRupiahNumbers(grandTotal.toString());
        $('#grandTotal').html(grandTotal)
        $('#total_transaksi').val(grandTotal);
        $('#total_transaksi_dengan_diskon').val(funcFormatRupiahNumbers(grandTotalDenganDiskon.toString()));


    }

    $(function () {

        var jenis_diskon = $('#jenis_diskon').val();
        if (jenis_diskon == 'persen') {
            $('.form-diskon').removeClass('d-none');
            $('.form-total_transaksi_dengan_diskon').removeClass('d-none');
            $('#diskon_label').html('%');
        }else if (jenis_diskon == 'nominal') {
            $('.form-diskon').removeClass('d-none');
            $('#diskon_label').html('Rp.');
            $('.form-total_transaksi_dengan_diskon').removeClass('d-none');
        }else {
            $('.form-diskon').addClass('d-none');
            $('.form-total_transaksi_dengan_diskon').addClass('d-none');
        }
        totalBill()

        let selectOptionsProduct = $('#firstProduct').html();

        $('.select2').select2();

        $(document).on('input','#diskon_value',function () {
            totalBill()
        })

        $(document).on('change','#jenis_diskon',function () {
            var jenis_diskon = $(this).val();
            if (jenis_diskon == 'persen') {
                $('.form-diskon').removeClass('d-none');
                $('.form-total_transaksi_dengan_diskon').removeClass('d-none');
                $('#diskon_label').html('%');
            }else if (jenis_diskon == 'nominal') {
                $('.form-diskon').removeClass('d-none');
                $('#diskon_label').html('Rp.');
                $('.form-total_transaksi_dengan_diskon').removeClass('d-none');
            }else {
                $('.form-diskon').addClass('d-none');
                $('.form-total_transaksi_dengan_diskon').addClass('d-none');
            }
            totalBill()
        })

        $(document).on('change','.product-select',function () {
            var id = $(this).val();
            var harga = $(this).find(':selected').data('price').toString();

            $(this).closest('tr').find('.harga input').val(funcFormatRupiahNumbers(harga));
            $(this).closest('tr').find('.qty input').val(1);
            $(this).closest('tr').find('.total input').val(funcFormatRupiahNumbers(harga));

            totalBill()
        })

        $(document).on('input','.qtyInput',function () {
            var harga = formatNumberVal($(this).closest('tr').find('.harga input').val());
            var qty = $(this).val();
            var total = (harga*qty).toString();
            $(this).closest('tr').find('.total input').val(funcFormatRupiahNumbers(total));
            totalBill()
        })

        $(document).on('click','.btn-remove',function () {
            $(this).closest('tr').remove();
        })

        $(document).on('click','.btn-add',function () {
            var html = `
            <tr>
                <td>
                    <select name="product[]" id="" class="form-control product-select select2">
                    `+
                        selectOptionsProduct
                    +`
                    </select>
                </td>
                <td class="harga">
                    <input type="text" name="price[]" id="" class="form-control" readonly>
                </td>
                <td class="qty">
                    <input type="number" name="qty[]" class="form-control qtyInput" value="0">
                </td>
                <td class="total">
                    <input type="text" name="total[]" class="form-control totalInput" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-remove">Hapus</button>
                </td>
            </tr>
            `;

            $('#tbody').append(html);
            $('.select2').select2()
        })

        $(document).on('click','.btn-checkout',function () {
            totalBill()
            $('#formmodal').modal('show');
        })


        $(document).on('input','#uang_diterima',function () {
            totalBill()
        })

        $('.btn-simpan').on('click',function () {
            $('#form').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Menyimpan Data", icon: "success"})
                                .then(function(){
                                    document.location='/sales';
                            });
                    } else {
                        var pesan = "";
                        console.log(response);

                        jQuery.each(response.pesan,function (key,value) {
                            pesan +=value+' ';
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
