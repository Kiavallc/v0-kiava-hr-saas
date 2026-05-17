<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Document Status</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-blue-900">Required Documents</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $requiredDocuments->count() }}</p>
            </div>

            <div class="bg-red-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-red-900">Missing</h3>
                <p class="text-2xl font-bold text-red-600">{{ $missingDocuments->count() }}</p>
            </div>

            <div class="bg-yellow-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-yellow-900">Expiring Soon</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ $expiringDocuments->count() }}</p>
            </div>

            <div class="bg-orange-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-orange-900">Expired</h3>
                <p class="text-2xl font-bold text-orange-600">{{ $expiredDocuments->count() }}</p>
            </div>
        </div>

        @if ($missingDocuments->count() > 0)
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="font-semibold text-red-900 mb-2">Missing Documents</h3>
                <ul class="space-y-1">
                    @foreach ($missingDocuments as $doc)
                        <li class="text-red-700 text-sm">{{ $doc->name }} - Required by {{ $doc->deadline?->format('M d, Y') ?? 'No deadline' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($expiredDocuments->count() > 0)
            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                <h3 class="font-semibold text-orange-900 mb-2">Expired Documents</h3>
                <ul class="space-y-1">
                    @foreach ($expiredDocuments as $doc)
                        <li class="text-orange-700 text-sm">{{ $doc->requirement->name }} - Expired on {{ $doc->expiration_date->format('M d, Y') }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($expiringDocuments->count() > 0)
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="font-semibold text-yellow-900 mb-2">Expiring Soon</h3>
                <ul class="space-y-1">
                    @foreach ($expiringDocuments as $doc)
                        <li class="text-yellow-700 text-sm">{{ $doc->requirement->name }} - Expires on {{ $doc->expiration_date->format('M d, Y') }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Upload Document</h2>
        <livewire:document-upload />
    </div>
</div>
