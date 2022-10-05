<?php

namespace App\Http\Controllers\dashborad\admin;

use App\Exports\exportBatchReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Directorate;
use App\Models\gazLogs;
use App\Models\Rigon;
use Mpdf\Config\ConfigVariables;
use Excel;


class batchReportsController extends Controller
{
   
    
    public function index()
    {
        try
        {
            $data['directorates']=Directorate::select('id','dirName')->whereHas('rigon')->whereHas('agent')->orderby('id','DESC')->get();
            return view('dashboard.admin.reports.batchReport',$data);
        }
       catch (\Exception $ex)
        {
           return response()->json(
           [
               'status'         => false,
               'exceptionError' => $ex,
           ]);
        }
    }
    public function show(Request $request)
    {
        try
        {
            $gazLogs=gazLogs::with(
                [
                   'station'=>function($q)
                   {
                     $q->select('id','staName')->get();
                   }
                   ,'agent'=>function($q)
                   {
                     $q->select('id','agentName')->get();
                   }
                   ,'directorate'=>function($q)
                   {
                     $q->select('id','dirName')->get();
                   }
                   ,'rigon'=>function($q)
                   {
                     $q->select('id','rigName')->get();
                   }
                 ])->select('id','dirId','rigId','staId','agentId','created_at','qty')
                 ->where('agentId',$request->agentId)
                 ->where('dirId',$request->dirId)
                 ->where('rigId',$request->rigId)
                 ->whereBetween('created_at', [$request->dateForm, $request->dateTo])
                 ->get();
                 $batchCount=$gazLogs->count();
                 $batchResult=$gazLogs->sum('qty');
                 $allowBookingCount=$gazLogs->where('allowBooking','0')->count();
                 $dateForm = $request->dateForm;
                 $dateTo   = $request->dateTo;
                 if($gazLogs)
                 {
                    
                    return response()->json([
                        'status'            => true,
                        'msg'               => 'تم الحفظ بنجاح',
                        'gazLogs'           => $gazLogs,
                        'batchCount'        => $batchCount,
                        'batchResult'       => $batchResult,
                        'allowBookingCount' => $allowBookingCount,
                        'dateForm'          => $dateForm,
                        'dateTo'            => $dateTo,
                    
                    ]);

                 }

            
        }
       catch (\Exception $ex)
        {
           return response()->json(
           [
               'status'         => false,
               'exceptionError' => $ex,
           ]);
        }
    }
    
    public function showRigons(Request $request)
    {
         try
           {
       
                $rigons = Rigon::select('id','rigName')->where('dirId',$request->dirId)->whereHas('agent')->get();
                if($rigons)
                return response()->json([
                    'status' => true,
                    'rigons' => $rigons,
                ]);
           }
        catch (\Exception $ex)
            {
                return response()->json([
                    'status'            => false,
                    'msg'               => 'error in function showRigons',
                    'exceptionError'    => $ex,
                ]);
            }
    }
    public function showAgents(Request $request)
    {
        try
          {
        
                $agents = Agent::select('id','agentName')->where('rigId',$request->rigId)->orderby('id','DESC')->get();
                
                if($agents)
                return response()->json([
                    'status' => true,
                    'agents' => $agents,
                ]);
          }
        catch (\Exception $ex)
            {
                return response()->json([
                    'status'           => false,
                    'msg'              => 'error in function showAgents',
                    'exception Error'  =>$ex,
                ]);
            }
    }
    
    
    public function exportExcelBatch(Request $request)
    {
    
          return Excel::download(new exportBatchReport('2022-09-10','2022-09-07'),'salah.xlsx');        
      
    }
    
  
}