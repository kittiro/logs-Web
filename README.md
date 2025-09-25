# WebManga Demo - Log Management System

A comprehensive web-based log management system built with Laravel, featuring real-time analytics dashboard and compliance with Computer Act requirements.

## 🌟 Features

### 📊 Analytics Dashboard
- Real-time log statistics and monitoring
- Interactive charts and graphs
- System status tracking
- Error monitoring and alerts
- Thai language interface

### 📁 Log Management
- View and manage all log files
- Download log files with integrity verification
- SHA256 checksum validation
- File preview functionality
- Search and filter capabilities

### 🔐 Security & Compliance
- Computer Act compliant logging
- User authentication system
- Secure file access controls
- Web access logging middleware

### 🎨 Modern UI/UX
- Responsive Bootstrap design
- Dark/Light theme toggle
- Mobile-friendly interface
- Intuitive navigation

## 🚀 Live Demo

Visit the live demo: [https://webmanga-demo.loca.lt/dashboard](https://webmanga-demo.loca.lt/dashboard)

### Demo Features:
- **Dashboard**: Analytics and system overview
- **Log Management**: File management interface
- **Web Access Logs**: Traffic monitoring
- **Return to Logs**: Easy navigation between sections

## 🛠️ Installation

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM (optional, for asset compilation)

### Quick Start

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/logs-Web.git
   cd logs-Web
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Create admin user**
   ```bash
   php artisan tinker
   User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);
   ```

6. **Start the server**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8080
   ```

7. **Access the application**
   - Dashboard: `http://localhost:8080/dashboard`
   - Log Management: `http://localhost:8080/logs`

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php      # Analytics dashboard
│   │   ├── LogController.php            # Log file management
│   │   └── WebAccessLogController.php   # Web access logging
│   └── Http/Middleware/
│       └── WebAccessLogger.php          # Access logging middleware
├── resources/views/
│   ├── dashboard.blade.php              # Main dashboard
│   ├── logs/                           # Log management views
│   └── layouts/app.blade.php           # Main layout
├── storage/logs/                       # Log files storage
├── DEMO_GUIDE.md                       # Demo instructions
├── DEMO_SCRIPT.md                      # Demo script
└── RAILWAY_DEPLOYMENT.md               # Deployment guide
```

## 🔧 Configuration

### Environment Variables
```env
APP_NAME="WebManga Demo"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Web Access Logging
The system automatically logs all web access through the `WebAccessLogger` middleware:
- IP addresses and user agents
- Request timestamps and methods
- Response status codes
- Computer Act compliance

## 🚀 Deployment

### Railway (Recommended)
1. Connect your GitHub repository to Railway
2. Set environment variables in Railway dashboard
3. Deploy automatically on push

See [RAILWAY_DEPLOYMENT.md](RAILWAY_DEPLOYMENT.md) for detailed instructions.

### Manual Deployment
1. Upload files to your server
2. Configure web server (Apache/Nginx)
3. Set proper file permissions
4. Configure environment variables

## 📊 Usage

### Dashboard Analytics
- View real-time system statistics
- Monitor login activities and errors
- Track web access patterns
- Analyze system performance

### Log Management
- Browse all log files with metadata
- Download files with integrity verification
- Preview file contents
- Search and filter by various criteria

### Web Access Monitoring
- Track visitor statistics
- Monitor suspicious activities
- Generate compliance reports
- Export access logs

## 🔒 Security Features

- **Authentication**: Laravel's built-in authentication
- **Authorization**: Role-based access control
- **Logging**: Comprehensive audit trails
- **Validation**: Input sanitization and validation
- **CSRF Protection**: Cross-site request forgery protection

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Check the [DEMO_GUIDE.md](DEMO_GUIDE.md) for usage instructions
- Review [LOGGING_SYSTEM.md](LOGGING_SYSTEM.md) for technical details

## 🎯 Roadmap

- [ ] Advanced analytics and reporting
- [ ] Real-time notifications
- [ ] API endpoints for external integration
- [ ] Multi-language support
- [ ] Advanced user management
- [ ] Log retention policies

---

**Built with ❤️ using Laravel and modern web technologies**