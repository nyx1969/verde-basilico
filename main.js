document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    menuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        mobileMenu.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    });

    // Chiudi il menu quando si clicca su un link
    const menuLinks = document.querySelectorAll('.mobile-menu a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            mobileMenu.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    });
});
