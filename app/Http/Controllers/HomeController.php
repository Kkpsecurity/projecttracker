<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public $filePath = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->filePath = 'project/';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $oppStatus = ['New Lead', 'Proposal Sent', 'Contracting Now'];
        $activeStatus = ['Active'];
        $closedStatus = ['Closed'];
        $completedStatus = ['Completed'];

        $segment = $request->segment(4);
        if ($segment == 'opp') {
            $status = $oppStatus;
        } elseif ($segment == 'active') {
            $status = $activeStatus;
        } elseif ($segment == 'closed') {
            $status = $closedStatus;
        } elseif ($segment == 'completed') {
            $status = $completedStatus;
        } else {
            $status = $oppStatus;
        }

        $clients = Client::whereIn('quick_status', $status)->orderBy('updated_at', 'desc')->paginate(10);

        $content = [
            'clients' => $clients,
        ];

        return view('admin.protrack.home_new', compact('content'));
    }

    public function process(Request $request)
    {
        $client = new Client;

        if (! $client) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found',
                ], 404);
            } else {
                flash('Client not found')->error();
            }
        }

        $validator = Validator::make($request->all(), [
            'corporate_name' => 'required|string',
            'client_name' => 'required|string',
            'project_name' => 'required|string',
            'status' => 'required|string',
            'quick_status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errorMessage = 'Please fill all required fields: '.$validator->errors();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $validator->errors(),
                ], 400);
            } else {
                flash($errorMessage)->error();
            }
        }

        $client->corporate_name = $request->corporate_name;
        $client->client_name = $request->client_name;
        $client->project_name = $request->project_name;
        $client->poc = $request->poc;
        $client->description = $request->description;

        $client->project_services_total = ! empty($request->project_services_total) ? str_replace(['$', ','], '', $request->project_services_total) : 0;
        $client->project_expenses_total = ! empty($request->project_expenses_total) ? str_replace(['$', ','], '', $request->project_expenses_total) : 0;
        $client->final_services_total = ! empty($request->final_services_total) ? str_replace(['$', ','], '', $request->final_services_total) : 0;
        $client->final_billing_total = ! empty($request->final_billing_total) ? str_replace(['$', ','], '', $request->final_billing_total) : 0;

        $client->status = $request->status;
        $client->quick_status = $request->quick_status ?? 'New Lead';
        $client->updated_at = now();
        $client->save();

        $data = [
            'success' => true,
            'message' => 'Updated successfully',
        ];

        if ($request->ajax()) {
            return response()->json($data);
        } else {
            flash('Updated successfully')->success();
        }
    }

    public function detail($id)
    {
        $client = Client::find($id);

        $content = [
            'client' => $client,
        ];

        return view('admin.protrack.detail', compact('content'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detailProcess(Request $request)
    {
        // create validation rules
        $rules = [
            'corporate_name' => 'required',
            'client_name' => 'required',
            'project_name' => 'required',
            'poc' => 'required',
            'status' => 'required',
            'quick_status' => 'required',
        ];

        // create custom validation messages
        $messages = [
            'corporate_name.required' => 'Corporate Name is required',
            'client_name.required' => 'Client Name is required',
            'project_name.required' => 'Project Name is required',
            'poc.required' => 'Point of Contact is required',
            'quick_status.required' => 'Quick Status is required',
        ];

        // validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        // if validation fails
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                flash('Please fill all required fields: '.$validator->errors())->error();

                return Redirect::back()->withErrors($validator)->withInput();
            }
        }

        $client = Client::find($request->id);

        $client->corporate_name = $request->corporate_name;
        $client->client_name = $request->client_name;
        $client->project_name = $request->project_name;
        $client->poc = $request->poc;
        $client->description = $request->description;
        $client->quick_status = $request->quick_status;
        $client->status = $request->status;

        $client->project_services_total = ! empty($request->project_services_total) ? str_replace(['$', ','], '', $request->project_services_total) : 0;
        $client->project_expenses_total = ! empty($request->project_expenses_total) ? str_replace(['$', ','], '', $request->project_expenses_total) : 0;
        $client->final_services_total = ! empty($request->final_services_total) ? str_replace(['$', ','], '', $request->final_services_total) : 0;
        $client->final_billing_total = ! empty($request->final_billing_total) ? str_replace(['$', ','], '', $request->final_billing_total) : 0;

        if ($request->hasFile('file1')) {
            $file1 = $request->file('file1')->storeAs($this->filePath, $request->file('file1')->getClientOriginalName());
            $client->file1 = $request->file('file1')->getClientOriginalName();
        }
        if ($request->hasFile('file2')) {
            $file1 = $request->file('file2')->storeAs($this->filePath, $request->file('file2')->getClientOriginalName());
            $client->file2 = $request->file('file2')->getClientOriginalName();
        }
        if ($request->hasFile('file3')) {
            $file3 = $request->file('file3')->storeAs($this->filePath, $request->file('file3')->getClientOriginalName());
            $client->file3 = $request->file('file3')->getClientOriginalName();
        }

        $client->updated_at = now();
        $client->save();

        $data['success'] = true;
        $data['message'] = 'Updated Successfully';

        if ($request->ajax()) {
            return response()->json($data);
        } else {
            flash('Updated successfully')->success();

            return redirect()->back()->with('success', $data['message']);
        }
    }

    public function detachFile($file, $id)
    {
        $client = Client::find($id);

        Storage::disk('public')->delete($this->filePath.$file);

        $client->{$file} = null;
        $client->save();

        return Redirect()->back();
    }

    public function downloadFile($filename)
    {
        $file = Storage::disk('public')->path($this->filePath.$filename);

        return response()->download($file);
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        $client->delete();

        // remove the actual file
        if ($client->file1) {
            Storage::disk(config('filesystems.default'))->delete($this->filePath.$client->file1);
        }

        if ($client->file2) {
            Storage::disk(config('filesystems.default'))->delete($this->filePath.$client->file2);
        }

        if ($client->file3) {
            Storage::disk(config('filesystems.default'))->delete($this->filePath.$client->file3);
        }

        flash(__('You have deleted successfully'))->success();

        return Redirect('home/tabs/active');
    }

    public function changePassword()
    {
        $profile = Auth()->user();
        $content = [
            'profile' => $profile,
        ];

        return view('change_password', compact('content'));
    }

    public function passwordProcess(Request $request)
    {
        $user = Auth::user();

        $validation = Validator::make($request->all(), [
            'password' => 'required|confirmed',
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

    /**
     * DataTables AJAX endpoint for ProTrack projects
     */
    public function datatable(Request $request, $tab = 'opp')
    {
        $oppStatus = ['New Lead', 'Proposal Sent', 'Contracting Now'];
        $activeStatus = ['Active'];
        $closedStatus = ['Closed'];
        $completedStatus = ['Completed'];

        // Determine status array based on tab
        switch ($tab) {
            case 'active':
                $status = $activeStatus;
                break;
            case 'closed':
                $status = $closedStatus;
                break;
            case 'completed':
                $status = $completedStatus;
                break;
            default:
                $status = $oppStatus;
                break;
        }

        $query = Client::whereIn('quick_status', $status);

        // Handle DataTables search
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('corporate_name', 'ILIKE', "%{$search}%")
                  ->orWhere('client_name', 'ILIKE', "%{$search}%")
                  ->orWhere('project_name', 'ILIKE', "%{$search}%")
                  ->orWhere('status', 'ILIKE', "%{$search}%")
                  ->orWhere('quick_status', 'ILIKE', "%{$search}%");
            });
        }

        // Handle individual column search
        if ($request->filled('columns')) {
            foreach ($request->input('columns') as $index => $column) {
                if (!empty($column['search']['value'])) {
                    $searchValue = $column['search']['value'];
                    switch ($index) {
                        case 0: // corporate_name
                            $query->where('corporate_name', 'ILIKE', "%{$searchValue}%");
                            break;
                        case 1: // client_name
                            $query->where('client_name', 'ILIKE', "%{$searchValue}%");
                            break;
                        case 2: // project_name
                            $query->where('project_name', 'ILIKE', "%{$searchValue}%");
                            break;
                        case 3: // status
                            $query->where('status', 'ILIKE', "%{$searchValue}%");
                            break;
                        case 4: // quick_status
                            $query->where('quick_status', 'ILIKE', "%{$searchValue}%");
                            break;
                    }
                }
            }
        }

        // Handle sorting
        if ($request->filled('order')) {
            $orderColumn = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir');
            
            $columns = ['corporate_name', 'client_name', 'project_name', 'status', 'quick_status', 'updated_at', 'actions'];
            
            if (isset($columns[$orderColumn]) && $columns[$orderColumn] !== 'actions') {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        // Get total count before pagination
        $totalRecords = Client::count();
        $filteredRecords = $query->count();

        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        
        if ($length != -1) {
            $query->offset($start)->limit($length);
        }

        $records = $query->get();

        // Format data for DataTables
        $data = $records->map(function ($record) {
            return [
                'corporate_name' => '<strong>' . htmlspecialchars($record->corporate_name ?? '') . '</strong>',
                'client_name' => htmlspecialchars($record->client_name ?? ''),
                'project_name' => htmlspecialchars($record->project_name ?? ''),
                'status' => $this->formatStatus($record->status ?? 'Unknown'),
                'quick_status' => $this->formatQuickStatus($record->quick_status ?? 'Unknown'),
                'updated_at' => '<small class="text-muted">' . $record->updated_at->diffForHumans() . '</small>',
                'actions' => $this->formatProTrackActions($record)
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Format status badge for ProTrack
     */
    private function formatStatus($status)
    {
        $status = $status ?? 'Unknown';
        $badgeClass = match(strtolower($status)) {
            'active' => 'success',
            'completed' => 'primary',
            'closed' => 'secondary',
            default => 'info'
        };
        
        return '<span class="badge badge-' . $badgeClass . '">' . htmlspecialchars($status) . '</span>';
    }

    /**
     * Format quick status badge for ProTrack
     */
    private function formatQuickStatus($quickStatus)
    {
        $quickStatus = $quickStatus ?? 'Unknown';
        $badgeClass = match(strtolower($quickStatus)) {
            'new lead' => 'warning',
            'proposal sent' => 'info',
            'contracting now' => 'primary',
            'active' => 'success',
            'completed' => 'primary',
            'closed' => 'secondary',
            default => 'light'
        };
        
        return '<span class="badge badge-' . $badgeClass . '">' . htmlspecialchars($quickStatus) . '</span>';
    }

    /**
     * Format action buttons for ProTrack
     */
    private function formatProTrackActions($record)
    {
        return '<div class="btn-group btn-group-sm">' .
               '<a href="' . route('admin.home.detail', $record->id) . '" class="btn btn-info btn-sm" title="View Details">' .
               '<i class="fas fa-eye"></i></a>' .
               '<a href="' . route('admin.home.detail', $record->id) . '" class="btn btn-warning btn-sm" title="Edit">' .
               '<i class="fas fa-edit"></i></a>' .
               '<a href="' . route('admin.home.detail.delete', $record->id) . '" class="btn btn-danger btn-sm btn-delete" title="Delete">' .
               '<i class="fas fa-trash"></i></a></div>';
    }
}
