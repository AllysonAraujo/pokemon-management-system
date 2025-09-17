# 🔥 Pokémon Management System

A complete Pokémon collection management system built with PHP, MySQL, and real-time PokeAPI integration. This system provides secure user authentication, full CRUD operations for Pokémon collections, and a beautiful responsive interface.

## ✨ Features

- **🔐 Secure Authentication**: Password hashing with bcrypt, session management, CSRF protection
- **📱 Responsive Design**: Mobile-friendly interface with Pokémon-themed styling
- **🎯 Real-time API Integration**: Live data from PokeAPI for sprites, types, and abilities
- **💾 Complete CRUD Operations**: Add, view, edit, and delete Pokémon from your collection
- **🎮 User-friendly Interface**: Intuitive dashboard with search and filter capabilities
- **🛡️ Security Features**: Input sanitization, prepared statements, secure sessions

## 🚀 Quick Start

### Prerequisites

- PHP 7.4+ with PDO MySQL extension
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache/Nginx) or PHP built-in server
- Internet connection (for PokeAPI integration)

### Installation

1. **Clone or download the repository**
   ```bash
   git clone https://github.com/AllysonAraujo/pokemon-management-system.git
   cd pokemon-management-system
   ```

2. **Set up the database**
   - Create a MySQL database named `pokemon_management_system`
   - Import the schema using MySQL Workbench or command line:
   ```bash
   mysql -u root -p pokemon_management_system < database/schema.sql
   ```

3. **Configure database connection**
   - Edit `config/database.php` if needed to match your database settings:
   ```php
   private $host = 'localhost';
   private $db_name = 'pokemon_management_system';
   private $username = 'root';
   private $password = '';
   ```

4. **Start the application**
   ```bash
   # Using PHP built-in server
   php -S localhost:8000
   
   # Or configure your web server to point to the project directory
   ```

5. **Access the application**
   - Open your browser and go to `http://localhost:8000`
   - Use demo credentials: **Username:** `admin`, **Password:** `password`
   - Or register a new account

## 📁 Project Structure

```
pokemon-management-system/
├── index.php              # Landing page
├── login.php              # User login
├── register.php           # User registration  
├── dashboard.php          # Main dashboard
├── pokemon_operations.php # CRUD operations API
├── config/
│   └── database.php       # Database config & security functions
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   └── js/
│       └── main.js        # JavaScript functionality
├── database/
│   └── schema.sql         # MySQL database schema
└── README.md             # This file
```

## 🗄️ Database Schema

The system uses three main tables:

### `users`
- User authentication and profile data
- Password hashing with bcrypt
- Unique username and email constraints

### `pokemon_collection` 
- User's Pokémon collection data
- Links to PokeAPI data (ID, sprite, types, abilities)
- Custom user fields (nickname, level, notes)
- Timestamps for tracking

### `sessions`
- Secure session management
- Automatic cleanup of expired sessions

## 🎮 How to Use

### Adding Pokémon
1. Click "Add Pokémon" on the dashboard
2. Search for a Pokémon by name (uses PokeAPI)
3. Set nickname, level, and notes
4. The system automatically fetches sprite, types, and abilities

### Managing Collection
- **View**: All your Pokémon displayed in cards with images and info
- **Search**: Filter your collection by name or nickname
- **Edit**: Update nickname, level, and notes
- **Delete**: Remove Pokémon from your collection

### Account Management
- Secure registration with email validation
- Login with username or email
- Password strength requirements
- Session protection and automatic logout

## 🔧 Technical Details

### Security Features
- **Password Hashing**: bcrypt with salt
- **CSRF Protection**: Token-based request validation
- **SQL Injection Prevention**: Prepared statements only
- **Input Sanitization**: All user inputs cleaned
- **Session Security**: HTTP-only cookies, regeneration

### API Integration
- **PokeAPI**: Real-time Pokémon data fetching
- **Error Handling**: Graceful fallbacks for API failures
- **Caching Strategy**: Stores fetched data in database
- **Rate Limiting**: Respectful API usage

### Frontend Features
- **Responsive Design**: Works on all screen sizes
- **Progressive Enhancement**: Works without JavaScript
- **Loading States**: User feedback during operations
- **Modal Dialogs**: Smooth user interactions
- **Real-time Search**: Instant collection filtering

## 🎨 Customization

### Themes
The CSS uses CSS custom properties for easy theming:
```css
:root {
  --primary-color: #ff6b6b;
  --secondary-color: #4834d4;
  --success-color: #00d2d3;
}
```

### Database Configuration
Modify `config/database.php` for different database setups:
- Change connection parameters
- Adjust session settings
- Modify security policies

## 🐛 Troubleshooting

### Common Issues

**Database Connection Failed**
- Check MySQL is running
- Verify database credentials
- Ensure database exists

**PokeAPI Not Working**
- Check internet connection
- API might be temporarily down
- System works offline with existing data

**Session Issues**
- Clear browser cookies
- Check PHP session configuration
- Verify file permissions

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the MIT License.

## 🙏 Acknowledgments

- [PokeAPI](https://pokeapi.co/) for providing free Pokémon data
- The PHP and MySQL communities for excellent documentation
- Pokémon fans worldwide for inspiration

## 📧 Support

For questions or support:
- Open an issue on GitHub
- Check the troubleshooting section
- Review the code comments for implementation details

---

**Happy Pokémon collecting! 🎮✨**
