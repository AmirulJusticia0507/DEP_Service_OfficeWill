<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MailLogController extends Controller
{
    private function baseUrl(): string
    {
        return rtrim((string) config('services.mailpit.api_url'), '/');
    }

    private function client()
    {
        return Http::acceptJson()->timeout(10);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $perPage = max(1, min(100, (int) $request->get('perPage', 20)));
        $page = max(1, (int) $request->get('page', 1));

        try {
            $base = $this->baseUrl();

            if ($search !== '') {
                $response = $this->client()->get($base.'/api/v1/search', [
                    'query' => $search,
                    'limit' => $perPage,
                    'start' => ($page - 1) * $perPage,
                ]);
            } else {
                $response = $this->client()->get($base.'/api/v1/messages', [
                    'limit' => $perPage,
                    'offset' => ($page - 1) * $perPage,
                ]);
            }

            if ($response->failed()) {
                throw new \RuntimeException('Mailpit responded with status '.$response->status());
            }

            $data = $response->json() ?? [];
            $messages = collect($data['messages'] ?? [])->map(fn ($m) => $this->decorate($m));
            $total = $search !== '' ? (int) ($data['count'] ?? 0) : (int) ($data['total'] ?? 0);
            $unread = $search !== '' ? (int) ($data['messages_unread'] ?? 0) : (int) ($data['unread'] ?? 0);
            $totalPages = max(1, (int) ceil($total / $perPage));
        } catch (\Throwable $e) {
            return view('admin.mail-log.index', [
                'messages' => collect(),
                'total' => 0,
                'unread' => 0,
                'perPage' => $perPage,
                'page' => 1,
                'totalPages' => 1,
                'search' => $search,
                'mailpitError' => __('Mailpit is not reachable. Please make sure it is running.'),
            ]);
        }

        return view('admin.mail-log.index', compact('messages', 'total', 'unread', 'perPage', 'page', 'totalPages', 'search'));
    }

    public function show(Request $request, string $id)
    {
        try {
            $detail = $this->client()->get($this->baseUrl().'/api/v1/message/'.rawurlencode($id))->json() ?? [];

            $this->client()->put($this->baseUrl().'/api/v1/messages', [
                'Read' => true,
                'IDs' => [$id],
            ]);

            return response()->json($detail);
        } catch (\Throwable $e) {
            return response()->json(['error' => __('Failed to load message.')], 502);
        }
    }

    public function markRead(Request $request, string $id)
    {
        try {
            $this->client()->put($this->baseUrl().'/api/v1/messages', [
                'Read' => true,
                'IDs' => [$id],
            ]);

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => __('Failed to reach Mailpit.')], 502);
        }
    }

    public function markAllRead()
    {
        try {
            $this->client()->put($this->baseUrl().'/api/v1/messages', ['Read' => true]);

            return back()->with('success', __('All messages marked as read.'));
        } catch (\Throwable $e) {
            return back()->with('error', __('Failed to reach Mailpit.'));
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            $this->client()->delete($this->baseUrl().'/api/v1/messages', ['IDs' => [$id]]);

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => __('Failed to reach Mailpit.')], 502);
        }
    }

    public function destroyAll()
    {
        try {
            $this->client()->delete($this->baseUrl().'/api/v1/messages');

            return back()->with('success', __('All messages deleted.'));
        } catch (\Throwable $e) {
            return back()->with('error', __('Failed to reach Mailpit.'));
        }
    }

    private function decorate(array $message): array
    {
        return [
            'id' => (string) ($message['ID'] ?? ''),
            'subject' => (string) ($message['Subject'] ?? ''),
            'from' => $message['From'] ?? [],
            'to' => $message['To'] ?? [],
            'created_at' => isset($message['Created']) ? Carbon::parse($message['Created']) : null,
            'read' => (bool) ($message['Read'] ?? false),
            'snippet' => (string) ($message['Snippet'] ?? ''),
            'size' => (int) ($message['Size'] ?? 0),
            'has_attachment' => (int) ($message['Attachments'] ?? 0) > 0,
        ];
    }
}
