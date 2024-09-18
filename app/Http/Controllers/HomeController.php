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
        $timeSlots = TimeSlot::all()->toArray();
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
        $bookedSlots = Availability::where('date', $todayDate)->get();
        $slotArray = [];
        foreach ($bookedSlots as $row) {
            array_push($slotArray, $row->idTimeSlot);
        }
        $data = TimeSlot::whereNotIn('id', $slotArray)->get();
        if ($todayDate == Carbon::now('Asia/Kolkata')->format('Y-m-d')) {
            $currentTimeSlotId = '';
            $currentHour = Carbon::now('Asia/Kolkata')->format('H');
            foreach ($data as $row) {
                if ($row->from <= $currentHour && $row->to >= $currentHour) {
                    $currentTimeSlotId = $row->id;
                    break;
                }
            }
            if ($currentTimeSlotId) {
                $expiredSlots = range(9, $currentTimeSlotId);
                $blockedSlots = array_merge($slotArray, $expiredSlots);
                $data = TimeSlot::whereNotIn('id', $blockedSlots)->get();
            }
        }
        return view('index', compact('data', 'todayDate'));
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

        if ($request->slots) {
            $ifUserBookedSlot = Availability::where('date', $request->date)->where('idusers', Auth::id())->count();
            if ($ifUserBookedSlot) {
                return redirect()->back()->with('error', 'Only one slot you can book, please book another date slot!');
            }

            $slot = Availability::where('date', $request->date)->where('idTimeSlot', $request->slots)->first();
            if (!$slot) {
                $arr['date'] = $request->date;
                $arr['idTimeSlot'] = $request->slots;
                $arr['idUsers'] = Auth::id();
                $insert = Availability::insert($arr);
                if ($insert) {
                    return redirect()->back()->with('success', 'Slots booked successfully!');
                } else {
                    return redirect()->back()->with('error', 'Slots Not booked, please try again!');
                }
            } else {
                return redirect()->back()->with('error', 'Slots Already Booked, Please select another slot!');
            }
        } else {
            return redirect()->back()->with('error', 'Please select slot!');
        }
    }

    public function slotList()
    {
        $pastSlots = $upcomingSlots = [];
        $currentdate = Carbon::now('Asia/Kolkata')->format('Y-m-d');
        // $currentdate = date('Y-m-d');
        $allSlots = Availability::where('idUsers',Auth::user()->id)->get();
        foreach($allSlots as $row){
            if(strtotime($row->date) <= strtotime($currentdate)){
                array_push($pastSlots,$row);
            }else{
                array_push($upcomingSlots,$row);
            }
        }
        // echo '<pre>';print_r($upcomingSlots);die;
        return view('slotList',compact('pastSlots','upcomingSlots'));
    }
}
