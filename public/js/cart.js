document.addEventListener('DOMContentLoaded', () => {
    // The cart badge element in the navigation
    const cartBadge = document.querySelector('.fa-cart-shopping').nextElementSibling.nextElementSibling;
    
    // Update cart badge on initial load
    fetchCart();

    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const button = e.currentTarget;
            const productId = button.dataset.id;
            const price = button.dataset.price;
            
            // Add loading state
            const icon = button.querySelector('i');
            const originalClass = icon.className;
            icon.className = 'fa-solid fa-spinner fa-spin';
            
            fetch('api/cart_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId,
                    price: price,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.require_login) {
                    alert(data.message);
                    window.location.href = 'login.php';
                    return;
                }
                
                if (data.success) {
                    // Update cart count badge
                    if (cartBadge) {
                        cartBadge.textContent = data.totalItems;
                        // Add a small bounce animation
                        cartBadge.classList.add('animate-bounce');
                        setTimeout(() => cartBadge.classList.remove('animate-bounce'), 1000);
                    }
                } else {
                    console.error('Error adding to cart:', data.message);
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                // Restore icon after request
                icon.className = originalClass;
            });
        });
    });

    function fetchCart() {
        fetch('api/cart_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'get' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && cartBadge) {
                cartBadge.textContent = data.totalItems;
            }
        })
        .catch(error => console.error('Error fetching cart:', error));
    }
});

// Global functions for cart page buttons
window.removeCartItem = function(productId) {
    fetch('api/cart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'remove', product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else if (data.require_login) {
            window.location.href = 'login.php';
        }
    })
    .catch(err => console.error(err));
};

window.updateQuantity = function(productId, change, price) {
    const qtySpan = document.querySelector(`.item-qty-${productId}`);
    if (!qtySpan) return;
    
    let currentQty = parseInt(qtySpan.textContent);
    let newQty = currentQty + change;
    
    if (newQty < 1) {
        window.removeCartItem(productId);
        return;
    }
    
    fetch('api/cart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'update', product_id: productId, quantity: newQty, price: price })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else if (data.require_login) {
            window.location.href = 'login.php';
        }
    })
    .catch(err => console.error(err));
};
