<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $notification = Notification::latest()->get();
        $notificationCount = Notification::where('status', '1')->count();

        // Search each table separately and add table name to results
        $users = DB::table('users')->where('name', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                                   ->get()->map(function ($user) use ($searchTerm) {
                                       $user->table = 'users';
                                       $user->matchedFields = $this->getMatchedFields($user, $searchTerm);
                                       return $user;
                                   });

        $orders = DB::table('orders')->where('cname', 'LIKE', "%{$searchTerm}%")
                                     ->orWhere('cadd', 'LIKE', "%{$searchTerm}%")
                                     ->orWhere('cgstin', 'LIKE', "%{$searchTerm}%")
                                     ->orWhere('cstyle_ref', 'LIKE', "%{$searchTerm}%")
                                     ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                                     ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                                     ->get()->map(function ($order) use ($searchTerm) {
                                         $order->table = 'orders';
                                         $order->matchedFields = $this->getMatchedFields($order, $searchTerm);
                                         return $order;
                                     });

        $empdetails = DB::table('empdetails')->where('tokenNo', 'LIKE', "%{$searchTerm}%")
                                             ->orWhere('sname', 'LIKE', "%{$searchTerm}%")
                                             ->orWhere('fullName', 'LIKE', "%{$searchTerm}%")
                                             ->orWhere('category', 'LIKE', "%{$searchTerm}%")
                                             ->orWhere('setOrder', 'LIKE', "%{$searchTerm}%")
                                             ->orWhere('status', 'LIKE', "%{$searchTerm}%")
                                             ->get()->map(function ($empdetail) use ($searchTerm) {
                                                 $empdetail->table = 'empdetails';
                                                 $empdetail->matchedFields = $this->getMatchedFields($empdetail, $searchTerm);
                                                 return $empdetail;
                                             });

        $customers = DB::table('customers')->where('cname', 'LIKE', "%{$searchTerm}%")
                                           ->orWhere('cadd', 'LIKE', "%{$searchTerm}%")
                                           ->orWhere('cgstin', 'LIKE', "%{$searchTerm}%")
                                           ->orWhere('styleref', 'LIKE', "%{$searchTerm}%")
                                           ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                                           ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                                           ->get()->map(function ($customer) use ($searchTerm) {
                                               $customer->table = 'customers';
                                               $customer->matchedFields = $this->getMatchedFields($customer, $searchTerm);
                                               return $customer;
                                           });

        $tasks = DB::table('tasks')->where('description', 'LIKE', "%{$searchTerm}%")
                                   ->get()->map(function ($task) use ($searchTerm) {
                                       $task->table = 'tasks';
                                       $task->matchedFields = $this->getMatchedFields($task, $searchTerm);
                                       return $task;
                                   });

        $events = DB::table('events')->where('title', 'LIKE', "%{$searchTerm}%")
                                     ->get()->map(function ($event) use ($searchTerm) {
                                         $event->table = 'events';
                                         $event->matchedFields = $this->getMatchedFields($event, $searchTerm);
                                         return $event;
                                     });

        $notifications = DB::table('notifications')->where('notification_title', 'LIKE', "%{$searchTerm}%")
                                                   ->orWhere('notification_type', 'LIKE', "%{$searchTerm}%")
                                                   ->get()->map(function ($notification) use ($searchTerm) {
                                                       $notification->table = 'notifications';
                                                       $notification->matchedFields = $this->getMatchedFields($notification, $searchTerm);
                                                       return $notification;
                                                   });

        // Combine the results
        $results = $users->concat($orders)
                         ->concat($empdetails)
                         ->concat($customers)
                         ->concat($tasks)
                         ->concat($events)
                         ->concat($notifications)
                         ->all();

         return view('frontend.search', compact('results', 'notification', 'notificationCount', 'searchTerm'));
    }

    private function getMatchedFields($record, $searchTerm)
    {
        $matchedFields = [];
        foreach ($record as $field => $value) {
            if (strpos($value, $searchTerm) !== false) {
                $matchedFields[$field] = $value;
            }
        }
        return $matchedFields;
    }
}