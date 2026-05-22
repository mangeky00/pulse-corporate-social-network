@extends('layouts.master')
@section('title')
    Welcome!
@endsection
@section('content')
    @include('includs.message-block')
    <div class="row">
        <div class="col-md-6">
            <h3>Sign Up</h3>
            <form action='/signup' method="post">
                @csrf
                <div class="form-group" {{ $errors->has('email') ? 'has-error' : '' }}>
                    <label for="email">Your Email</label>
                    <input class= "form-control" type="email" name="email" id="email"
                        value="{{ Request::old('email') }}">
                </div>
                <div class="form-group">
                    <label for="first-name">Your First Name</label>
                    <input class= "form-control" type="text" name="first-name" id="first-name"
                        value="{{ Request::old('first-name') }}">
                </div>
                <div class="form-group">
                    <label for="password">Your Password</label>
                    <input class="form-control" type="password" name="password" id="password"
                        value="{{ Request::old('password') }}">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        <div class="col-md-6">
            <h3>Sign In</h3>
            <form action="/signin" method="post">
                @csrf
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input class= "form-control" type="email" name="email" id="email"
                        value="{{ Request::old('email') }}">
                </div>
                <div class="form-group">
                    <label for="password">Your Password</label>
                    <input class="form-control" type="password" name="password" id="password"
                        value="{{ Request::old('password') }}">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
