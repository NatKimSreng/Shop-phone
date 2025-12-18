@extends('layouts.front')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-10">
        <h1 class="text-3xl font-bold mb-6">Payment Result</h1>
        <pre class="bg-gray-100 p-6 rounded overflow-x-auto">
{{ json_encode($result, JSON_PRETTY_PRINT) }}
        </pre>
        <a href="{{ route('home') }}" class="mt-6 inline-block px-6 py-3 bg-black text-white rounded">
            Back to Home
        </a>
    </div>
</div>
@endsection
