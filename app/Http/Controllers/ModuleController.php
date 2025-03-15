<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'name_module'        => 'required',
            'link_module'        => 'required',
            'icon_module'        => 'required',
            'order_module'       => 'required',
            'action_module'      => 'required',
        ];
        $pesan = [
            'name_module'        => 'Module Name Wajib Diisi',
            'link_module'        => 'Link Wajib Diisi',
            'icon_module'        => 'Icon Wajib Diisi',
            'order_module'       => 'Order Number Wajib Diisi',
            'action_module'      => 'Action Wajib Diisi',
        ];

        return Validator::make($request,$rule,$pesan);
    }

    public function index()
    {
        $get_module = get_module_id('module');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }
        $data = Module::select(['id_module','name_module','created_at','induk_module','link_module','order_module','updated_at'])->where('induk_module',"0")->orderBy('order_module','ASC')->get();
        return view('module.index',compact('data','get_module'));
    }

    public function create()
    {
        $modules = Module::where('induk_module', '0')->orderby('order_module', 'ASC')->get();
        return view('module.create',compact('modules'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status'=>false,'pesan'=>$validator->errors()]);
            }else{
                $data = new Module;
                $data->induk_module       = $request->post('induk_module') ?? 0;
                $data->code_module        = $request->post('code_module');
                $data->name_module        = $request->post('name_module');
                $data->link_module        = $request->post('link_module');
                $data->icon_module        = $request->post('icon_module');
                $data->order_module       = $request->post('order_module');
                $data->action_module      = $request->post('action_module');
                $data->description_module = $request->post('description_module');


                $data->save();

                $dataLog = $data;
                insert_log('Add Module','Module',$dataLog->getKey(),json_encode($dataLog));
                DB::commit();
                return response()->json(['status'=>true]);
            }

        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $modules = Module::where('induk_module', '0')->orderby('order_module', 'ASC')->get();
        $data = Module::find($id);
        return view('module.edit',compact('data','modules'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status'=>false,'pesan'=>$validator->errors()]);
            }else{
                $data = Module::find($id);
                $data->induk_module       = $request->post('induk_module') ?? 0;
                $data->code_module        = $request->post('code_module');
                $data->name_module        = $request->post('name_module');
                $data->link_module        = $request->post('link_module');
                $data->icon_module        = $request->post('icon_module');
                $data->order_module       = $request->post('order_module');
                $data->action_module      = $request->post('action_module');
                $data->description_module = $request->post('description_module');

                $data->save();

                $dataLog = $data;
                insert_log('Update Module','Module',$dataLog->getKey(),json_encode($dataLog));
                DB::commit();
                return response()->json(['status'=>true]);
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
        $data = Module::find($id);
        return view('module.show',compact('data'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Module::find($id);
            $dataLog = $data;
            insert_log('Delete Module','Module',$dataLog->getKey(),json_encode($dataLog));

            Module::destroy($id);
            DB::commit();
            return response()->json(['status'=>true]);
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function sort()
    {
        DB::beginTransaction();
        try {
            $sort = 1;
            foreach(request('main') as $key => $main)
            {
                if(is_array($main))
                {
                    $no = 1;
                    foreach ($main as $a => $b) {
                        $sortData[$b]['parent'] = $key;
                        $sortData[$b]['sort'] = $no;
                        $no++;
                    }
                }else{
                    $sortData[$main]['parent'] = "0";
                    $sortData[$main]['sort'] = $sort;
                    $sort++;
                }
            }

            foreach ($sortData as $id => $data) {
                $id = str_replace("mdl-","",$id);
                $parent = str_replace("mdl-","",$data['parent']);

                $set =  Module::find($id);
                $set->order_module = $data['sort'];
                $set->induk_module = $parent;
                $set->save();
            }
            $dataLog = $sortData;
            insert_log('Sort Module','Module',null,json_encode($dataLog));
            DB::commit();
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


}
