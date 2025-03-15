<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\Facades\Image;
use File;
use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'name_category' => 'required',
            'image_category' => 'nullable|mimes:jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP',
        ];
        $pesan = [
            'name_category.required' => 'Nama Kategori Wajib di isi',
            'image_category.mimes' => 'Format yang didukung jpg,jpeg,png,webp',
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index() {
        $get_module = get_module_id('category');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        return view('master-data.category', compact('get_module'));
    }

    public function json()
    {
        $datas = Category::select(['id_category', 'name_category','image_category', 'updated_at'])->orderBy('name_category', 'asc');

        return Datatables::of($datas)
            ->addColumn('image', function ($data) {

                $imagePath = public_path('file/category/' . $data->image_category);

                // Check if the image file exists; if not, use default.png
                if ($data->image_category && file_exists($imagePath)) {
                    $imageUrl = asset('file/category/' . $data->image_category);
                } else {
                    $imageUrl = asset('file/category/default.webp');
                }

                return '<img src="' . $imageUrl . '" class="img-thumbnail img-click" data-image="' . $imageUrl . '" width="50" height="50" style="cursor: pointer;">';
            })
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('category');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {

                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" data-id="' . $data->id_category . '" class="btn btn-sm btn-info btn-edit">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    $btn_hapus = '<button type="button" data-nama="'.$data->name_category.'" data-id="' . $data->id_category . '" class="btn btn-sm btn-danger btn-hapus">Hapus</button>';
                }
                return '
                <div class="btn-group">
                    ' . $btn_edit . '

                    ' . $btn_hapus . '
                </div>
              ';
            })
            ->rawColumns(['image', 'action']) // Ensure HTML is rendered
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function show($id)
    {
        $get_data = Category::find($id);
        $get_data->image_category = $get_data->image;
        return response()->json(['status' => true, 'data' => $get_data]);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = new Category();
                $new_data->name_category = $request->name_category;

                //gambar category
                if ($request->file('image_category')) {

                    File::delete(public_path("/file/category/".$new_data->image_category));

                    $file = $request->file('image_category');
                    $infoExtension = $file->getClientOriginalExtension();

                    $imgName = str_replace(" ", "-", $file->getClientOriginalName());
                    $imgName = date('YmdHis') . '-' . $imgName;

                    $destinationPath = public_path('/file/category/');
                    $file->move($destinationPath, $imgName);

                    if ($infoExtension == 'jpeg') {
                        $new_data->image_category = str_replace("jpeg","webp",$imgName);
                    }
                    if ($infoExtension == 'bmp') {
                        $new_data->image_category = str_replace("bmp","webp",$imgName);
                    }
                    if ($infoExtension == 'png') {
                        $new_data->image_category = str_replace("png","webp",$imgName);
                    }
                    if ($infoExtension == 'jpg') {
                        $new_data->image_category = str_replace("jpg","webp",$imgName);
                    }


                    $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $imgName);
                    $image = Image::make(public_path("/file/category/".$imgName))
                                ->encode('webp', 90)
                                ->save(public_path('/file/category/') . $fileNameNoExtension.'.webp');

                    File::delete(public_path("/file/category/".$imgName));
                }


                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add Category','Category',$dataLog->getKey(),json_encode($dataLog));

                DB::commit();
                return response()->json(['status' => true,'type' => 'add']);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update($id,Request $request) {
        DB::beginTransaction();
        try {

            $dataRequest = $request->all();
            if ($request->image_category == 'undefined') {
                unset($dataRequest['image_category']);
            }
            $validator = $this->rules($dataRequest);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = Category::find($id);
                $new_data->name_category = $request->name_category;

                //gambar category
                if ($request->file('image_category')) {

                    File::delete(public_path("/file/category/".$new_data->image_category));

                    $file = $request->file('image_category');
                    $infoExtension = $file->getClientOriginalExtension();

                    $imgName = str_replace(" ", "-", $file->getClientOriginalName());
                    $imgName = date('YmdHis') . '-' . $imgName;

                    $destinationPath = public_path('/file/category/');
                    $file->move($destinationPath, $imgName);

                    if ($infoExtension == 'jpeg') {
                        $new_data->image_category = str_replace("jpeg","webp",$imgName);
                    }
                    if ($infoExtension == 'bmp') {
                        $new_data->image_category = str_replace("bmp","webp",$imgName);
                    }
                    if ($infoExtension == 'png') {
                        $new_data->image_category = str_replace("png","webp",$imgName);
                    }
                    if ($infoExtension == 'jpg') {
                        $new_data->image_category = str_replace("jpg","webp",$imgName);
                    }


                    $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $imgName);
                    $image = Image::make(public_path("/file/category/".$imgName))
                                ->encode('webp', 90)
                                ->save(public_path('/file/category/') . $fileNameNoExtension.'.webp');

                    File::delete(public_path("/file/category/".$imgName));
                }



                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Category','Category',$dataLog->getKey(),json_encode($dataLog));

                DB::commit();
                return response()->json(['status' => true,'type' => 'edit']);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id) {
        DB::beginTransaction();
        try {
            $data = Category::find($id);
            $dataLog = $data;
            insert_log('Delete Category','Category',$dataLog->getKey(),json_encode($dataLog));
            Category::destroy($id);
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
