<?php

namespace App\Http\Controllers;

use App\Client;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $oppStatus = ['New Lead', 'Proposal Sent', 'Contracting Now'];
        $activeStatus = ['Active'];
        $closedStatus = ['Closed'];

        $segment = \Request::segment(3);
        if($segment == 'opp') {
            $status = $oppStatus;
        } elseif($segment == 'active') {
            $status = $activeStatus;
        } elseif ($segment == 'closed') {
            $status = $closedStatus;
        } else {
            $status = $oppStatus;
        }

        $clients = Client::whereIn('quick_status', $status)->orderBy('updated_at', 'desc')->paginate(10);

        $content = [
            'clients' => $clients
        ];

        return view('home', compact('content'));
    }

    public function process(Request $request)
    {

        $client = new Client();

        $client->corporate_name = $request->corporate_name;
        $client->client_name = $request->client_name;
        $client->project_name = $request->project_name;
        $client->poc = $request->poc;
        $client->description = $request->description;
        $client->status = $request->status;
        $client->quick_status = $request->quick_status ?? 'New Lead';
        $client->created_at = now();
        $client->updated_at = now();
        $client->save();

        $data['success'] = true;
        $data['message'] = 'Updated Successfully';

        return response()->json($data);
    }

    public function detail($id)
    {
        $client = Client::find($id);

        $content = [
            'client' => $client
        ];

        return view('detail', compact('content'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detailProcess(Request $request)
    {

       $client =  Client::find($request->id);

        $client->corporate_name = $request->corporate_name;
        $client->client_name = $request->client_name;
        $client->project_name = $request->project_name;
        $client->poc = $request->poc;
        $client->description = $request->description;
        $client->status = $request->status;
        $client->quick_status = $request->quick_status;

        if($request->hasFile('file1')) {
            $file1 =$request->file('file1')->storeAs('project', $request->file('file1')->getClientOriginalName());
            $client->file1 =    $request->file('file1')->getClientOriginalName();
        }
        if($request->hasFile('file2')) {
            $file1 = $request->file('file2')->storeAs('project', $request->file('file2')->getClientOriginalName());
            $client->file2 =    $request->file('file2')->getClientOriginalName();
        }
        if($request->hasFile('file3')) {
            $file3 = $request->file('file3')->storeAs('project', $request->file('file3')->getClientOriginalName());
            $client->file3 =    $request->file('file3')->getClientOriginalName();
        }

        $client->updated_at = now();
        $client->save();

        $data['success'] = true;
        $data['message'] = 'Updated Successfully';

        flash(__('You have updated successfullt'))->success();
        return redirect()->back();
    }

    public function detachFile($file , $id) {
        $client = Client::find($id);

        Storage::delete('app/project/' . $file);

        $client->{$file} = null;
        $client->save();

        return Redirect()->back();
    }

    function downloadFile($filename){
        $file = Storage::disk('public')->url($filename);
        return response()->download(storage_path("app/public/project/{$filename}"));
    }

    public function destroy($id) {
        $client = Client::find($id);
        $client->delete();

        return Redirect()->back();
    }

    public function changePassword() {
        $profile = Auth()->user();

        $content = [
            'profile' => $profile
        ];

        return view('change_password', compact('content'));
    }

    public function passwordProcess(Request $request) {
        $user = Auth::user();

        $validation = Validator::make($request->all(), [
            'password'      => 'required|confirmed'
        ]);

        if ($validation->fails()) {
            flash($validation->errors()->first())->error();
            return Redirect()->back();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        flash(__('You have updated your password'))->success();
        return redirect()->back();
    }






}
