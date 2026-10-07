#!/bin/sh

# check-php-extensions.sh - Runs inside the container
# Usage: ./check-php-extensions.sh (inside container)
# Or: docker exec -it container-name ./check-php-extensions.sh

# Define required and recommended PHP extensions for Laravel
REQUIRED_EXTENSIONS="ctype curl dom fileinfo filter hash mbstring openssl pcre pdo session tokenizer xml"
RECOMMENDED_EXTENSIONS="bcmath gd intl json pcntl pdo_mysql redis zip"

# Function to get current extensions
get_current_extensions() {
    php -m 2>/dev/null | tail -n +8 | tr '[:upper:]' '[:lower:]'
}

# Function to check if extension exists (case insensitive)
extension_exists() {
    ext="$1"
    echo "$CURRENT_EXTENSIONS" | grep -Fxq "$ext"
}

# Function to find missing extensions
find_missing() {
    ext_list="$1"
    result=""
    for ext in $ext_list; do
        if ! extension_exists "$ext"; then
            result="$result $ext"
        fi
    done
    echo "$result"
}

# Function to find extra extensions
find_extra() {
    all_exts="$1"
    required="$2"
    recommended="$3"
    result=""

    for ext in $all_exts; do
        # Skip if in required or recommended
        in_required=0
        in_recommended=0

        for req in $required; do
            if [ "$ext" = "$req" ]; then
                in_required=1
                break
            fi
        done

        if [ $in_required -eq 0 ]; then
            for rec in $recommended; do
                if [ "$ext" = "$rec" ]; then
                    in_recommended=1
                    break
                fi
            done
        fi

        if [ $in_required -eq 0 ] && [ $in_recommended -eq 0 ]; then
            result="$result $ext"
        fi
    done

    echo "$result"
}

# Check if php command exists
if ! command -v php >/dev/null 2>&1; then
    echo "❌ Error: php command not found" >&2
    exit 1
fi

# Get current extensions
CURRENT_EXTENSIONS=$(get_current_extensions)

# Check if we got any extensions
if [ -z "$CURRENT_EXTENSIONS" ]; then
    echo "❌ Error: Could not get PHP extensions" >&2
    exit 1
fi

# Find missing extensions
MISSING_REQUIRED=$(find_missing "$REQUIRED_EXTENSIONS")
MISSING_RECOMMENDED=$(find_missing "$RECOMMENDED_EXTENSIONS")
EXTRA_EXTENSIONS=$(find_extra "$CURRENT_EXTENSIONS" "$REQUIRED_EXTENSIONS" "$RECOMMENDED_EXTENSIONS")

# Trim whitespace
MISSING_REQUIRED=$(echo "$MISSING_REQUIRED" | sed 's/^ //;s/ $//')
MISSING_RECOMMENDED=$(echo "$MISSING_RECOMMENDED" | sed 's/^ //;s/ $//')
EXTRA_EXTENSIONS=$(echo "$EXTRA_EXTENSIONS" | sed 's/^ //;s/ $//')

# Count missing
count_missing_required=0
count_missing_recommended=0
count_extra=0

if [ -n "$MISSING_REQUIRED" ]; then
    count_missing_required=$(echo "$MISSING_REQUIRED" | wc -w | tr -d ' ')
fi

if [ -n "$MISSING_RECOMMENDED" ]; then
    count_missing_recommended=$(echo "$MISSING_RECOMMENDED" | wc -w | tr -d ' ')
fi

if [ -n "$EXTRA_EXTENSIONS" ]; then
    count_extra=$(echo "$EXTRA_EXTENSIONS" | wc -w | tr -d ' ')
fi

# Output results
echo "=========================================="
echo "PHP Extensions Status for Laravel"
echo "=========================================="
echo ""
echo "PHP Version: $(php -v | head -1)"
echo ""

if [ $count_missing_required -eq 0 ]; then
    echo "✅ All REQUIRED extensions are present!"
else
    echo "❌ MISSING REQUIRED extensions ($count_missing_required):"
    for ext in $MISSING_REQUIRED; do
        echo "   - $ext"
    done
fi

echo ""

if [ $count_missing_recommended -eq 0 ]; then
    echo "✅ All RECOMMENDED extensions are present!"
else
    echo "⚠️  MISSING RECOMMENDED extensions ($count_missing_recommended):"
    for ext in $MISSING_RECOMMENDED; do
        echo "   - $ext"
    done
fi

echo ""

if [ $count_extra -gt 0 ]; then
    echo "ℹ️  EXTRA extensions loaded ($count_extra - not in required/recommended list):"
    for ext in $EXTRA_EXTENSIONS; do
        echo "   - $ext"
    done
fi

echo ""
echo "=========================================="

# Generate installation commands for missing extensions
if [ $count_missing_required -gt 0 ] || [ $count_missing_recommended -gt 0 ]; then
    echo ""
    echo "📝 To install missing extensions (run as root):"
    echo ""

    # Check if apk is available (Alpine)
    if command -v apk >/dev/null 2>&1; then
        echo "# For Alpine Linux:"
        echo "apk add --no-cache \\"

        # Map PHP extensions to Alpine packages
        ALL_MISSING="$MISSING_REQUIRED $MISSING_RECOMMENDED"

        # Track packages to avoid duplicates
        packages=""
        for ext in $ALL_MISSING; do
            case "$ext" in
                curl) pkg="curl" ;;
                gd) pkg="gd" ;;
                zip) pkg="zip" ;;
                intl) pkg="intl" ;;
                bcmath) pkg="bcmath" ;;
                pdo_mysql) pkg="mysqlnd" ;;
                redis) pkg="redis" ;;
                *) continue ;;
            esac
            if [ -n "$pkg" ]; then
                # Check if package already added
                echo "$packages" | grep -q "$pkg" || packages="$packages $pkg"
            fi
        done

        # Output packages
        count=0
        total_packages=$(echo "$packages" | wc -w | tr -d ' ')
        if [ $total_packages -gt 0 ]; then
            for pkg in $packages; do
                count=$((count + 1))
                if [ $count -eq $total_packages ]; then
                    echo "    $pkg"
                else
                    echo "    $pkg \\"
                fi
            done
        fi

        # PHP extensions to install via docker-php-ext-install
        php_exts=""
        for ext in $ALL_MISSING; do
            case "$ext" in
                curl|gd|zip|intl|bcmath|json|hash|session) ;;
                *)
                    php_exts="$php_exts $ext"
                    ;;
            esac
        done

        if [ -n "$php_exts" ]; then
            echo "&& docker-php-ext-install \\"
            count=0
            total_php_exts=$(echo "$php_exts" | wc -w | tr -d ' ')
            for php_ext in $php_exts; do
                count=$((count + 1))
                if [ $count -eq $total_php_exts ]; then
                    echo "    $php_ext"
                else
                    echo "    $php_ext \\"
                fi
            done
        fi

        # Enable extensions if needed
        if [ -n "$php_exts" ]; then
            echo "&& docker-php-ext-enable \\"
            count=0
            total_enable=$(echo "$php_exts" | wc -w | tr -d ' ')
            for php_ext in $php_exts; do
                count=$((count + 1))
                if [ $count -eq $total_enable ]; then
                    echo "    $php_ext"
                else
                    echo "    $php_ext \\"
                fi
            done
        fi
        echo ""
    fi

    # Check if apt-get is available (Debian/Ubuntu)
    if command -v apt-get >/dev/null 2>&1; then
        echo "# For Debian/Ubuntu:"
        echo "apt-get update && apt-get install -y \\"

        # Map PHP extensions to Debian packages
        packages=""
        for ext in $ALL_MISSING; do
            case "$ext" in
                curl) pkg="libcurl4-openssl-dev" ;;
                gd) pkg="libgd-dev" ;;
                zip) pkg="libzip-dev" ;;
                intl) pkg="libicu-dev" ;;
                bcmath) pkg="libbcmath-dev" ;;
                pdo_mysql) pkg="libmysqlclient-dev" ;;
                redis) pkg="libhiredis-dev" ;;
                *) continue ;;
            esac
            if [ -n "$pkg" ]; then
                echo "$packages" | grep -q "$pkg" || packages="$packages $pkg"
            fi
        done

        count=0
        total_packages=$(echo "$packages" | wc -w | tr -d ' ')
        if [ $total_packages -gt 0 ]; then
            for pkg in $packages; do
                count=$((count + 1))
                if [ $count -eq $total_packages ]; then
                    echo "    $pkg"
                else
                    echo "    $pkg \\"
                fi
            done
            echo "&& docker-php-ext-install \\"
        fi

        # PHP extensions to install
        php_exts=""
        for ext in $ALL_MISSING; do
            case "$ext" in
                curl|gd|zip|intl|bcmath|json|hash|session) ;;
                *)
                    php_exts="$php_exts $ext"
                    ;;
            esac
        done

        if [ -n "$php_exts" ]; then
            count=0
            total_php_exts=$(echo "$php_exts" | wc -w | tr -d ' ')
            for php_ext in $php_exts; do
                count=$((count + 1))
                if [ $count -eq $total_php_exts ]; then
                    echo "    $php_ext"
                else
                    echo "    $php_ext \\"
                fi
            done
        fi
        echo ""
    fi
fi

# Check if running in container
if [ -f /.dockerenv ] || [ -f /run/.containerenv ]; then
    echo "🐳 Running inside container"
else
    echo "💻 Running on host system"
fi

echo ""
echo "🔧 To run this script inside a container:"
echo "   docker exec -it container-name ./check-php-extensions.sh"
echo ""
