<?php
// Demo mode - shows the interface without requiring database
$demo_mode = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔥 Pokémon Management System - DEMO</h1>
            <p>Welcome back, <strong>Demo User</strong>!</p>
            <div class="nav">
                <div class="nav-links">
                    <button id="addPokemonBtn" class="btn">Add Pokémon</button>
                    <a href="#" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; text-align: center;">
                <div style="background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin: 0; font-size: 2rem;">5</h3>
                    <p style="margin: 5px 0 0 0;">Total Pokémon</p>
                </div>
                <div style="background: linear-gradient(135deg, #4834d4, #686de0); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin: 0; font-size: 2rem;">Demo User</h3>
                    <p style="margin: 5px 0 0 0;">Trainer Name</p>
                </div>
                <div style="background: linear-gradient(135deg, #00d2d3, #54a0ff); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin: 0; font-size: 2rem;">Sep 2024</h3>
                    <p style="margin: 5px 0 0 0;">Member Since</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="card">
            <h2>Your Pokémon Collection</h2>
            <div class="search-container">
                <input type="text" id="collectionSearch" class="search-input" placeholder="Search your collection by name or nickname...">
            </div>
        </div>

        <!-- Demo Pokemon Collection -->
        <div class="pokemon-grid">
            <!-- Pikachu -->
            <div class="pokemon-card">
                <div class="pokemon-sprite">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png" alt="pikachu">
                </div>
                <div class="pokemon-name">
                    Pikachu
                    <div class="pokemon-nickname" style="font-size: 0.9rem; font-weight: normal; opacity: 0.8;">
                        "Sparky"
                    </div>
                </div>
                <div class="pokemon-info">
                    <p><strong>Level:</strong> 45</p>
                    <p><strong>Caught:</strong> Sep 15, 2024</p>
                    <p><strong>Notes:</strong> My trusty companion!</p>
                </div>
                <div class="pokemon-types">
                    <span class="type-badge">electric</span>
                </div>
                <div class="pokemon-actions">
                    <button class="btn btn-secondary edit-pokemon">Edit</button>
                    <button class="btn btn-danger delete-pokemon">Release</button>
                </div>
            </div>

            <!-- Charizard -->
            <div class="pokemon-card">
                <div class="pokemon-sprite">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/6.png" alt="charizard">
                </div>
                <div class="pokemon-name">Charizard</div>
                <div class="pokemon-info">
                    <p><strong>Level:</strong> 78</p>
                    <p><strong>Caught:</strong> Sep 10, 2024</p>
                    <p><strong>Notes:</strong> Powerful fire-type</p>
                </div>
                <div class="pokemon-types">
                    <span class="type-badge">fire</span>
                    <span class="type-badge">flying</span>
                </div>
                <div class="pokemon-actions">
                    <button class="btn btn-secondary edit-pokemon">Edit</button>
                    <button class="btn btn-danger delete-pokemon">Release</button>
                </div>
            </div>

            <!-- Blastoise -->
            <div class="pokemon-card">
                <div class="pokemon-sprite">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/9.png" alt="blastoise">
                </div>
                <div class="pokemon-name">
                    Blastoise
                    <div class="pokemon-nickname" style="font-size: 0.9rem; font-weight: normal; opacity: 0.8;">
                        "Tsunami"
                    </div>
                </div>
                <div class="pokemon-info">
                    <p><strong>Level:</strong> 65</p>
                    <p><strong>Caught:</strong> Sep 12, 2024</p>
                </div>
                <div class="pokemon-types">
                    <span class="type-badge">water</span>
                </div>
                <div class="pokemon-actions">
                    <button class="btn btn-secondary edit-pokemon">Edit</button>
                    <button class="btn btn-danger delete-pokemon">Release</button>
                </div>
            </div>

            <!-- Venusaur -->
            <div class="pokemon-card">
                <div class="pokemon-sprite">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/3.png" alt="venusaur">
                </div>
                <div class="pokemon-name">Venusaur</div>
                <div class="pokemon-info">
                    <p><strong>Level:</strong> 52</p>
                    <p><strong>Caught:</strong> Sep 8, 2024</p>
                    <p><strong>Notes:</strong> Grass gym leader!</p>
                </div>
                <div class="pokemon-types">
                    <span class="type-badge">grass</span>
                    <span class="type-badge">poison</span>
                </div>
                <div class="pokemon-actions">
                    <button class="btn btn-secondary edit-pokemon">Edit</button>
                    <button class="btn btn-danger delete-pokemon">Release</button>
                </div>
            </div>

            <!-- Mewtwo -->
            <div class="pokemon-card">
                <div class="pokemon-sprite">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/150.png" alt="mewtwo">
                </div>
                <div class="pokemon-name">
                    Mewtwo
                    <div class="pokemon-nickname" style="font-size: 0.9rem; font-weight: normal; opacity: 0.8;">
                        "Psychic Master"
                    </div>
                </div>
                <div class="pokemon-info">
                    <p><strong>Level:</strong> 100</p>
                    <p><strong>Caught:</strong> Sep 17, 2024</p>
                    <p><strong>Notes:</strong> Legendary psychic Pokémon!</p>
                </div>
                <div class="pokemon-types">
                    <span class="type-badge">psychic</span>
                </div>
                <div class="pokemon-actions">
                    <button class="btn btn-secondary edit-pokemon">Edit</button>
                    <button class="btn btn-danger delete-pokemon">Release</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Pokemon Modal -->
    <div id="addPokemonModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Pokémon</h2>
            <form id="addPokemonForm">
                <div class="form-group">
                    <label for="pokemonSearch">Search Pokémon</label>
                    <input type="text" id="pokemonSearch" placeholder="Type Pokémon name to search..." autocomplete="off">
                    <div id="searchResults" class="hidden" style="background: white; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; max-height: 200px; overflow-y: auto;"></div>
                </div>
                
                <div class="form-group">
                    <label for="pokemon_name">Pokémon Name</label>
                    <input type="text" id="pokemon_name" name="pokemon_name" placeholder="e.g., pikachu, charizard">
                </div>
                
                <div class="form-group">
                    <label for="nickname">Nickname (Optional)</label>
                    <input type="text" id="nickname" name="nickname" placeholder="Give your Pokémon a nickname">
                </div>
                
                <div class="form-group">
                    <label for="level">Level</label>
                    <input type="number" id="level" name="level" value="1" min="1" max="100">
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes (Optional)</label>
                    <textarea id="notes" name="notes" placeholder="Any special notes about this Pokémon..."></textarea>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn" onclick="alert('Demo mode - Database integration would save this Pokémon!')" style="width: 100%;">Add to Collection (Demo)</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Demo JavaScript functionality
        document.getElementById('addPokemonBtn').addEventListener('click', function() {
            document.getElementById('addPokemonModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        });

        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('addPokemonModal').style.display = 'none';
            document.body.style.overflow = '';
        });

        // Collection search demo
        document.getElementById('collectionSearch').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const pokemonCards = document.querySelectorAll('.pokemon-card');
            
            pokemonCards.forEach(card => {
                const name = card.querySelector('.pokemon-name').textContent.toLowerCase();
                if (name.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Demo PokeAPI search
        document.getElementById('pokemonSearch').addEventListener('input', async function(e) {
            const query = e.target.value.toLowerCase().trim();
            const resultsDiv = document.getElementById('searchResults');
            
            if (!query || query.length < 2) {
                resultsDiv.innerHTML = '';
                resultsDiv.classList.add('hidden');
                return;
            }

            resultsDiv.innerHTML = '<div style="padding: 10px;">🔍 Searching PokeAPI...</div>';
            resultsDiv.classList.remove('hidden');

            try {
                const response = await fetch(`https://pokeapi.co/api/v2/pokemon/${query}`);
                if (response.ok) {
                    const pokemon = await response.json();
                    resultsDiv.innerHTML = `
                        <div style="display: flex; align-items: center; padding: 10px; cursor: pointer; border-bottom: 1px solid #eee;" onclick="selectPokemon('${pokemon.name}')">
                            <img src="${pokemon.sprites.front_default}" alt="${pokemon.name}" width="64" height="64" style="margin-right: 10px;">
                            <div>
                                <strong style="text-transform: capitalize;">${pokemon.name}</strong>
                                <p style="margin: 0; color: #666;">ID: ${pokemon.id}</p>
                            </div>
                        </div>
                    `;
                } else {
                    resultsDiv.innerHTML = '<div style="padding: 10px;">❌ No Pokémon found with that name.</div>';
                }
            } catch (error) {
                resultsDiv.innerHTML = '<div style="padding: 10px;">⚠️ Error searching for Pokémon.</div>';
            }
        });

        function selectPokemon(pokemonName) {
            document.getElementById('pokemon_name').value = pokemonName;
            document.getElementById('searchResults').innerHTML = '';
            document.getElementById('searchResults').classList.add('hidden');
        }

        // Button demos
        document.querySelectorAll('.edit-pokemon').forEach(btn => {
            btn.addEventListener('click', () => alert('Demo mode - Would open edit modal with current Pokémon data!'));
        });

        document.querySelectorAll('.delete-pokemon').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Are you sure you want to release this Pokémon?')) {
                    alert('Demo mode - Pokémon would be removed from collection!');
                }
            });
        });
    </script>
</body>
</html>