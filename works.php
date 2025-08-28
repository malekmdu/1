<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/models.php';

$workModel = new WorkModel();
$categoryModel = new CategoryModel();

// Get filters
$selectedCategory = sanitize($_GET['category'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;

// Get works with pagination
$query = "SELECT * FROM works WHERE 1=1";
$params = [];

if ($selectedCategory) {
    $query .= " AND category = ?";
    $params[] = $selectedCategory;
}

$query .= " ORDER BY created_at DESC";

$result = paginate($query, $params, $page, $perPage);
$works = $result['data'];
$pagination = $result['pagination'];

$categories = $categoryModel->getAll('work');
$pageTitle = 'Works - ' . getSiteSetting('site_title', 'GeoPortfolio');
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
                <h1 class="display-4 mb-3">My Works</h1>
                <p class="lead text-muted">Explore my portfolio of GIS and Remote Sensing projects</p>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="works.php" class="btn <?php echo !$selectedCategory ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        All Works
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="works.php?category=<?php echo urlencode($category['name']); ?>" 
                           class="btn <?php echo $selectedCategory === $category['name'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Works Grid -->
        <div class="row">
            <?php if (empty($works)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">No works found</h3>
                    <p class="text-muted">
                        <?php if ($selectedCategory): ?>
                            No works found in the "<?php echo htmlspecialchars($selectedCategory); ?>" category.
                        <?php else: ?>
                            No works have been added yet.
                        <?php endif; ?>
                    </p>
                    <?php if (isLoggedIn()): ?>
                        <a href="admin/works.php" class="btn btn-primary">Add New Work</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($works as $work): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($work['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($work['image_url']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($work['title']); ?>"
                                     style="height: 250px; object-fit: <?php echo $work['image_style'] ?? 'cover'; ?>;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($work['title']); ?></h5>
                                <p class="card-text"><?php echo truncateText($work['description'], 120); ?></p>
                                
                                <div class="mb-2">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($work['category']); ?></span>
                                    <?php if ($work['place']): ?>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($work['place']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($work['tags']): ?>
                                    <div class="mb-2">
                                        <?php
                                        $tags = json_decode($work['tags'], true);
                                        if ($tags):
                                        ?>
                                            <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                                                <span class="badge bg-light text-dark me-1"><?php echo htmlspecialchars($tag); ?></span>
                                            <?php endforeach; ?>
                                            <?php if (count($tags) > 3): ?>
                                                <span class="text-muted small">+<?php echo count($tags) - 3; ?> more</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?php echo formatDate($work['created_at']); ?>
                                    </small>
                                    <a href="work-detail.php?id=<?php echo $work['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        Learn More <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Works pagination">
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