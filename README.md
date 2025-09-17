# Pokémon Management System

Sistema completo de gerenciamento de Pokémon com autenticação segura, CRUD, integração com PokeAPI e banco de dados MySQL.

![Pokemon Management System](https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png)

## 📋 Características

### 🔐 Sistema de Autenticação Seguro
- Registro de usuários com validação
- Login seguro com hash de senhas (bcrypt)
- Gerenciamento de sessões
- Rotas protegidas

### 📱 Coleção de Pokémon
- CRUD completo (Criar, Ler, Atualizar, Deletar)
- Integração com PokeAPI para dados reais
- Sprites oficiais dos Pokémon
- Informações de tipos e habilidades
- Apelidos personalizados e notas

### 🎨 Interface Responsiva
- Design moderno com tema Pokémon
- Mobile-friendly
- Cards interativos
- Modais para operações
- Busca em tempo real

### 🗄️ Banco de Dados
- Estrutura MySQL otimizada
- Relacionamentos entre tabelas
- Índices para performance
- Integridade referencial

## 🚀 Instalação

### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL, mbstring

### Passos de Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/AllysonAraujo/pokemon-management-system.git
   cd pokemon-management-system
   ```

2. **Configure o banco de dados**
   ```bash
   # Acesse o MySQL
   mysql -u root -p
   
   # Execute o script de criação do banco
   source database/schema.sql
   ```

3. **Configure a conexão**
   ```php
   // Em config/database.php, ajuste as credenciais:
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'pokemon_management');
   define('DB_USER', 'seu_usuario');
   define('DB_PASS', 'sua_senha');
   ```

4. **Configure o servidor web**
   - Apache: Aponte o DocumentRoot para a raiz do projeto
   - Nginx: Configure o root para a pasta do projeto
   - Ou use o servidor embutido do PHP: `php -S localhost:8000`

5. **Acesse o sistema**
   - Abra http://localhost ou http://localhost:8000
   - Registre uma nova conta
   - Comece a gerenciar sua coleção!

## 📁 Estrutura do Projeto

```
pokemon-management-system/
├── index.php              # Página inicial
├── login.php              # Página de login
├── register.php           # Página de registro
├── dashboard.php          # Dashboard principal
├── pokemon_operations.php # API para operações CRUD
├── logout.php             # Script de logout
├── config/
│   └── database.php      # Configuração do banco
├── assets/
│   ├── css/
│   │   └── style.css     # Estilos CSS
│   └── js/
│       └── main.js       # JavaScript
├── database/
│   └── schema.sql        # Script de criação do banco
└── README.md             # Documentação
```

## 🗄️ Estrutura do Banco de Dados

### Tabela `users`
- `id` - Chave primária
- `username` - Nome de usuário único
- `email` - Email único
- `password_hash` - Senha criptografada
- `created_at`, `updated_at` - Timestamps

### Tabela `pokemon_collection`
- `id` - Chave primária
- `user_id` - Referência ao usuário
- `pokemon_id` - ID do Pokémon na PokeAPI
- `pokemon_name` - Nome do Pokémon
- `pokemon_type1`, `pokemon_type2` - Tipos
- `pokemon_sprite` - URL do sprite
- `pokemon_abilities` - Habilidades
- `nickname` - Apelido personalizado
- `level_caught` - Nível capturado
- `date_caught` - Data de captura
- `notes` - Notas personalizadas
- `created_at`, `updated_at` - Timestamps

### Tabela `user_sessions` (opcional)
- Gerenciamento avançado de sessões
- Controle de expiração
- Segurança adicional

## 🔧 API Endpoints

### Operações Pokémon (`pokemon_operations.php`)

#### GET
- `?action=search_pokeapi&query={nome_ou_id}` - Busca Pokémon na PokeAPI

#### POST
- `action=add` - Adiciona Pokémon à coleção
- `action=edit` - Edita Pokémon da coleção
- `action=delete` - Remove Pokémon da coleção
- `action=get` - Obtém dados de um Pokémon específico

## 🎯 Funcionalidades

### Dashboard
- Visualização da coleção em cards
- Estatísticas da coleção
- Busca e filtros
- Adicionar novos Pokémon

### Operações CRUD
- **Create**: Buscar Pokémon na PokeAPI e adicionar à coleção
- **Read**: Visualizar coleção com detalhes
- **Update**: Editar apelidos, níveis e notas
- **Delete**: Remover Pokémon da coleção

### Integração PokeAPI
- Busca por nome ou ID
- Sprites oficiais de alta qualidade
- Informações de tipos e habilidades
- Dados sempre atualizados

## 🔒 Segurança

- **Senhas**: Hash bcrypt
- **Sessões**: Configuração segura
- **SQL Injection**: Prepared statements
- **XSS**: Sanitização de inputs
- **CSRF**: Validação de origem

## 📱 Responsividade

- Design mobile-first
- Breakpoints para tablet e desktop
- Grid responsivo
- Modais adaptáveis
- Navegação otimizada

## 🎨 Design

### Paleta de Cores
- Primária: Gradientes inspirados no Pokémon
- Tipos: Cores oficiais dos tipos Pokémon
- UI: Tons neutros e modernos

### Tipografia
- Arial para legibilidade
- Hierarquia clara
- Tamanhos responsivos

## 🔄 Melhorias Futuras

- [ ] Sistema de favoritos
- [ ] Comparação de Pokémon
- [ ] Estatísticas avançadas
- [ ] Export/Import da coleção
- [ ] Sistema de conquistas
- [ ] PWA (Progressive Web App)
- [ ] Dark mode
- [ ] Multiplayer/Social features

## 🐛 Solução de Problemas

### Erro de conexão com banco
1. Verifique as credenciais em `config/database.php`
2. Confirme se o MySQL está rodando
3. Teste a conexão manualmente

### Erro de permissões
1. Verifique permissões das pastas
2. Configure o servidor web corretamente

### PokeAPI não responde
1. Verifique conexão com internet
2. API pode estar temporariamente indisponível

## 📄 Licença

Este projeto é open source e está disponível sob a [MIT License](LICENSE).

## 🤝 Contribuição

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch para sua feature
3. Faça commit das mudanças
4. Push para a branch
5. Abra um Pull Request

## 👨‍💻 Autor

**Allyson Araújo**
- GitHub: [@AllysonAraujo](https://github.com/AllysonAraujo)

## 🙏 Agradecimentos

- [PokeAPI](https://pokeapi.co/) pelos dados dos Pokémon
- Comunidade Pokémon pela inspiração
- The Pokémon Company pelo universo incrível

---

**Gotta Catch 'Em All! 🌟**
