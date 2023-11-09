let navbar = document.querySelector('.header .navbar');

  document.querySelector('#menu-btn').onclick = () =>{
    navbar.classList.toggle('active');
  }
  
  window.onscroll = () =>{
    navbar.classList.remove('active');
  }

const diapositivas = document.querySelectorAll('.diapositivas');
let currentIndex = 0;

function mostrarDiapositiva() {
    diapositivas.forEach((diapositiva) => {
        diapositiva.style.display = 'none';
    });

    currentIndex = (currentIndex + 1) % diapositivas.length;
    diapositivas[currentIndex].style.display = 'block';

    setTimeout(mostrarDiapositiva, 2000);
}

mostrarDiapositiva();