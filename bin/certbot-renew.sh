#!/bin/bash
set -euo pipefail

if [[ "$*" == *"--help"* ]]; then
    cat <<EOF
certbot-renew.sh - A certbot wrapper for VPN environments that temporarily opens firewall ports and stops Apache.

Usage: certbot-renew.sh [--dry-run] [certbot options...]

Options:
  --dry-run            Simulate renewal without making changes.
                       This option is passed through to certbot, but the script
                       also detects it and logs that a dry run is being performed.
                       Note: The script will still check whether certificates are
                       due for renewal. To override that and force a dry run
                       regardless of expiration status, set SKIP_EXPIRY_CHECK=true.

Environment variables:
  SKIP_EXPIRY_CHECK    If set to 'true', skips the certificate expiration check
                       and forces a renewal attempt (or dry run) even if certs
                       are not yet due. Useful for testing.

Other arguments:
  Any other arguments are passed directly to certbot.
  For available certbot options, see:
  https://certbot.eff.org/docs/using.html#certbot-command-line-options

Examples:
  # Simulate renewal even if certs aren't due:
  SKIP_EXPIRY_CHECK=true ./certbot-renew.sh --dry-run

  # Run actual renewal (will exit early if certs have more than 30 days left):
  ./certbot-renew.sh

Typical crontab entry:
  MAILTO=you@example.com
  30 4 * * sun /usr/local/bin/certbot-renew.sh 2>&1 | awk '{ print strftime("[%Y-%m-%d %H:%M:%S] "), $0 }' | tee /root/logs/cert-renewal.$(date "+%F_%H-%M-%S")


EOF
    exit 0
fi

if [[ "$*" == *"--dry-run"* ]]; then
    echo "Running in dry-run mode."
fi

# Honor external SKIP_EXPIRY_CHECK env var, default to false
# setting SKIP_EXPIRY_CHECK to true with --dry-run means we will simulate a renewal
# even if no cert is yet due for renewal

: "${SKIP_EXPIRY_CHECK:=false}"

if [[ "$SKIP_EXPIRY_CHECK" != "true" ]]; then

    echo "Checking certificate expiration..."
    days_remaining=$(certbot certificates 2>/dev/null | \
        grep -oP 'VALID: \K[0-9]+(?= days)' | sort -n | head -1)

    if [[ -z "$days_remaining" ]]; then
        echo "ERROR: Could not determine certificate expiry. Proceeding anyway."
    elif (( days_remaining > 30 )); then
        echo "All certificates have more than 30 days remaining ($days_remaining). Exiting."
        exit 0
    else
        echo "Earliest certificate expires in $days_remaining days. Proceeding with renewal."
    fi
else
    echo "SKIP_EXPIRY_CHECK is true — skipping expiration check."
fi

ports_opened=false

cleanup() {
    if [ "$ports_opened" = "true" ]; then
        echo "Closing ports 80 and 443..."
        if ufw delete allow in on ens3 to any port 80 proto tcp >/dev/null 2>&1 && \
           ufw delete allow in on ens3 to any port 443 proto tcp >/dev/null 2>&1; then
            echo "Ports closed successfully."
        else
            echo "WARNING: failed to close one or more ports!"
        fi
    else
        echo "Skipping port closure: ports were not opened successfully."
    fi

    echo "Restarting Apache..."
    if systemctl start apache2 >/dev/null 2>&1; then
        echo "Apache restarted successfully."
    else
        echo "WARNING: failed to restart Apache!"
    fi

    echo "Script completed."
}
trap cleanup EXIT


echo "Stopping web server..."
systemctl stop apache2

echo "Opening ports 80 and 443..."
ufw allow in on ens3 to any port 80 proto tcp >/dev/null 2>&1 || true
ufw allow in on ens3 to any port 443 proto tcp >/dev/null 2>&1 || true
echo "Ports opened (or already open)."
ports_opened=true

echo "Running certbot renew..."
if certbot renew --standalone "$@"; then
    echo "certbot renew $*: completed successfully."
else
    echo "certbot renew $* failed."
    exit 1
fi
