# PRODUCTION DEPLOYMENT GUIDE - RISDA ODOMETER

**Server:** jara.my (cPanel/Herosite)  
**Environment:** Production  
**Last Updated:** October 2025

---

## 🚀 FRESH INSTALLATION FROM GIT

### Step 1: Clone Repository

```bash
# SSH to production server
ssh kflegacy@indigo.herosite.pro

# Navigate to deployment directory
cd ~/RISDA

# Clone from Git (if first time)
git clone https://github.com/your-repo/RISDA-ODOMETER.git Application

# OR pull latest (if exists)
cd Application
git pull origin main
```

---

### Step 2: Install PHP Dependencies

```bash
cd ~/RISDA/Application

# Install Composer dependencies (production mode)
composer install --no-dev --optimize-autoloader
```

---

### Step 3: Install & Build Frontend Assets

```bash
# Install NPM dependencies
npm install

# Build for production (IMPORTANT!)
npm run build

# Verify build completed
ls -la public/build/manifest.json
```

---

### Step 4: Setup Environment File

```bash
# Copy example .env
cp .env.example .env

# Edit .env file
nano .env
```

**Paste this .env configuration:**

```env
APP_NAME="RISDA Odometer"
APP_ENV=production
APP_KEY=base64:Y7TE+3nF63nw9u8J/2HmfvQXq12ctzg9KbaB3zpoX08=
APP_DEBUG=false
APP_URL=https://jara.my
APP_TIMEZONE=Asia/Kuala_Lumpur

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
PHP_CLI_SERVER_WORKERS=4

# Password Hashing
BCRYPT_ROUNDS=12
HASH_DRIVER=argon2id
ARGON2ID_MEMORY=131072
ARGON2ID_THREADS=4
ARGON2ID_TIME=6

# RISDA Custom Salt Configuration
RISDA_SALT_ENABLED=true
RISDA_PEPPER=RISDA_SECURE_PEPPER_2024_ODOMETER_SYSTEM
RISDA_SALT_ROUNDS=3

# Logging
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kflegacy_risda
DB_USERNAME=kflegacy_risda
DB_PASSWORD=gPXP73PPCRxKu0NnQV

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Broadcasting & Queue
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=database

# Mail
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_FROM_ADDRESS="noreply@jara.my"
MAIL_FROM_NAME="${APP_NAME}"

# Vite
VITE_APP_NAME="${APP_NAME}"

# Firebase Cloud Messaging
FCM_ENABLED=true
```

**Save:** `Ctrl+X`, `Y`, `Enter`

---

### Step 5: Generate Application Key (If New)

```bash
# Only if APP_KEY is empty
php artisan key:generate
```

---

### Step 6: Run Database Migrations

```bash
# Run migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --force
```

---

### Step 7: Setup .htaccess Files

```bash
# Root .htaccess (redirect to public)
cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to public folder
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L,QSA]
</IfModule>
EOF

# Verify public/.htaccess exists
ls -la public/.htaccess

# If not exists, create it:
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On
    
    # Add CORS headers
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With, X-API-Key, Accept"

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
```

---

### Step 8: Storage & Permissions

```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/build
chmod 644 .htaccess
chmod 644 public/.htaccess

# Set ownership
chown -R kflegacy:kflegacy storage
chown -R kflegacy:kflegacy bootstrap/cache
chown -R kflegacy:kflegacy public/build
chown kflegacy:kflegacy .htaccess
chown kflegacy:kflegacy public/.htaccess
```

---

### Step 8: Optimize for Production

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
php artisan optimize
```

---

### Step 9: Clean Up Development Files

```bash
# Remove hot file (if exists)
rm -f public/hot
rm -f storage/framework/hot

# Remove dev dependencies
composer install --no-dev --optimize-autoloader
```

---

### Step 10: Restart Services

```bash
# Create restart trigger for PHP-FPM
mkdir -p tmp
touch tmp/restart.txt

# Or restart via cPanel
# Go to: cPanel → Application Manager → Restart
```

---

## 🔄 UPDATE/REDEPLOY (Existing Installation)

**Quick update commands:**

```bash
cd ~/RISDA/Application

# 1. Pull latest code
git pull origin main

# 2. Update dependencies
composer install --no-dev --optimize-autoloader
npm install

# 3. Rebuild assets
npm run build

# 4. Run migrations (if any new)
php artisan migrate --force

# 5. Clear & cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. Remove dev files
rm -f public/hot storage/framework/hot

# 7. Restart
touch tmp/restart.txt
```

---

## 🧪 VERIFICATION CHECKLIST

After deployment, verify:

```bash
# 1. Check environment
php artisan config:show app.env
# Should show: production

# 2. Check app key exists
php artisan config:show app.key
# Should show: base64:...

# 3. Check database connection
php artisan db:show

# 4. Check build files
ls -la public/build/manifest.json
ls -la public/build/assets/

# 5. Check storage link
ls -la public/storage

# 6. Check permissions
ls -la storage/
ls -la bootstrap/cache/
```

---

## 🌐 BROWSER TEST

1. Visit: `https://jara.my`
2. Hard refresh: `Ctrl+Shift+R`
3. Check console for errors
4. Test login
5. Test navigation

---

## 🚨 TROUBLESHOOTING

### Issue: Alpine.js Errors

**Temporary workaround (until fixed):**
- Site still functional
- Ignore console errors
- Will fix in future update

### Issue: Assets not loading

```bash
rm -f public/hot
php artisan config:clear
php artisan cache:clear
```

### Issue: 500 Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Issue: Database connection

```bash
# Test connection
php artisan db:show

# Check .env
cat .env | grep DB_
```

---

## 📋 COMPLETE ONE-LINER FRESH INSTALL

**Copy & paste for complete fresh installation:**

```bash
cd ~/RISDA && git clone https://github.com/your-repo/RISDA-ODOMETER.git Application && cd Application && composer install --no-dev --optimize-autoloader && npm install && npm run build && cp .env.example .env && php artisan key:generate && php artisan storage:link && chmod -R 755 storage bootstrap/cache public/build && chmod 644 .htaccess public/.htaccess && rm -f public/hot storage/framework/hot && echo "✅ Fresh installation complete! Now edit .env file with database credentials, then run migrations."
```

---

## 📋 ONE-LINER UPDATE/REDEPLOY

**Copy & paste for quick updates:**

```bash
cd ~/RISDA/Application && git pull origin main && composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan migrate --force && rm -f public/hot storage/framework/hot && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan optimize && chmod -R 755 storage bootstrap/cache public/build && touch tmp/restart.txt && echo "✅ Deployment complete!"
```

---

## 🔐 SECURITY CHECKLIST

- [ ] `APP_ENV=production` ✅
- [ ] `APP_DEBUG=false` ✅
- [ ] Strong `APP_KEY` generated ✅
- [ ] Database credentials secured ✅
- [ ] `public/hot` deleted ✅
- [ ] Proper file permissions ✅
- [ ] Error logs monitored ✅

---

## 📞 SUPPORT

**Issues?** Contact:
- Developer: Faizan
- Date: October 2025

**Remember to:**
1. ✅ Always run `npm run build` (not `npm run dev`!)
2. ✅ Delete `public/hot` after dev
3. ✅ Clear cache after updates
4. ✅ Test in incognito mode

---

**END OF DEPLOYMENT GUIDE**

