<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/models.php';

$blogModel = new BlogModel();
$blogId = intval($_GET['id'] ?? 0);

if (!$blogId) {
    redirect('blog.php', 'Blog post not found.', 'error');
}

$post = $blogModel->getById($blogId);

if (!$post) {
    redirect('blog.php', 'Blog post not found.', 'error');
}

$pageTitle = htmlspecialchars($post['title']) . ' - ' . getSiteSetting('site_title', 'GeoPortfolio');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(truncateText($post['summary'], 160)); ?>">
    
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
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="blog.php">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($post['title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <article>
                    <!-- Post Header -->
                    <header class="mb-4">
                        <h1 class="display-5 mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
                        
                        <!-- Post Meta -->
                        <div class="mb-4">
                            <div class="d-flex flex-wrap align-items-center text-muted mb-2">
                                <span class="me-3">
                                    <i class="fas fa-user me-1"></i>
                                    By <?php echo htmlspecialchars($post['author']); ?>
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?php echo formatDate($post['publish_date']); ?>
                                </span>
                                <?php if ($post['place']): ?>
                                    <span class="me-3">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($post['place']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-info"><?php echo htmlspecialchars($post['category']); ?></span>
                        </div>
                    </header>

                    <!-- Featured Image -->
                    <?php if ($post['image_url']): ?>
                        <div class="mb-4">
                            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                                 class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($post['title']); ?>"
                                 style="width: 100%; height: 400px; object-fit: <?php echo $post['image_style'] ?? 'cover'; ?>;">
                        </div>
                    <?php endif; ?>

                    <!-- Post Summary -->
                    <div class="mb-4">
                        <p class="lead"><?php echo htmlspecialchars($post['summary']); ?></p>
                    </div>

                    <!-- Post Content -->
                    <div class="content">
                        <?php echo $post['content']; ?>
                    </div>

                    <!-- Post Footer -->
                    <footer class="mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Published by <?php echo htmlspecialchars($post['author']); ?></h6>
                                <small class="text-muted">
                                    <?php echo formatDate($post['publish_date'], 'F j, Y'); ?>
                                    <?php if ($post['updated_at'] !== $post['created_at']): ?>
                                        • Updated <?php echo formatDate($post['updated_at'], 'F j, Y'); ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div>
                                <a href="contact.php" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-2"></i>Contact Author
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            </div>

            <div class="col-lg-4">
                <!-- Article Info Sidebar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Article Info</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-5">Category:</dt>
                            <dd class="col-7"><?php echo htmlspecialchars($post['category']); ?></dd>
                            
                            <dt class="col-5">Author:</dt>
                            <dd class="col-7"><?php echo htmlspecialchars($post['author']); ?></dd>
                            
                            <dt class="col-5">Published:</dt>
                            <dd class="col-7"><?php echo formatDate($post['publish_date']); ?></dd>
                            
                            <?php if ($post['place']): ?>
                                <dt class="col-5">Location:</dt>
                                <dd class="col-7"><?php echo htmlspecialchars($post['place']); ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Navigation</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="blog.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Blog
                            </a>
                            <a href="contact.php" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>Discuss This Post
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                <?php
                $relatedPosts = $blogModel->getAll($post['category'], 4);
                $relatedPosts = array_filter($relatedPosts, function($p) use ($blogId) {
                    return $p['id'] != $blogId;
                });
                $relatedPosts = array_slice($relatedPosts, 0, 3);
                ?>
                <?php if ($relatedPosts): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Related Posts</h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($relatedPosts as $related): ?>
                                <div class="mb-3">
                                    <h6 class="mb-1">
                                        <a href="blog-detail.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($related['title']); ?>
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-1"><?php echo truncateText($related['summary'], 80); ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?php echo formatDate($related['publish_date']); ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Categories -->
                <?php
                $categoryModel = new CategoryModel();
                $blogCategories = $categoryModel->getAll('blog');
                ?>
                <?php if ($blogCategories): ?>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Categories</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($blogCategories as $category): ?>
                                    <a href="blog.php?category=<?php echo urlencode($category['name']); ?>" 
                                       class="badge bg-light text-dark text-decoration-none">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'templates/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>