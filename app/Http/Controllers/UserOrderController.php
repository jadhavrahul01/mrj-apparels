<?php

namespace App\Http\Controllers;

use App\Imports\OrdersImport;
use App\Exports\OrderExport;
use App\Mail\AssignOrderMail;
use App\Mail\OrdersMail;
use App\Models\Customer;
use App\Models\Empdetail;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\JsonDecoder;
use Barryvdh\DomPDF\Facade\Pdf;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\URL;

class UserOrderController extends Controller
{
    protected function order()
    {
        $notification = Notification::latest()->get();
        $notificationCount = Notification::where('status', '1')->count();
        return view('frontend.customerOrder', compact('notification', 'notificationCount'));
    }

    protected function makeUserOrder()
    {
        $notification = Notification::latest()->get();
        $notificationCount = Notification::where('status', '1')->count();
        $customers = Customer::all();

        return view('frontend.order', compact('notification', 'notificationCount', 'customers'));
    }

    protected function random()
    {
        do {
            $orderid = random_int(100000, 999999);
        } while (Order::where("order_id", "=", $orderid)->first());

        return $orderid;
    }

    public function downloadPdf($orderId)
    {
        $order = Order::find($orderId);
        $pdf = PDF::loadView('pdf_view', compact('order'));
        return $pdf->download('order_' . $order->order_id . '.pdf');
    }

    protected function makeOrder(Request $request)
    {
        $random = $this->random();
        $user = Auth::user();
        $today = Carbon::now()->format('Y-m-d');
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $cnameWords = explode(" ", $request->cname);
        $caddWords = explode(" ", $request->cadd);
        $initials = "";

        foreach ($cnameWords as $w) {
            $initials .= $w[0];
        }

        $initials .= $caddWords[0][0];

        $sname = strtoupper($initials) . $random;
        $url = URL::signedRoute('share-entry', [
            'cid' => $random,
            'sname' => $sname,
        ]);

        $mtakers = $request->input('mtaker');
        $mtakerDates = $request->input('mdatetime');
        $mtakerObjects = [];
        for ($i = 0; $i < count($mtakers); $i++) {
            $mtakerObject = [
                'mtaker' => $mtakers[$i],
                'mtaker_date' => $mtakerDates[$i]
            ];
            $mtakerObjects[] = $mtakerObject;
        }

        if ($request->hasfile("poimg")) {
            $file = $request->file("poimg");
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(\public_path("poimg/"), $imageName);



            $order = new Order([
                'order_id' => $random,
                'cname' => $request->cname,
                'mtaker' => json_encode($mtakerObjects),
                // 'mtaker1' => $request->mtaker1,
                // 'mtakerDate1' => $request->mdatetime1,
                // 'mtaker2' => $request->mtaker2,
                // 'mtakerDate2' => $request->mdatetime2,
                'ponumber' => $request->pono,
                'poimg' => $imageName,
                'u_id' => $random,
                'cadd' => $request->cadd,
                'cgstin' => $request->cgstin,
                'fabrics_status' => 0,
                'cstyle_ref' => $request->styleref,
                'email' => $request->email1,
                'email2' => $request->email2,
                'email3' => $request->email3,
                'email4' => $request->email4,
                'email5' => $request->email5,
                'phone' => $request->phone1,
                'phone2' => $request->phone2,
                'phone3' => $request->phone3,
                'phone4' => $request->phone4,
                'phone5' => $request->phone5,
                'empdetails_url' => $url,
            ]);
            $order->save();

            $notification = new Notification([
                'notification_title' => $random . " order is placed by user " . $user->name,
                'notification_type' => "order",
                'status' => '1',
            ]);
            $notification->save();

            $event = new Event([
                'title' => $random . ' is placed by user ',
                'assignName1' => $user->name,
                'start_date' => $today,
                'end_date' => $tomorrow,
            ]);
            $event->save();

            $mailData = [
                'orderId' => $random,
                'title' => 'Order Placed Details',
                'name' => "$request->cname",
                'add' => "$request->cadd",
                'gstin' => "$request->cgstin",
                'remark' => "$request->styleref",
                'email' => "$request->email1" . ' ' . "$request->email2" . ' ' . "$request->email3" . ' ' . "$request->email4" . ' ' . "$request->email5",
                'phone' => "$request->phone1" . ' ' . "$request->phone2" . ' ' . "$request->phone3" . ' ' . "$request->phone4" . ' ' . "$request->phone5",
            ];

            // Mail::to('2490rahuljadhav@gmail.com')->cc("$request->email1", "$request->email2", "$request->email3", "$request->email4", "$request->email5")->send(new OrdersMail($mailData));

            $customer = Customer::where('cname', $request->cname)->where('cgstin', $request->cgstin)->where('email', $request->email)->first();

            if (!$customer) {
                $cusDetails = new Customer([
                    'cname' => $request->cname,
                    'cadd' => $request->cadd,
                    'cgstin' => $request->cgstin,
                    'email' => $request->email1,
                    'phone' => $request->phone1,
                ]);
                $cusDetails->save();
            }





            return redirect($url)->with('success', 'Order placed successfully');
        } else {
            $order = new Order([
                'order_id' => $random,
                'cname' => $request->cname,
                'mtaker' => json_encode($mtakerObjects),
                // 'mtaker1' => $request->mtaker1,
                // 'mtakerDate1' => $request->mdatetime1,
                // 'mtaker2' => $request->mtaker2,
                // 'mtakerDate2' => $request->mdatetime2,
                'u_id' => $random,
                'cadd' => $request->cadd,
                'cgstin' => $request->cgstin,
                'fabrics_status' => 0,
                'cstyle_ref' => $request->styleref,
                'email' => $request->email1,
                'email2' => $request->email2,
                'email3' => $request->email3,
                'email4' => $request->email4,
                'email5' => $request->email5,
                'phone' => $request->phone1,
                'phone2' => $request->phone2,
                'phone3' => $request->phone3,
                'phone4' => $request->phone4,
                'phone5' => $request->phone5,
                'empdetails_url' => $url,

            ]);
            $order->save();

            $notification = new Notification([
                'notification_title' => $random . " order is placed by customer " . $request->cname,
                'notification_type' => "order",
                'status' => '1',
            ]);
            $notification->save();

            $event = new Event([
                'title' => $random . ' is placed by customer ',
                'assignName1' => $request->cname,
                'start_date' => $today,
                'end_date' => $tomorrow,
            ]);
            $event->save();

            $mailData = [
                'orderId' => $random,
                'title' => 'Order Placed Details',
                'name' => "$request->cname",
                'add' => "$request->cadd",
                'gstin' => "$request->cgstin",
                'remark' => "$request->styleref",
                'email' => "$request->email1" . ' ' . "$request->email2" . ' ' . "$request->email3" . ' ' . "$request->email4" . ' ' . "$request->email5",
                'phone' => "$request->phone1" . ' ' . "$request->phone2" . ' ' . "$request->phone3" . ' ' . "$request->phone4" . ' ' . "$request->phone5",
            ];

            // Mail::to('2490rahuljadhav@gmail.com')->cc("$request->email1", "$request->email2", "$request->email3", "$request->email4", "$request->email5")->send(new OrdersMail($mailData));

            $customer = Customer::where('cname', $request->cname)->where('phone', $request->phone1)->where('email', $request->email1)->first();

            if (!$customer) {
                $cusDetails = new Customer;
                $cusDetails->cname = $request->cname;
                $cusDetails->cadd = $request->cadd;
                $cusDetails->cgstin = $request->cgstin;
                $cusDetails->styleref = $request->styleref;
                $cusDetails->email = $request->email1;
                $cusDetails->phone = $request->phone1;
                $cusDetails->save();
            }
            return redirect($url)->with('success', 'Order placed successfully');
        }
    }

    public function sendMessage() // TODO
    {
        $account_sid = env('TWILIO_SID');
        $auth_token = env('TWILIO_AUTH_TOKEN');
        $twilio_number = env('TWILIO_NUMBER');
        $to_number = "+919720864702"; //$phone_number;

        $client = new Client($account_sid, $auth_token);
        $message = $client->messages->create(
            "whatsapp:+919720864702",
            array(
                'from' => 'whatsapp:' . $twilio_number,
                'body' => 'Your order has been placed successfully.'
            )
        );

        if ($message->sid) {
            return response()->json(['status' => 'success', 'message' => 'Message sent successfully.']);
        }
    }

    protected function customerOrder(Request $request)
    {
        $random = $this->random();

        $order = new Order([
            'order_id' => $random,
            'u_id' => $random,
            'cname' => $request->cname,
            'cadd' => $request->cadd,
            'cgstin' => $request->cgstin,
            'fabrics_status' => 0,
            'remark' => $request->remark,
            'email' => $request->email1,
            'email2' => $request->email2,
            'email3' => $request->email3,
            'email4' => $request->email4,
            'email5' => $request->email5,
            'phone' => $request->phone1,
            'phone2' => $request->phone2,
            'phone3' => $request->phone3,
            'phone4' => $request->phone4,
            'phone5' => $request->phone5,
        ]);
        $order->save();

        $notification = new Notification([
            'notification_title' => $random . " order is placed by customer",
            'notification_type' => "order",
            'status' => '1',
        ]);
        $notification->save();

        $mailData = [
            'title' => 'Order Placed Details',
            'name' => "$request->cname",
            'orderId' => "$random",
            'add' => "$request->cadd",
            'gstin' => "$request->cgstin",
            'remark' => "$request->remark",
            'email' => "$request->email1" . ' ' . "$request->email2" . ' ' . "$request->email3" . ' ' . "$request->email4" . ' ' . "$request->email5",
            'phone' => "$request->phone1" . ' ' . "$request->phone2" . ' ' . "$request->phone3" . ' ' . "$request->phone4" . ' ' . "$request->phone5",
        ];

        Mail::to('2490rahuljadhav@gmail.com')->cc("$request->email1", "$request->email2", "$request->email3", "$request->email4", "$request->email5")->send(new OrdersMail($mailData));


        $customer = Customer::where('cname', $request->cname)->where('phone', $request->phone1)->where('email', $request->email1)->first();

        if (!$customer) {
            $cusDetails = new Customer;
            $cusDetails->cname = $request->cname;
            $cusDetails->cadd = $request->cadd;
            $cusDetails->cgstin = $request->cgstin;
            $cusDetails->email = $request->email1;
            $cusDetails->phone = $request->phone1;
            $cusDetails->save();
        }

        return redirect('/order')->with('success', 'Order placed successfully');
    }

    protected function orders(Request $request)
    {
        $fabricStatus = Order::where('status', '9')->count();
        $aordersCount = Order::count();
        $pedingOrders = Order::where('status', '1')->count();
        $notification = Notification::latest()->get();
        $notificationCount = Notification::where('status', '1')->count();
        $orders = Order::latest();

        if (!empty($request->get('c'))) {
            $orders = $orders->where('cname', 'like', '%' . $request->get('c') . '%');
        }
        $orders = $orders->paginate(20);
        return view('frontend.orders', compact('orders', 'fabricStatus', 'aordersCount', 'pedingOrders', 'notification', 'notificationCount'));
    }

    protected function assign(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $user = Auth::user();
        $order->assignId = $request->userID;
        $order->assignName = $request->userName;
        $order->assign_status = 0;
        $order->update();

        $mailData = [
            'title' => 'Your Assigned Order Details',
            'name' => $user->name,
            'orderid' => $order->order_id,
            'ordername' => $order->cname,
        ];

        Mail::to($user->email)->send(new AssignOrderMail($mailData));

        return redirect('orders')->with('success', 'Order assigned successfully');
    }

    public function bulkUpdate(Request $request)
    {
        $ids = $request->input('ids');
        $orderId = $request->input('orderId');
        $status = $request->input('status');

        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'No employees selected for update');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return back()->with('error', 'Order not found!');
        }

        $employees = Empdetail::whereIn('id', $ids)->get();
        foreach ($employees as $emp) {
            switch ($status) {
                case 'MD':
                    $emp->status = "MD";
                    $order->status = 4;
                    $order->save();
                    break;
                case 'MP':
                    $emp->status = "MP";
                    break;
                case 'RD':
                    $emp->status = "RD";
                    break;
                case 'D':
                    $emp->status = "D";
                    break;
                default:
                    return back()->with('error', 'Invalid status');
            }
            $emp->save();
        }

        return back()->with('success', 'Bulk update successful');
    }

    protected function measurement_pending($id)
    {
        $emp = Empdetail::findOrFail($id);

        if ($emp) {
            $emp->status = "MP";
            $emp->update();

            return back()->with('success', 'Employee status updated successfully to Measurement Pending');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }


    protected function measurement_done($id, $orderId)
    {
        $order = Order::find($orderId);
        $emp = Empdetail::findOrFail($id);

        if ($emp) {
            $order->status = 4;
            $order->update();
            $emp->status = "MD";
            $emp->update();

            return back()->with('success', 'Employee status updated successfully to Measurement Done');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    protected function processing_done($id)
    {
        $order = Order::findOrFail($id);

        if ($order) {
            $order->status = 5;
            $order->update();

            return back()->with('success', 'Order status updated successfully to Proccessing Done');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    protected function dispatching_pending($id)
    {
        $order = Order::findOrFail($id);

        if ($order) {
            $order->status = 6;
            $order->update();

            return back()->with('success', 'Order status updated successfully to Dispatching Pending');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    protected function readyfordispatch_paymentpending($id)
    {
        $order = Order::findOrFail($id);

        if ($order) {
            $order->status = 7;
            $order->update();

            return back()->with('success', 'Order status updated successfully to Ready for Dispatch Payment Pending');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    protected function ready_dispatch($id)
    {
        $emp = Empdetail::findOrFail($id);

        if ($emp) {
            $emp->status = "RD";
            $emp->update();

            return back()->with('success', 'Employee status updated successfully to Ready for Dispatch');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    protected function emp_dispatch($id)
    {
        $emp = Empdetail::findOrFail($id);

        if ($emp) {
            $emp->status = "D";
            $emp->update();

            return back()->with('success', 'Status updated successfully to Dispatched');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    public function invoice_store(Request $request)
    {
        $request->validate([
            "number" => "required|string",
            "date" => "required|string",
            "slip_number" => "required|string",
            "slip_date" => "required|string",
            "upload_copy" => "required|image|mimes:jpeg,png,jpg,pdf",
        ]);

        if ($request->hasFile("upload_copy")) {
            $file = $request->file("upload_copy");
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(\public_path("invoice_upload_copy/"), $imageName);

            $invoices = new Invoice([
                "number" => $request->number,
                "date" => $request->date,
                "slip_number" => $request->slip_number,
                "slip_date" => $request->slip_date,
                "upload_copy" => $imageName,
            ]);
            $invoices->save();
        }
        return back()->with('success', 'Updated successfully to Dispatched');
    }
    protected function dispatch($id)
    {
        $order = Order::findOrFail($id);

        if ($order) {
            $order->status = 9;
            $order->update();

            return back()->with('success', 'Order status updated successfully to Dispatched');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    protected function orderEdit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $notification = Notification::latest()->get();
        $notificationCount = Notification::where('status', '1')->count();

        $employees = Empdetail::latest();
        if (!empty($request->get('s'))) {
            $employees = $employees->where('fullName', 'like', '%' . $request->get('s') . '%');
        }

        $employees = $employees->paginate(20);
        // $employees = Empdetail::paginate(10);
        return view('frontend.orderedit', compact('order', 'employees', 'notification', 'notificationCount'));
    }

    protected function orderUpdate(Request $request, $id)
    {
        $orders = Order::findOrFail($id);

        if ($request->hasFile("poimg")) {
            if (file::exists("poimg/" . $orders->poimg)) {
                File::delete("poimg/" . $orders->poimg);
            }
            $file = $request->file("poimg");
            $imageName = time() . "_" . $file->getClientOriginalName();
            $file->move(\public_path("/poimg"), $imageName);
            $request['poimg'] = $imageName;
        } elseif (!$request->hasfile("poimg")) {
            $imageName = $request->oldpoimg;
        }

        // Prepare mtaker data
        $mtakerNames = $request->input('mtaker');
        $mtakerDates = $request->input('mdatetime');
        $mtakers = [];

        foreach ($mtakerNames as $index => $name) {
            $mtakers[] = [
                'mtaker' => $name,
                'mtaker_date' => $mtakerDates[$index]
            ];
        }

        $orders->update([
            "poimg" => $imageName,
            "cstyle_ref" => $request->styleref,
            "ponumber" => $request->pono,
            "mtaker1" => $request->mtaker1,
            "mtakerDate1" => $request->mtakerDate1,
            "mtaker2" => $request->mtaker2,
            "mtakerDate2" => $request->mtakerDate2,
            "mtaker" => json_encode($mtakers),
        ]);

        if ($orders) {
            return back()->with('success', 'Order Updated Successfully');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }


    public function import()
    {
        Excel::import(new OrdersImport, request()->file('file'));
        return back();
    }

    public function export()
    {
        return Excel::download(new OrderExport, 'orders.csv');
    }

    protected function fetchcustomer(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|max:198',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $customer = Customer::find($id);

            if ($customer) {
                return response()->json([
                    'status' => 200,
                    'customer' => $customer
                ]);
            } else {
                return response()->json([
                    'status' => 403,
                    'errors' => 'Not Found!',
                ]);
            }
        }
    }
    // protected function fetchcustomer(Request $request, $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'id' => 'required|max:198',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 400,
    //             'errors' => $validator->messages(),
    //         ]);
    //     } else {
    //         $order = Order::find($id);

    //         if ($order) {
    //             return response()->json([
    //                 'status' => 200,
    //                 'order' => $order
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => 403,
    //                 'errors' => 'Not Found!',
    //             ]);
    //         }
    //     }
    // }
}
