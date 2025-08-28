<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/models.php';

$profileData = getProfileData();
$skillModel = new SkillModel();
$skills = $skillModel->getAllWithItems();

$pageTitle = 'About - ' . getSiteSetting('site_title', 'GeoPortfolio');
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
        <?php if ($profileData): ?>
            <!-- Profile Header -->
            <div class="row mb-5">
                <div class="col-lg-4 text-center">
                    <?php if ($profileData['avatar_url']): ?>
                        <img src="<?php echo htmlspecialchars($profileData['avatar_url']); ?>" 
                             alt="Profile" class="img-fluid rounded-circle shadow mb-4" style="max-width: 300px;">
                    <?php endif; ?>
                </div>
                <div class="col-lg-8">
                    <h1 class="display-4 mb-3"><?php echo htmlspecialchars($profileData['name']); ?></h1>
                    <h2 class="h4 text-primary mb-4"><?php echo htmlspecialchars($profileData['title']); ?></h2>
                    <p class="lead"><?php echo nl2br(htmlspecialchars($profileData['bio'])); ?></p>
                    
                    <?php if ($profileData['resume_url'] && $profileData['resume_url'] !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($profileData['resume_url']); ?>" 
                           class="btn btn-primary btn-lg" target="_blank">
                            <i class="fas fa-download me-2"></i>Download Resume
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- What I Do Section -->
            <?php
            $db = getDB();
            $whatIDo = $db->query("SELECT * FROM what_i_do ORDER BY sort_order, id")->fetchAll();
            ?>
            <?php if ($whatIDo): ?>
                <section class="mb-5">
                    <h2 class="text-center mb-5"><?php echo htmlspecialchars($profileData['expertise_title'] ?? 'My Expertise'); ?></h2>
                    <?php if ($profileData['expertise_description']): ?>
                        <p class="text-center text-muted mb-5"><?php echo htmlspecialchars($profileData['expertise_description']); ?></p>
                    <?php endif; ?>
                    
                    <div class="row">
                        <?php foreach ($whatIDo as $item): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-cogs fa-3x text-primary mb-3"></i>
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Skills Section -->
            <?php if ($skills): ?>
                <section class="mb-5">
                    <h2 class="text-center mb-5">Technical Skills</h2>
                    <div class="row">
                        <?php foreach ($skills as $skillCategory): ?>
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><?php echo htmlspecialchars($skillCategory['category']); ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <?php foreach ($skillCategory['items'] as $skill): ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><?php echo htmlspecialchars($skill['name']); ?></span>
                                                    <span><?php echo $skill['percentage']; ?>%</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?php echo $skill['percentage']; ?>%"
                                                         aria-valuenow="<?php echo $skill['percentage']; ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Experience & Education -->
            <div class="row">
                <!-- Experience -->
                <div class="col-lg-6 mb-4">
                    <h3>Professional Experience</h3>
                    <?php
                    $experience = $db->query("SELECT * FROM experience ORDER BY sort_order, id")->fetchAll();
                    ?>
                    <?php foreach ($experience as $exp): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($exp['role']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($exp['company']); ?></h6>
                                <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($exp['period']); ?></small></p>
                                <?php if ($exp['description']): ?>
                                    <p class="card-text"><?php echo htmlspecialchars($exp['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Education -->
                <div class="col-lg-6 mb-4">
                    <h3>Education</h3>
                    <?php
                    $education = $db->query("SELECT * FROM education ORDER BY sort_order, id")->fetchAll();
                    ?>
                    <?php foreach ($education as $edu): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($edu['degree']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($edu['institution']); ?></h6>
                                <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($edu['period']); ?></small></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Certifications & Training -->
            <div class="row">
                <!-- Certifications -->
                <div class="col-lg-6 mb-4">
                    <h3>Certifications</h3>
                    <?php
                    $certifications = $db->query("SELECT * FROM certifications ORDER BY sort_order, id")->fetchAll();
                    ?>
                    <?php foreach ($certifications as $cert): ?>
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($cert['name']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($cert['issuer']); ?></small>
                                    </div>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($cert['date']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Training -->
                <div class="col-lg-6 mb-4">
                    <h3>Professional Training</h3>
                    <?php
                    $training = $db->query("SELECT * FROM training ORDER BY sort_order, id")->fetchAll();
                    ?>
                    <?php foreach ($training as $train): ?>
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($train['name']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($train['institution']); ?></small>
                                    </div>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($train['year']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Core Competencies & Memberships -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <h3>Core Competencies</h3>
                    <?php
                    $competencies = $db->query("SELECT * FROM core_competencies ORDER BY sort_order, id")->fetchAll();
                    ?>
                    <div class="d-flex flex-wrap">
                        <?php foreach ($competencies as $comp): ?>
                            <span class="badge bg-info me-2 mb-2 p-2"><?php echo htmlspecialchars($comp['name']); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <h3>Professional Memberships</h3>
                    <?php
                    $memberships = $db->query("SELECT * FROM memberships ORDER BY sort_order, id")->fetchAll();
                    ?>
                    <?php foreach ($memberships as $member): ?>
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($member['name']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($member['period']); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <h1>About</h1>
                <p class="lead">Profile information will be displayed here once configured.</p>
                <?php if (isLoggedIn()): ?>
                    <a href="admin/" class="btn btn-primary">Configure Profile</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'templates/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>