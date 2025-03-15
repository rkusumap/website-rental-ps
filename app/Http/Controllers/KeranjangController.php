<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\RentalDetail;
use App\Models\Product;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'id' => 'required'
        ];
        $pesan = [
            'id.required' => 'Id Wajib di isi'
        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index() {

        $get_module = get_module_id('keranjang');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        return view('rental.keranjang', compact('get_module'));
    }

    public function json()
    {
        $datas = Rental::with(['user', 'rental_detail_one.product'])
                ->select('tb_rental.*')
                ->orderBy('created_at', 'desc')
                ->where('user_rental', auth()->user()->id);


        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('keranjang');

                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {
                        $btn_detail = '<button type="button" data-id="' . $data->id_rental . '"  class="btn btn-sm btn-info btn-detail">Detail</button>';
                }

                //bayar
                $btn_bayar = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    if ($data->payment_status_rental == 'pending') {
                        $btn_bayar = '<button type="button" data-id="' . $data->id_rental . '" data-snap="'.$data->snap_token.'" class="btn btn-sm btn-success btn-bayar">Bayar</button>';
                    }
                }

                return '
                <div class="btn-group">
                    '. $btn_detail .'
                    ' . $btn_bayar . '
                </div>
              ';
            })
            ->addColumn('tanggal', function ($data) {
                return fdate($data->date_start_rental, 'DDMMYYYY').' - '. fdate($data->date_akhir_rental, 'DDMMYYYY');
            })

            ->addColumn('status_bayar', function ($data) {
                if ($data->payment_status_rental == 'pending') {
                    return '<span class="badge badge-warning">Pending</span>';
                }
                if ($data->payment_status_rental == 'success') {
                    return '<span class="badge badge-success">Success</span>';
                }

            })

            ->addColumn('status_rental', function ($data) {
                return reference('status_rental', strval($data->return_status_rental));
            })

            ->addColumn('biaya', function ($data) {
                return 'Rp '.number_format($data->grand_total_rental, 0, ',', '.');
            })
            ->rawColumns(['action', 'status_bayar'])
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function show($id)
    {
        // dd($id);
        $get_data = Rental::where('id_rental', $id)->with(['rental_detail_one.product', 'user'])->first();
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $status = \Midtrans\Transaction::status($get_data->order_id);
        if ($get_data->one_day_rental == 1) {
            $formated_date = fdate($get_data->date_start_rental, 'DDMMYYYY');
        }
        else{
            $formated_date = fdate($get_data->date_start_rental, 'DDMMYYYY') .' - '. fdate($get_data->date_akhir_rental, 'DDMMYYYY');
        }
        $total = rupiah_format($get_data->grand_total_rental);
        return response()->json([
            'status' => true,
            'data' => $get_data,
            'status' => $status,
            'formated_date' => $formated_date,
            'total' => $total
        ]);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $new_data =  Rental::find($request->id);
                $new_data->payment_status_rental = 'success';
                $new_data->save();

                $dataLog = $new_data;
                insert_log('Update Payment Rental','Rental',$dataLog->getKey(),json_encode($dataLog));

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


}
