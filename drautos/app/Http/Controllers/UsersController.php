<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::orderBy('id','ASC');
        
        if($request->has('city') && $request->city != null) {
            $query->where('city', $request->city);
        }

        // Filter by status if requested
        if($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Text Search
        if($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%")
                  ->orWhere('phone', 'LIKE', "%$search%");
            });
        }
        
        $users = $query->paginate(5000);
        $cities = User::select('city')->whereNotNull('city')->distinct()->pluck('city')->sort();
        $pendingCount = User::where('status', 'pending')->count();
        
        return view('backend.users.index')->with('users', $users)->with('cities', $cities)->with('pendingCount', $pendingCount);
    }

    public function updateRating(Request $request, $id) {
        $request->validate([
            'loyalty_rating' => 'nullable|integer|min:0|max:5',
            'goodwill_rating' => 'nullable|integer|min:0|max:5',
            'payment_rating' => 'nullable|integer|min:0|max:5',
            'behaviour_rating' => 'nullable|integer|min:0|max:5',
        ]);
        
        $user = User::findOrFail($id);
        $user->loyalty_rating = $request->loyalty_rating ?? 0;
        $user->goodwill_rating = $request->goodwill_rating ?? 0;
        $user->payment_rating = $request->payment_rating ?? 0;
        $user->behaviour_rating = $request->behaviour_rating ?? 0;
        $user->save();
        
        if($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Ratings updated successfully']);
        }
        
        return redirect()->back()->with('success', 'Ratings updated successfully');
    }

    /**
     * Approve a pending user registration and notify via WhatsApp.
     */
    public function approve(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        // Send WhatsApp approval notification
        if ($user->phone) {
            try {
                $whatsapp = new WhatsAppService();
                $msg = "Assalam-o-Alaikum " . strtoupper($user->name) . ",\n\n" .
                       "✅ Your registration request at Dr Auto Store has been APPROVED!\n\n" .
                       "You can now login using your registered phone number and password.\n\n" .
                       "Login at: " . url('/user/login') . "\n\n" .
                       "Welcome aboard! We look forward to serving you.\n\n" .
                       "Regards,\nDr Auto Store Team";
                $whatsapp->sendMessage($user->phone, $msg);
            } catch (\Exception $e) {
                \Log::warning('WhatsApp approval notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'User "' . $user->name . '" has been approved and notified via WhatsApp.');
    }

    /**
     * Show pending registration requests.
     */
    public function pendingRequests(Request $request)
    {
        $query = User::where('status', 'pending')->orderBy('id', 'DESC');

        if($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%")
                  ->orWhere('phone', 'LIKE', "%$search%");
            });
        }

        $users = $query->paginate(5000);
        $cities = User::select('city')->whereNotNull('city')->distinct()->pluck('city')->sort();
        $pendingCount = $users->total();
        return view('backend.users.index', compact('users', 'cities', 'pendingCount'))->with('filterStatus', 'pending');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->has('role')) $request->merge(['role' => 'user']);
        if(!$request->has('status')) $request->merge(['status' => 'active']);
        if(!$request->has('password')) $request->merge(['password' => '123456']); // Default password
            
        // Auto-generate email if missing
        if(empty($request->email) && !empty($request->phone)) {
            $request->merge(['email' => $request->phone . '_' . uniqid() . '@local.com']);
        } elseif(empty($request->email)) {
            $request->merge(['email' => uniqid() . '@local.com']);
        }

        $this->validate($request,
        [
            'name'=>'string|required|max:100',
            'email'=>'string|required|unique:users',
            'password'=>'string|required',
            'role'=>'required|in:admin,user',
            'status'=>'required|in:active,inactive,pending',
            'photo'=>'nullable|string',
            'phone'=>'nullable|string',
            'address'=>'nullable|string',
            'city'=>'nullable|string',
            'shipping_address'=>'nullable|string',
            'shipping_city'=>'nullable|string',
            'courier_company'=>'nullable|string',
            'courier_number'=>'nullable|string',
            'customer_type'=>'nullable|string|in:wholesale,retail,walkin,salesman',
        ]);
        // dd($request->all());
        $data=$request->all();
        $data['password']=Hash::make($request->password);

        // dd($data);
        try {
            $status = User::create($data);
            
            if($request->ajax() || $request->expectsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                // Ensure no garbage output before JSON
                if (ob_get_length()) ob_clean();
                return response()->json(['status' => 'success', 'user' => $status]);
            }

            if($status){
                request()->session()->flash('success','Successfully added user');
            }
            else{
                request()->session()->flash('error','Error occurred while adding user');
            }
            return redirect()->route('users.index');

        } catch (\Exception $e) {
            if($request->ajax() || $request->expectsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                if (ob_get_length()) ob_clean();
                return response()->json([
                    'status' => 'error', 
                    'message' => 'DATABASE_ERROR: ' . $e->getMessage(),
                    'debug_info' => $request->all()
                ], 500);
            }
            return back()->with('error', $e->getMessage());
        }

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
        $user=User::findOrFail($id);
        return view('backend.users.edit')->with('user',$user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user=User::findOrFail($id);
        $this->validate($request,
        [
            'name'=>'string|required|max:30',
            'email'=>'string|required',
            'role'=>'required|in:admin,user',
            'status'=>'required|in:active,inactive,pending',
            'photo'=>'nullable|string',
            'phone'=>'nullable|string',
            'address'=>'nullable|string',
            'city'=>'nullable|string',
            'shipping_address'=>'nullable|string',
            'shipping_city'=>'nullable|string',
            'courier_company'=>'nullable|string',
            'courier_number'=>'nullable|string',
            'customer_type'=>'nullable|string|in:wholesale,retail,walkin,salesman',
        ]);
        // dd($request->all());
        $data=$request->all();
        // Handle password update
        if($request->password){
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        
        $status=$user->fill($data)->save();
        if($status){
            request()->session()->flash('success','Successfully updated');
        }
        else{
            request()->session()->flash('error','Error occured while updating');
        }
        return redirect()->route('users.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete=User::findorFail($id);
        $status=$delete->delete();
        if($status){
            request()->session()->flash('success','User Successfully deleted');
        }
        else{
            request()->session()->flash('error','There is an error while deleting users');
        }
        return redirect()->route('users.index');
    }
}
