@extends('layouts.app')

@section('title', 'Passports')

@section('content')
    <h1>Passports</h1>
    <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Create New Passport</a>
    <a href="{{ route('scan') }}" class="btn btn-primary mb-3">Scan</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Has Data</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->data ? 'Yes' : 'No'  }}</td>
                    <td>
                        @if ($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" width="100">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-secondary"
                            onclick="processImage(event,'{{ $post->id ?? null }}')">Process</button>
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
