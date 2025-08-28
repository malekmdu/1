<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();

$success = '';
$error = '';

// Get current profile data
$profileData = getProfileData();
$contactInfo = getContactInfo();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['section'])) {
        $db = getDB();
        
        switch ($_POST['section']) {
            case 'profile':
                try {
                    $stmt = $db->prepare("INSERT INTO profile_data (name, title, bio, summary, avatar_url, resume_url, expertise_title, expertise_description) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                                         ON DUPLICATE KEY UPDATE 
                                         name = VALUES(name), title = VALUES(title), bio = VALUES(bio), 
                                         summary = VALUES(summary), avatar_url = VALUES(avatar_url), 
                                         resume_url = VALUES(resume_url), expertise_title = VALUES(expertise_title), 
                                         expertise_description = VALUES(expertise_description)");
                    
                    $stmt->execute([
                        sanitize($_POST['name']),
                        sanitize($_POST['title']),
                        sanitize($_POST['bio']),
                        sanitize($_POST['summary']),
                        sanitize($_POST['avatar_url']),
                        sanitize($_POST['resume_url']),
                        sanitize($_POST['expertise_title']),
                        sanitize($_POST['expertise_description'])
                    ]);
                    
                    $success = 'Profile updated successfully!';
                } catch (Exception $e) {
                    $error = 'Failed to update profile: ' . $e->getMessage();
                }
                break;
                
            case 'contact':
                try {
                    $stmt = $db->prepare("INSERT INTO contact_info (email, phone, address) 
                                         VALUES (?, ?, ?) 
                                         ON DUPLICATE KEY UPDATE 
                                         email = VALUES(email), phone = VALUES(phone), address = VALUES(address)");
                    
                    $stmt->execute([
                        sanitize($_POST['email']),
                        sanitize($_POST['phone']),
                        sanitize($_POST['address'])
                    ]);
                    
                    $success = 'Contact information updated successfully!';
                } catch (Exception $e) {
                    $error = 'Failed to update contact info: ' . $e->getMessage();
                }
                break;
        }
    }
    
    // Refresh data after update
    $profileData = getProfileData();
    $contactInfo = getContactInfo();
}

$pageTitle = 'Profile Setup - Admin';
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
                        <h1 class="h2">Profile Setup</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <a href="../about.php" class="btn btn-outline-secondary" target="_blank">
                                <i class="fas fa-eye me-1"></i>Preview About Page
                            </a>
                        </div>
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

                <!-- Profile Tabs -->
                <div class="card shadow">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                                    <i class="fas fa-user me-1"></i>Basic Profile
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                                    <i class="fas fa-envelope me-1"></i>Contact Info
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="profileTabsContent">
                            <!-- Basic Profile Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                                <form method="POST" action="">
                                    <input type="hidden" name="section" value="profile">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo $profileData ? htmlspecialchars($profileData['name']) : ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="form-label">Professional Title *</label>
                                            <input type="text" class="form-control" id="title" name="title" 
                                                   value="<?php echo $profileData ? htmlspecialchars($profileData['title']) : ''; ?>" 
                                                   placeholder="e.g., GIS Analyst, Remote Sensing Specialist" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="summary" class="form-label">Professional Summary</label>
                                        <textarea class="form-control" id="summary" name="summary" rows="3"
                                                  placeholder="Brief summary for the homepage hero section"><?php echo $profileData ? htmlspecialchars($profileData['summary']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Full Biography</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="6"
                                                  placeholder="Detailed biography for the about page"><?php echo $profileData ? htmlspecialchars($profileData['bio']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="avatar_url" class="form-label">Profile Photo URL</label>
                                            <input type="url" class="form-control" id="avatar_url" name="avatar_url" 
                                                   value="<?php echo $profileData ? htmlspecialchars($profileData['avatar_url']) : ''; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="resume_url" class="form-label">Resume/CV URL</label>
                                            <input type="url" class="form-control" id="resume_url" name="resume_url" 
                                                   value="<?php echo $profileData ? htmlspecialchars($profileData['resume_url']) : ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="expertise_title" class="form-label">Expertise Section Title</label>
                                        <input type="text" class="form-control" id="expertise_title" name="expertise_title" 
                                               value="<?php echo $profileData ? htmlspecialchars($profileData['expertise_title']) : 'My Expertise'; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="expertise_description" class="form-label">Expertise Description</label>
                                        <textarea class="form-control" id="expertise_description" name="expertise_description" rows="2"><?php echo $profileData ? htmlspecialchars($profileData['expertise_description']) : ''; ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Save Profile
                                    </button>
                                </form>
                            </div>

                            <!-- Contact Info Tab -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <form method="POST" action="">
                                    <input type="hidden" name="section" value="contact">
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo $contactInfo ? htmlspecialchars($contactInfo['email']) : ''; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo $contactInfo ? htmlspecialchars($contactInfo['phone']) : ''; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address/Location</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo $contactInfo ? htmlspecialchars($contactInfo['address']) : ''; ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Save Contact Info
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>