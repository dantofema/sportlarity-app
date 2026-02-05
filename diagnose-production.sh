#!/bin/bash
#
# Sportlarity.app Production Diagnostic Script
# Diagnoses the /tmp/0f4425c3/index.php error
#

echo "=============================================="
echo "  SPORTLARITY.APP DIAGNOSTIC REPORT"
echo "  Generated: $(date)"
echo "=============================================="
echo ""

echo "=== 1. PHP-FPM 8.3 POOLS ==="
ls -la /etc/php/8.3/fpm/pool.d/
echo ""

echo "=== 2. PHP-FPM 8.4 POOLS (if exists) ==="
ls -la /etc/php/8.4/fpm/pool.d/ 2>/dev/null || echo "PHP 8.4 pool directory not found"
echo ""

echo "=== 3. SPORTLARITY.APP POOL CONFIG ==="
cat /etc/php/8.3/fpm/pool.d/sportlarity.app.conf 2>/dev/null || echo "Pool config not found!"
echo ""

echo "=== 4. NGINX CONFIG FOR SPORTLARITY.APP ==="
cat /etc/nginx/sites-available/sportlarity.app 2>/dev/null || echo "Nginx config not found!"
echo ""

echo "=== 5. PHP-FPM SOCKETS ==="
ls -la /var/run/php/
echo ""

echo "=== 6. DEPLOYMENT STRUCTURE ==="
echo "Current symlink:"
ls -la /home/forge/sportlarity.app/current 2>/dev/null
echo ""
echo "Actual path:"
readlink -f /home/forge/sportlarity.app/current 2>/dev/null
echo ""
echo "Public/index.php exists:"
ls -la /home/forge/sportlarity.app/current/public/index.php 2>/dev/null || echo "NOT FOUND!"
echo ""

echo "=== 7. TMP DIRECTORIES IN PUBLIC ==="
find /home/forge/sportlarity.app -type d -name "tmp" 2>/dev/null || echo "None found"
echo ""

echo "=== 8. PHP 8.3 SESSION SETTINGS ==="
php8.3 -i 2>/dev/null | grep -E "session\.(save_path|cookie_path|name)" | head -10
echo ""

echo "=== 9. PHP-FPM 8.3 STATUS ==="
systemctl status php8.3-fpm --no-pager 2>/dev/null | head -15
echo ""

echo "=== 10. RECENT NGINX ERRORS ==="
tail -10 /var/log/nginx/sportlarity.app-error.log 2>/dev/null || echo "Log not found"
echo ""

echo "=== 11. PRODUCTION .ENV CHECK ==="
cd /home/forge/sportlarity.app/current 2>/dev/null
if [ -f .env ]; then
    echo "SESSION-related variables:"
    grep -E "^(SESSION_|APP_ENV|APP_DEBUG)" .env 2>/dev/null | sed 's/\(PASSWORD\|KEY\|SECRET\)=.*/\1=***REDACTED***/'
else
    echo ".env file not found!"
fi
echo ""

echo "=============================================="
echo "  DIAGNOSTIC COMPLETE"
echo "=============================================="
