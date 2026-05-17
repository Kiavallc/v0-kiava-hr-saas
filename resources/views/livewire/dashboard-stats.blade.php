<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Total Employees -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Employees</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalEmployees }}</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Documents Uploaded -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Documents Uploaded</p>
                <p class="text-3xl font-bold text-gray-900">{{ $documentsUploaded }}</p>
            </div>
            <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Documents Approved -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Documents Approved</p>
                <p class="text-3xl font-bold text-green-600">{{ $documentsApproved }}</p>
            </div>
            <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Expiring Documents -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Expiring Soon (30 days)</p>
                <p class="text-3xl font-bold {{ $documentsExpiring > 0 ? 'text-orange-600' : 'text-gray-900' }}">{{ $documentsExpiring }}</p>
            </div>
            <div class="p-3 {{ $documentsExpiring > 0 ? 'bg-orange-100' : 'bg-gray-100' }} rounded-lg">
                <svg class="w-6 h-6 {{ $documentsExpiring > 0 ? 'text-orange-600' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Compliance Percentage -->
    <div class="bg-white rounded-lg shadow p-6 md:col-span-4">
        <p class="text-gray-600 text-sm mb-2">Overall Compliance</p>
        <div class="flex items-center justify-between">
            <div class="w-full mr-4">
                <div class="relative pt-1">
                    <div class="overflow-hidden h-2 mb-2 text-xs flex rounded bg-gray-200">
                        <div style="width: {{ $compliancePercentage }}%"
                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $compliancePercentage >= 80 ? 'bg-green-500' : ($compliancePercentage >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}">
                        </div>
                    </div>
                </div>
            </div>
            <span class="text-2xl font-bold {{ $compliancePercentage >= 80 ? 'text-green-600' : ($compliancePercentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $compliancePercentage }}%
            </span>
        </div>
    </div>
</div>

<script>
    if (window.Echo) {
        // Listen for dashboard updates
        Echo.private('company.' + companyId)
            .listen('DashboardUpdated', (e) => {
                Livewire.dispatch('dashboard.updated', e);
            })
            .listen('DocumentUploaded', () => {
                Livewire.dispatch('document.uploaded');
            })
            .listen('DocumentApproved', () => {
                Livewire.dispatch('document.approved');
            })
            .listen('DocumentRejected', () => {
                Livewire.dispatch('document.rejected');
            });
    }
</script>
