#!/bin/bash
set -euo pipefail

# change this path if you prefer another location
logfile="$HOME/logs/cert-renewal.$(date +"%Y-%m-%d_%H-%M-%S")"

# for keeping track of whether ports 80 and 443 are open
ports_opened=false

# for future use:
# email="david@davidmintz.org"

log() {
    echo "$@" >> "$logfile"
}
# for future use:
# days=$(certbot certificates 2>/dev/null | perl -ne 'print "$1\n" if /VALID:\s+(\d+)\s+days/')

cleanup() {
    if [ "$ports_opened" = true ]; then
        log "Closing ports 80 and 443..."
        if ufw delete allow in on ens3 to any port 80 proto tcp >/dev/null 2>&1 && \
           ufw delete allow in on ens3 to any port 443 proto tcp >/dev/null 2>&1; then
            log "Ports closed successfully."
        else
            log "WARNING: failed to close one or more ports!"
        fi
    else
        log "Skipping port closure: ports were not opened successfully."
    fi

    log "Restarting Apache..."
    if systemctl start apache2 >/dev/null 2>&1; then
        log "Apache restarted successfully."
    else
        log "WARNING: failed to restart Apache!"
    fi

    log "Script completed."
}
trap cleanup EXIT

log "Stopping web server..."
systemctl stop apache2

log "Opening ports 80 and 443..."
ufw allow in on ens3 to any port 80 proto tcp >/dev/null 2>&1
ufw allow in on ens3 to any port 443 proto tcp >/dev/null 2>&1
log "Ports opened successfully."
ports_opened=true

log "Running certbot renew..."
if certbot renew --standalone "$@" 2>&1 | tee -a "$logfile"; then
    log "certbot renew $*: completed successfully."
else
    log "certbot renew $* failed."
    exit 1
fi


