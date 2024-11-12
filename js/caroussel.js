// Fonction pour initialiser un carrousel
function initCarousel(carouselContainer, nextBtnId, prevBtnId, slidesToShow) {
  const carousel = carouselContainer.querySelector('.carousel');
  const slides = carouselContainer.querySelectorAll('.slide');
  const totalSlides = slides.length;
  let currentSlide = 0;

  // Gestion du bouton suivant
  document.getElementById(nextBtnId).addEventListener('click', () => {
    if (currentSlide < totalSlides - slidesToShow) {
      currentSlide += slidesToShow;
    } else {
      currentSlide = 0; // Revenir au début
    }
    updateCarousel();
  });

  // Gestion du bouton précédent
  document.getElementById(prevBtnId).addEventListener('click', () => {
    if (currentSlide > 0) {
      currentSlide -= slidesToShow;
    } else {
      currentSlide = totalSlides - slidesToShow; // Aller à la fin
    }
    updateCarousel();
  });

  function updateCarousel() {
    const slideWidth = slides[0].clientWidth; // Largeur d'une diapositive
    carousel.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
  }
}

// Initialisation des deux carrousels
initCarousel(document.querySelector('#carousel1'), 'next1', 'prev1', 3);
initCarousel(document.querySelector('#carousel2'), 'next2', 'prev2', 3);
