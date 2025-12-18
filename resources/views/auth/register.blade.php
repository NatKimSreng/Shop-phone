<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Register')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <style>
        /* Your custom styles here (or move to layout CSS if shared) */

        *{|
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
            background: #f7f9fb;
        }

        .form-card {
            width: 320px;
            height: auto;
            max-width: 100%;
            background: #fff;
            border: 1px solid #e0e6ea;
            border-radius: 25px;
            padding: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .form-card form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin: 0;
        }

        .form-card label {
            font-size: 14px;
            margin-bottom: 6px;
            display: block;
        }

        .form-card input[type="text"],
        .form-card input[type="email"],
        .form-card input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d1d7db;
            border-radius: 20px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .form-card .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .form-card .btn {
            display: block;
            margin: 12px auto 0;
            padding: 8px 18px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            width: 100px;
            justify-content: center;
            text-decoration: none;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
        }
    </style>

    <div class="container">
        <div class="form-card">
            <form class="form-group" action="{{ route('register.store') }}" method="POST">
                <legend style="font-size:18px;margin:0 0 8px 0; text-align: center;">Register</legend>
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <p class="error-message">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div>
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" placeholder="Username"
                        value="{{ old('username') }}" required>
                    @error('username')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" placeholder="Email" value="{{ old('email') }}"
                        required>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                {{-- <div>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Password" minlength="8"
                        required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Confirm Password" required>
                    @error('password_confirmation')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div style="position: relative;">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Password" minlength="8" required
                        style="padding-right: 35px;">
                    <i class="fa fa-eye" id="togglePassword1"
                        style="position: absolute; right: 10px; top: 35px; cursor: pointer;"></i>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div style="position: relative;">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Confirm Password" required style="padding-right: 35px;">
                    <i class="fa fa-eye" id="togglePassword2"
                        style="position: absolute; right: 10px; top: 35px; cursor: pointer;"></i>
                    @error('password_confirmation')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Removed Remember Me from register (optional) --}}

                <div style="display: flex; justify-content: center; gap: 10px; ">
                    <button type="submit" class="btn">Register</button>
                    <a href="{{ route('admin.login') }}" class="btn btn-secondary">Sign in</a>
                </div>
            </form>
        </div>
    </div>

</body>

</html>

<script>
    const togglePassword1 = document.querySelector('#togglePassword1');
    const togglePassword2 = document.querySelector('#togglePassword2');
    const password = document.querySelector('#password');
    const confirmPassword = document.querySelector('#password_confirmation');

    togglePassword1.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    togglePassword2.addEventListener('click', function () {
        const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPassword.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
</script>
