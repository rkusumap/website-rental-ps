<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Rental;
use App\Models\RentalDetail;
use App\Models\MethodPayment;
use App\Models\RentalMinggu;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Validator;
use DataTables;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use File;
use Carbon\Carbon;
use Auth;

class RentalController extends Controller
{
    public function rules($request, $id = null)
    {
        $rule = [
            'jenis_ps' => 'required',
            'tanggal_awal' => 'required',
            'tanggal_akhir' => 'required',
            'biaya' => 'required',

        ];
        $pesan = [
            'jenis_ps.required' => 'Jenis PS Wajib di isi',
            'tanggal_awal.required' => 'Tanggal Awal Wajib di isi',
            'tanggal_akhir.required' => 'Tanggal Akhir Wajib di isi',
            'biaya.required' => 'Biaya Wajib di isi',
        ];
        return Validator::make($request, $rule, $pesan);
    }
    public function index(Request $request) {
        $get_module = get_module_id('sewa-ps');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }
        $dataProduct = Product::orderBy('name_product','asc')->get();
        $dataRentalMinggu = RentalMinggu::first();
        return view('rental.index', compact('get_module','dataProduct','dataRentalMinggu'));
    }

    public function getEvents(Request $request)
    {

        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $events = array();
        if (Auth::user()->role->code_level == "CUSTOMER") {

            $events = Rental::where(function ($query) use ($start, $end) {
                            $query->whereBetween('date_start_rental', [$start, $end])
                                ->orWhereBetween('date_akhir_rental', [$start, $end]);
                        })
                        ->where('user_rental', Auth::user()->id)
                        ->get();

        }

        else if(Auth::user()->role->code_level == "ADM" ){
            $events = Rental::whereBetween('date_start_rental', [$start, $end])
                        ->orWhereBetween('date_akhir_rental', [$start, $end])
                        ->get();
        }


        $formattedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id_rental,
                'title' => $event->title_rental,
                'start' => $event->date_start_rental,
                'end' => $event->date_akhir_rental."T12:00:00",
            ];
        });

        return response()->json($formattedEvents);
    }

    public function getByDate(Request $request) {
        $tanggal = $request->query('start');

        $dataIdRental = array();
        $dataRentalDetail = RentalDetail::where('date_rtd', $tanggal)->get();

        foreach ($dataRentalDetail as $key => $value) {
            if (count($dataIdRental) == 0) {
                $dataIdRental[] = $value->rental_rtd;
            }
            else{
                if (!in_array($value->rental_rtd, $dataIdRental)) {
                    $dataIdRental[] = $value->rental_rtd;
                }
            }
        }

        // $dataRental = Rental::whereIn('id_rental', $dataIdRental)->get();
        // foreach ($dataRental as $rental) {
        //     $dataRD = RentalDetail::where('rental_rtd', $rental->id_rental)->get();
        //     foreach ($dataRD as $drd) {
        //         $dataNRD = RentalDetail::where('date_rtd', $drd->date_rtd)->get();
        //         foreach ($dataNRD as $nrd) {
        //             if (count($dataIdRental) == 0) {
        //                 $dataIdRental[] = $nrd->rental_rtd;
        //             }
        //             else{
        //                 if (!in_array($nrd->rental_rtd, $dataIdRental)) {
        //                     $dataIdRental[] = $nrd->rental_rtd;
        //                 }
        //             }
        //         }
        //     }
        // }
        $events = Rental::whereIn('id_rental', $dataIdRental);
        if (Auth::user()->role->code_level == "CUSTOMER") {
            $events = $events->where('user_rental', Auth::user()->id);
        }
        $events = $events->get();
        if ($events->isEmpty()) {
            return response()->json(['message' => 'No events found'], 404);
        }

        $formattedEvents = $events->map(function ($event) {
            return [
                'user' => $event->user->name,
                'code_rental' => $event->code_rental,
                'tanggal' => fdate($event->date_start_rental,'DDMMYYYY').' - '.fdate($event->date_akhir_rental,'DDMMYYYY'),
                'biaya' => rupiah_format($event->grand_total_rental),
                'status_bayar' => $event->payment_status_rental,
                'status_rental' => reference('status_rental',strval($event->return_status_rental)),
                'snap_token' => $event->snap_token,
                'id_rental' => $event->id_rental,
                'd_none_bayar' => reference('status_bayar_d_none',strval($event->payment_status_rental)),
                'warna_bayar' => reference('status_bayar_warna',strval($event->payment_status_rental)),

            ];
        });

        // dd($formattedEvents);

        return response()->json($formattedEvents);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $dataProduct = Product::find($request->jenis_ps);
                $dataRentalMinggu = RentalMinggu::first();

                $new_data = new Rental();
                $new_data->code_rental = $this->getCode();
                $new_data->user_rental = Auth::user()->id;
                $new_data->title_rental = $dataProduct->name_product . ' - '. Auth::user()->name;
                $new_data->one_day_rental = $request->satuhari;
                $new_data->date_start_rental = $request->tanggal_awal;
                $new_data->date_akhir_rental = $request->tanggal_akhir;
                $new_data->grand_total_rental = rupiah_value($request->biaya);

                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add Rental','Rental',$dataLog->getKey(),json_encode($dataLog));

                for ($i=0; $i < $request->total_days; $i++) {
                    $new_data_detail = new RentalDetail();
                    $new_data_detail->rental_rtd = $new_data->id_rental;
                    $new_data_detail->product_rtd = $request->jenis_ps;

                    // Calculate date and check if it's Saturday or Sunday
                    $date = Carbon::parse($request->tanggal_awal)->addDays($i);
                    $new_data_detail->date_rtd = $date;

                    // Base price
                    $price = $dataProduct->biaya_rental_product;

                    // If the day is Saturday (6) or Sunday (0), add 5000
                    if ($date->dayOfWeek == Carbon::SATURDAY || $date->dayOfWeek == Carbon::SUNDAY) {
                        $price += $dataRentalMinggu->biaya_rm;
                    }

                    $new_data_detail->price_rtd = $price;
                    $new_data_detail->save();
                }

                // Set your Merchant Server Key
                \Midtrans\Config::$serverKey = config('midtrans.serverKey');
                // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
                \Midtrans\Config::$isProduction = false;
                // Set sanitization on (default)
                \Midtrans\Config::$isSanitized = true;
                // Set 3DS transaction for credit card to true
                \Midtrans\Config::$is3ds = true;
                $order_id = rand();
                $params = array(
                    'transaction_details' => array(
                        'order_id' => $order_id,
                        'gross_amount' => $new_data->grand_total_rental,
                    ),
                    'customer_details' => array(
                        'first_name' => Auth::user()->name,
                        'last_name' => '',
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->customer->phone_customer,
                    )
                );

                $snapToken = \Midtrans\Snap::getSnapToken($params);

                $updateRental = Rental::find($new_data->id_rental);
                $updateRental->snap_token = $snapToken;
                $updateRental->order_id = $order_id;
                $updateRental->payment_status_rental = 'pending';
                $updateRental->save();



                DB::commit();
                return response()->json(['status' => true, 'pesan' => 'Data berhasil disimpan']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'pesan' => $e->getMessage()]);
        }
    }

    public function getCode()
     {
        $x = 1;
        do {
            $rentalmaxcheck = Rental::first();

            if($rentalmaxcheck == NULL)
            {
                $code_lastest = 0;
            }else{
                $transmax = Rental::first()->max('code_rental');
                $code_lastest = substr($transmax,8);
            }

            $code_new = date('Ymd').sprintf('%05d', $code_lastest+$x);
            $check = Rental::where('code_rental',$code_new)->count();
            $x++;
        } while ($check > 0);
        return $code_new;
    }

    public function check_stock($id_product) {
        if ($id_product != 'kosong') {
            $stock_product = check_stock($id_product);
            if ($stock_product < 1) {
                return response()->json(['status' => false, 'pesan' => 'Stok PS tidak mencukupi']);
            }

            return response()->json(['status' => true, 'pesan' => 'Stok PS mencukupi']);
        }
    }
}
