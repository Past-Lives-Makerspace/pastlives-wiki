#!/usr/bin/env bash
# Deploy the latest main to the live wiki. Run on the Hetzner VPS as root:
#   /var/www/wiki.pastlives.space/public/deploy.sh
set -euo pipefail

PUBLIC=/var/www/wiki.pastlives.space/public
RUN_AS=wiki_pastlives_space

sudo -u "$RUN_AS" git -C "$PUBLIC" pull --ff-only
sudo -u "$RUN_AS" php "$PUBLIC/maintenance/run.php" update --quick

echo "Done — verify: https://wiki.pastlives.space/Special:Version"
