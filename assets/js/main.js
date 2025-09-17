/**
 * Pokemon Management System - Main JavaScript
 */

class PokemonManager {
    constructor() {
        this.pokeApiBase = 'https://pokeapi.co/api/v2/pokemon/';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupModals();
        this.setupSearch();
    }

    setupEventListeners() {
        // Add Pokemon form submission
        const addForm = document.getElementById('addPokemonForm');
        if (addForm) {
            addForm.addEventListener('submit', this.handleAddPokemon.bind(this));
        }

        // Edit Pokemon form submission
        const editForm = document.getElementById('editPokemonForm');
        if (editForm) {
            editForm.addEventListener('submit', this.handleEditPokemon.bind(this));
        }

        // Pokemon search
        const pokemonSearch = document.getElementById('pokemonSearch');
        if (pokemonSearch) {
            pokemonSearch.addEventListener('input', this.handlePokemonSearch.bind(this));
        }

        // Delete confirmations
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-pokemon')) {
                if (confirm('Are you sure you want to delete this Pokémon from your collection?')) {
                    this.deletePokemon(e.target.dataset.id);
                }
            }
        });

        // Edit Pokemon buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('edit-pokemon')) {
                this.openEditModal(e.target.dataset.id);
            }
        });
    }

    setupModals() {
        // Modal close functionality
        const modals = document.querySelectorAll('.modal');
        const closeBtns = document.querySelectorAll('.close');
        
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.closeModal(btn.closest('.modal'));
            });
        });

        // Close modal when clicking outside
        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal);
                }
            });
        });

        // Open add pokemon modal
        const addBtn = document.getElementById('addPokemonBtn');
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                this.openModal('addPokemonModal');
            });
        }
    }

    setupSearch() {
        const searchInput = document.getElementById('collectionSearch');
        if (searchInput) {
            searchInput.addEventListener('input', this.filterCollection.bind(this));
        }
    }

    async handleAddPokemon(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const pokemonName = formData.get('pokemon_name').toLowerCase().trim();

        if (!pokemonName) {
            this.showAlert('Please enter a Pokémon name', 'error');
            return;
        }

        // Show loading
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.innerHTML = '<span class="loading"></span> Loading...';
        submitBtn.disabled = true;

        try {
            // Fetch Pokemon data from PokeAPI
            const pokemonData = await this.fetchPokemonData(pokemonName);
            
            if (!pokemonData) {
                this.showAlert('Pokémon not found. Please check the name and try again.', 'error');
                return;
            }

            // Add the fetched data to form
            formData.set('pokemon_id', pokemonData.id);
            formData.set('pokemon_sprite', pokemonData.sprites.front_default);
            formData.set('pokemon_types', JSON.stringify(pokemonData.types.map(t => t.type.name)));
            formData.set('pokemon_abilities', JSON.stringify(pokemonData.abilities.map(a => a.ability.name)));

            // Submit to server
            const response = await fetch('pokemon_operations.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Pokémon added to your collection successfully!', 'success');
                this.closeModal(document.getElementById('addPokemonModal'));
                form.reset();
                // Reload the page to show new pokemon
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message || 'Failed to add Pokémon', 'error');
            }

        } catch (error) {
            console.error('Error adding Pokemon:', error);
            this.showAlert('An error occurred while adding the Pokémon', 'error');
        } finally {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    }

    async handleEditPokemon(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.innerHTML = '<span class="loading"></span> Updating...';
        submitBtn.disabled = true;

        try {
            const response = await fetch('pokemon_operations.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Pokémon updated successfully!', 'success');
                this.closeModal(document.getElementById('editPokemonModal'));
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message || 'Failed to update Pokémon', 'error');
            }

        } catch (error) {
            console.error('Error updating Pokemon:', error);
            this.showAlert('An error occurred while updating the Pokémon', 'error');
        } finally {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    }

    async handlePokemonSearch(e) {
        const query = e.target.value.toLowerCase().trim();
        const resultsDiv = document.getElementById('searchResults');
        
        if (!query || query.length < 2) {
            resultsDiv.innerHTML = '';
            resultsDiv.classList.add('hidden');
            return;
        }

        resultsDiv.innerHTML = '<div class="loading"></div> Searching...';
        resultsDiv.classList.remove('hidden');

        try {
            const pokemonData = await this.fetchPokemonData(query);
            
            if (pokemonData) {
                resultsDiv.innerHTML = `
                    <div class="search-result" onclick="pokemonManager.selectPokemon('${pokemonData.name}')">
                        <img src="${pokemonData.sprites.front_default}" alt="${pokemonData.name}" width="64" height="64">
                        <div>
                            <strong>${this.capitalize(pokemonData.name)}</strong>
                            <p>ID: ${pokemonData.id}</p>
                        </div>
                    </div>
                `;
            } else {
                resultsDiv.innerHTML = '<p>No Pokémon found with that name.</p>';
            }
        } catch (error) {
            resultsDiv.innerHTML = '<p>Error searching for Pokémon.</p>';
        }
    }

    async fetchPokemonData(pokemonName) {
        try {
            const response = await fetch(`${this.pokeApiBase}${pokemonName.toLowerCase()}`);
            if (!response.ok) {
                return null;
            }
            return await response.json();
        } catch (error) {
            console.error('Error fetching Pokemon data:', error);
            return null;
        }
    }

    selectPokemon(pokemonName) {
        const searchInput = document.getElementById('pokemonSearch');
        const nameInput = document.getElementById('pokemon_name');
        const resultsDiv = document.getElementById('searchResults');
        
        if (searchInput) searchInput.value = pokemonName;
        if (nameInput) nameInput.value = pokemonName;
        resultsDiv.innerHTML = '';
        resultsDiv.classList.add('hidden');
    }

    async openEditModal(pokemonId) {
        try {
            const response = await fetch(`pokemon_operations.php?action=get&id=${pokemonId}`);
            const result = await response.json();
            
            if (result.success) {
                const pokemon = result.data;
                
                // Populate edit form
                document.getElementById('edit_pokemon_id').value = pokemon.id;
                document.getElementById('edit_nickname').value = pokemon.nickname || '';
                document.getElementById('edit_level').value = pokemon.level;
                document.getElementById('edit_notes').value = pokemon.notes || '';
                
                this.openModal('editPokemonModal');
            } else {
                this.showAlert('Failed to load Pokémon data', 'error');
            }
        } catch (error) {
            console.error('Error loading Pokemon for edit:', error);
            this.showAlert('An error occurred while loading Pokémon data', 'error');
        }
    }

    async deletePokemon(pokemonId) {
        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('pokemon_id', pokemonId);
            formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

            const response = await fetch('pokemon_operations.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Pokémon removed from collection', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showAlert(result.message || 'Failed to delete Pokémon', 'error');
            }

        } catch (error) {
            console.error('Error deleting Pokemon:', error);
            this.showAlert('An error occurred while deleting the Pokémon', 'error');
        }
    }

    filterCollection(e) {
        const query = e.target.value.toLowerCase().trim();
        const pokemonCards = document.querySelectorAll('.pokemon-card');
        
        pokemonCards.forEach(card => {
            const name = card.querySelector('.pokemon-name')?.textContent.toLowerCase() || '';
            const nickname = card.querySelector('.pokemon-nickname')?.textContent.toLowerCase() || '';
            
            if (name.includes(query) || nickname.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modal) {
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
            
            // Reset forms in modal
            const forms = modal.querySelectorAll('form');
            forms.forEach(form => {
                if (form.id !== 'editPokemonForm') { // Don't reset edit form automatically
                    form.reset();
                }
            });
            
            // Hide search results
            const searchResults = modal.querySelector('#searchResults');
            if (searchResults) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
            }
        }
    }

    showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        
        // Insert at top of main container
        const container = document.querySelector('.container');
        if (container) {
            container.insertBefore(alert, container.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Form validation
    validateForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return false;

        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = '#dc3545';
                isValid = false;
            } else {
                field.style.borderColor = '#ddd';
            }
        });

        return isValid;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.pokemonManager = new PokemonManager();
});

// Utility functions
function confirmDelete(message = 'Are you sure?') {
    return confirm(message);
}

// Handle escape key to close modals
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal[style*="block"]');
        openModals.forEach(modal => {
            if (window.pokemonManager) {
                window.pokemonManager.closeModal(modal);
            }
        });
    }
});