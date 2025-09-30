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
    
    console.log('Inicializando botões de editar. Encontrados:', editButtons.length, 'botões');
    
    editButtons.forEach((button, index) => {
        const pokemonId = button.dataset.id;
        console.log(`Botão ${index + 1}: ID = "${pokemonId}"`);
        
        button.addEventListener('click', function() {
            const pokemonId = this.dataset.id;
            console.log('Clicou em editar Pokémon, ID:', pokemonId);
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
            if (confirm('Tem certeza de que deseja remover este Pokémon da sua coleção?')) {
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
        showAlert('Por favor, digite o nome ou ID de um Pokémon', 'error');
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
        console.log('Enviando dados para adicionar Pokémon:', Object.fromEntries(formData));
        
        const response = await fetch('pokemon_operations.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Response status:', response.status);
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        const data = JSON.parse(responseText);
        console.log('Parsed response:', data);
        
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('add-pokemon-modal').style.display = 'none';
            
            console.log('Pokémon adicionado com sucesso! ID retornado:', data.pokemon_id);
            
            // Try dynamic update first, then fallback to reload
            const dynamicUpdateSuccess = await addPokemonToDOMDynamically(formData, data.pokemon_id);
            
            if (!dynamicUpdateSuccess) {
                console.log('Atualização dinâmica falhou, recarregando página...');
                // Force reload without cache
                setTimeout(() => {
                    console.log('Recarregando página...');
                    window.location.reload(true);
                }, 2000);
            } else {
                console.log('Pokémon adicionado dinamicamente à página!');
            }
        } else {
            showAlert(data.message, 'error');
            console.error('Erro do servidor:', data.message);
        }
    } catch (error) {
        showAlert('Ocorreu um erro. Tente novamente.', 'error');
        console.error('Add Pokemon error:', error);
    }
}

// Add Pokemon to DOM dynamically without reload
async function addPokemonToDOMDynamically(formData, realPokemonId) {
    try {
        const pokemonGrid = document.getElementById('pokemon-grid');
        const emptyCollection = document.querySelector('.empty-collection');
        
        if (!pokemonGrid && !emptyCollection) {
            console.log('Não foi possível encontrar elementos necessários no DOM');
            return false;
        }
        
        // Extract data from form
        const pokemonData = {
            pokemon_id: formData.get('pokemon_id'),
            pokemon_name: formData.get('pokemon_name'),
            pokemon_type1: formData.get('pokemon_type1'),
            pokemon_type2: formData.get('pokemon_type2'),
            pokemon_sprite: formData.get('pokemon_sprite'),
            nickname: formData.get('nickname'),
            level_caught: formData.get('level_caught'),
            notes: formData.get('notes'),
            date_caught: new Date().toISOString().split('T')[0] // Today's date
        };
        
        // If empty collection, replace with grid
        if (emptyCollection) {
            emptyCollection.style.display = 'none';
            
            // Create pokemon grid if it doesn't exist
            if (!pokemonGrid) {
                const newGrid = document.createElement('div');
                newGrid.className = 'pokemon-grid';
                newGrid.id = 'pokemon-grid';
                
                const main = document.querySelector('.pokemon-collection');
                main.appendChild(newGrid);
            }
        }
        
        // Create new Pokemon card
        const newCard = createPokemonCard(pokemonData, realPokemonId);
        
        // Add to grid (at the beginning since it's ordered by created_at DESC)
        const grid = document.getElementById('pokemon-grid');
        grid.insertAdjacentHTML('afterbegin', newCard);
        
        // Update stats
        updateStatsAfterAdd(pokemonData);
        
        // Re-initialize search functionality for new card
        initializeSearch();
        
        // Re-initialize edit buttons for new card
        const newEditButton = grid.querySelector('.pokemon-card:first-child .edit-pokemon-btn');
        if (newEditButton && realPokemonId) {
            console.log('Configurando botão de editar com ID:', realPokemonId);
            newEditButton.addEventListener('click', function() {
                const pokemonId = this.dataset.id;
                console.log('Clicou em editar, Pokemon ID:', pokemonId);
                openEditModal(pokemonId);
            });
        }
        
        return true;
    } catch (error) {
        console.error('Erro na atualização dinâmica:', error);
        return false;
    }
}

// Create Pokemon card HTML
function createPokemonCard(pokemon, realPokemonId = null) {
    const displayName = pokemon.nickname || capitalizeFirstLetter(pokemon.pokemon_name);
    const type2HTML = pokemon.pokemon_type2 ? 
        `<span class="type type-${pokemon.pokemon_type2.toLowerCase()}">${capitalizeFirstLetter(pokemon.pokemon_type2)}</span>` : '';
    const nicknameHTML = pokemon.nickname ? 
        `<p class="pokemon-species">${capitalizeFirstLetter(pokemon.pokemon_name)}</p>` : '';
    
    // Add notes if they exist
    const notesHTML = pokemon.notes && pokemon.notes.trim() !== '' ? 
        `<div class="pokemon-notes"><strong>Anotações:</strong> ${pokemon.notes}</div>` : '';
    
    // Use the real Pokemon ID from database if provided, otherwise fallback to 'new'
    const editButtonId = realPokemonId || 'new';
    
    return `
        <div class="pokemon-card" data-pokemon-id="${pokemon.pokemon_id}" data-name="${pokemon.pokemon_name.toLowerCase()}">
            <div class="pokemon-image">
                <img src="${pokemon.pokemon_sprite}" alt="${pokemon.pokemon_name}">
            </div>
            <div class="pokemon-info">
                <h3 class="pokemon-name">${displayName}</h3>
                ${nicknameHTML}
                <div class="pokemon-types">
                    <span class="type type-${pokemon.pokemon_type1.toLowerCase()}">${capitalizeFirstLetter(pokemon.pokemon_type1)}</span>
                    ${type2HTML}
                </div>
                <div class="pokemon-level">Level ${pokemon.level_caught}</div>
                <div class="pokemon-date">Capturado em: ${new Date(pokemon.date_caught).toLocaleDateString('pt-BR')}</div>
                ${notesHTML}
            </div>
            <div class="pokemon-actions">
                <button class="btn btn-sm btn-outline edit-pokemon-btn" data-id="${editButtonId}">Editar</button>
            </div>
        </div>
    `;
}

// Update stats after adding Pokemon
function updateStatsAfterAdd(pokemon) {
    try {
        const totalStat = document.querySelector('.stat-card:first-child .stat-number');
        if (totalStat) {
            const currentTotal = parseInt(totalStat.textContent) || 0;
            totalStat.textContent = currentTotal + 1;
        }
        
        // Note: Type counting would require more complex logic to track unique types
        // For now, we'll just update the total count
    } catch (error) {
        console.error('Erro ao atualizar estatísticas:', error);
    }
}

// Open edit modal
async function openEditModal(pokemonId) {
    const editModal = document.getElementById('edit-pokemon-modal');
    
    console.log('OpenEditModal chamado com ID:', pokemonId);
    
    if (!pokemonId || pokemonId === 'new') {
        showAlert('ID do Pokémon inválido. Recarregue a página e tente novamente.', 'error');
        console.error('ID inválido para edição:', pokemonId);
        return;
    }
    
    try {
        showLoading('Carregando dados do Pokémon...');
        
        const formData = new FormData();
        formData.append('action', 'get');
        formData.append('id', pokemonId);
        
        console.log('Enviando requisição para buscar Pokémon ID:', pokemonId);
        
        const response = await fetch('pokemon_operations.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Response status:', response.status);
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        const data = JSON.parse(responseText);
        console.log('Dados recebidos:', data);
        
        hideLoading();
        
        if (data.success) {
            const pokemon = data.pokemon;
            
            document.getElementById('edit-id').value = pokemon.id;
            document.getElementById('edit-nickname').value = pokemon.nickname || '';
            document.getElementById('edit-level').value = pokemon.level_caught;
            document.getElementById('edit-notes').value = pokemon.notes || '';
            
            console.log('Modal preenchido com dados do Pokémon:', pokemon.id);
            editModal.style.display = 'block';
        } else {
            showAlert(data.message, 'error');
            console.error('Erro do servidor:', data.message);
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
    
    console.log('Editando Pokémon com dados:', Object.fromEntries(formData));
    
    try {
        const response = await fetch('pokemon_operations.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Response status:', response.status);
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        const data = JSON.parse(responseText);
        console.log('Parsed response:', data);
        
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('edit-pokemon-modal').style.display = 'none';
            
            console.log('Pokémon editado com sucesso! ID:', data.pokemon_id);
            
            // Try dynamic update first, then fallback to reload
            const dynamicUpdateSuccess = await updatePokemonInDOMDynamically(formData, data.pokemon_id);
            
            if (!dynamicUpdateSuccess) {
                console.log('Atualização dinâmica falhou, recarregando página...');
                setTimeout(() => {
                    console.log('Recarregando página...');
                    window.location.reload(true);
                }, 2000);
            } else {
                console.log('Pokémon atualizado dinamicamente na página!');
            }
        } else {
            showAlert(data.message, 'error');
            console.error('Erro do servidor:', data.message);
        }
    } catch (error) {
        showAlert('Ocorreu um erro. Tente novamente.', 'error');
        console.error('Edit Pokemon error:', error);
    }
}

// Update Pokemon in DOM dynamically after edit
async function updatePokemonInDOMDynamically(formData, pokemonId) {
    try {
        console.log('Tentando atualizar Pokémon no DOM. ID:', pokemonId);
        
        // Find the Pokemon card by the data-id in the edit button
        const pokemonCards = document.querySelectorAll('.pokemon-card');
        let targetCard = null;
        
        pokemonCards.forEach(card => {
            const editButton = card.querySelector('.edit-pokemon-btn');
            if (editButton && editButton.dataset.id === pokemonId.toString()) {
                targetCard = card;
                console.log('Card encontrado para atualização:', card);
            }
        });
        
        if (!targetCard) {
            console.log('Card não encontrado para atualização. ID procurado:', pokemonId);
            return false;
        }
        
        // Get updated data from form
        const nickname = formData.get('nickname');
        const level_caught = formData.get('level_caught');
        const notes = formData.get('notes');
        
        // Update the card content
        const nameElement = targetCard.querySelector('.pokemon-name');
        const levelElement = targetCard.querySelector('.pokemon-level');
        
        if (nameElement) {
            // If nickname exists, show it, otherwise show the original pokemon name
            if (nickname && nickname.trim() !== '') {
                nameElement.textContent = nickname;
                
                // Add or update species name if nickname exists
                let speciesElement = targetCard.querySelector('.pokemon-species');
                if (!speciesElement) {
                    speciesElement = document.createElement('p');
                    speciesElement.className = 'pokemon-species';
                    nameElement.parentNode.insertBefore(speciesElement, nameElement.nextSibling);
                }
                
                // Get original pokemon name from card data
                const originalName = targetCard.dataset.name;
                speciesElement.textContent = capitalizeFirstLetter(originalName);
            } else {
                // Remove nickname, show original name
                const originalName = targetCard.dataset.name;
                nameElement.textContent = capitalizeFirstLetter(originalName);
                
                // Remove species element if it exists
                const speciesElement = targetCard.querySelector('.pokemon-species');
                if (speciesElement) {
                    speciesElement.remove();
                }
            }
        }
        
        if (levelElement) {
            levelElement.textContent = `Level ${level_caught}`;
        }
        
        // Update or add/remove notes
        let notesElement = targetCard.querySelector('.pokemon-notes');
        if (notes && notes.trim() !== '') {
            if (!notesElement) {
                // Create notes element if it doesn't exist
                notesElement = document.createElement('div');
                notesElement.className = 'pokemon-notes';
                
                // Insert after the date element
                const dateElement = targetCard.querySelector('.pokemon-date');
                if (dateElement) {
                    dateElement.parentNode.insertBefore(notesElement, dateElement.nextSibling);
                } else {
                    // Fallback: add to the end of pokemon-info
                    const infoElement = targetCard.querySelector('.pokemon-info');
                    infoElement.appendChild(notesElement);
                }
            }
            notesElement.innerHTML = `<strong>Anotações:</strong> ${notes}`;
        } else {
            // Remove notes element if notes are empty
            if (notesElement) {
                notesElement.remove();
            }
        }
        
        console.log('Card atualizado com sucesso!');
        return true;
        
    } catch (error) {
        console.error('Erro na atualização dinâmica do Pokémon:', error);
        return false;
    }
}

// Delete Pokemon
async function deletePokemon() {
    const pokemonId = document.getElementById('edit-id').value;
    
    console.log('Deletando Pokémon ID:', pokemonId);
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', pokemonId);
    
    try {
        const response = await fetch('pokemon_operations.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Delete response status:', response.status);
        const responseText = await response.text();
        console.log('Delete response text:', responseText);
        
        const data = JSON.parse(responseText);
        console.log('Delete parsed response:', data);
        
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('edit-pokemon-modal').style.display = 'none';
            
            console.log('Pokémon deletado com sucesso!');
            
            // Try dynamic removal first, then fallback to reload
            const dynamicRemovalSuccess = removePokemonFromDOMDynamically(pokemonId);
            
            if (!dynamicRemovalSuccess) {
                console.log('Remoção dinâmica falhou, recarregando página...');
                setTimeout(() => {
                    console.log('Recarregando página...');
                    window.location.reload(true);
                }, 1500);
            } else {
                console.log('Pokémon removido dinamicamente da página!');
            }
        } else {
            showAlert(data.message, 'error');
            console.error('Erro do servidor:', data.message);
        }
    } catch (error) {
        showAlert('Ocorreu um erro. Tente novamente.', 'error');
        console.error('Delete Pokemon error:', error);
    }
}

// Remove Pokemon from DOM dynamically
function removePokemonFromDOMDynamically(pokemonId) {
    try {
        console.log('Tentando remover Pokémon do DOM. ID:', pokemonId);
        
        // Find the Pokemon card by the data-id in the edit button
        const pokemonCards = document.querySelectorAll('.pokemon-card');
        let targetCard = null;
        
        pokemonCards.forEach(card => {
            const editButton = card.querySelector('.edit-pokemon-btn');
            if (editButton && editButton.dataset.id === pokemonId.toString()) {
                targetCard = card;
                console.log('Card encontrado para remoção:', card);
            }
        });
        
        if (!targetCard) {
            console.log('Card não encontrado para remoção. ID procurado:', pokemonId);
            return false;
        }
        
        // Remove the card with animation
        targetCard.style.transition = 'all 0.3s ease';
        targetCard.style.opacity = '0';
        targetCard.style.transform = 'scale(0.8)';
        
        setTimeout(() => {
            targetCard.remove();
            
            // Check if collection is now empty
            const remainingCards = document.querySelectorAll('.pokemon-card');
            if (remainingCards.length === 0) {
                // Show empty collection message
                const pokemonGrid = document.getElementById('pokemon-grid');
                const main = document.querySelector('.pokemon-collection');
                
                if (pokemonGrid) {
                    pokemonGrid.style.display = 'none';
                }
                
                const emptyMessage = `
                    <div class="empty-collection">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/54.png" alt="Psyduck" class="empty-pokemon">
                        <h3>Sua coleção está vazia!</h3>
                        <p>Comece adicionando seu primeiro Pokémon à coleção.</p>
                    </div>
                `;
                
                main.innerHTML = emptyMessage;
            }
            
            // Update stats
            updateStatsAfterRemove();
        }, 300);
        
        console.log('Card removido com sucesso!');
        return true;
        
    } catch (error) {
        console.error('Erro na remoção dinâmica do Pokémon:', error);
        return false;
    }
}

// Update stats after removing Pokemon
function updateStatsAfterRemove() {
    try {
        const totalStat = document.querySelector('.stat-card:first-child .stat-number');
        if (totalStat) {
            const currentTotal = parseInt(totalStat.textContent) || 0;
            if (currentTotal > 0) {
                totalStat.textContent = currentTotal - 1;
            }
        }
    } catch (error) {
        console.error('Erro ao atualizar estatísticas:', error);
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