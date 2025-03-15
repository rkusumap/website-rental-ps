<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Module;
use App\Models\Role;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use File;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'name_company' => 'required',

            'phone_company' => 'required',
            'address_company' => 'required',
            'description_company' => 'required',
            'logo' => 'nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG',

        ];
        $pesan = [
            'name_company.required' => 'Nama Perusahaan Wajib di isi',

            'phone_company.required' => 'Nomor Telepon Wajib di isi',
            'address_company.required' => 'Alamat Wajib di isi',
            'description_company.required' => 'Deskripsi Wajib di isi',
            'logo.mimes' => 'Gambar tidak sesuai format',


        ];
        return Validator::make($request, $rule, $pesan);
    }
    public function index() {
        $get_data = Company::find(auth()->user()->company_user);

        return view('master-data.company', compact('get_data'));
    }

    public function update($id,Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data = Company::find($id);
                $new_data->name_company = $request->name_company;
                $new_data->phone_company = $request->phone_company;
                $new_data->address_company = $request->address_company;
                $new_data->description_company = $request->description_company;
                if ($request->file('logo')) {

                    File::delete(public_path("/static/".$new_data->logo_company));

                    $file = $request->file('logo');
                    $infoExtension = $file->getClientOriginalExtension();
                    $uuid = (string) Str::uuid();
                    $imgName = str_replace(" ", "-", $file->getClientOriginalName());
                    $imgName =  $uuid. '-' . date('YmdHis') . '-' . $imgName;

                    $destinationPath = public_path('/static/');
                    $file->move($destinationPath, $imgName);

                    if ($infoExtension == 'jpeg') {
                        $new_data->logo_company = str_replace("jpeg","webp",$imgName);
                    }
                    if ($infoExtension == 'bmp') {
                        $new_data->logo_company = str_replace("bmp","webp",$imgName);
                    }
                    if ($infoExtension == 'png') {
                        $new_data->logo_company = str_replace("png","webp",$imgName);
                    }
                    if ($infoExtension == 'jpg') {
                        $new_data->logo_company = str_replace("jpg","webp",$imgName);
                    }


                    $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $imgName);
                    $image = Image::make(public_path("/static/".$imgName))
                                ->encode('webp', 90)
                                ->save(public_path('/static/') . $fileNameNoExtension.'.webp');

                    File::delete(public_path("/static/".$imgName));
                }

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Company','Company',$dataLog->getKey(),json_encode($dataLog));

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
}
