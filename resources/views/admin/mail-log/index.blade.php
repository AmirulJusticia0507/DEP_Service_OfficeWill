@extends('layouts.app')
@section('title', __('Mail Log'))
@section('header-icon')
    <i class="ti ti-mail"></i>
@endsection
@section('breadcrumbs')
    <span class="text-slate-800 dark:text-slate-100 font-medium">Administration</span>
    <span class="text-slate-400 dark:text-slate-500 mx-1">/</span>
    <span class="text-slate-800 dark:text-slate-100 font-medium">{{ __('Mail Log') }}</span>
@endsection
@section('content')
<div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
    <h2 class="text-lg font-bold text-primary">{{ __('Mail Log') }}</h2>
    <div class="flex items-center gap-2 text-xs">
        <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 dark:bg-navy-700 dark:text-slate-300"><i class="ti ti-inbox"></i> {{ __('Total') }}: {{ $total }}</span>
        <span class="px-2 py-1 rounded bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"><i class="ti ti-mail-opened"></i> {{ __('Unread') }}: {{ $unread }}</span>
    </div>
</div>

@if(!empty($mailpitError))
<div class="bg-rose-100 text-rose-800 px-4 py-3 rounded mb-4 text-sm border-l-4 border-rose-500 dark:bg-rose-900 dark:text-rose-200">{{ $mailpitError }}</div>
@endif

<div class="bg-white rounded shadow p-4 mb-4 dark:bg-navy-800">
    <form method="GET" class="flex items-end gap-3 flex-wrap">
        <div class="flex-1 min-w-[220px]">
            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Search') }}</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="subject / address" class="form-input">
        </div>
        <button type="submit" class="btn-primary text-xs"><i class="ti ti-search"></i> {{ __('Search') }}</button>
        @if($search)
        <a href="{{ route('admin.mail-log.index') }}" class="btn-secondary text-xs">{{ __('Reset') }}</a>
        @endif
        <div class="flex-1"></div>
        <button type="button" id="mailMarkAllRead" class="btn-secondary text-xs"><i class="ti ti-eye-check"></i> {{ __('Mark all read') }}</button>
        <button type="button" id="mailDeleteAll" class="text-xs px-3 py-1.5 rounded border border-rose-300 text-rose-600 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-400 dark:hover:bg-rose-950 transition"><i class="ti ti-trash"></i> {{ __('Delete all') }}</button>
    </form>
</div>

<div class="bg-white rounded shadow dark:bg-navy-800 overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th class="w-8"></th>
                <th>{{ __('From') }}</th>
                <th>{{ __('To') }}</th>
                <th>{{ __('Subject') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Size') }}</th>
                <th class="w-20">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($messages as $m)
            <tr data-mid="{{ $m['id'] }}" class="{{ $m['read'] ? '' : 'bg-amber-50/50 dark:bg-amber-900/10' }}">
                <td>
                    @if(!$m['read'])
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500" title="{{ __('Unread') }}"></span>
                    @else
                        <span class="inline-block w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    @endif
                </td>
                <td>
                    <span class="font-medium">{{ $m['from']['Name'] ?? '—' }}</span>
                    @if(!empty($m['from']['Address']))
                    <span class="block text-[11px] text-slate-400">{{ $m['from']['Address'] }}</span>
                    @endif
                </td>
                <td class="text-xs text-slate-500 dark:text-slate-400">{{ collect($m['to'])->pluck('Address')->implode(', ') }}</td>
                <td class="max-w-[280px] truncate font-medium">{{ $m['subject'] ?: __('No subject') }}</td>
                <td class="whitespace-nowrap text-xs">{{ $m['created_at']?->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                <td class="whitespace-nowrap text-xs">
                    {{ number_format($m['size'] / 1024, 1) }} KB
                    @if($m['has_attachment']) <i class="ti ti-paperclip text-amber-500"></i> @endif
                </td>
                <td class="whitespace-nowrap text-center">
                    <button type="button" class="mail-view inline-flex items-center justify-center w-7 h-7 rounded hover:bg-primary/10 text-primary dark:hover:bg-white/10" title="{{ __('View') }}" data-id="{{ $m['id'] }}">
                        <i class="ti ti-eye"></i>
                    </button>
                    <button type="button" class="mail-delete inline-flex items-center justify-center w-7 h-7 rounded hover:bg-rose-50 text-rose-500 dark:hover:bg-rose-950" title="{{ __('Delete') }}" data-id="{{ $m['id'] }}">
                        <i class="ti ti-trash"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-10">
                    <i class="ti ti-inbox text-3xl text-slate-300 dark:text-slate-600 mb-2 inline-block"></i>
                    @if($search)
                    <p class="text-slate-400 dark:text-slate-500">{{ __('No messages found for your search.') }}</p>
                    @else
                    <p class="text-slate-400 dark:text-slate-500">{{ __('No emails received yet.') }}</p>
                    @endif
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($totalPages > 1)
<div class="mt-4 flex items-center justify-between">
    <span class="text-xs text-slate-400">{{ $page }} / {{ $totalPages }}</span>
    <div class="flex gap-2">
        @if($page > 1)
        <a href="{{ route('admin.mail-log.index', ['page' => $page - 1, 'search' => $search]) }}" class="btn-secondary text-xs">{{ __('Previous') }}</a>
        @endif
        @if($page < $totalPages)
        <a href="{{ route('admin.mail-log.index', ['page' => $page + 1, 'search' => $search]) }}" class="btn-secondary text-xs">{{ __('Next') }}</a>
        @endif
    </div>
</div>
@endif

<div id="mailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60" data-mail-close></div>
    <div class="relative bg-white dark:bg-navy-800 rounded shadow-xl w-full max-w-3xl max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 bg-primary text-white rounded-t">
            <h3 class="text-sm font-semibold truncate pr-4" id="mailModalSubject"></h3>
            <button type="button" class="text-white/80 hover:text-white text-2xl leading-none" data-mail-close>&times;</button>
        </div>
        <div class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300 space-y-1 border-b border-slate-100 dark:border-slate-700" id="mailModalMeta"></div>
        <div class="flex-1 overflow-auto bg-white">
            <iframe id="mailModalFrame" sandbox="allow-same-origin allow-popups" class="w-full h-full min-h-[440px]"></iframe>
            <div id="mailModalText" class="hidden p-4 whitespace-pre-wrap font-mono text-xs text-slate-800"></div>
        </div>
    </div>
</div>

<script>
(function () {
    const csrfToken = '{{ csrf_token() }}';
    const headers = { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' };

    function openModal(id) {
        fetch('/admin/mail-log/message/' + encodeURIComponent(id))
            .then(r => r.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                const subject = data.Subject || '(no subject)';
                const from = data.From && data.From.Name
                    ? data.From.Name + ' <' + data.From.Address + '>'
                    : (data.From ? data.From.Address : '—');
                const to = (data.To || []).map(t => t.Name ? t.Name + ' <' + t.Address + '>' : t.Address).join(', ');
                const date = data.Date ? new Date(data.Date).toLocaleString() : '—';

                document.getElementById('mailModalSubject').textContent = subject;
                document.getElementById('mailModalMeta').innerHTML =
                    '<div><span class="text-slate-400">From:</span> ' + escapeHtml(from) + '</div>' +
                    '<div><span class="text-slate-400">To:</span> ' + escapeHtml(to) + '</div>' +
                    '<div><span class="text-slate-400">Date:</span> ' + escapeHtml(date) + '</div>';

                const frame = document.getElementById('mailModalFrame');
                const textView = document.getElementById('mailModalText');
                if (data.HTML) {
                    frame.classList.remove('hidden');
                    textView.classList.add('hidden');
                    frame.srcdoc = data.HTML;
                } else {
                    frame.classList.add('hidden');
                    textView.classList.remove('hidden');
                    textView.textContent = data.Text || '(empty)';
                }
                document.getElementById('mailModal').classList.remove('hidden');
                updateRow(id, true);
            })
            .catch(err => alert(err.message || 'Failed to load message'));
    }

    function closeModal() {
        document.getElementById('mailModal').classList.add('hidden');
        document.getElementById('mailModalFrame').srcdoc = '';
    }

    function updateRow(id, read) {
        const rows = document.querySelectorAll('tr[data-mid="' + id + '"]');
        rows.forEach(function (row) {
            row.classList.toggle('bg-amber-50/50', !read);
            row.classList.toggle('dark:bg-amber-900/10', !read);
            const dot = row.querySelector('.w-2.h-2');
            if (dot) {
                dot.className = 'inline-block w-2 h-2 rounded-full ' + (read ? 'bg-slate-300 dark:bg-slate-600' : 'bg-amber-500');
            }
        });
    }

    function deleteMessage(id) {
        if (!confirm('{{ __("Delete this message?") }}')) return;
        fetch('/admin/mail-log/' + encodeURIComponent(id), { method: 'DELETE', headers: headers })
            .then(r => r.json())
            .then(data => { if (data.ok) location.reload(); })
            .catch(() => alert('{{ __("Failed to reach Mailpit.") }}'));
    }

    function escapeHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    document.querySelectorAll('.mail-view').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.getAttribute('data-id')));
    });
    document.querySelectorAll('.mail-delete').forEach(btn => {
        btn.addEventListener('click', () => deleteMessage(btn.getAttribute('data-id')));
    });
    document.querySelectorAll('[data-mail-close]').forEach(el => {
        el.addEventListener('click', closeModal);
    });

    const markAllBtn = document.getElementById('mailMarkAllRead');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            if (!confirm('{{ __("Mark all messages as read?") }}')) return;
            fetch('/admin/mail-log/read-all', { method: 'PUT', headers: headers })
                .then(() => location.reload())
                .catch(() => alert('{{ __("Failed to reach Mailpit.") }}'));
        });
    }

    const deleteAllBtn = document.getElementById('mailDeleteAll');
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', function () {
            if (!confirm('{{ __("Delete ALL messages? This cannot be undone.") }}')) return;
            fetch('/admin/mail-log', { method: 'DELETE', headers: headers })
                .then(() => location.reload())
                .catch(() => alert('{{ __("Failed to reach Mailpit.") }}'));
        });
    }
})();
</script>
@endsection
