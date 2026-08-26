<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Task;
use App\Models\Document;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $totalClients = Client::count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $completedThisMonth = Task::where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->count();
        $totalDocuments = Document::count();

        // Recent activity aggregation (latest clients, tasks, documents)
        $recentClients = Client::latest()->take(3)->get()->map(function ($client) {
            return [
                'type' => 'client',
                'title' => "Client {$client->name} was added",
                'subtitle' => $client->entity_type,
                'url' => route('clients.show', $client->id),
                'created_at' => $client->created_at,
            ];
        });

        $recentTasks = Task::with('client')->latest()->take(4)->get()->map(function ($task) {
            return [
                'type' => 'task',
                'title' => "Task '{$task->title}' " . ($task->status === 'completed' ? 'was completed' : 'was created'),
                'subtitle' => $task->client ? "Client: {$task->client->name}" : null,
                'url' => route('tasks.show', $task->id),
                'created_at' => $task->updated_at ?? $task->created_at,
            ];
        });

        $recentDocs = Document::with('client')->latest()->take(3)->get()->map(function ($doc) {
            return [
                'type' => 'document',
                'title' => "Document '{$doc->name}' uploaded",
                'subtitle' => $doc->client ? "Client: {$doc->client->name}" : null,
                'url' => route('clients.show', $doc->client_id),
                'created_at' => $doc->created_at,
            ];
        });

        $activities = $recentClients->concat($recentTasks)->concat($recentDocs)
            ->sortByDesc('created_at')
            ->take(6);

        $upcomingTasks = Task::with('client')
            ->where('status', '!=', 'completed')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', [
            'totalClients' => $totalClients,
            'pendingTasks' => $pendingTasks,
            'completedThisMonth' => $completedThisMonth,
            'totalDocuments' => $totalDocuments,
            'activities' => $activities,
            'upcomingTasks' => $upcomingTasks,
        ]);
    }
}
