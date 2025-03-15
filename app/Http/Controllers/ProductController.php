<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Category;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\Facades\Image;
use File;

class ProductController extends Controller
{
    public function rules($request, $id = null)
    {
        $rule = [
            'name_product' => 'required',
            'brand_product' => 'nullable',
            'category_product' => 'required',
            'unit_product' => 'required',
            'hpp_product' => 'required',
            'price_product' => 'required'

        ];
        $pesan = [
            'name_product.required' => 'Nama Wajib di isi',
            // 'brand_product.required' => 'Merek Wajib di isi',
            'category_product.required' => 'Kategori Wajib di isi',
            'unit_product.required' => 'Satuan Wajib di isi',
            'hpp_product.required' => 'Harga Pembelian Wajib di isi',
            'price_product.required' => 'Harga Jual Wajib di isi',


        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index()
    {
        $get_module = get_module_id('user');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }
        return view('master-data.product.index', compact('get_module'));
    }

    public function json()
    {
        $datas = Product::select('*')->orderBy('name_product', 'asc');
        ;
        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('product');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {
                    $btn_detail = '<a class="dropdown-item" href="' . route('product.show', $data->id_product) . '">Detail</a>';
                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" onclick="location.href=' . "'" . route('product.edit', $data->id_product) . "'" . ';" class="btn btn-sm btn-info">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    $btn_hapus = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id_product . '" data-nama="' . $data->name_product . '">Hapus</a>';
                }
                return '
                <div class="btn-group">
                    ' . $btn_edit . '
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton1">
                        ' . $btn_detail . '
                        ' . $btn_hapus . '

                    </div>
                </div>
              ';
            })
            ->addColumn('stock', function ($data) {
                // Calculate stock
                $stock_product = check_stock($data->id_product);
                return $stock_product;
            })


            ->rawColumns([])
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function create()
    {
        $dataCategory = Category::orderBy('name_category', 'ASC')->get();
        $dataBrand = Brand::orderBy('name_brand', 'ASC')->get();
        $dataUnit = Unit::orderBy('name_unit', 'ASC')->get();
        return view('master-data.product.create',compact('dataCategory','dataBrand','dataUnit'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $data = new Product();
                $data->name_product = $request->name_product;
                $data->brand_product = $request->brand_product;
                $data->category_product = $request->category_product;
                $data->unit_product = $request->unit_product;
                $data->hpp_product = rupiah_value($request->hpp_product);
                $data->price_product = rupiah_value($request->price_product);
                $data->biaya_rental_product = rupiah_value($request->biaya_rental_product);

                //gambar product
                if ($request->file('image_product')) {

                    File::delete(public_path("/file/product/".$data->image_product));

                    $file = $request->file('image_product');
                    $infoExtension = $file->getClientOriginalExtension();

                    $imgName = str_replace(" ", "-", $file->getClientOriginalName());
                    $imgName = date('YmdHis') . '-' . $imgName;

                    $destinationPath = public_path('/file/product/');
                    $file->move($destinationPath, $imgName);

                    if ($infoExtension == 'jpeg') {
                        $data->image_product = str_replace("jpeg","webp",$imgName);
                    }
                    if ($infoExtension == 'bmp') {
                        $data->image_product = str_replace("bmp","webp",$imgName);
                    }
                    if ($infoExtension == 'png') {
                        $data->image_product = str_replace("png","webp",$imgName);
                    }
                    if ($infoExtension == 'jpg') {
                        $data->image_product = str_replace("jpg","webp",$imgName);
                    }


                    $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $imgName);
                    $image = Image::make(public_path("/file/product/".$imgName))
                                ->encode('webp', 90)
                                ->save(public_path('/file/product/') . $fileNameNoExtension.'.webp');

                    File::delete(public_path("/file/product/".$imgName));
                }


                $data->save();

                $dataLog = $data;
                insert_log('Add Product','Product',$dataLog->getKey(),json_encode($dataLog));
                DB::commit();
                return response()->json(['status' => true]);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $get_data = Product::find($id);
        return view('master-data.product.show', compact('get_data'));
    }

    public function edit($id)
    {
        $get_data = Product::find($id);
        $dataCategory = Category::orderBy('name_category', 'ASC')->get();
        $dataBrand = Brand::orderBy('name_brand', 'ASC')->get();
        $dataUnit = Unit::orderBy('name_unit', 'ASC')->get();
        return view('master-data.product.edit', compact('get_data','dataCategory','dataBrand','dataUnit'));
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all(), $id);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $data = Product::find($id);
                $data->name_product = $request->name_product;
                $data->brand_product = $request->brand_product;
                $data->category_product = $request->category_product;
                $data->unit_product = $request->unit_product;
                $data->hpp_product = rupiah_value($request->hpp_product);
                $data->price_product = rupiah_value($request->price_product);
                $data->biaya_rental_product = rupiah_value($request->biaya_rental_product);

                //gambar product
                if ($request->file('image_product')) {

                    File::delete(public_path("/file/product/".$data->image_product));

                    $file = $request->file('image_product');
                    $infoExtension = $file->getClientOriginalExtension();

                    $imgName = str_replace(" ", "-", $file->getClientOriginalName());
                    $imgName = date('YmdHis') . '-' . $imgName;

                    $destinationPath = public_path('/file/product/');
                    $file->move($destinationPath, $imgName);

                    if ($infoExtension == 'jpeg') {
                        $data->image_product = str_replace("jpeg","webp",$imgName);
                    }
                    if ($infoExtension == 'bmp') {
                        $data->image_product = str_replace("bmp","webp",$imgName);
                    }
                    if ($infoExtension == 'png') {
                        $data->image_product = str_replace("png","webp",$imgName);
                    }
                    if ($infoExtension == 'jpg') {
                        $data->image_product = str_replace("jpg","webp",$imgName);
                    }


                    $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $imgName);
                    $image = Image::make(public_path("/file/product/".$imgName))
                                ->encode('webp', 90)
                                ->save(public_path('/file/product/') . $fileNameNoExtension.'.webp');

                    File::delete(public_path("/file/product/".$imgName));
                }


                $data->save();

                $dataLog = $data;
                insert_log('Update Product','Product',$dataLog->getKey(),json_encode($dataLog));
                DB::commit();
                return response()->json(['status' => true]);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Product::find($id);
            $dataLog = $data;
            insert_log('Delete Product','Product',$dataLog->getKey(),json_encode($dataLog));
            Product::destroy($id);
            DB::commit();
            return response()->json(['status' => true]);
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
