@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <h1>Products</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @if (Auth::user()->user_type =='admin')
                <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Add Product</a>
                @endif
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            @if (Auth::user()->user_type !='client')
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->description }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->quantity }}</td>
                                @if (Auth::user()->user_type !='client')
                                    <td>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                            @if (Auth::user()->user_type =='moderator' || Auth::user()->user_type =='admin')
                                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Edit</a>
                                            @endif
                                            @csrf
                                            @method('DELETE')
                                            @if (Auth::user()->user_type =='admin')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            @endif
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
