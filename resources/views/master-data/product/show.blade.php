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
                    Detail Data Product
                </h3>
                <nav>
                    <a href="/product">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                    </a>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action=""  class="form-sample" id="form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Produk</label>
                                    <div class="col-sm-10">
                                        <label class="col-form-label">: {{$get_data->name_product}}</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Merek</label>
                                    <div class="col-sm-10">
                                        <label class="col-form-label">: {{$get_data->brand->name_brand}}</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Kategori</label>
                                    <div class="col-sm-10">
                                        <label class="col-form-label">: {{$get_data->category->name_category}}</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Satuan</label>
                                    <div class="col-sm-10">
                                        <label class="col-form-label">: {{$get_data->unit->name_unit}}</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Harga Beli</label>
                                    <div class="col-sm-10">
                                        <label class="col-form-label">: RP. {{rupiah_format($get_data->hpp_product)}}</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Harga Jual</label>
                                    <div class="col-sm-10">
                                        <label class="col-form-label">: RP. {{rupiah_format($get_data->price_product)}}</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Gambar Produk</label>
                                    <div class="col-sm-10">
                                        <img class="ih250" src="{{$get_data->image_product}}" alt="">
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

@endsection
