<?php
// About page. The team grid at the bottom is entirely database-driven —
// managed from admin/team.php, ordered by the "display_order" staff set there.

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();
$team = $pdo->query('SELECT * FROM team_members ORDER BY display_order ASC, id ASC')->fetchAll();

$pageTitle = 'About Us — Golden Crust Bakery';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Our Story</span>
        <h1>Baking for the Neighborhood Since 2015</h1>
    </div>
</section>

<section class="section">
    <div class="container about-story">
        <div class="reveal">
            <h2>How It Started</h2>
            <p>Golden Crust Bakery began in a small rented kitchen in 2015, when founder Amara Khan started
                selling sourdough loaves at the local farmers market on weekends. Word spread quickly, and
                within a year Amara opened our storefront on Maple Street.</p>
            <p>Over a decade later, we're still doing things the same way: mixing dough by hand, baking in
                small batches, and closing the doors as soon as the day's bread runs out. We believe good
                bread doesn't need shortcuts &mdash; just time, good flour, and people who care.</p>
        </div>
        <div class="reveal" data-reveal-delay="150">
            <h2>What We Believe In</h2>
            <div class="value-grid">
                <div class="value-card">
                    <div class="value-icon">🌱</div>
                    <h3>Fresh, Never Frozen</h3>
                    <p>Everything is baked the same day it's sold.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3>Honest Ingredients</h3>
                    <p>No preservatives, no shortcuts.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🏘️</div>
                    <h3>Community First</h3>
                    <p>Proud to be part of the Riverside neighborhood.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stats-band">
    <div class="container stats-grid">
        <div class="stat-item reveal">
            <span class="counter" data-count-to="2015" data-suffix="" data-plain="true">0</span>
            <p>Year Founded</p>
        </div>
        <div class="stat-item reveal" data-reveal-delay="100">
            <span class="counter" data-count-to="12" data-suffix="">0</span>
            <p>Team Members</p>
        </div>
        <div class="stat-item reveal" data-reveal-delay="200">
            <span class="counter" data-count-to="40" data-suffix="+">0</span>
            <p>Menu Items</p>
        </div>
        <div class="stat-item reveal" data-reveal-delay="300">
            <span class="counter" data-count-to="3" data-suffix="">0</span>
            <p>Local Awards</p>
        </div>
    </div>
</section>

<section class="section section-tinted">
    <div class="container">
        <div class="section-heading reveal">
            <h2>Our Journey</h2>
            <p>A few milestones along the way.</p>
        </div>
        <div class="timeline">
            <div class="timeline-item reveal">
                <div class="timeline-dot"></div>
                <div class="timeline-year">2015</div>
                <h3>A Farmers Market Stall</h3>
                <p>Amara starts selling sourdough loaves at the Riverside farmers market on weekends.</p>
            </div>
            <div class="timeline-item reveal" data-reveal-delay="100">
                <div class="timeline-dot"></div>
                <div class="timeline-year">2016</div>
                <h3>Our First Storefront</h3>
                <p>Golden Crust Bakery opens its doors on Maple Street with a small team of three.</p>
            </div>
            <div class="timeline-item reveal" data-reveal-delay="200">
                <div class="timeline-dot"></div>
                <div class="timeline-year">2019</div>
                <h3>Expanded Kitchen</h3>
                <p>We knock down a wall and double our baking capacity to keep up with morning demand.</p>
            </div>
            <div class="timeline-item reveal" data-reveal-delay="300">
                <div class="timeline-dot"></div>
                <div class="timeline-year">2023</div>
                <h3>Neighborhood Choice Award</h3>
                <p>Voted Riverside's favorite local bakery three years running by the Riverside Gazette.</p>
            </div>
            <div class="timeline-item reveal" data-reveal-delay="400">
                <div class="timeline-dot"></div>
                <div class="timeline-year">Today</div>
                <h3>Still Family-Run</h3>
                <p>Twelve of us now bake, decorate, and serve the neighborhood every day but Monday.</p>
            </div>
        </div>
    </div>
</section>

<section class="press-strip reveal">
    <div class="container press-strip">
        <div class="press-item"><span class="press-icon">🏆</span> Riverside Gazette &mdash; Best Bakery 2023</div>
        <div class="press-item"><span class="press-icon">⭐</span> 4.9-star average, 1,200+ reviews</div>
        <div class="press-item"><span class="press-icon">📰</span> Featured in Local Eats Weekly</div>
    </div>
</section>

<section class="section team-section">
    <div class="container">
        <div class="section-heading reveal">
            <h2>Meet the Team</h2>
            <p>The people behind every loaf and layer.</p>
        </div>
        <div class="team-grid">
            <?php foreach ($team as $i => $member): ?>
                <div class="team-card reveal" data-reveal-delay="<?= $i * 100 ?>">
                    <img src="/<?= e($member['photo_path']) ?>" alt="<?= e($member['name']) ?>" loading="lazy">
                    <h3><?= e($member['name']) ?></h3>
                    <p class="team-role"><?= e($member['role']) ?></p>
                    <p class="team-bio"><?= e($member['bio']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section cta-band">
    <div class="container cta-band-inner">
        <div>
            <h2>Come Say Hello</h2>
            <p>We're open Tuesday through Sunday &mdash; the coffee's always on.</p>
        </div>
        <a href="/contact.php" class="btn btn-primary">Get Directions</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
