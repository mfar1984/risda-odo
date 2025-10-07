# QUICK DEPLOYMENT REFERENCE

**For:** jara.my production server  
**Updated:** October 2025

---

## 🚀 REDEPLOY (EXISTING INSTALLATION)

```bash
cd ~/RISDA/Application && \
git pull origin main && \
composer install --no-dev --optimize-autoloader && \
npm install && \
npm run build && \
php artisan migrate --force && \
rm -f public/hot storage/framework/hot && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan config:cache && \
php artisan optimize && \
touch tmp/restart.txt && \
echo "✅ Done!"
```

---

## ⚠️ IMPORTANT: After Build

**Always delete hot file:**
```bash
rm -f public/hot storage/framework/hot
```

**Then clear cache:**
```bash
php artisan config:clear && php artisan cache:clear
```

---

## 🔍 VERIFY

```bash
# Check files deployed
ls -la public/build/manifest.json

# Check environment
cat .env | grep APP_ENV
# Should show: production

# Check no hot file
ls public/hot
# Should show: No such file
```

---

## 🌐 BROWSER

Hard refresh: `Ctrl+Shift+R` or `Cmd+Shift+R`

---

## 📝 .ENV (Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://jara.my

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=kflegacy_risda
DB_USERNAME=kflegacy_risda
DB_PASSWORD=gPXP73PPCRxKu0NnQV
```

Full `.env` in: `docs/PRODUCTION_DEPLOYMENT.md`

---

**END**

