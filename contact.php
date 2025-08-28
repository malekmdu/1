<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/models.php';

$messageModel = new MessageModel();
$contactInfo = getContactInfo();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $institution = sanitize($_POST['institution'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Create message
        $messageData = [
            'name' => $name,
            'email' => $email,
            'institution' => $institution,
            'address' => $address,
            'message' => $message
        ];
        
        if ($messageModel->create($messageData)) {
            $success = 'Thank you for your message! I will get back to you soon.';
            
            // Log email notification (for admin)
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO email_log (recipient, subject, body, status) VALUES (?, ?, ?, 'sent')");
            $stmt->execute([
                $contactInfo['email'],
                'New Contact Form Submission',
                "New message from: $name ($email)\n\nMessage:\n$message"
            ]);
            
            // Clear form on success
            $name = $email = $institution = $address = $message = '';
        } else {
            $error = 'Sorry, there was an error sending your message. Please try again.';
        }
    }
}

$pageTitle = 'Contact - ' . getSiteSetting('site_title', 'GeoPortfolio');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'templates/navbar.php'; ?>

    <div class="container py-5" style="margin-top: 80px;">
        <!-- Page Header -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 mb-3">Contact Me</h1>
                <p class="lead text-muted">Let's discuss your next geospatial project</p>
            </div>
        </div>

        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h3 class="mb-0">Send a Message</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="institution" class="form-label">Institution/Organization</label>
                                    <input type="text" class="form-control" id="institution" name="institution" 
                                           value="<?php echo htmlspecialchars($institution ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="address" name="address" 
                                           value="<?php echo htmlspecialchars($address ?? ''); ?>"
                                           placeholder="City, Country">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="6" 
                                          placeholder="Tell me about your project, collaboration ideas, or any questions you have..."
                                          required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($contactInfo['email']): ?>
                            <div class="mb-3">
                                <h6><i class="fas fa-envelope text-primary me-2"></i>Email</h6>
                                <p class="mb-0">
                                    <a href="mailto:<?php echo htmlspecialchars($contactInfo['email']); ?>">
                                        <?php echo htmlspecialchars($contactInfo['email']); ?>
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($contactInfo['phone']): ?>
                            <div class="mb-3">
                                <h6><i class="fas fa-phone text-primary me-2"></i>Phone</h6>
                                <p class="mb-0">
                                    <a href="tel:<?php echo htmlspecialchars($contactInfo['phone']); ?>">
                                        <?php echo htmlspecialchars($contactInfo['phone']); ?>
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($contactInfo['address']): ?>
                            <div class="mb-3">
                                <h6><i class="fas fa-map-marker-alt text-primary me-2"></i>Location</h6>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($contactInfo['address'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Connect With Me</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <?php if (getSiteSetting('linkedin_url')): ?>
                                <a href="<?php echo getSiteSetting('linkedin_url'); ?>" 
                                   class="btn btn-outline-primary" target="_blank">
                                    <i class="fab fa-linkedin me-2"></i>LinkedIn
                                </a>
                            <?php endif; ?>
                            
                            <?php if (getSiteSetting('github_url')): ?>
                                <a href="<?php echo getSiteSetting('github_url'); ?>" 
                                   class="btn btn-outline-dark" target="_blank">
                                    <i class="fab fa-github me-2"></i>GitHub
                                </a>
                            <?php endif; ?>
                            
                            <?php if (getSiteSetting('twitter_url')): ?>
                                <a href="<?php echo getSiteSetting('twitter_url'); ?>" 
                                   class="btn btn-outline-info" target="_blank">
                                    <i class="fab fa-twitter me-2"></i>Twitter
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Response Time -->
                <div class="card shadow">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                        <h6>Response Time</h6>
                        <p class="text-muted mb-0">I typically respond within 24 hours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'templates/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>