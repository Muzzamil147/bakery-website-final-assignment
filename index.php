<?php
// Home page. Pulls featured products and gallery photos straight from the
// database — nothing on this page is hardcoded, so adding a product or
// photo in the admin panel can make it show up here too.

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();

// Handle the newsletter signup form (posts back to this same page).
$newsletterError = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_email'])) {
    $email = trim($_POST['newsletter_email']);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $newsletterError = 'Please enter a valid email address.';
    } else {
        // INSERT IGNORE: the email column is UNIQUE, so re-subscribing with
        // the same address just silently does nothing instead of erroring.
        $stmt = $pdo->prepare('INSERT IGNORE INTO newsletter_subscribers (email) VALUES (:email)');
        $stmt->execute(['email' => $email]);
        $_SESSION['newsletter_subscribed'] = true;
        header('Location: /index.php#newsletter');
        exit;
    }
}
$newsletterSubscribed = !empty($_SESSION['newsletter_subscribed']);
unset($_SESSION['newsletter_subscribed']);

$featuredProducts = $pdo->query(
    'SELECT p.*, c.name AS category_name FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE p.is_available = 1
     ORDER BY p.id DESC LIMIT 4'
)->fetchAll();

$galleryTeaser = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC LIMIT 6')->fetchAll();

$pageTitle = 'Golden Crust Bakery — Freshly Baked, Always Golden';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <span class="eyebrow">Est. 2015 &middot; Riverside, CA</span>
            <h1>Freshly Baked,<br>Always Golden.</h1>
            <p>Golden Crust Bakery is a family-run neighborhood bakery, baking artisan bread, cakes, and
                pastries fresh every single morning using local, honest ingredients.</p>
            <div class="hero-actions">
                <a href="/services.php" class="btn btn-primary">View Our Menu</a>
                <a href="/contact.php" class="btn btn-outline">Order a Custom Cake</a>
            </div>
        </div>
        <div class="hero-art" aria-hidden="true">
            <div class="hero-blob"></div>
        </div>
    </div>
</section>

<section class="stats-band">
    <div class="container stats-grid">
        <div class="stat-item reveal">
            <span class="counter" data-count-to="10" data-suffix="+">0</span>
            <p>Years Baking</p>
        </div>
        <div class="stat-item reveal" data-reveal-delay="100">
            <span class="counter" data-count-to="52000" data-suffix="+">0</span>
            <p>Pastries Baked</p>
        </div>
        <div class="stat-item reveal" data-reveal-delay="200">
            <span class="counter" data-count-to="15400" data-suffix="+">0</span>
            <p>Happy Customers</p>
        </div>
        <div class="stat-item reveal" data-reveal-delay="300">
            <span class="counter" data-count-to="49" data-suffix="/50">0</span>
            <p>Average Rating</p>
        </div>
    </div>
</section>

<section class="section features">
    <div class="container">
        <div class="feature-grid">
            <div class="feature-card reveal">
                <div class="feature-icon">🌾</div>
                <h3>Baked Fresh Daily</h3>
                <p>Every loaf, cake, and pastry is made from scratch each morning &mdash; nothing sits overnight.</p>
            </div>
            <div class="feature-card reveal" data-reveal-delay="120">
                <div class="feature-icon">📍</div>
                <h3>Local Ingredients</h3>
                <p>We source flour, dairy, and produce from farms within 50 miles whenever we can.</p>
            </div>
            <div class="feature-card reveal" data-reveal-delay="240">
                <div class="feature-icon">🎂</div>
                <h3>Custom Orders</h3>
                <p>Birthdays, weddings, or just because &mdash; we design cakes tailored to your celebration.</p>
            </div>
        </div>
    </div>
</section>

<section class="section section-tinted">
    <div class="container">
        <div class="section-heading reveal">
            <h2>How We Bake</h2>
            <p>From flour to your table, every loaf follows the same four steps.</p>
        </div>
        <div class="process-grid">
            <div class="process-step reveal">
                <div class="process-number">1</div>
                <h3>Mix</h3>
                <p>Flour, water, and starter are mixed by hand before sunrise.</p>
            </div>
            <div class="process-step reveal" data-reveal-delay="120">
                <div class="process-number">2</div>
                <h3>Proof</h3>
                <p>Dough rests slowly, developing flavor the old-fashioned way.</p>
            </div>
            <div class="process-step reveal" data-reveal-delay="240">
                <div class="process-number">3</div>
                <h3>Bake</h3>
                <p>Stone-deck ovens give every loaf its signature golden crust.</p>
            </div>
            <div class="process-step reveal" data-reveal-delay="360">
                <div class="process-number">4</div>
                <h3>Serve</h3>
                <p>On the shelf within the hour &mdash; as fresh as it gets.</p>
            </div>
        </div>
    </div>
</section>

<section class="section featured">
    <div class="container">
        <div class="section-heading reveal">
            <h2>Fresh From the Oven</h2>
            <p>A few favorites from our menu.</p>
        </div>
        <div class="product-grid">
            <?php foreach ($featuredProducts as $i => $product): ?>
                <div class="product-card reveal" data-reveal-delay="<?= $i * 100 ?>">
                    <div class="product-image">
                        <img src="/<?= e($product['image_path']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                    </div>
                    <div class="product-body">
                        <span class="product-category"><?= e($product['category_name']) ?></span>
                        <h3><?= e($product['name']) ?></h3>
                        <p class="product-price">$<?= number_format((float) $product['price'], 2) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="/services.php" class="btn btn-outline">See Full Menu</a>
        </div>
    </div>
</section>

<section class="section section-tinted">
    <div class="container">
        <div class="section-heading reveal">
            <h2>What Our Customers Say</h2>
            <p>A few kind words from regulars.</p>
        </div>
        <div class="testimonial-grid">
            <div class="testimonial-card reveal">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">"The sourdough alone is worth the drive across town. Best crust I've had outside of San Francisco."</p>
                <div class="testimonial-author">
                    <img src="/assets/uploads/testimonials/priya.svg" alt="Priya S." class="testimonial-avatar">
                    <div>
                        <div class="testimonial-name">Priya S.</div>
                        <div class="testimonial-role">Regular customer since 2019</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal" data-reveal-delay="120">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">"Ordered a custom birthday cake with two days' notice and they absolutely delivered. Gorgeous and delicious."</p>
                <div class="testimonial-author">
                    <img src="/assets/uploads/testimonials/marcus.svg" alt="Marcus J." class="testimonial-avatar">
                    <div>
                        <div class="testimonial-name">Marcus J.</div>
                        <div class="testimonial-role">Custom cake order</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal" data-reveal-delay="240">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">"Our office orders croissants from Golden Crust every Friday. Never once been disappointed."</p>
                <div class="testimonial-author">
                    <img src="/assets/uploads/testimonials/elena.svg" alt="Elena R." class="testimonial-avatar">
                    <div>
                        <div class="testimonial-name">Elena R.</div>
                        <div class="testimonial-role">Weekly regular</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($galleryTeaser)): ?>
<section class="section">
    <div class="container">
        <div class="section-heading reveal">
            <h2>A Peek Inside</h2>
            <p>More photos on our <a href="/gallery.php" class="auth-link">Gallery page</a>.</p>
        </div>
        <div class="gallery-teaser-grid reveal">
            <?php foreach ($galleryTeaser as $image): ?>
                <a href="/gallery.php">
                    <img src="/<?= e($image['image_path']) ?>" alt="<?= e($image['caption'] ?: 'Gallery photo') ?>" loading="lazy">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section-tinted">
    <div class="container">
        <div class="section-heading reveal">
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="faq-list reveal">
            <div class="faq-item open">
                <button type="button" class="faq-question">Do you take custom cake orders?</button>
                <div class="faq-answer">Yes! We recommend at least 48 hours' notice for custom cakes, though we'll always try to
                    accommodate shorter timelines when we can. Reach out through the <a href="/contact.php">contact page</a> with
                    your date and design ideas.</div>
            </div>
            <div class="faq-item">
                <button type="button" class="faq-question">Do you offer gluten-free or vegan options?</button>
                <div class="faq-answer">We currently bake a small rotating selection of gluten-free and vegan pastries on weekends
                    &mdash; call ahead to check what's available that day.</div>
            </div>
            <div class="faq-item">
                <button type="button" class="faq-question">What are your hours?</button>
                <div class="faq-answer">We're open Tuesday through Sunday, 7:00 AM to 6:00 PM, and closed on Mondays so our team
                    gets a well-earned rest.</div>
            </div>
            <div class="faq-item">
                <button type="button" class="faq-question">Do you deliver?</button>
                <div class="faq-answer">Local delivery is available for orders over $40 within Riverside city limits. Mention it
                    when you place your order.</div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="newsletter">
    <div class="container">
        <div class="newsletter-band reveal">
            <h2>Get Fresh Updates</h2>
            <p>Seasonal menu drops, holiday hours, and the occasional discount &mdash; straight to your inbox.</p>

            <?php if ($newsletterSubscribed): ?>
                <div class="auth-success" style="max-width:420px;margin:0 auto 16px;">You're subscribed &mdash; thanks!</div>
            <?php elseif ($newsletterError): ?>
                <div class="alert alert-error" style="max-width:420px;margin:0 auto 16px;"><?= e($newsletterError) ?></div>
            <?php endif; ?>

            <form method="post" action="/index.php#newsletter" class="newsletter-form">
                <input type="email" name="newsletter_email" placeholder="you@example.com" required>
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        </div>
    </div>
</section>

<section class="section cta-band">
    <div class="container cta-band-inner">
        <div>
            <h2>Planning something special?</h2>
            <p>Reach out and let's talk about your custom order.</p>
        </div>
        <a href="/contact.php" class="btn btn-primary">Contact Us</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
