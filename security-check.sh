#!/bin/bash
# ============================================================================
# SPORTLARITY SECURITY DIAGNOSTIC SCRIPT
# ============================================================================
# Run this script on the production server (159.89.91.175) via Forge SSH
# Usage: bash security-check.sh
# ============================================================================

set -e

echo "=============================================="
echo "SPORTLARITY SECURITY DIAGNOSTIC"
echo "Date: $(date)"
echo "Host: $(hostname)"
echo "=============================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

alert() {
    echo -e "${RED}[ALERT]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

ok() {
    echo -e "${GREEN}[OK]${NC} $1"
}

info() {
    echo "[INFO] $1"
}

# Adjust this path to match your Forge deployment
APP_PATH="/home/forge/sportlarity.app/current"

# ============================================================================
# 1. CPU AND PROCESS ANALYSIS
# ============================================================================
echo "=============================================="
echo "1. HIGH CPU PROCESSES"
echo "=============================================="

info "Top 15 processes by CPU usage:"
ps aux --sort=-%cpu | head -16

echo ""
info "Looking for known crypto miners..."
MINERS=$(ps aux | grep -iE 'xmrig|kdevtmpfsi|kinsing|minergate|cryptonight|minerd|cpuminer|cgminer|bfgminer|ethminer|t-rex|phoenix|gminer|nbminer|lolminer' | grep -v grep || true)
if [ -n "$MINERS" ]; then
    alert "CRYPTO MINER DETECTED!"
    echo "$MINERS"
else
    ok "No known crypto miners found in process list"
fi

echo ""
info "Looking for suspicious processes (high CPU, unusual names)..."
ps aux --sort=-%cpu | awk '$3 > 50 {print}' | head -10

# ============================================================================
# 2. CRONTAB ANALYSIS
# ============================================================================
echo ""
echo "=============================================="
echo "2. CRONTAB ANALYSIS"
echo "=============================================="

info "Forge user crontab:"
crontab -l 2>/dev/null || echo "No crontab for forge user"

echo ""
info "Root crontab (if accessible):"
sudo crontab -l 2>/dev/null || echo "Cannot access root crontab"

echo ""
info "System cron files in /etc/cron.d/:"
ls -la /etc/cron.d/ 2>/dev/null || true

echo ""
info "Looking for suspicious cron entries..."
for crondir in /etc/cron.d /etc/cron.daily /etc/cron.hourly /etc/cron.weekly; do
    if [ -d "$crondir" ]; then
        for file in "$crondir"/*; do
            if [ -f "$file" ]; then
                SUSPICIOUS=$(grep -lE 'curl.*\|.*sh|wget.*\|.*sh|base64.*-d|eval|python.*-c|perl.*-e|ruby.*-e' "$file" 2>/dev/null || true)
                if [ -n "$SUSPICIOUS" ]; then
                    warning "Suspicious content in: $file"
                    cat "$file"
                fi
            fi
        done
    fi
done

# ============================================================================
# 3. RECENTLY MODIFIED PHP FILES
# ============================================================================
echo ""
echo "=============================================="
echo "3. RECENTLY MODIFIED PHP FILES (last 7 days)"
echo "=============================================="

if [ -d "$APP_PATH" ]; then
    info "Scanning $APP_PATH for recently modified PHP files (excluding vendor)..."
    find "$APP_PATH" -path "$APP_PATH/vendor" -prune -o -name "*.php" -mtime -7 -type f -print 2>/dev/null | head -50
else
    warning "App path not found at $APP_PATH"
fi

# ============================================================================
# 4. SUSPICIOUS PHP FILES IN PUBLIC/STORAGE
# ============================================================================
echo ""
echo "=============================================="
echo "4. SUSPICIOUS PHP FILES IN PUBLIC/STORAGE"
echo "=============================================="

if [ -d "$APP_PATH" ]; then
    info "PHP files in public directory (excluding index.php):"
    SUSPICIOUS_PUBLIC=$(find "$APP_PATH/public" -name "*.php" ! -name "index.php" 2>/dev/null)
    if [ -n "$SUSPICIOUS_PUBLIC" ]; then
        alert "SUSPICIOUS PHP FILES IN PUBLIC:"
        echo "$SUSPICIOUS_PUBLIC"
        for file in $SUSPICIOUS_PUBLIC; do
            echo "--- Content of $file ---"
            head -20 "$file"
            echo "---"
        done
    else
        ok "Only index.php in public directory"
    fi

    echo ""
    info "PHP files in storage directory:"
    STORAGE_PHP=$(find "$APP_PATH/storage" -name "*.php" 2>/dev/null)
    if [ -n "$STORAGE_PHP" ]; then
        alert "PHP FILES FOUND IN STORAGE!"
        echo "$STORAGE_PHP"
    else
        ok "No PHP files in storage directory"
    fi
fi

# ============================================================================
# 5. OBFUSCATED CODE DETECTION
# ============================================================================
echo ""
echo "=============================================="
echo "5. OBFUSCATED CODE DETECTION"
echo "=============================================="

if [ -d "$APP_PATH" ]; then
    info "Searching for obfuscated PHP code patterns..."
    
    # base64_decode with eval (malware pattern)
    MALWARE=$(grep -rl "eval.*base64_decode\|base64_decode.*eval" "$APP_PATH" --include="*.php" 2>/dev/null | grep -v vendor | grep -v node_modules || true)
    if [ -n "$MALWARE" ]; then
        alert "LIKELY MALWARE - eval+base64_decode pattern found:"
        echo "$MALWARE"
    fi

    # shell functions outside vendor
    SHELL=$(grep -rlE "^\s*(shell_exec|exec|system|passthru|proc_open|popen)\s*\(" "$APP_PATH" --include="*.php" 2>/dev/null | grep -v vendor | grep -v node_modules || true)
    if [ -n "$SHELL" ]; then
        warning "Files with shell execution functions (review manually):"
        echo "$SHELL"
    fi

    # gzinflate/str_rot13 (common in obfuscation)
    OBFUSC=$(grep -rlE "(gzinflate|str_rot13|gzuncompress)" "$APP_PATH" --include="*.php" 2>/dev/null | grep -v vendor | grep -v node_modules || true)
    if [ -n "$OBFUSC" ]; then
        alert "Files with obfuscation functions:"
        echo "$OBFUSC"
    fi

    # Very long single lines (typical of obfuscated code)
    info "Checking for files with suspiciously long lines..."
    find "$APP_PATH" -path "$APP_PATH/vendor" -prune -o -name "*.php" -type f -print 2>/dev/null | while read file; do
        LONGLINE=$(awk 'length > 5000' "$file" 2>/dev/null | head -1)
        if [ -n "$LONGLINE" ]; then
            warning "Very long line in: $file"
        fi
    done
fi

# ============================================================================
# 6. NETWORK CONNECTIONS
# ============================================================================
echo ""
echo "=============================================="
echo "6. SUSPICIOUS NETWORK CONNECTIONS"
echo "=============================================="

info "Established connections to external IPs:"
ss -tn state established 2>/dev/null | grep -v "127.0.0.1" | grep -v "::1" | head -20

echo ""
info "Listening ports:"
ss -tlnp 2>/dev/null | head -20

# ============================================================================
# 7. SSH KEYS CHECK
# ============================================================================
echo ""
echo "=============================================="
echo "7. SSH KEYS CHECK"
echo "=============================================="

info "Authorized keys for forge user:"
cat /home/forge/.ssh/authorized_keys 2>/dev/null | wc -l
echo "Total number of authorized SSH keys above"

info "Recently modified SSH files:"
find /home/forge/.ssh -mtime -7 -type f 2>/dev/null || true

# ============================================================================
# 8. WRITABLE DIRECTORIES CHECK
# ============================================================================
echo ""
echo "=============================================="
echo "8. WORLD-WRITABLE DIRECTORIES"
echo "=============================================="

info "World-writable directories in app (potential upload points):"
find "$APP_PATH" -type d -perm -0002 2>/dev/null | grep -v vendor | head -20

# ============================================================================
# 9. HIDDEN FILES CHECK
# ============================================================================
echo ""
echo "=============================================="
echo "9. HIDDEN PHP FILES"
echo "=============================================="

info "Hidden PHP files (starting with .):"
find "$APP_PATH" -name ".*php" -type f 2>/dev/null | grep -v vendor

info "PHP files with suspicious names:"
find "$APP_PATH" -type f \( -name "*.php.bak" -o -name "*.php.old" -o -name "*.php.txt" -o -name "wp-*.php" -o -name "shell*.php" -o -name "c99*.php" -o -name "r57*.php" -o -name "backdoor*.php" \) 2>/dev/null | grep -v vendor

# ============================================================================
# 10. ENVIRONMENT CHECK
# ============================================================================
echo ""
echo "=============================================="
echo "10. ENVIRONMENT CHECK"
echo "=============================================="

if [ -f "$APP_PATH/.env" ]; then
    info ".env file exists"
    
    if grep -q "APP_DEBUG=true" "$APP_PATH/.env"; then
        alert "APP_DEBUG=true in production! This exposes sensitive information."
    else
        ok "APP_DEBUG is not true"
    fi
    
    if grep -q "APP_ENV=local" "$APP_PATH/.env"; then
        alert "APP_ENV=local in production!"
    else
        ok "APP_ENV is not local"
    fi
else
    warning ".env file not found at $APP_PATH"
fi

# ============================================================================
# 11. LARAVEL LOG ANALYSIS
# ============================================================================
echo ""
echo "=============================================="
echo "11. RECENT LARAVEL ERRORS"
echo "=============================================="

LOGFILE="$APP_PATH/storage/logs/laravel.log"
if [ -f "$LOGFILE" ]; then
    info "Last 30 lines of Laravel log:"
    tail -30 "$LOGFILE"
    
    echo ""
    info "Authentication failures in last 1000 lines:"
    tail -1000 "$LOGFILE" | grep -i "authentication\|login\|failed\|unauthorized" | tail -10 || echo "None found"
else
    warning "Laravel log not found"
fi

# ============================================================================
# SUMMARY
# ============================================================================
echo ""
echo "=============================================="
echo "DIAGNOSTIC COMPLETE"
echo "=============================================="
echo ""
echo "IMMEDIATE ACTIONS IF INFECTION CONFIRMED:"
echo ""
echo "1. Kill suspicious processes:"
echo "   kill -9 <PID>"
echo ""
echo "2. Remove malicious cron entries:"
echo "   crontab -e  # and remove suspicious lines"
echo ""
echo "3. Delete malicious files found above"
echo ""
echo "4. Force all users to change passwords (run in tinker):"
echo "   User::query()->update(['password_change_required' => true]);"
echo ""
echo "5. Clear password reset tokens:"
echo "   DB::table('password_reset_tokens')->truncate();"
echo ""
echo "6. Clear all sessions:"
echo "   php artisan session:table && php artisan migrate"
echo "   DB::table('sessions')->truncate();"
echo ""
echo "7. Regenerate APP_KEY (will log out all users):"
echo "   php artisan key:generate"
echo ""
echo "8. Review and rotate all API keys/secrets in .env"
echo ""
echo "9. If infection persists, consider rebuilding server"
echo ""
