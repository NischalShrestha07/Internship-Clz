<div id="notification-toast" class="fixed top-5 right-5 z-50 hidden">
    <div id="notification-content"
        class="w-72 p-4 border-l-4 rounded-lg shadow-lg flex items-start gap-2">
        <span id="notification-icon" class="material-symbols-outlined text-base"></span>
        <div id="notification-message" class="flex-1 text-sm font-medium"></div>
        <button id="notification-close" class="transition-colors">
            <span class="material-symbols-outlined text-base">close</span>
        </button>
    </div>
</div>

@if (session('status') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('status'))
            showNotification('{{ session('status') }}', 'success');
        @endif
        @if (session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
    });
</script>
@endif
