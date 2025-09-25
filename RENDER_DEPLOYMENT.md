# Render Deployment Guide for WebManga Demo

## 🎨 About Render

Render is a modern cloud platform that makes it easy to deploy Laravel applications with:
- **Free Tier**: 750 hours/month (enough for demos and small projects)
- **Automatic SSL**: HTTPS enabled by default
- **Git Integration**: Auto-deploy from GitHub
- **PHP 8.2 Support**: Native Laravel support
- **Persistent Storage**: For SQLite database and logs

## 🚀 Quick Deployment Steps

### Method 1: Deploy via Render Dashboard (Recommended)

1. **Sign up at Render**
   - Go to https://render.com
   - Sign up with your GitHub account

2. **Create New Web Service**
   - Click "New +" → "Web Service"
   - Connect your GitHub repository
   - Select this repository

3. **Configure Service**
   - **Name**: `webmanga-demo`
   - **Environment**: `PHP`
   - **Build Command**: 
     ```bash
     composer install --no-dev --optimize-autoloader && chmod +x build-render.sh && ./build-render.sh
     ```
   - **Start Command**:
     ```bash
     php artisan migrate --force && php artisan db:seed --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=$PORT
     ```

4. **Set Environment Variables**
   ```
   APP_NAME=WebManga Demo
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:YOUR_GENERATED_KEY_HERE
   APP_TIMEZONE=Asia/Bangkok
   DB_CONNECTION=sqlite
   DB_DATABASE=/opt/render/project/src/database/database.sqlite
   SESSION_DRIVER=cookie
   LOG_CHANNEL=stack
   ```

5. **Add Persistent Disk** (Important!)
   - Go to "Disks" tab
   - Add disk: Name: `webmanga-storage`, Size: `1GB`, Mount Path: `/opt/render/project/src/storage`

### Method 2: Deploy with render.yaml (Infrastructure as Code)

This repository includes `render.yaml` for automatic configuration:

1. **Push to GitHub** (already done)
2. **Import to Render**
   - Go to Render Dashboard
   - Click "New +" → "Blueprint"
   - Connect repository
   - Render will read `render.yaml` automatically

## 🔧 Configuration Files

This project includes Render-optimized files:

- **`render.yaml`**: Render service configuration
- **`build-render.sh`**: Render-specific build script
- **`Dockerfile.render`**: Docker configuration for Render
- **`RENDER_DEPLOYMENT.md`**: This deployment guide

## 🗄️ Database Configuration

**SQLite Database**:
- Location: `/opt/render/project/src/database/database.sqlite`
- Automatically created during build
- Persistent storage via Render disk
- Migrations run automatically on deploy

## 👤 Default Users

The system creates default users:
- **Admin**: username: `admin`, password: `password`
- **Demo**: username: `demo`, password: `password`

## 🔍 Features Included

✅ **Web Access Logging System**
- Computer Act compliance
- Real-time log viewing
- Log filtering and download
- Statistics dashboard

✅ **User Authentication**
- Login/Register system
- Protected routes
- Session management

✅ **Responsive Design**
- Tailwind CSS
- Mobile-friendly
- Modern UI

## 📊 Monitoring & Logs

**Render Dashboard provides**:
- Application logs
- Build logs
- Performance metrics
- Uptime monitoring

**Application logs location**:
- Laravel logs: `/opt/render/project/src/storage/logs/`
- Web access logs: Available via web interface

## 🛠️ Troubleshooting

### Common Issues

1. **Build Fails**
   - Check build logs in Render dashboard
   - Ensure `build-render.sh` is executable
   - Verify composer dependencies

2. **Database Issues**
   - Ensure persistent disk is mounted
   - Check database file permissions
   - Verify SQLite is installed

3. **Permission Errors**
   - Storage directories created automatically
   - Build script sets proper permissions
   - Check disk mount path

### Render-Specific Tips

- **Free tier limitations**: 750 hours/month, sleeps after 15min inactivity
- **Custom domains**: Available on paid plans
- **Environment variables**: Set in Render dashboard
- **Logs**: Available in real-time via dashboard

## 💰 Pricing

**Free Tier**:
- 750 hours/month
- 512MB RAM
- Shared CPU
- Perfect for demos

**Starter Plan** ($7/month):
- Always on
- 512MB RAM
- Shared CPU
- Custom domains

## 🔗 Useful Links

- **Render Documentation**: https://render.com/docs
- **PHP on Render**: https://render.com/docs/deploy-php
- **Laravel Deployment**: https://render.com/docs/deploy-laravel
- **Support**: https://render.com/support

## 🎯 Next Steps After Deployment

1. **Visit your app URL** (provided by Render)
2. **Test user registration/login**
3. **Check web access logs functionality**
4. **Monitor performance** via Render dashboard
5. **Set up custom domain** (if needed)

---

**Happy deploying! 🚀**

Your Laravel application should now be running smoothly on Render with all features working correctly.