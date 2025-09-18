// Pokemon Management System JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
    initializeModals();
    
    // Initialize Pokemon operations
    initializePokemonOperations();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize form submissions
    initializeFormSubmissions();
});

// Modal functionality
function initializeModals() {
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.modal .close');
    
    // Close modal when clicking close button
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Add Pokemon button
    const addPokemonBtn = document.getElementById('add-pokemon-btn');
    const addPokemonModal = document.getElementById('add-pokemon-modal');
    
    if (addPokemonBtn && addPokemonModal) {
        addPokemonBtn.addEventListener('click', function() {
            addPokemonModal.style.display = 'block';
            resetAddPokemonForm();
        });
    }
    
    // Edit Pokemon buttons
    const editButtons = document.querySelectorAll('.edit-pokemon-btn');
    const editModal = document.getElementById('edit-pokemon-modal');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const pokemonId = this.dataset.id;
            openEditModal(pokemonId);
        });
    });
}

// Pokemon operations
function initializePokemonOperations() {
    // Search Pokemon form
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            searchPokemon();
        });
    }
    
    // Add Pokemon form
    const addForm = document.getElementById('add-pokemon-form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addPokemon();
        });
    }
    
    // Edit Pokemon form
    const editForm = document.getElementById('edit-pokemon-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            editPokemon();
        });
    }
    
    // Delete Pokemon button
    const deleteBtn = document.getElementById('delete-pokemon-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this Pokémon from your collection?')) {
                deletePokemon();
            }
        });
    }
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('search-pokemon');
    const pokemonCards = document.querySelectorAll('.pokemon-card');
    
    if (searchInput && pokemonCards.length > 0) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            pokemonCards.forEach(card => {
                const pokemonName = card.dataset.name || '';
                const pokemonId = card.dataset.pokemonId || '';
                
                if (pokemonName.includes(query) || pokemonId.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

// Form submissions
function initializeFormSubmissions() {
    // Add loading states to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Carregando...';
                
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitBtn.dataset.originalText || 'Submit';
                }, 3000);
            }
        });
    });
}

// Search Pokemon via PokeAPI
async function searchPokemon() {
    const query = document.getElementById('pokemon-search').value.trim();
    const previewDiv = document.getElementById('pokemon-preview');
    const addForm = document.getElementById('add-pokemon-form');
    
    if (!query) {
        showAlert('Please enter a Pokémon name or ID', 'error');
        return;
    }
    
    try {
        showLoading('Buscando Pokémon...');
        
        const response = await fetch(`pokemon_operations.php?action=search_pokeapi&query=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        hideLoading();
        
        if (data.success) {
            const pokemon = data.pokemon;
            
            // Show preview
            document.getElementById('preview-sprite').src = pokemon.sprite;
            document.getElementById('preview-name').textContent = capitalizeFirstLetter(pokemon.name);
            document.getElementById('preview-types').innerHTML = pokemon.types.map(type => 
                `<span class="type type-${type}">${capitalizeFirstLetter(type)}</span>`
            ).join(' ');
            document.getElementById('preview-abilities').innerHTML = '<strong>Abilities:</strong> ' + 
                pokemon.abilities.map(ability => capitalizeFirstLetter(ability)).join(', ');
            
            // Fill form fields
            document.getElementById('pokemon-id').value = pokemon.id;
            document.getElementById('pokemon-name').value = pokemon.name;
            document.getElementById('pokemon-type1').value = pokemon.types[0] || '';
            document.getElementById('pokemon-type2').value = pokemon.types[1] || '';
            document.getElementById('pokemon-sprite').value = pokemon.sprite;
            document.getElementById('pokemon-abilities').value = pokemon.abilities.join(', ');
            
            previewDiv.style.display = 'flex';
            addForm.style.display = 'block';
        } else {
            showAlert(data.message, 'error');
            previewDiv.style.display = 'none';
            addForm.style.display = 'none';
        }
    } catch (error) {
        hideLoading();
        showAlert('Ocorreu um erro durante a busca. Tente novamente.', 'error');
        console.error('Search error:', error);
    }
}

// Add Pokemon to collection
async function addPokemon() {
    const formData = new FormData(document.getElementById('add-pokemon-form'));
    formData.append('action', 'add');
    
    try {
        const response = await fetch('pokemon_operations.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('add-pokemon-modal').style.display = 'none';
            // Refresh the page to show the new Pokemon
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        showAlert('Ocorreu um erro. Tente novamente.', 'error');
        console.error('Add Pokemon error:', error);
    }
}

// Open edit modal
async function openEditModal(pokemonId) {
    const editModal = document.getElementById('edit-pokemon-modal');
    
    try {
        showLoading('Carregando dados do Pokémon...');
        
        const formData = new FormData();
        formData.append('action', 'get');
        formData.append('id', pokemonId);
        
        const response = await fetch('pokemon_operations.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        hideLoading();
        
        if (data.success) {
            const pokemon = data.pokemon;
            
            document.getElementById('edit-id').value = pokemon.id;
            document.getElementById('edit-nickname').value = pokemon.nickname || '';
            document.getElementById('edit-level').value = pokemon.level_caught;
            document.getElementById('edit-notes').value = pokemon.notes || '';
            
            editModal.style.display = 'block';
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        hideLoading();
        showAlert('Ocorreu um erro. Tente novamente.', 'error');
        console.error('Load Pokemon error:', error);
    }
}

// Edit Pokemon
async function editPokemon() {
    const formData = new FormData(document.getElementById('edit-pokemon-form'));
    formData.append('action', 'edit');
    
    try {
        const response = await fetch('pokemon_operations.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('edit-pokemon-modal').style.display = 'none';
            // Refresh the page to show updated Pokemon
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        showAlert('Ocorreu um erro. Tente novamente.', 'error');
        console.error('Edit Pokemon error:', error);
    }
}

// Delete Pokemon
async function deletePokemon() {
    const pokemonId = document.getElementById('edit-id').value;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', pokemonId);
    
    try {
        const response = await fetch('pokemon_operations.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('edit-pokemon-modal').style.display = 'none';
            // Refresh the page to remove deleted Pokemon
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        showAlert('Ocorreu um erro. Tente novamente.', 'error');
        console.error('Delete Pokemon error:', error);
    }
}

// Reset add Pokemon form
function resetAddPokemonForm() {
    document.getElementById('search-form').reset();
    document.getElementById('pokemon-preview').style.display = 'none';
    document.getElementById('add-pokemon-form').style.display = 'none';
    
    if (document.getElementById('add-pokemon-form')) {
        document.getElementById('add-pokemon-form').reset();
    }
}

// Show alert message
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-dynamic');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dynamic`;
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.maxWidth = '400px';
    alert.textContent = message;
    
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Show loading indicator
function showLoading(message = 'Carregando...') {
    // Remove existing loading
    const existingLoading = document.querySelectorAll('.loading-indicator');
    existingLoading.forEach(loading => loading.remove());
    
    const loading = document.createElement('div');
    loading.className = 'loading-indicator';
    loading.style.position = 'fixed';
    loading.style.top = '50%';
    loading.style.left = '50%';
    loading.style.transform = 'translate(-50%, -50%)';
    loading.style.background = 'rgba(0,0,0,0.8)';
    loading.style.color = 'white';
    loading.style.padding = '20px 30px';
    loading.style.borderRadius = '10px';
    loading.style.zIndex = '9999';
    loading.style.fontSize = '16px';
    loading.textContent = message;
    
    document.body.appendChild(loading);
}

// Hide loading indicator
function hideLoading() {
    const loadingIndicators = document.querySelectorAll('.loading-indicator');
    loadingIndicators.forEach(loading => loading.remove());
}

// Utility function to capitalize first letter
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Form validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Password strength indicator (for future use)
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

// Smooth scroll for future navigation
function smoothScrollTo(target) {
    document.querySelector(target).scrollIntoView({
        behavior: 'smooth'
    });
}

// Add some cool animations on page load
window.addEventListener('load', function() {
    // Animate Pokemon cards
    const pokemonCards = document.querySelectorAll('.pokemon-card');
    pokemonCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Animate feature cards
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
});

// Service Worker registration (for future PWA features)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // Will implement in future versions
        console.log('Service Worker support detected');
    });
}