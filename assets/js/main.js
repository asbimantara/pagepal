document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
  const navLinks = document.querySelector(".nav-links");

  mobileMenuBtn.addEventListener("click", function () {
    navLinks.classList.toggle('active');
    if (navLinks.classList.contains('active')) {
      navLinks.style.display = 'flex';
    } else {
      setTimeout(() => {
        navLinks.style.display = 'none';
      }, 300); // Sesuaikan dengan durasi transisi CSS
    }
  });
});

// Quote Slider
document.addEventListener('DOMContentLoaded', function() {
    const quoteSlider = document.getElementById('quoteSlider');
    if (!quoteSlider) return;

    const quotes = quoteSlider.querySelectorAll('.quote');
    const prevBtn = quoteSlider.querySelector('.prev-quote');
    const nextBtn = quoteSlider.querySelector('.next-quote');
    const dotsContainer = quoteSlider.querySelector('.quote-dots');
    
    let currentQuote = 0;

    // Create dots
    quotes.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToQuote(index));
        dotsContainer.appendChild(dot);
    });

    const dots = dotsContainer.querySelectorAll('.dot');

    function updateQuotes() {
        quotes.forEach(quote => quote.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        quotes[currentQuote].classList.add('active');
        dots[currentQuote].classList.add('active');
    }

    function nextQuote() {
        currentQuote = (currentQuote + 1) % quotes.length;
        updateQuotes();
    }

    function prevQuote() {
        currentQuote = (currentQuote - 1 + quotes.length) % quotes.length;
        updateQuotes();
    }

    function goToQuote(index) {
        currentQuote = index;
        updateQuotes();
    }

    // Event listeners
    nextBtn.addEventListener('click', nextQuote);
    prevBtn.addEventListener('click', prevQuote);

    // Auto slide every 5 seconds
    let autoSlide = setInterval(nextQuote, 5000);

    // Pause auto slide when hovering over slider
    quoteSlider.addEventListener('mouseenter', () => clearInterval(autoSlide));
    quoteSlider.addEventListener('mouseleave', () => {
        clearInterval(autoSlide);
        autoSlide = setInterval(nextQuote, 5000);
    });
});
