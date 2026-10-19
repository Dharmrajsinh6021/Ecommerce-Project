<?php
require_once __DIR__ . '/config/db.php';

$message = '';
$message_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = clean($_POST['name'] ?? '');
    $email   = clean($_POST['email'] ?? '');
    $phone   = clean($_POST['phone'] ?? '');
    $subject = clean($_POST['subject'] ?? '');
    $user_msg= clean($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($user_msg)) {
        $message = "Please fill in all required fields (Name, Email, Message).";
        $message_type = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $user_msg]);
            $message = "Thank you! Your message has been sent successfully. Our support desk will reach out shortly.";
            $message_type = 'success';
        } catch (Exception $e) {
            $message = "Failed to submit message: " . $e->getMessage();
            $message_type = 'danger';
        }
    }
}

$page_title = "Contact Us";
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Get in Touch With Us</h1>
        <p class="mb-0 text-white-50">Have questions about an order or product specs? We are here to help!</p>
    </div>
</div>

<div class="container mb-5">
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show rounded-4" role="alert">
            <i class="fa fa-info-circle me-2"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Contact Information Cards -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Contact Details</h5>

                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                        <i class="fa fa-location-dot fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Store Location</h6>
                        <p class="text-muted small mb-0">
                            TechTower ,<br>
                            SG Highway, Ahmedabad, Gujarat - 3****0, India
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                        <i class="fa fa-phone fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Customer Care Helpline</h6>
                        <p class="text-muted small mb-0">
                            +91 98123 45678 / +91 76543 21000<br>
                            Toll Free: 1800 *** ****
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-info-subtle text-info p-3 me-3">
                        <i class="fa fa-envelope fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Email Inquiries</h6>
                        <p class="text-muted small mb-0">
                            support@techtower.com<br>
                            sales@techtower.com
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3">
                        <i class="fa fa-clock fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Store Hours</h6>
                        <p class="text-muted small mb-0">
                            Monday - Saturday: 9:30 AM - 8:30 PM<br>
                            Sunday: 10:00 AM - 6:00 PM
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <h5 class="fw-bold mb-2">Send Us a Direct Message</h5>
                <p class="text-muted small mb-4">Fill out the form below and our customer relationship team will respond within 24 hours.</p>

                <form method="POST" action="contact.php">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Order Tracking, Warranty Query">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Message Content <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Write your question, complaint, or feedback here..." required></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                                <i class="fa fa-paper-plane me-2"></i> Submit Inquiry
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
