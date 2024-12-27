document.addEventListener("DOMContentLoaded", function () {
    const carousels = document.querySelectorAll("[data-carousel='static']");

    carousels.forEach((carousel) => {
        const items = carousel.querySelectorAll("[data-carousel-item]");
        const indicators = carousel.querySelectorAll("[data-carousel-slide-to]");
        const prevButton = carousel.querySelector("[data-carousel-prev]");
        const nextButton = carousel.querySelector("[data-carousel-next]");
        let currentIndex = 0;
        let autoplayInterval;

        const showItem = (index) => {
            items.forEach((item, i) => {
                item.classList.toggle("active", i === index);
            });
            indicators.forEach((indicator, i) => {
                indicator.classList.toggle("active", i === index);
            });
        };

        const autoplay = () => {
            autoplayInterval = setInterval(() => {
                currentIndex = (currentIndex + 1) % items.length;
                showItem(currentIndex);
            }, 3000);
        };

        const stopAutoplay = () => {
            clearInterval(autoplayInterval);
        };

        nextButton.addEventListener("click", () => {
            stopAutoplay();
            currentIndex = (currentIndex + 1) % items.length;
            showItem(currentIndex);
            autoplay();
        });

        prevButton.addEventListener("click", () => {
            stopAutoplay();
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            showItem(currentIndex);
            autoplay();
        });

        indicators.forEach((indicator, index) => {
            indicator.addEventListener("click", () => {
                stopAutoplay();
                currentIndex = index;
                showItem(currentIndex);
                autoplay();
            });
        });

        // Initialisation
        showItem(currentIndex);
        autoplay();
    });
});