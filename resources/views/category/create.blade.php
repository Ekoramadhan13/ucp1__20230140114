<x-app-layout>
    <div class="py-12 min-h-screen bg-navy-950 flex items-center justify-center">
        <div class="max-w-md w-full px-4">
            
            {{-- Card Container --}}
            <div class="bg-navy-900 border border-white/5 shadow-2xl rounded-[2.5rem] p-10 relative overflow-hidden">
                {{-- Subtle Gradient Glow --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-brand-primary/10 blur-[80px] rounded-full"></div>
                <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-brand-secondary/10 blur-[80px] rounded-full"></div>

                {{-- Header --}}
                <div class="relative mb-10">
                    <a href="{{ route('category.index') }}" class="group inline-flex items-center text-gray-500 hover:text-white transition-colors mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-white tracking-tight">Add Category</h1>
                    <p class="text-gray-500 mt-2">Create a new category for products</p>
                </div>

                {{-- Form --}}
                <form action="{{ route('category.store') }}" method="POST" class="relative space-y-8">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-400 mb-3 ml-1">Category</label>
                        <input type="text" name="name" id="name" 
                            class="w-full bg-navy-950 border-white/5 border-2 rounded-2xl px-6 py-4 text-white placeholder-gray-600 focus:border-brand-primary focus:ring-0 transition-all"
                            placeholder="e.g. Electronic" required value="{{ old('name') }}">
                        @error('name')
                            <p class="text-red-400 text-xs mt-2 ml-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <a href="{{ route('category.index') }}" 
                            class="flex-1 text-center px-6 py-4 border-2 border-white/5 text-gray-400 font-bold rounded-2xl hover:bg-white/5 hover:text-white transition-all">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="flex-[1.5] px-6 py-4 bg-brand-primary hover:bg-brand-secondary text-white font-bold rounded-2xl shadow-lg shadow-brand-primary/25 hover:shadow-brand-primary/40 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>

            <p class="text-center text-gray-600 mt-8 text-sm">
                &copy; {{ date('Y') }} Eko Ramadhan — Management System
            </p>
        </div>
    </div>
</x-app-layout>
