<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display the notification management page
     */
    public function index()
    {
        $notifications = DB::table('notifications')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $notificationStats = [
            'total' => DB::table('notifications')->count(),
            'unread' => DB::table('notifications')->where('is_read', 0)->count(),
            'today' => DB::table('notifications')->whereDate('created_at', today())->count(),
            'this_week' => DB::table('notifications')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
        ];

        return view('notifications.index', compact('notifications', 'notificationStats'));
    }

    /**
     * Create a new notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,warning,error,success',
            'target_type' => 'required|in:all,role,user',
            'target_id' => 'nullable|integer',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        try {
            $notificationId = DB::table('notifications')->insertGetId([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'target_type' => $request->target_type,
                'target_id' => $request->target_id,
                'priority' => $request->priority,
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // If target is specific users, create user notifications
            if ($request->target_type === 'user' && $request->target_id) {
                DB::table('user_notifications')->insert([
                    'notification_id' => $notificationId,
                    'user_id' => $request->target_id,
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bildirim oluşturuldu.',
                'data' => ['id' => $notificationId]
            ]);

        } catch (\Exception $e) {
            Log::error('Notification creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            DB::table('notifications')->where('id', $id)->update([
                'is_read' => 1,
                'read_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bildirim okundu olarak işaretlendi.'
            ]);

        } catch (\Exception $e) {
            Log::error('Notification mark as read error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            DB::table('notifications')->update([
                'is_read' => 1,
                'read_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tüm bildirimler okundu olarak işaretlendi.'
            ]);

        } catch (\Exception $e) {
            Log::error('Mark all notifications as read error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread notifications for current user
     */
    public function getUnreadNotifications()
    {
        $notifications = DB::table('notifications')
            ->where('is_read', 0)
            ->where(function($query) {
                $query->where('target_type', 'all')
                      ->orWhere(function($q) {
                          $q->where('target_type', 'user')
                            ->where('target_id', auth()->id());
                      });
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            DB::table('notifications')->where('id', $id)->delete();
            DB::table('user_notifications')->where('notification_id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bildirim silindi.'
            ]);

        } catch (\Exception $e) {
            Log::error('Notification deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => DB::table('notifications')->count(),
            'unread' => DB::table('notifications')->where('is_read', 0)->count(),
            'today' => DB::table('notifications')->whereDate('created_at', today())->count(),
            'this_week' => DB::table('notifications')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'by_type' => DB::table('notifications')
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'by_priority' => DB::table('notifications')
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Send system notification (internal method)
     */
    public static function sendSystemNotification($title, $message, $type = 'info', $priority = 'medium')
    {
        try {
            DB::table('notifications')->insert([
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'target_type' => 'all',
                'target_id' => null,
                'priority' => $priority,
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('System notification error: ' . $e->getMessage());
            return false;
        }
    }
}
