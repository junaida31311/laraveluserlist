<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userlist;
 use Validator;
class UserListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
          $data =  $this->curlCall();
          $data_array = json_decode($data);
          $fetchdata = userlist::all();
          if(!count($fetchdata) >0)
          {
            foreach ( $data_array as $value) {
            $list[] = [
                'userid' => $value->userId,
                'title' => $value->title,
                'body' => $value->body,            
                ];
            }
            userlist::insert($list);
            $fetchdata = userlist::all();
          }

          return view('userlist')->with('displaydata',$fetchdata);
        } catch (Exception $e) {
            
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
                if(request()->ajax())
                {
                $user = userlist::findOrFail($id);
                return response()->json(['edit_data' => $user]);
                }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

           $rules = array(
                'title'    =>  'required',
                'body'     =>  'required'
            );

            $error = Validator::make($request->all(), $rules);

            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

        $form_data = array(
            'title'       =>   $request->title,
            'body'        =>   $request->body,
           
        );
        userlist::whereId($request->id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = userlist::findOrFail($id);
        $data->delete();
    }



    public function curlCall()
    {
        try {

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://jsonplaceholder.typicode.com/posts",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",              
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              $response =  "cURL Error #:" . $err;
            }
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function exportData(Request $request)
    {
        $fileName = 'list.csv';
        //$tasks = Task::all();
        $filepath =  storage_path();
        $listofuser = userlist::whereIn('id', $request->list)->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('id', 'userid', 'title', 'body');

        $callback = function() use($listofuser, $columns) {
             $file = fopen('php://output', 'w');
            //$file = fopen($filepath, 'w');
            fputcsv($file, $columns);

            foreach ($listofuser as $list) {
                $row['id']  = $list->id;
                $row['userid']    = $list->userid;
                $row['title']    = $list->title;
                $row['body']  = $list->body;
               
                fputcsv($file, array($row['id'], $row['userid'], $row['title'], $row['body']));
            }

            fclose($file);
        };

         //return \Response::download($filepath, $fileName, $headers);
        return response()->stream($callback, 200, $headers);
        
        //return (new StreamedResponse($callback, 200, $headers))->sendContent();
    }
}
