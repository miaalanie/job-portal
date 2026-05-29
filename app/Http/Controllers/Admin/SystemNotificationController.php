<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemNotificationController extends Controller
{
    public function index()
    {
        $query = DB::table('system_notifications')
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc');

        if (auth()->user()->idperusahaan != null) {
            $query->where('user_id', auth()->id());
        } else {
            $query->whereNull('user_id');
        }

        $notifications = $query->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function read($id)
    {
        $notification = DB::table('system_notifications')->where('id', $id)->first();
        if (!$notification) {
            return redirect()->back()->with('error', 'Notifikasi tidak ditemukan.');
        }

        // Mark as read
        DB::table('system_notifications')->where('id', $id)->update(['is_read' => 1]);

        // Redirect to target URL if exists
        if ($notification?->url) {
            return redirect($notification->url);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        DB::table('system_notifications')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function markAllAsRead()
    {
        $query = DB::table('system_notifications')->where('is_read', 0);

        if (auth()->user()->idperusahaan != null) {
            $query->where('user_id', auth()->id());
        } else {
            $query->whereNull('user_id');
        }

        $query->update(['is_read' => 1]);
        
        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
