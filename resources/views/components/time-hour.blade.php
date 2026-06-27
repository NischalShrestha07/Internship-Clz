@props(['currentTime', 'pendingLeaveRequests', 'approvedLeaveRequests', 'isHrManager'])

<div
    class="bg-gradient-to-br from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-all">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="text-center sm:text-left">
            <p class="text-sm text-gray-500 dark:text-gray-400">Current Time</p>
            <p class="text-3xl font-semibold text-gray-900 dark:text-white" id="componentCurrentTime">
                {{ $currentTime }}
            </p>
        </div>

        <div class="text-center sm:text-right">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $isHrManager ? 'Approved Leaves' : 'Pending Leaves' }}
            </p>
            <p class="text-3xl font-semibold text-indigo-600 dark:text-indigo-400">
                {{ $isHrManager ? $approvedLeaveRequests : $pendingLeaveRequests }}
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateComponentTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
            const formattedTime = `${formattedHours}:${formattedMinutes} ${ampm}`;

            const componentTime = document.getElementById('componentCurrentTime');
            if (componentTime) {
                componentTime.textContent = formattedTime;
            }
        }

        updateComponentTime();
        setInterval(updateComponentTime, 1000);
    });
</script>