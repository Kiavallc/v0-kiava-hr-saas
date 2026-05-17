<form wire:submit.prevent="upload" class="space-y-4">
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700">{{ session('message') }}</p>
        </div>
    @endif

    <div>
        <label for="documentRequirementId" class="block text-sm font-medium text-slate-700 mb-2">
            Document Type
        </label>
        <select
            wire:model="documentRequirementId"
            id="documentRequirementId"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="">Select a document type</option>
            @foreach (Auth::user()->company->documentRequirements as $req)
                <option value="{{ $req->id }}">{{ $req->name }}</option>
            @endforeach
        </select>
        @error('documentRequirementId')
            <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="documentFile" class="block text-sm font-medium text-slate-700 mb-2">
            Select File
        </label>
        <input
            type="file"
            wire:model="documentFile"
            id="documentFile"
            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        @error('documentFile')
            <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="expirationDate" class="block text-sm font-medium text-slate-700 mb-2">
            Expiration Date
        </label>
        <input
            type="date"
            wire:model="expirationDate"
            id="expirationDate"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        @error('expirationDate')
            <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">
            Notes (Optional)
        </label>
        <textarea
            wire:model="notes"
            id="notes"
            rows="3"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Any additional notes..."
        ></textarea>
    </div>

    <button
        type="submit"
        wire:loading.attr="disabled"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 disabled:opacity-50"
    >
        <span wire:loading.remove>Upload Document</span>
        <span wire:loading>Uploading...</span>
    </button>
</form>
