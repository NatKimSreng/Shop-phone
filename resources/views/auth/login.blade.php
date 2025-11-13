<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Login')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <style>
        /* Your custom styles here (or move to layout CSS if shared) */
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
            width: 300px;
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
            <form class="form-group" action="{{ route('login.store') }}" method="POST">
                <legend style="font-size:18px;margin:0 0 8px 0; text-align: center;">Login</legend>
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <p class="error-message">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div>
                    <label for="Email">Email:</label>
                    <input type="text" name="email" id="username" placeholder="Email" value="{{ old('email') }}"
                        required>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                {{-- <div>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div> --}}
                <div style="position: relative;">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Password" required
                        style="padding-right: 35px;">

                    <!-- Eye icon -->
                    <i class="fa fa-eye" id="togglePassword"
                        style="position: absolute; right: 10px; top: 35px; cursor: pointer;"></i>

                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" style="margin:0;">Remember Me</label>
                </div>

                <div style="display: flex; justify-content: center; gap: 10px; ">
                    <button type="submit" class="btn">Login</button>
                    <a href="{{ route('auth.register') }}" class="btn btn-secondary">Register</a>
                </div>
            </form>
        </div>
    </div>


</body>

</html>

{{-- Toggle Password Visibility Script --}}

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the icon class
        this.classList.toggle('fa-eye-slash');
    });
</script>
