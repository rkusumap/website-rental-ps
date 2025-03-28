@extends('admin.layout')

@section('css')

@endsection

@section('page-title')

@endsection

@section('content')
<div class="page-header">
    <h3 class="page-title">
      Dashboard
    </h3>
  </div>
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" style="background: white">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('')}}">Beranda</a></li>

                </ol>
            </nav>
        </div>


        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <b>
                                        Total Pemasukan Rental
                                    </b>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <b id="total_penjualan">Rp {{rupiah_format($pemasukanRental)}}</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>






</div>
@endsection

@section('script')

@endsection
