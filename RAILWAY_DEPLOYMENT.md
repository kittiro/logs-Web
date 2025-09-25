# Railway Deployment Guide for WebManga Demo

## Prerequisites
1. Railway account (sign up at https://railway.app)
2. Railway CLI installed (optional but recommended)

## Deployment Methods

### Method 1: Deploy via Railway Dashboard (Recommended)

1. **Connect Repository**
   - Go to https://railway.app/dashboard
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Connect your GitHub account and select this repository

2. **Configure Environment Variables**
   Railway will automatically detect this is a Laravel project. Set these environment variables in the Railway dashboard:
   
   ```
   APP_NAME=WebManga Demo
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:YOUR_APP_KEY_HERE
   APP_TIMEZONE=Asia/Bangkok
   APP_URL=https://your-app-name.up.railway.app
   
   DB_CONNECTION=sqlite
   DB_DATABASE=/tmp/database.sqlite
   
   SESSION_DRIVER=cookie
   SESSION_LIFETIME=120
   
   LOG_CHANNEL=stack
   LOG_LEVEL=info
   ```

3. **Generate APP_KEY**
   You can generate an APP_KEY by running locally:
   ```bash
   php artisan key:generate --show
   ```
   Then copy the generated key to Railway's environment variables.

### Method 2: Deploy via Railway CLI

1. **Install Railway CLI**
   ```bash
   npm install -g @railway/cli
   ```

2. **Login and Deploy**
   ```bash
   railway login
   railway init
   railway up
   ```

3. **Set Environment Variables**
   ```bash
   railway variables set APP_NAME="WebManga Demo"
   railway variables set APP_ENV=production
   railway variables set APP_DEBUG=false
   railway variables set APP_KEY="base64:YOUR_APP_KEY_HERE"
   railway variables set APP_TIMEZONE="Asia/Bangkok"
   railway variables set DB_CONNECTION=sqlite
   railway variables set DB_DATABASE="/tmp/database.sqlite"
   ```

## Configuration Files

This project includes optimized Railway configuration:

- **Dockerfile.railway**: Optimized Docker configuration for Railway
- **nixpacks.toml**: Nixpacks configuration (Railway's default builder)
- **start.sh**: Startup script with proper Laravel setup

## Features Included in Demo

✅ **Web Access Logging System**
- Compliant with Computer Act requirements
- Real-time log viewing and filtering
- Automatic log cleanup
- Download logs functionality
- Statistics dashboard

✅ **User Authentication**
- Login/Register system
- Protected routes
- User management

✅ **Responsive Dashboard**
- Modern UI with Tailwind CSS
- Mobile-friendly design
- Navigation system

## Post-Deployment Steps

1. **Verify Deployment**
   - Visit your Railway app URL
   - Check that the homepage loads correctly
   - Test user registration/login

2. **Access Admin Features**
   - Register a new account or use seeded user
   - Navigate to "Web Access Logs" to view logging system
   - Test log filtering and download functionality

3. **Monitor Logs**
   - Use Railway dashboard to monitor application logs
   - Check for any deployment issues

## Troubleshooting

### Common Issues

1. **APP_KEY Error**
   - Make sure you've set a valid APP_KEY in environment variables
   - Generate one using `php artisan key:generate --show`

2. **Database Issues**
   - SQLite database is created automatically
   - Migrations run on startup
   - Check Railway logs if database setup fails

3. **File Permissions**
   - Storage directories are created automatically
   - Permissions are set in the startup script

### Railway-Specific Tips

- Railway automatically detects Laravel projects
- Uses Nixpacks by default (faster than Docker)
- Provides automatic HTTPS
- Includes monitoring and logging

## Cost Estimation

Railway offers:
- **Hobby Plan**: $5/month with generous limits
- **Pro Plan**: $20/month for production apps
- Free tier available for testing

## Support

For Railway-specific issues:
- Railway Documentation: https://docs.railway.app
- Railway Discord: https://discord.gg/railway
- Railway GitHub: https://github.com/railwayapp

For application issues, check the Laravel logs in Railway dashboard.