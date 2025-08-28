<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/models.php';

$blogModel = new BlogModel();
$categoryModel = new CategoryModel();

// Get filters
$selectedCategory = sanitize($_GET['category'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 9;

// Get blog posts with pagination
$query = "SELECT * FROM blog_posts WHERE 1=1";
$params = [];

if ($selectedCategory) {
    $query .= " AND category = ?";
    $params[] = $selectedCategory;
}

$query .= " ORDER BY publish_date DESC, created_at DESC";

$result = paginate($query, $params, $page, $perPage);
$blogPosts = $result['data'];
$pagination = $result['pagination'];

$categories = $categoryModel->getAll('blog');
$pageTitle = 'Blog - ' . getSiteSetting('site_title', 'GeoPortfolio');
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
                <h1 class="display-4 mb-3">Blog</h1>
                <p class="lead text-muted">Insights, tutorials, and thoughts on GIS and Remote Sensing</p>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="blog.php" class="btn <?php echo !$selectedCategory ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        All Posts
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="blog.php?category=<?php echo urlencode($category['name']); ?>" 
                           class="btn <?php echo $selectedCategory === $category['name'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Blog Posts Grid -->
        <div class="row">
            <?php if (empty($blogPosts)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-blog fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">No blog posts found</h3>
                    <p class="text-muted">
                        <?php if ($selectedCategory): ?>
                            No posts found in the "<?php echo htmlspecialchars($selectedCategory); ?>" category.
                        <?php else: ?>
                            No blog posts have been published yet.
                        <?php endif; ?>
                    </p>
                    <?php if (isLoggedIn()): ?>
                        <a href="admin/blog.php" class="btn btn-primary">Write New Post</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($blogPosts as $post): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <article class="card h-100 shadow-sm">
                            <?php if ($post['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     style="height: 250px; object-fit: <?php echo $post['image_style'] ?? 'cover'; ?>;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="blog-detail.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h5>
                                <p class="card-text"><?php echo truncateText($post['summary'], 120); ?></p>
                                
                                <div class="mb-2">
                                    <span class="badge bg-info"><?php echo htmlspecialchars($post['category']); ?></span>
                                    <?php if ($post['place']): ?>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($post['place']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        <?php echo htmlspecialchars($post['author']); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?php echo formatDate($post['publish_date']); ?>
                                    </small>
                                </div>
                                <div class="mt-2">
                                    <a href="blog-detail.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        Read More <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Blog pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['has_prev']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?><?php echo $selectedCategory ? '&category=' . urlencode($selectedCategory) : ''; ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $selectedCategory ? '&category=' . urlencode($selectedCategory) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?><?php echo $selectedCategory ? '&category=' . urlencode($selectedCategory) : ''; ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'templates/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>