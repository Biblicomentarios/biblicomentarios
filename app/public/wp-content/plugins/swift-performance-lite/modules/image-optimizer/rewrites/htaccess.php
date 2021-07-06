<?php ob_start();?>

# ------------------------------------------------------------------------------
# | WebP                                                                       |
# ------------------------------------------------------------------------------

<IfModule mod_setenvif.c>
  # Vary: Accept for all the requests to jpeg and png
  SetEnvIf Request_URI "\.(jpe?g|png)$" REQUEST_image
</IfModule>

<IfModule mod_rewrite.c>
  RewriteEngine On

  # Check if browser supports WebP images
  RewriteCond %{HTTP_ACCEPT} image/webp

  # Check if WebP replacement image exists
  RewriteCond <?php echo ABSPATH;?>$1.$2.webp -f

  # Serve WebP image instead
  RewriteRule (.+)\.(jpe?g|png)$ $1.$2.webp [T=image/webp]
</IfModule>

<IfModule mod_headers.c>
  Header append Vary Accept env=REQUEST_image
</IfModule>

<IfModule mod_mime.c>
  AddType image/webp .webp
</IfModule>

<?php if (Swift_Performance::check_option('webp-no-cache', 1)) :?>
<IfModule mod_headers.c>
    <filesMatch "\.(jpe?g|png|webp)$">
        Header set Cache-Control "private, max-age=2592000"
    </filesMatch>
</IfModule>
<?php endif;?>
<?php return ob_get_clean();?>