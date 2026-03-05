@extends('layouts.app')

@section('title', 'Show Post')

@section('content')
<h1>{{ $post->title }}</h1>
@if($post->image)
    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="img-fluid">
@else
    <p>No Image</p>
@endif
<p><strong>ID:</strong> {{ $post->id }}</p>
<p><strong>Created At:</strong> {{ $post->created_at }}</p>
<p><strong>Updated At:</strong> {{ $post->updated_at }}</p>
<a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>
<a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">Edit</a>
<button class="btn btn-secondary" onclick="processImage('{{ $post->image ?? null}}')">Process</button>
@endsection
