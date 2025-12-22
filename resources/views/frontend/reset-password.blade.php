@extends('layouts.frontend.index')
@section('contents')
    <section class="reset-password-sec w-100 ">
        <div class="reset-container">
            <h2>Reset Your Password</h2>
            <form method="POST" action="{{ route('user.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email Address"
                        value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="New Password" required>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password"
                        required>
                </div>

                <button type="submit" class="btn-reset">Reset Password</button>
            </form>

            <!--<div class="text-small">-->
            <!--    Remembered your password? <a href="{{ route('user.login') }}">Sign In</a>-->
            <!--</div>-->
        </div>
    </section>
@endsection

@section('styles')
    <style>
        body {
            background: linear-gradient(135deg, #4c418c 0%, #006F94 100%);
        }

        .reset-container {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 15px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.15);
            /* animation: fadeIn 0.6s ease; */
            margin: 100px 0px;
        }

        .reset-container h2 {
            text-align: center;
            margin-bottom: 1.2rem;
            font-weight: 600;
            color: #4c418c;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-control {
            padding: 0.7rem 1rem;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #4c418c;
            box-shadow: 0 0 0 0.2rem rgba(76, 65, 140, 0.15);
        }

        .btn-reset {
            background: linear-gradient(135deg, #4c418c 0%, #006F94 100%);
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            width: 100%;
            /* transition: background 0.3s ease; */
        }

        .btn-reset:hover {
            background: linear-gradient(135deg, #3b3275 0%, #005f7f 100%);
        }

        .text-small {
            font-size: 0.85rem;
            text-align: center;
            margin-top: 1rem;
        }

        .text-small a {
            color: #4c418c;
            text-decoration: none;
            font-weight: 500;
        }

        .text-small a:hover {
            text-decoration: underline;
        }

        .reset-password-sec {
            display: flex;
            justify-content: center;
        }
    </style>
@endsection
