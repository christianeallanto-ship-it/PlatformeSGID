{{-- Auto-detect background images for the slideshow --}}
@php
    $bgImages = [];
    foreach (['login_bg.png', 'login_bg1.jpg', 'login_bg2.jpg', 'login_bg3.jpg', 'login_bg4.jpg'] as $file) {
        if (file_exists(public_path('images/' . $file))) {
            $bgImages[] = asset('images/' . $file);
        }
    }
    // Fallback if none exist
    if (empty($bgImages)) {
        $bgImages[] = 'linear-gradient(135deg, #0a1128 0%, #064e3b 100%)';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BENINCLEAN') }}</title>

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <style>
            body {
                font-family: 'Inter', sans-serif;
                overflow-x: hidden;
            }

            /* Animations for background and card */
            @keyframes fadeInBg {
                0% {
                    opacity: 0;
                    transform: scale(1.1);
                    filter: blur(4px);
                }
                30% {
                    opacity: 1;
                    filter: blur(0px);
                }
                100% {
                    transform: scale(1.02);
                }
            }

            @keyframes slideUpCard {
                0% {
                    opacity: 0;
                    transform: translateY(35px) scale(0.97);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .bg-animate {
                animation: fadeInBg 6s cubic-bezier(0.1, 0.8, 0.2, 1) forwards;
            }

            .card-animate {
                animation: slideUpCard 0.8s cubic-bezier(0.25, 1, 0.5, 1) 0.2s forwards;
                opacity: 0;
            }

            /* Design matching exactly the WhatsApp screenshot layout & color */
            .guest-card input[type="text"], 
            .guest-card input[type="email"], 
            .guest-card input[type="password"] {
                padding-left: 2.75rem !important;
                border-radius: 0.5rem !important;
                border-color: #cbd5e1 !important;
                box-shadow: none !important;
                transition: all 0.2s !important;
            }
            .guest-card input[type="text"]:focus, 
            .guest-card input[type="email"]:focus, 
            .guest-card input[type="password"]:focus {
                border-color: #00bac6 !important;
                box-shadow: 0 0 0 3px rgba(0, 186, 198, 0.15) !important;
            }
            .guest-card button, 
            .guest-card .bg-gray-800,
            .guest-card button[type="submit"] {
                background: #00bac6 !important;
                border-radius: 0.5rem !important;
                font-weight: 600 !important;
                color: white !important;
                padding-top: 0.625rem !important;
                padding-bottom: 0.625rem !important;
                border: none !important;
                box-shadow: 0 4px 10px rgba(0, 186, 198, 0.2) !important;
                transition: all 0.2s !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .guest-card button:hover, 
            .guest-card .bg-gray-800:hover {
                background: #00a8b4 !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 6px 14px rgba(0, 186, 198, 0.3) !important;
            }

            /* Ken Burns Effect for background slideshow */
            @keyframes kenBurns {
                0% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            .bg-slide-active {
                animation: kenBurns 8s ease-out forwards;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased min-h-screen relative flex items-center justify-center p-4 bg-slate-900">
        
        <!-- Slideshow Background Container -->
        <div class="absolute inset-0 overflow-hidden z-0 bg-slate-950">
            @foreach($bgImages as $index => $img)
                <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-opacity duration-[1500ms] ease-in-out bg-slide {{ $index === 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-105' }}" 
                     style="background-image: {{ str_contains($img, 'gradient') ? $img : "url('$img')" }}; z-index: 0; transform-origin: center;">
                </div>
            @endforeach
            <!-- Professional dark gradient overlay to ensure card readability -->
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/70 via-transparent to-slate-950/70 z-0"></div>
        </div>

        @php
            $title = "Connexion";
            $subtitle = "Plateforme de monitoring";
            if (Route::is('register')) {
                $title = "Inscription";
                $subtitle = "Créer un compte BENINCLEAN";
            } elseif (Route::is('password.request') || Route::is('password.email')) {
                $title = "Récupération";
                $subtitle = "Réinitialiser votre mot de passe";
            } elseif (Route::is('password.reset')) {
                $title = "Nouveau mot de passe";
                $subtitle = "Choisissez votre nouveau mot de passe";
            } elseif (Route::is('verification.notice')) {
                $title = "Vérification";
                $subtitle = "Confirmez votre adresse e-mail";
            }
        @endphp

        <!-- Login/Auth Card (Centered and Animated) -->
        <div class="w-full sm:max-w-md bg-white shadow-2xl rounded-2xl overflow-hidden z-10 guest-card border border-slate-100 flex flex-col card-animate">
            <!-- Card Header: Solid turquoise color matching the screenshot exactly -->
            <div class="px-6 py-10 text-center relative flex flex-col items-center justify-center" style="background: #00bac6;">
                <h2 class="text-2xl font-bold text-white tracking-wide mt-2">{{ $title }}</h2>
                <p class="text-xs text-cyan-100/90 font-medium mt-1">{{ $subtitle }}</p>
                
                <!-- Circular Avatar overlapping the header exactly like the screenshot -->
                <div class="absolute -bottom-7 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                    <div class="w-full h-full rounded-full flex items-center justify-center" style="background: #00bac6;">
                        <i data-lucide="user-check" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Form Content slot -->
            <div class="px-8 pt-12 pb-8 flex-1">
                {{ $slot }}
            </div>
        </div>
        
        <!-- Small Footer -->
        <div class="text-xs text-slate-300 mt-6 z-10 text-center font-medium drop-shadow absolute bottom-4">
            &copy; {{ date('Y') }} BENINCLEAN &bull; Tous droits réservés
        </div>

        <!-- Script to inject icons inside inputs and toggle active states -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Email inputs
                const emailInputs = document.querySelectorAll('input[type="email"]');
                emailInputs.forEach(input => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative mt-1';
                    input.parentNode.insertBefore(wrapper, input);
                    wrapper.appendChild(input);
                    
                    const icon = document.createElement('i');
                    icon.setAttribute('data-lucide', 'mail');
                    icon.className = 'absolute left-3.5 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-400 transition-colors';
                    wrapper.appendChild(icon);
                });

                // Password inputs
                const passwordInputs = document.querySelectorAll('input[type="password"]');
                passwordInputs.forEach(input => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative mt-1';
                    input.parentNode.insertBefore(wrapper, input);
                    wrapper.appendChild(input);
                    
                    const icon = document.createElement('i');
                    icon.setAttribute('data-lucide', 'lock');
                    icon.className = 'absolute left-3.5 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-400 transition-colors';
                    wrapper.appendChild(icon);
                });

                // Name or text inputs
                const textInputs = document.querySelectorAll('input[type="text"]');
                textInputs.forEach(input => {
                    if (input.id === 'name' || input.name === 'name') {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'relative mt-1';
                        input.parentNode.insertBefore(wrapper, input);
                        wrapper.appendChild(input);
                        
                        const icon = document.createElement('i');
                        icon.setAttribute('data-lucide', 'user');
                        icon.className = 'absolute left-3.5 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-400 transition-colors';
                        wrapper.appendChild(icon);
                    }
                });

                // Initialize Lucide icons
                if (window.lucide) {
                    window.lucide.createIcons();
                }

                // Handle icon active color on focus
                const inputs = document.querySelectorAll('.guest-card input[type="text"], .guest-card input[type="email"], .guest-card input[type="password"]');
                inputs.forEach(input => {
                    input.addEventListener('focus', () => {
                        const icon = input.parentElement.querySelector('i');
                        if (icon) {
                            icon.classList.remove('text-slate-400');
                            icon.classList.add('text-[#00bac6]');
                            icon.style.color = '#00bac6';
                        }
                    });
                    input.addEventListener('blur', () => {
                        const icon = input.parentElement.querySelector('i');
                        if (icon) {
                            icon.classList.remove('text-[#00bac6]');
                            icon.classList.add('text-slate-400');
                            icon.style.color = '';
                        }
                    });
                });

                // Background slideshow logic with Ken Burns effect
                const slides = document.querySelectorAll('.bg-slide');
                if (slides.length > 1) {
                    let currentSlide = 0;
                    
                    // Activate Ken Burns on first slide
                    slides[0].classList.add('bg-slide-active');

                    setInterval(() => {
                        const prev = slides[currentSlide];
                        prev.classList.remove('opacity-100', 'scale-100', 'bg-slide-active');
                        prev.classList.add('opacity-0', 'scale-105');
                        
                        currentSlide = (currentSlide + 1) % slides.length;
                        
                        const next = slides[currentSlide];
                        next.classList.remove('opacity-0', 'scale-105');
                        next.classList.add('opacity-100', 'scale-100', 'bg-slide-active');
                    }, 6000); // changes every 6 seconds
                }
            });
        </script>
    </body>
</html>
