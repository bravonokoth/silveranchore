@extends('layouts.admin')

@section('page-title', 'Edit Media')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.media.update', $medium) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')

            <div class="mb-3">
                <label>Current Image</label><br>
                <img src="{{ asset('storage/' . $medium->path) }}" width="200" alt="Current">
            </div>

            <div class="mb-3">
                <label>New Image (optional)</label>
                <input type="file" name="path" class="form-control">
            </div>

            <div class="mb-3">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="image" {{ $medium->type == 'image' ? 'selected' : '' }}>Image</option>
                    <option value="video" {{ $medium->type == 'video' ? 'selected' : '' }}>Video</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection