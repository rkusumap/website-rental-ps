<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Options;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use File;
use Illuminate\Support\Str;


class OptionController extends Controller
{

    //rules
    public function rules($request)
    {
        $rule = [
            'name' => 'required',
            'description' => 'required',
            'logo' => 'nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG',
        ];
        $pesan = [
            'name.required' => 'Nama Website Wajib di isi',
            'description.required' => 'Deskripsi Website Wajib di isi',
            'address_employee.required' => 'Alamat Wajib di isi',
            'logo.mimes' => 'Gambar tidak sesuai format',
        ];

        return Validator::make($request, $rule, $pesan);
    }
    public function index()
    {
        $get_data = Options::first();
        $get_module = get_module_id('option');
        return view('option.index', compact('get_data', 'get_module'));
    }

    public function store(Request $request)
    {
        $set_data = Options::first();
        if ($set_data) {
            $data = Options::find($set_data->id_option);
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $data->name = $request->name;
                $data->description = $request->description;
                //logo
                if ($request->file('logo')) {

                    File::delete(public_path("/static/".$data->logo));

                    $file = $request->file('logo');
                    $infoExtension = $file->getClientOriginalExtension();
                    $uuid = (string) Str::uuid();
                    $imgName = str_replace(" ", "-", $file->getClientOriginalName());
                    $imgName =  $uuid. '-' . date('YmdHis') . '-' . $imgName;

                    $destinationPath = public_path('/static/');
                    $file->move($destinationPath, $imgName);

                    if ($infoExtension == 'jpeg') {
                        $data->logo = str_replace("jpeg","webp",$imgName);
                    }
                    if ($infoExtension == 'bmp') {
                        $data->logo = str_replace("bmp","webp",$imgName);
                    }
                    if ($infoExtension == 'png') {
                        $data->logo = str_replace("png","webp",$imgName);
                    }
                    if ($infoExtension == 'jpg') {
                        $data->logo = str_replace("jpg","webp",$imgName);
                    }


                    $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $imgName);
                    $image = Image::make(public_path("/static/".$imgName))
                                ->encode('webp', 90)
                                ->save(public_path('/static/') . $fileNameNoExtension.'.webp');

                    File::delete(public_path("/static/".$imgName));
                }

                $data->save();
            }
        } else {
            $data = new Options();

            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $data->name = $request->name;
                $data->description = $request->description;

                //logo
                if ($request->file('logo')) {

                    File::delete(public_path("/static/".$data->logo));

                    $file = $request->file('logo');
                    $infoExtension = $file->getClientOriginalExtension();
                    $uuid = (string) Str::uuid();
                    $imgName = str_replace(" ", "-", $file->getClientOriginalName());
                    $imgName =  $uuid. '-' . date('YmdHis') . '-' . $imgName;

                    $destinationPath = public_path('/static/');
                    $file->move($destinationPath, $imgName);

                    if ($infoExtension == 'jpeg') {
                        $data->logo = str_replace("jpeg","webp",$imgName);
                    }
                    if ($infoExtension == 'bmp') {
                        $data->logo = str_replace("bmp","webp",$imgName);
                    }
                    if ($infoExtension == 'png') {
                        $data->logo = str_replace("png","webp",$imgName);
                    }
                    if ($infoExtension == 'jpg') {
                        $data->logo = str_replace("jpg","webp",$imgName);
                    }


                    $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $imgName);
                    $image = Image::make(public_path("/static/".$imgName))
                                ->encode('webp', 90)
                                ->save(public_path('/static/') . $fileNameNoExtension.'.webp');

                    File::delete(public_path("/static/".$imgName));
                }
                $data->save();
            }
        }

        return response()->json(['status' => true]);
    }

}
