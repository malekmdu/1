<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();
requireAdmin(); // Only admins can access settings

$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_settings') {
        $db = getDB();
        
        try {
            $settings = [
                'site_title' => sanitize($_POST['site_title']),
                'site_description' => sanitize($_POST['site_description']),
                'copyright_text' => sanitize($_POST['copyright_text']),
                'favicon_url' => sanitize($_POST['favicon_url']),
                'twitter_url' => sanitize($_POST['twitter_url']),
                'github_url' => sanitize($_POST['github_url']),
                'linkedin_url' => sanitize($_POST['linkedin_url']),
                'google_analytics_id' => sanitize($_POST['google_analytics_id']),
                'smtp_host' => sanitize($_POST['smtp_host']),
                'smtp_port' => sanitize($_POST['smtp_port']),
                'smtp_username' => sanitize($_POST['smtp_username']),
                'smtp_password' => sanitize($_POST['smtp_password']),
                'smtp_encryption' => sanitize($_POST['smtp_encryption'])
            ];
            
            foreach ($settings as $key => $value) {
                $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                $stmt->execute([$key, $value]);
            }
            
            $success = 'Settings updated successfully!';
        } catch (Exception $e) {
            $error = 'Failed to update settings: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Site Settings - Admin';
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
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'templates/admin-navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'templates/admin-sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="admin-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">Site Settings</h1>
                    </div>
                </div>

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

                <!-- Settings Form -->
                <div class="card shadow">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                    <i class="fas fa-cog me-1"></i>General
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                                    <i class="fas fa-share-alt me-1"></i>Social Media
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                                    <i class="fas fa-envelope me-1"></i>Email Settings
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_settings">
                            
                            <div class="tab-content" id="settingsTabsContent">
                                <!-- General Settings Tab -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="site_title" class="form-label">Site Title *</label>
                                        <input type="text" class="form-control" id="site_title" name="site_title" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('site_title', 'GeoPortfolio')); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="site_description" class="form-label">Site Description</label>
                                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars(getSiteSetting('site_description', '')); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="copyright_text" class="form-label">Copyright Text</label>
                                        <input type="text" class="form-control" id="copyright_text" name="copyright_text" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('copyright_text', 'GeoPortfolio. All rights reserved.')); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="favicon_url" class="form-label">Favicon URL</label>
                                        <input type="url" class="form-control" id="favicon_url" name="favicon_url" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('favicon_url', '')); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                                        <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('google_analytics_id', '')); ?>"
                                               placeholder="G-XXXXXXXXXX">
                                    </div>
                                </div>

                                <!-- Social Media Tab -->
                                <div class="tab-pane fade" id="social" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="twitter_url" class="form-label">Twitter/X Profile URL</label>
                                        <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('twitter_url', '')); ?>"
                                               placeholder="https://twitter.com/username">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="linkedin_url" class="form-label">LinkedIn Profile URL</label>
                                        <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('linkedin_url', '')); ?>"
                                               placeholder="https://linkedin.com/in/username">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="github_url" class="form-label">GitHub Profile URL</label>
                                        <input type="url" class="form-control" id="github_url" name="github_url" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('github_url', '')); ?>"
                                               placeholder="https://github.com/username">
                                    </div>
                                </div>

                                <!-- Email Settings Tab -->
                                <div class="tab-pane fade" id="email" role="tabpanel">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Configure SMTP settings for sending emails (contact form notifications, etc.)
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="smtp_host" class="form-label">SMTP Host</label>
                                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                                   value="<?php echo htmlspecialchars(getSiteSetting('smtp_host', '')); ?>"
                                                   placeholder="smtp.gmail.com">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="smtp_port" class="form-label">SMTP Port</label>
                                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                                   value="<?php echo htmlspecialchars(getSiteSetting('smtp_port', '587')); ?>"
                                                   placeholder="587">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="smtp_username" class="form-label">SMTP Username</label>
                                        <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('smtp_username', '')); ?>"
                                               placeholder="your-email@gmail.com">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="smtp_password" class="form-label">SMTP Password</label>
                                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                               value="<?php echo htmlspecialchars(getSiteSetting('smtp_password', '')); ?>"
                                               placeholder="Your app password">
                                        <small class="text-muted">For Gmail, use an App Password instead of your regular password</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="smtp_encryption" class="form-label">Encryption</label>
                                        <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                            <option value="tls" <?php echo getSiteSetting('smtp_encryption', 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            <option value="ssl" <?php echo getSiteSetting('smtp_encryption', 'tls') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Save All Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>