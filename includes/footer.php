</main>

<footer class="footer">
    <div class="footer-card glass">
        <div class="footer-marca">
            <img src="assets/img/logo.png" alt="" width="56" height="56">
            <div>
                <b><?= h(SITE_NOMBRE) ?></b>
                <em><?= h(SITE_FIRMA) ?></em>
            </div>
        </div>

        <div class="redes grande">
            <a href="<?= h(FACEBOOK_URL) ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= icono_facebook() ?><span>Facebook</span></a>
            <a href="<?= h(INSTAGRAM_URL) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icono_instagram() ?><span>Instagram</span></a>
        </div>

        <p class="copy">© <?= date('Y') ?> <?= h(SITE_NOMBRE) ?> <?= h(SITE_FIRMA) ?>. Todos los derechos reservados.</p>
    </div>
</footer>

<script src="assets/js/main.js?v=1" defer></script>
</body>
</html>
