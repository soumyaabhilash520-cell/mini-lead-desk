<?php
/**
 * LeadDesk Mini - Public Landing Page & Lead Capture
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "LeadDesk Mini — Next-Gen Lead Management";

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<!-- ═══ HERO SECTION ════════════════════════════════════════════════════════ -->
<section class="hero-section">
    <div class="hero-bg-glow"></div>
    <div class="hero-bg-glow-2"></div>

    <div class="container position-relative">
        <div class="row align-items-center g-5">

            <!-- Left: Copy -->
            <div class="col-lg-6">
                <div class="hero-pill">
                    <span class="dot"></span>
                    NEXT-GEN LEAD MANAGEMENT
                </div>

                <h1 class="hero-heading">
                    Convert Visitors into<br>
                    <span class="text-gradient">High-Value Leads</span>
                </h1>

                <p class="hero-sub">
                    The ultimate SaaS toolkit for sales teams. Capture, track, and close
                    deals faster with our real-time analytical engine and glass-fluid interface.
                </p>

                <ul class="hero-checkmarks">
                    <li>AJAX Tracking</li>
                    <li>Secure Cloud</li>
                    <li>Dark Mode</li>
                    <li>CSV Export</li>
                </ul>
            </div>

            <!-- Right: Lead Capture Form -->
            <div class="col-lg-6">
                <div class="lead-form-card">
                    <!-- Stats strip -->
                    <div class="form-stat-strip">
                        <div class="form-stat-item">
                            <span class="val">1.2k+</span>
                            <span class="lbl">Leads Captured</span>
                        </div>
                        <div style="width:1px;background:var(--border);flex-shrink:0;"></div>
                        <div class="form-stat-item">
                            <span class="val">88%</span>
                            <span class="lbl">Conversion</span>
                        </div>
                        <div style="width:1px;background:var(--border);flex-shrink:0;"></div>
                        <div class="form-stat-item">
                            <span class="val">Real-time</span>
                            <span class="lbl">Analytics</span>
                        </div>
                    </div>

                    <h3>Get Started</h3>

                    <?php if ($flash_success): ?>
                        <div class="alert-dark-danger" style="border-color:rgba(52,211,153,.25);background:rgba(52,211,153,.1);color:#6ee7b7;margin-bottom:1rem;">
                            <i class="fa-solid fa-circle-check"></i>
                            <?= htmlspecialchars($flash_success) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($flash_error): ?>
                        <div class="alert-dark-danger" style="margin-bottom:1rem;">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <?= htmlspecialchars($flash_error) ?>
                        </div>
                    <?php endif; ?>

                    <form id="leadCaptureForm" action="submit-lead.php" method="POST" novalidate>
                        <input type="hidden" name="csrf_token" id="csrf_token" value="<?= get_csrf_token() ?>">

                        <div class="row g-3">
                            <!-- Full Name -->
                            <div class="col-12 form-group">
                                <label for="name" class="form-label-custom">Full Name</label>
                                <input type="text" class="form-control-custom" id="name" name="name"
                                       placeholder="John Doe" required minlength="3" maxlength="100">
                                <div class="invalid-feedback-custom"></div>
                            </div>

                            <!-- Email -->
                            <div class="col-12 form-group">
                                <label for="email" class="form-label-custom">Email Address</label>
                                <input type="email" class="form-control-custom" id="email" name="email"
                                       placeholder="john@company.com" required>
                                <div class="invalid-feedback-custom"></div>
                            </div>

                            <!-- Budget -->
                            <div class="col-12 form-group">
                                <label for="budget" class="form-label-custom">Budget Range</label>
                                <select class="form-select-custom" id="budget" name="budget" required>
                                    <option value="" disabled selected>Select your budget</option>
                                    <option value="Under $500">Under $500</option>
                                    <option value="$500–$1,000">$500 – $1,000</option>
                                    <option value="$1,000–$5,000">$1,000 – $5,000</option>
                                    <option value="Over $5,000">Over $5,000</option>
                                </select>
                                <div class="invalid-feedback-custom"></div>
                            </div>

                            <!-- Message -->
                            <div class="col-12 form-group">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <label for="message" class="form-label-custom">Message</label>
                                    <span id="charCounter" style="font-size:.72rem;color:var(--text-muted);">0 / 300</span>
                                </div>
                                <textarea class="form-control-custom" id="message" name="message" rows="3"
                                          placeholder="Tell us about your project..." required minlength="10" maxlength="1000"
                                          style="resize:vertical;"></textarea>
                                <div class="invalid-feedback-custom"></div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 mt-1">
                                <button type="submit" id="submitLeadBtn" class="btn-brand w-100" style="justify-content:center;padding:.85rem;">
                                    <i class="fa-solid fa-paper-plane"></i> Get Started
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ FEATURES SECTION ════════════════════════════════════════════════════ -->
<section id="features" class="features-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow">Features</span>
            <h2 style="font-size:clamp(1.75rem,4vw,2.75rem);font-weight:800;">
                Engineered for <span class="text-gradient">Performance</span>
            </h2>
            <p style="color:var(--text-secondary);max-width:500px;margin:.75rem auto 0;font-size:.92rem;line-height:1.7;">
                A suite of high-velocity tools designed to handle millions of data points
                without breaking a sweat.
            </p>
        </div>

        <div class="row g-4">
            <!-- AJAX Tracking — wide left card -->
            <div class="col-lg-6">
                <div class="feature-card h-100" style="background:linear-gradient(135deg,var(--bg-surface) 60%,var(--bg-surface-2));">
                    <div class="feature-icon">
                        <i class="fa-solid fa-chart-mixed"></i>
                    </div>
                    <h4>AJAX Tracking</h4>
                    <p>Real-time behavior monitoring that captures user intent before they even hit submit.
                       Our background tracking scripts are lightweight and asynchronous, ensuring zero impact
                       on your site's page load speed.</p>

                    <!-- Mini image strip -->
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <div style="height:90px;border-radius:var(--radius-md);background:var(--bg-surface-3);border:1px solid var(--border);overflow:hidden;display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:1.8rem;">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="height:90px;border-radius:var(--radius-md);background:var(--bg-surface-3);border:1px solid var(--border);overflow:hidden;display:flex;align-items:center;justify-content:center;color:var(--brand);font-size:1.8rem;">
                                <i class="fa-solid fa-network-wired"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secure-First Architecture -->
            <div class="col-lg-6">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(129,140,248,.12);border-color:rgba(129,140,248,.2);color:#818cf8;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h4>Secure-First Architecture</h4>
                    <p>Enterprise-grade encryption for every single lead. GDPR compliant and SOC2 ready by default.</p>

                    <ul class="feature-check-list" style="margin-top:1.25rem;">
                        <li><span class="check-tag">✓</span> SSL ENCRYPTION</li>
                        <li><span class="check-tag">✓</span> DATA ANONYMIZATION</li>
                        <li><span class="check-tag">✓</span> CSRF PROTECTION</li>
                        <li><span class="check-tag">✓</span> PREPARED STATEMENTS</li>
                    </ul>
                </div>
            </div>

            <!-- Real-Time Engine -->
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(251,191,36,.12);border-color:rgba(251,191,36,.2);color:#fbbf24;">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h4>Real-Time Engine</h4>
                    <p>Zero-latency alerts delivered to your CRM the moment a high-intent lead interacts with your form.</p>
                </div>
            </div>

            <!-- Omnichannel Sync -->
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(34,211,238,.12);border-color:rgba(34,211,238,.2);color:#22d3ee;">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </div>
                    <h4>Omnichannel Sync</h4>
                    <p>Automatically sync captures across Slack, Discord, Email, and your native Mobile App. Stay connected.</p>
                </div>
            </div>

            <!-- CSV + Analytics -->
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="fa-solid fa-file-csv"></i>
                    </div>
                    <h4>Analytics &amp; Export</h4>
                    <p>Interactive Chart.js dashboards with budget distribution, status breakdown, and one-click CSV export.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ LEAD FORM ANCHOR (id="contact") ═══════════════════════════════════ -->
<div id="contact"></div>

<!-- ═══ PUBLIC FOOTER ═══════════════════════════════════════════════════════ -->
<footer class="public-footer">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div>
                <span class="footer-brand">
                    <span class="brand-dot" style="width:8px;height:8px;border-radius:50%;background:var(--brand);display:inline-block;"></span>
                    LeadDesk Mini
                </span>
                <div class="footer-copy mt-1">
                    &copy; 2024 LeadDesk Mini. Built for
                    <a href="https://digitalheroesco.com" target="_blank" rel="noopener noreferrer" style="color:var(--brand);">Digital Heroes Training Task</a>
                </div>
            </div>

            <div class="d-flex align-items-center gap-4">
                <div class="footer-links d-flex gap-3">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
                <div class="footer-actions">
                    <a href="#" class="footer-icon-btn"><i class="fa-solid fa-globe"></i></a>
                    <a href="#" class="footer-icon-btn"><i class="fa-solid fa-share-nodes"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php include __DIR__ . '/includes/footer.php'; ?>
