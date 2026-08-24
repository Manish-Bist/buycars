    <section class="footer" id="footer">
        <div class="box-container">
            <div class="box">
                <h3>BuyCars</h3>
                <p style="color:#ccc;font-size:1.5rem;line-height:1.8;">Your trusted marketplace to buy and sell cars with confidence. Verified listings, direct contact with sellers, zero hassle.</p>
            </div>
            <div class="box">
                <h3>quick links</h3>
                <a href="<?= BASE_URL ?>index.php">&#8250; home</a>
                <a href="<?= BASE_URL ?>cars.php">&#8250; vehicles</a>
                <a href="<?= BASE_URL ?>sell-car.php">&#8250; sell your car</a>
                <a href="<?= BASE_URL ?>index.php#reviews">&#8250; reviews</a>
                <a href="<?= BASE_URL ?>index.php#contact">&#8250; contact</a>
            </div>
            <div class="box">
                <h3>contact info</h3>
                <a href="tel:+9779800000000">&#128222; +977 980-0000000</a>
                <a href="mailto:support@buycars.com">&#9993; support@buycars.com</a>
                <a href="#">&#128205; Kathmandu, Nepal</a>
            </div>
        </div>
    </section>

    <section class="contact-info" id="contact-info">
        <div class="social">
            <a href="#">f</a>
            <a href="#">in</a>
            <a href="#">t</a>
            <a href="#">yt</a>
        </div>
        <div class="links">
            <a href="#">Privacy Policy</a>
            <a href="#">Our Company</a>
            <a href="#">Terms Of Use</a>
        </div>
        <p>&copy; <?= date('Y') ?> All Rights Reserved &mdash; BuyCars</p>
    </section>

<button id="backToTop" title="Back to top">&#8679;</button>

<!-- CDN scripts loaded with defer so they NEVER block page rendering -->
<script defer src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
<script defer src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Local scripts always load instantly -->
<script src="<?= BASE_URL ?>assets/js/script.js"></script>
<script src="<?= BASE_URL ?>assets/js/app.js"></script>
</body>
</html>
