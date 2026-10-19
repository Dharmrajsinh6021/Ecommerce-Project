<?php
$page_title = "About Us";
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-primary text-white py-5 mb-5 text-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%) !important;">
    <div class="container py-3">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">About TechTower</span>
        <h1 class="display-5 fw-extrabold mb-3">Empowering Your Digital Lifestyle</h1>
        <p class="lead col-lg-8 mx-auto text-light">We are dedicated to bringing cutting-edge technology, authentic consumer gadgets, and unbeatable customer service right to your fingertips.</p>
    </div>
</div>

<div class="container mb-5">
    <!-- Company Overview & Business Concept -->
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <span class="text-primary fw-bold text-uppercase small">Who We Are</span>
            <h2 class="fw-bold mb-3">The Leading Destination for Genuine Consumer Electronics</h2>
            <p class="text-secondary leading-relaxed">
                Founded with a passion for high-performance computing and innovative electronics, <strong>TechTower</strong> bridges the gap between world-class technology brands and passionate tech enthusiasts.
            </p>
            <p class="text-secondary leading-relaxed">
                Our business concept revolves around three core tenets: <strong>Authenticity</strong>, <strong>Accessibility</strong>, and <strong>Reliability</strong>. Every product on our shelves is sourced directly from certified original manufacturers, backed by full brand warranties and dedicated post-purchase technical support.
            </p>
            <div class="row g-3 pt-2">
                <div class="col-6">
                    <div class="border rounded-4 p-3 bg-white text-center shadow-sm">
                        <h3 class="fw-extrabold text-primary mb-0">100%</h3>
                        <small class="text-muted fw-semibold">Genuine Products</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded-4 p-3 bg-white text-center shadow-sm">
                        <h3 class="fw-extrabold text-primary mb-0">50K+</h3>
                        <small class="text-muted fw-semibold">Delighted Customers</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <img src="https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=700&auto=format&fit=crop&q=80" alt="Tech Store" class="img-fluid rounded-4 shadow-lg">
        </div>
    </div>

    <!-- Mission & Vision Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fa fa-bullseye fs-3"></i>
                </div>
                <h4 class="fw-bold mb-3">Our Mission</h4>
                <p class="text-secondary leading-relaxed mb-0">
                    To democratize access to world-class personal computing, audio devices, and smart mobility electronics across India. We aim to empower students, creative professionals, and tech lovers by providing genuine tech at fair prices with transparent warranties.
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fa fa-eye fs-3"></i>
                </div>
                <h4 class="fw-bold mb-3">Our Vision</h4>
                <p class="text-secondary leading-relaxed mb-0">
                    To become the most dependable and customer-centric e-commerce tech ecosystem in the region, recognized for seamless digital shopping experiences, rapid dispatch networks, and dedicated post-sale guidance.
                </p>
            </div>
        </div>
    </div>

    <!-- Products & Services Offered -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-5">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase small">What We Deliver</span>
            <h2 class="fw-bold">Our Products & Services</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="p-3">
                    <i class="fa fa-mobile-screen-button text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold">Smartphones & Tablets</h5>
                    <p class="small text-muted">Flagship devices and durable accessories from Apple, Samsung, Google, and OnePlus.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-3">
                    <i class="fa fa-laptop-code text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold">Laptops & Workstations</h5>
                    <p class="small text-muted">High-end gaming laptops, lightweight ultrabooks, and enterprise desktop configurations.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-3">
                    <i class="fa fa-headphones-simple text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold">Audiophile & Wearables</h5>
                    <p class="small text-muted">Active noise-canceling headphones, sports wearables, and intelligent smartwatch series.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
