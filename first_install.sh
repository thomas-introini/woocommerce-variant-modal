docker compose run --rm \
  -e HOME=/var/www/html \
  -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache \
  cli sh -lc '
    mkdir -p "$WP_CLI_CACHE_DIR" &&
    wp core install --url="http://localhost:8000" --title="WCVM Dev" \
      --admin_user=admin --admin_password=admin --admin_email=admin@example.com &&
    wp rewrite structure "/%postname%/" &&
    wp rewrite flush --hard &&
    wp plugin install woocommerce --activate &&
    wp plugin activate woocommerce-variant-modal &&
    wp theme install storefront --activate
  '
