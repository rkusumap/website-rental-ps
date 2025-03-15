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
                    Tambah Product
                </h3>
                <nav>
                    <a href="/product">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon-text"><i class="fa fa-chevron-left text-dark btn-icon-prepend"></i> Kembali</button>
                    </a>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('product.store')}}" method="POST" class="form-sample" id="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Produk</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name_product" class="form-control"
                                        placeholder="">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Merek</label>
                                    <div class="col-sm-10">
                                        <select name="brand_product" id="brand_product" class="form-control select2">
                                            <option value="">-Pilih Merek-</option>
                                            @foreach ($dataBrand as $brand)
                                                <option value="{{$brand->id_brand}}">{{$brand->name_brand}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Kategori</label>
                                    <div class="col-sm-10">
                                        <select name="category_product" id="category_product" class="form-control select2">
                                            <option value="">-Pilih Kategori-</option>
                                            @foreach ($dataCategory as $category)
                                                <option value="{{$category->id_category}}">{{$category->name_category}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Satuan</label>
                                    <div class="col-sm-10">
                                        <select name="unit_product" id="unit_product" class="form-control select2">
                                            <option value="">-Pilih Satuan-</option>
                                            @foreach ($dataUnit as $unit)
                                                <option value="{{$unit->id_unit}}">{{$unit->name_unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Harga Beli</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="hpp_product" class="form-control rupiahNumber"
                                        value="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Harga Jual</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="price_product" class="form-control rupiahNumber"
                                        value="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Biaya Rental Per Hari</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="biaya_rental_product" class="form-control rupiahNumber"
                                        value="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Gambar Produk</label>
                                    <div class="col-sm-10">
                                        <input type="file"  name="image_product" id="image_product" class="dropify"/>
                                    </div>
                                </div>



                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <button type="submit" class="btn btn-simpan btn-primary mr-2">Simpan</button>
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
        $('.dropify').dropify();
        $('.select2').select2()
        $('.btn-simpan').on('click',function () {
            $('#form').ajaxForm({
                success: function(response) {
                    if (response.status==true) {
                        swal({title: "Success!", text: "Berhasil Menyimpan Data", icon: "success"})
                                .then(function(){
                                    document.location='/product';
                            });
                    } else {
                        var pesan = "";
                        jQuery.each(response.pesan,function (key,value) {
                            pesan +=value+'. ';
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
