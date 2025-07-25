#!/bin/bash
set -euo pipefail

logfile=$(mktemp)

# for future use:
# email="david@davidmintz.org"
# result=""

log() {
    echo "$@" >> "$logfile"
}
# for future use:
# days=$(certbot certificates 2>/dev/null | perl -ne 'print "$1\n" if /VALID:\s+(\d+)\s+days/')

cleanup() {
    log "Cleaning up: closing ports and restarting Apache..."
    ufw delete allow in on ens3 to any port 80 proto tcp || true
    ufw delete allow in on ens3 to any port 443 proto tcp || true
    systemctl start apache2 || true
}
trap cleanup EXIT
# stop the web server
systemctl stop apache2

# open ports for cert renewal
ufw allow in on ens3 to any port 80 proto tcp
ufw allow in on ens3 to any port 443 proto tcp

log "Running certbot renew..."
if certbot renew --standalone "$@" 2>&1 | tee -a "$logfile"; then
    log "certbot renew $*: completed successfully."

else
    log "certbot renew $* failed."
    exit 1
fi

# close ports whether renewal worked or not
# sudo ufw delete allow in on ens3 to any port 80 proto tcp
# sudo ufw delete allow in on ens3 to any port 443 proto tcp

# start server again
# sudo systemctl start apache2

