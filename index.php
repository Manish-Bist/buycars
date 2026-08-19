<?php
require_once __DIR__ . '/config/config.php';
$page_title = 'Home';

// Popular vehicles = latest approved listings
$vehicles = $pdo->query("SELECT * FROM cars WHERE status='approved' ORDER BY created_at DESC LIMIT 8")->fetchAll();

// Featured = most viewed approved listings
$featured = $pdo->query("SELECT * FROM cars WHERE status='approved' ORDER BY views DESC LIMIT 8")->fetchAll();

// Reviews
$reviews = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 6")->fetchAll();

// quick stats for the icon strip
$totalCars   = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='approved'")->fetchColumn();
$totalSold   = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='sold'")->fetchColumn();
$totalUsers  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();

require_once __DIR__ . '/includes/header.php';
?>

<section class="home" id="home">
    <h3 data-speed="-2" class="home-parallax">find your car</h3>
    <img data-speed="5" class="home-parallax" src="assets/image/homecar2.png" alt="">
    <a data-speed="7" href="cars.php" class="btn home-parallax">explore cars</a>
</section>

<!-- quick search bar -->
<section class="quick-search" data-aos="fade-up">
    <form action="cars.php" method="GET" class="quick-search-form">
        <div class="qs-field">
            <i class='bx bx-purchase-tag'></i>
            <input type="text" name="q" placeholder="Search brand or model e.g. Toyota">
        </div>
        <div class="qs-field">
            <i class='bx bx-money'></i>
            <select name="max_price">
                <option value="">Any budget</option>
                <option value="20000">Under $20,000</option>
                <option value="40000">Under $40,000</option>
                <option value="60000">Under $60,000</option>
                <option value="100000">Under $100,000</option>
            </select>
        </div>
        <button type="submit" class="btn"><i class='bx bx-search'></i> search cars</button>
    </form>
</section>

<section class="icons-container" data-aos="fade-up">
    <div class="icons">
        <i class="fas fa-car"></i>
        <div class="content"><h3><?= (int)$totalCars ?>+</h3><p>cars listed</p></div>
    </div>
    <div class="icons">
        <i class="fas fa-handshake"></i>
        <div class="content"><h3><?= (int)$totalSold ?>+</h3><p>cars sold</p></div>
    </div>
    <div class="icons">
        <i class="fas fa-users"></i>
        <div class="content"><h3><?= (int)$totalUsers ?>+</h3><p>happy members</p></div>
    </div>
    <div class="icons">
        <i class="fas fa-shield-alt"></i>
        <div class="content"><h3>100%</h3><p>verified listings</p></div>
    </div>
</section>

<section class="services gallery" id="services" data-aos="fade-up">
    <h1 class="heading"> our <span>Gallery</span> </h1>
    <div class="box-container">
        <div class="box gllerybox"><img src="assets/image/gallery1.png" alt=""></div>
        <div class="box gllerybox"><img src="assets/image/gallery2.jpg" alt=""></div>
        <div class="box gllerybox"><img src="assets/image/gallery7.jpg" alt=""></div>
        <div class="box gllerybox"><img src="assets/image/gallery4.jpg" alt=""></div>
        <div class="box gllerybox"><img src="assets/image/gallery5.jpg" alt=""></div>
        <div class="box gllerybox"><img src="assets/image/gallery6.jpg" alt=""></div>
    </div>
</section>

<section class="vehicles" id="vehicles" data-aos="fade-up">
    <h1 class="heading"> popular <span>vehicles</span> </h1>

    <?php if ($vehicles): ?>
    <div class="swiper vehicles-slider">
        <div class="swiper-wrapper">
            <?php foreach ($vehicles as $car): ?>
                <div class="swiper-slide box">
                    <img src="<?= car_primary_image($pdo, $car['id']) ?>" alt="<?= clean($car['title']) ?>">
                    <div class="content">
                        <h3><?= clean($car['title']) ?></h3>
                        <div class="price"><span>price : </span><?= format_price($car['price']) ?>/-</div>
                        <p>
                            <?= clean($car['condition_type']) ?>
                            <span class="fas fa-circle"></span> <?= (int)$car['year'] ?>
                            <span class="fas fa-circle"></span> <?= clean($car['transmission']) ?>
                            <span class="fas fa-circle"></span> <?= clean($car['fuel_type']) ?>
                        </p>
                        <a href="car-details.php?id=<?= $car['id'] ?>" class="btn">check out</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <?php else: ?>
        <p style="text-align:center;color:#888;">No vehicles listed yet. Be the first to <a href="sell-car.php">sell a car</a>!</p>
    <?php endif; ?>
</section>

<section class="services" id="services-list" data-aos="fade-up">
    <h1 class="heading"> our <span>services</span> </h1>
    <div class="box-container">
        <div class="box myservice">
            <i class="fas fa-car"></i>
            <h3>car selling</h3>
            <p>List your car in minutes and reach thousands of verified buyers on our marketplace.</p>
            <a href="sell-car.php" class="btn"> get started</a>
        </div>
        <div class="box">
            <i class="fas fa-search-dollar"></i>
            <h3>easy buying</h3>
            <p>Browse, filter and compare cars by brand, price and year, then contact sellers directly.</p>
            <a href="cars.php" class="btn"> browse cars</a>
        </div>
        <div class="box">
            <i class="fas fa-shield-alt"></i>
            <h3>admin verified</h3>
            <p>Every listing is reviewed by our team before it goes live, keeping the marketplace safe.</p>
            <a href="#" class="btn"> read more</a>
        </div>
        <div class="box">
            <i class="fas fa-heart"></i>
            <h3>save favourites</h3>
            <p>Add cars to your wishlist and come back to compare them anytime.</p>
            <a href="wishlist.php" class="btn"> read more</a>
        </div>
        <div class="box">
            <i class="fas fa-comments"></i>
            <h3>direct inquiries</h3>
            <p>Message sellers directly through the platform, no middle man involved.</p>
            <a href="#" class="btn"> read more</a>
        </div>
        <div class="box">
            <i class="fas fa-headset"></i>
            <h3>24/7 support</h3>
            <p>Our support team is always ready to help with any issue you run into.</p>
            <a href="index.php#contact" class="btn"> contact us</a>
        </div>
    </div>
</section>

<section class="featured" id="featured" data-aos="fade-up">
    <h1 class="heading"> <span>featured</span> cars </h1>

    <?php if ($featured): ?>
    <div class="swiper featured-slider">
        <div class="swiper-wrapper">
            <?php foreach ($featured as $car): ?>
                <div class="swiper-slide box">
                    <img src="<?= car_primary_image($pdo, $car['id']) ?>" alt="<?= clean($car['title']) ?>">
                    <div class="content">
                        <h3><?= clean($car['title']) ?></h3>
                        <div class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="price"><?= format_price($car['price']) ?>/-</div>
                        <a href="car-details.php?id=<?= $car['id'] ?>" class="btn servicebtn">check out</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <?php endif; ?>
</section>

<section class="newsletter" data-aos="zoom-in">
    <h3>subscribe for latest updates</h3>
    <p>Get notified when new cars matching your budget are listed on BuyCars.</p>
    <form action="newsletter-submit.php" method="POST">
        <?= csrf_field() ?>
        <input type="email" name="email" placeholder="enter your email" required>
        <input type="submit" value="subscribe">
    </form>
</section>

<section class="reviews" id="reviews" data-aos="fade-up">
    <h1 class="heading"> client's <span>review</span> </h1>
    <div class="swiper review-slider">
        <div class="swiper-wrapper">
            <?php foreach ($reviews as $r): ?>
                <div class="swiper-slide box">
                    <img src="<?= clean($r['image'] ?: 'assets/image/pic-1.png') ?>" alt="">
                    <div class="content">
                        <p><?= clean($r['message']) ?></p>
                        <h3><?= clean($r['name']) ?></h3>
                        <div class="stars">
                            <?php for ($i = 0; $i < (int)$r['rating']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<section class="contact" id="contact" data-aos="fade-up">
    <h1 class="heading"><span>contact</span> us</h1>
    <div class="row">
        <iframe class="map"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d113193.98!2d85.2871!3d27.7089!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb198a307baabf%3A0xb5137c1bf18db1ea!2sKathmandu!5e0!3m2!1sen!2snp"
            allowfullscreen="" loading="lazy"></iframe>

        <form action="contact-submit.php" method="POST" id="contactForm">
            <?= csrf_field() ?>
            <h3>get in touch</h3>
            <input type="text" name="name" placeholder="your name" class="box" required>
            <input type="email" name="email" placeholder="your email" class="box" required>
            <input type="text" name="subject" placeholder="subject" class="box">
            <textarea name="message" placeholder="your message" class="box" cols="30" rows="10" required></textarea>
            <input type="submit" value="send message" class="btn">
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
