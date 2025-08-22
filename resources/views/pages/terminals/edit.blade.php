@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Edit product</h4>
                        <form action="{{ route('products.update', $product->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}">
                            </div>
                            <div class="form-group  mb-3">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}">
                            </div>
                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $product->description }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
