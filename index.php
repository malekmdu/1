<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/models.php';

$profileData = getProfileData();
$contactInfo = getContactInfo();
$workModel = new WorkModel();
$blogModel = new BlogModel();
$categoryModel = new CategoryModel();

// Get featured works and recent blog posts
$featuredWorks = $workModel->getAll(null, 6);
$recentBlogs = $blogModel->getAll(null, 3);
$workCategories = $categoryModel->getAll('work');
$blogCategories = $categoryModel->getAll('blog');

$pageTitle = getSiteSetting('site_title', 'GeoPortfolio');
$pageDescription = getSiteSetting('site_description', 'A modern portfolio for GIS & Remote Sensing professionals');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="icon" href="<?php echo getSiteSetting('favicon_url', 'assets/images/favicon.ico'); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><?php echo htmlspecialchars($pageTitle); ?></a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="works.php">Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/">Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <?php if ($profileData): ?>
                            <h1 class="display-4 fw-bold mb-4"><?php echo htmlspecialchars($profileData['name']); ?></h1>
                            <h2 class="h4 mb-4 text-light"><?php echo htmlspecialchars($profileData['title']); ?></h2>
                            <p class="lead mb-4"><?php echo htmlspecialchars($profileData['summary']); ?></p>
                        <?php else: ?>
                            <h1 class="display-4 fw-bold mb-4">Welcome to GeoPortfolio</h1>
                            <h2 class="h4 mb-4 text-light">GIS & Remote Sensing Professional</h2>
                            <p class="lead mb-4">Discover innovative geospatial solutions and cutting-edge research projects.</p>
                        <?php endif; ?>
                        
                        <div class="hero-buttons">
                            <a href="works.php" class="btn btn-light btn-lg me-3">View Works</a>
                            <a href="contact.php" class="btn btn-outline-light btn-lg">Get in Touch</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image text-center">
                        <?php if ($profileData && $profileData['avatar_url']): ?>
                            <img src="<?php echo htmlspecialchars($profileData['avatar_url']); ?>" 
                                 alt="Profile" class="img-fluid rounded-circle shadow-lg" style="max-width: 400px;">
                        <?php else: ?>
                            <div class="placeholder-avatar bg-light rounded-circle mx-auto shadow-lg d-flex align-items-center justify-content-center" 
                                 style="width: 400px; height: 400px;">
                                <i class="fas fa-user fa-5x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Works Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-5">Featured Works</h2>
                </div>
            </div>
            <div class="row">
                <?php foreach ($featuredWorks as $work): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($work['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($work['image_url']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($work['title']); ?>"
                                     style="height: 200px; object-fit: <?php echo $work['image_style'] ?? 'cover'; ?>;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($work['title']); ?></h5>
                                <p class="card-text"><?php echo truncateText($work['description'], 100); ?></p>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($work['category']); ?></span>
                                <?php if ($work['place']): ?>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($work['place']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <a href="work-detail.php?id=<?php echo $work['id']; ?>" class="btn btn-outline-primary">Learn More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="works.php" class="btn btn-primary">View All Works</a>
            </div>
        </div>
    </section>

    <!-- Recent Blog Posts Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-5">Recent Blog Posts</h2>
                </div>
            </div>
            <div class="row">
                <?php foreach ($recentBlogs as $blog): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($blog['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                     style="height: 200px; object-fit: <?php echo $blog['image_style'] ?? 'cover'; ?>;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                <p class="card-text"><?php echo truncateText($blog['summary'], 100); ?></p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        By <?php echo htmlspecialchars($blog['author']); ?> • 
                                        <?php echo formatDate($blog['publish_date']); ?>
                                    </small>
                                </p>
                                <span class="badge bg-info"><?php echo htmlspecialchars($blog['category']); ?></span>
                            </div>
                            <div class="card-footer">
                                <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" class="btn btn-outline-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="blog.php" class="btn btn-primary">View All Posts</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo getSiteSetting('copyright_text', 'GeoPortfolio. All rights reserved.'); ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-links">
                        <?php if (getSiteSetting('twitter_url')): ?>
                            <a href="<?php echo getSiteSetting('twitter_url'); ?>" class="text-white me-3">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (getSiteSetting('github_url')): ?>
                            <a href="<?php echo getSiteSetting('github_url'); ?>" class="text-white me-3">
                                <i class="fab fa-github"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (getSiteSetting('linkedin_url')): ?>
                            <a href="<?php echo getSiteSetting('linkedin_url'); ?>" class="text-white me-3">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>