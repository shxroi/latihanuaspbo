// Slideshow functionality
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");
    
    if (n > slides.length) {slideIndex = 1}    
    if (n < 1) {slideIndex = slides.length}
    
    for (i = 0; i < slides.length; i++) {
        slides[i].classList.remove("opacity-100");
        slides[i].classList.add("opacity-0");
        setTimeout(() => {
            slides[i].classList.add("hidden");
        }, 300); // Menunggu transisi opacity selesai
        dots[i].classList.remove("bg-white");
        dots[i].classList.add("bg-white/50");
    }
    
    slides[slideIndex-1].classList.remove("hidden");
    // Trigger reflow
    slides[slideIndex-1].offsetHeight;
    slides[slideIndex-1].classList.remove("opacity-0");
    slides[slideIndex-1].classList.add("opacity-100");
    
    dots[slideIndex-1].classList.remove("bg-white/50");
    dots[slideIndex-1].classList.add("bg-white");
}

// Auto slide
setInterval(function() {
    plusSlides(1);
}, 5000);

// Scroll to products
function scrollToProducts() {
    document.getElementById('productsGrid').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

// Product Modal
let currentProduct = null;

function showProductDetail(product) {
    currentProduct = product;
    document.getElementById('modalTitle').textContent = product.name;
    document.getElementById('modalImage').src = '../uploads/products/' + product.image_url;
    document.getElementById('modalDescription').textContent = product.description;
    document.getElementById('modalPrice').textContent = formatRupiah(product.price);
    document.getElementById('modalStock').textContent = 'Stock: ' + product.stock;
    document.getElementById('productModal').classList.remove('hidden');
}

function closeProductModal() {
    document.getElementById('productModal').classList.add('hidden');
    currentProduct = null;
}

function addToCartFromModal() {
    if (currentProduct) {
        addToCart(currentProduct.product_id);
        closeProductModal();
    }
}
