@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Add New Address</h2>
        @if (session('success'))
            <div class="alert alert-success p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('addresses.store') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="type">Address Type</label>
                            <select name="type" id="type" class="form-control rounded-input" required>
                                <option value="shipping">Shipping</option>
                                <option value="billing">Billing</option>
                            </select>
                            @error('type')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control rounded-input" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control rounded-input" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control rounded-input" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control rounded-input" value="{{ old('phone_number') }}" required>
                        @error('phone_number')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="line1">Address Line 1</label>
                        <input type="text" name="line1" id="line1" class="form-control rounded-input" value="{{ old('line1') }}" required>
                        @error('line1')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="line2">Address Line 2 (Optional)</label>
                        <input type="text" name="line2" id="line2" class="form-control rounded-input" value="{{ old('line2') }}">
                        @error('line2')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="city">City</label>
                            <input type="text" name="city" id="city" class="form-control rounded-input" value="{{ old('city', 'Nairobi') }}" required>
                            @error('city')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="state">State (Optional)</label>
                            <input type="text" name="state" id="state" class="form-control rounded-input" value="{{ old('state', 'Nairobi') }}">
                            @error('state')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-2">
                            <label for="postal_code">Postal Code (Optional)</label>
                            <input type="text" name="postal_code" id="postal_code" class="form-control rounded-input" value="{{ old('postal_code') }}">
                            @error('postal_code')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" name="country" id="country" class="form-control rounded-input" value="{{ old('country', 'Kenya') }}" required>
                        @error('country')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mt-5"> <!-- Increased margin -->
                        <button type="submit" class="btn btn-primary yellow-button btn-sm-width">Save Address</button>
                        <a href="{{ route('addresses.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .rounded-input {
            border-radius: 1rem !important; /* Curved edges for inputs */
        }

        .yellow-button {
            background-color: #ffc107 !important; /* Yellow background */
            border-color: #ffc107 !important;
            color: #000 !important; /* Black text for contrast */
        }

        .yellow-button:hover {
            background-color: #e0a800 !important; /* Darker yellow on hover */
            border-color: #e0a800 !important;
        }

        .btn-sm-width {
            width: 20% !important; /* Button width set to 20% */
        }
    </style>
@endsection