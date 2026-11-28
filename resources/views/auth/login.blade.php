<x-layout-app :title="$title">
    <main class="login-page">
        <section class="login-card" aria-labelledby="login-title">
            <div class="login-brand">
                <img src="{{ asset('kemenkum.png') }}" alt="" width="70" height="70">
                <div>
                    <h1>{{ config('app.name') }}</h1>
                    <p>{{ config('app.brand_description') }}</p>
                </div>
            </div>

            <div class="login-panel">
                <h2 id="login-title">Masuk ke akun</h2>

                <form method="POST" action="{{ route('authenticate') }}" id="login-form" data-submit-message="Sedang masuk…">
                    @csrf

                    @if($errors->any())
                        <p class="login-error" role="alert">{{ $errors->first() }}</p>
                    @endif

                    <label for="email">Alamat email</label>
                    <input type="email" id="email" name="email" autocomplete="username"
                        value="{{ old('email') }}" @if($errors->has('email')) aria-invalid="true" @endif required>

                    <label for="password">Kata sandi</label>
                    <div class="login-password">
                        <input type="password" id="password" name="password" autocomplete="current-password"
                            @if($errors->has('password')) aria-invalid="true" @endif required>
                        <button type="button" class="login-password-toggle" data-password-toggle
                            aria-controls="password" aria-label="Tampilkan kata sandi" aria-pressed="false" hidden>
                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>

                    <label class="login-remember">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Ingat saya</span>
                    </label>

                    <button type="submit" class="login-submit">Masuk</button>
                </form>
            </div>
        </section>
    </main>
</x-layout-app>
