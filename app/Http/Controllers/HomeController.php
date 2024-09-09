<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\City;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

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
        return view('home');
    }

    public function availableslot()
    {
        // $timeSlots = TimeSlot::all()->toArray();

        // $a = Availability::groupBy()

        $timeSlots = TimeSlot::all()->toArray();
        // $bookedSlots = Availability::get()->toArray();
        // $fromSlot = $bookedSlots['fromTime'];
        // $toTime = $bookedSlots['toTime'];   
        // $fromDisableSlot = $toDisableSlot = [];
        // foreach($bookedSlots as $singleBook){
        //     for($i = $singleBook['fromTime']; $i <= $singleBook['toTime']; $i++){
        //         if($i != $singleBook['fromTime']){
        //             $toDisableSlot[$singleBook['day']][] = $i;
        //         }
        //         if($i != $singleBook['toTime']){
        //             $fromDisableSlot[$singleBook['day']][] = $i;
        //         }
        //     }
        // }
        // echo '<pre>';print_r($fromDisableSlot);die;
        return view('availableslot', compact('timeSlots'));
    }

    public function addslot(Request $request)
    {
        $availability = Availability::where('day', $request->day)->get()->toArray();
        $blockedSlots = [];
        for ($i = 0; $i < count($availability); $i++) {
            array_push($blockedSlots, $i);
        }

        $data['fromTime'] = $request->fromslot;
        $data['toTime'] = $request->toslot;
        $data['day'] = $request->day;
        echo '<pre>';
        print_r($availability);
        die;
        if (in_array($request->fromslot, $blockedSlots) || in_array($request->toslot, $blockedSlots)) {
            return redirect()->route('availableslot')->with('error', 'this Slots is already booked!');
        } else {
            $insert = Availability::insert($data);
            if ($insert) {
                return redirect()->route('availableslot')->with('success', $request->day . 'Slot From ' . $request->fromslot . 'To' . $request->toslot . ' added Successfully!');
            } else {
                return redirect()->route('availableslot')->with('error', $request->day . 'Slot is Not added!');
            }
        }
    }

    public function booking()
    {
        $todayDate = $_GET ? $_GET['date'] : Carbon::now('Asia/Kolkata')->format('Y-m-d');
        $bookedSlots = Availability::where('date',$todayDate)->get();
        $slotArray = [];
        foreach($bookedSlots as $row){
            array_push($slotArray,$row->idTimeSlot);
        }
        $data = TimeSlot::whereNotIn('id',$slotArray)->get();
        return view('index', compact('data','todayDate'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $items = City::where('name', 'LIKE', "%{$query}%")->get(); // Adjust column name as necessary

        // Format data for Select2
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = ['id' => $item->idCity, 'text' => $item->name];
        }

        return response()->json($formattedItems);
    }

    public function bookSlots(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'slots' => 'required'
        ]);

        if($request->slots){
            $ifUserBookedSlot = Availability::where('date',$request->date)->where('idusers',Auth::id())->count();
            if($ifUserBookedSlot){
                return redirect()->back()->with('error', 'Only one slot you can book, please book another date slot!');
            }

            $slot = Availability::where('date',$request->date)->where('idTimeSlot',$request->slots)->first();
            if(!$slot){
                $arr['date'] = $request->date;
                $arr['idTimeSlot'] = $request->slots;
                $arr['idUsers'] = Auth::id();
                $insert = Availability::insert($arr);
                if($insert){
                    return redirect()->back()->with('success', 'Slots booked successfully!');
                }else{
                    return redirect()->back()->with('error', 'Slots Not booked, please try again!');
                }
            }else{
                return redirect()->back()->with('error', 'Slots Already Booked, Please select another slot!');
            }
        }else{
            return redirect()->back()->with('error', 'Please select slot!');
        }
    }

    public function slotList(){
        return view('slotList');
    }
}
