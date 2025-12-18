@extends('layouts.front')

@section('content')
<div class="max-w-md mx-auto mt-20">
    <form action="{{ route('payment.verify') }}" method="POST" class="bg-white p-8 rounded-xl shadow">
        @csrf
        <h2 class="text-2xl font-bold mb-6">Verify Payment Manually</h2>
        <input type="text" name="md5" placeholder="Paste MD5 here" required
               class="w-full p-3 border rounded mb-4">
        <button type="submit" class="w-full bg-black text-white py-3 rounded">
            Check Payment
        </button>
    </form>
</div>
@endsection
