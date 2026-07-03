@extends('layouts.agent')

@section('page_title', 'Client Reviews')

@section('content')
    <div class="mb-6">
        <h3 class="font-outfit font-bold text-lg text-slate-200">Reviews & Feedback</h3>
        <p class="text-xs text-slate-500 mt-0.5">See what buyers have said about their communication and service experience with you.</p>
    </div>

    @if(empty($reviews))
        <div class="glass-panel rounded-2xl p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-900 flex items-center justify-center text-slate-600 mx-auto mb-4">
                <i class="fa-solid fa-comments text-2xl"></i>
            </div>
            <h4 class="text-slate-400 font-semibold text-sm">No reviews yet</h4>
            <p class="text-xs text-slate-600 mt-1">Once buyers leave a review, it will be posted here.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($reviews as $review)
                <div class="glass-panel p-6 rounded-2xl border hover:border-purple-500/10 transition duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center font-bold text-slate-300">
                                {{ substr($review->buyer_name, 0, 1) }}
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-slate-200 block">{{ $review->buyer_name }}</span>
                                <span class="text-[10px] text-slate-500 block">{{ $review->buyer_email }}</span>
                            </div>
                        </div>

                        <!-- Rating Stars -->
                        <div class="flex items-center gap-1 bg-purple-500/10 border border-purple-500/20 px-2 py-0.5 rounded-lg text-xs font-bold text-purple-400">
                            <span>{{ number_format($review->rating, 1) }}</span>
                            <i class="fa-solid fa-star text-[10px]"></i>
                        </div>
                    </div>

                    <!-- Review Comment -->
                    <p class="text-xs text-slate-400 leading-relaxed italic bg-slate-950/40 p-4 rounded-xl border border-slate-900">
                        "{{ $review->comments }}"
                    </p>

                    <!-- Timestamp -->
                    <div class="text-right mt-3 text-[10px] text-slate-600 font-medium">
                        Posted on {{ date('d M Y, h:i A', strtotime($review->createdat)) }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
