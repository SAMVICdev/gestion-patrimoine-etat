{{-- Fond institutionnel commun aux écrans d'authentification --}}
<div class="login-edge" aria-hidden="true"></div>

<div class="login-photo" aria-hidden="true">
    <img class="login-photo-img is-active" src="{{ asset('images/place_independance.jpg') }}" alt="">
    <img class="login-photo-img" src="{{ asset('images/palais_congres.jpg') }}" alt="">
    <img class="login-photo-img" src="{{ asset('images/palais_congres_02.jpg') }}" alt="">
    <div class="login-photo-overlay"></div>
</div>

<div class="login-blason" aria-hidden="true">
    <img src="{{ asset('images/blason_rt.png') }}"
         alt=""
         class="login-blason-img"/>
</div>

<script>
    // Diaporama d'arrière-plan — photos réelles de Lomé
    (function () {
        const slides = document.querySelectorAll('.login-photo-img');
        if (slides.length < 2) return;
        let current = 0;
        setInterval(function () {
            slides[current].classList.remove('is-active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('is-active');
        }, 6000);
    })();
</script>
