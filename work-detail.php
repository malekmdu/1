<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/models.php';

$workModel = new WorkModel();
$workId = intval($_GET['id'] ?? 0);

if (!$workId) {
    redirect('works.php', 'Work not found.', 'error');
}

$work = $workModel->getById($workId);

if (!$work) {
    redirect('works.php', 'Work not found.', 'error');
}

$pageTitle = htmlspecialchars($work['title']) . ' - ' . getSiteSetting('site_title', 'GeoPortfolio');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(truncateText($work['description'], 160)); ?>">
    
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
                <li class="breadcrumb-item"><a href="works.php">Works</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($work['title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <!-- Work Image -->
                <?php if ($work['image_url']): ?>
                    <div class="mb-4">
                        <img src="<?php echo htmlspecialchars($work['image_url']); ?>" 
                             class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($work['title']); ?>"
                             style="width: 100%; height: 400px; object-fit: <?php echo $work['image_style'] ?? 'cover'; ?>;">
                    </div>
                <?php endif; ?>

                <!-- Work Title -->
                <h1 class="display-5 mb-3"><?php echo htmlspecialchars($work['title']); ?></h1>

                <!-- Work Meta -->
                <div class="mb-4">
                    <span class="badge bg-primary me-2"><?php echo htmlspecialchars($work['category']); ?></span>
                    <?php if ($work['place']): ?>
                        <span class="badge bg-secondary me-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?php echo htmlspecialchars($work['place']); ?>
                        </span>
                    <?php endif; ?>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?php echo formatDate($work['created_at']); ?>
                    </span>
                </div>

                <!-- Work Description -->
                <div class="mb-4">
                    <h3>Project Overview</h3>
                    <p class="lead"><?php echo htmlspecialchars($work['description']); ?></p>
                </div>

                <!-- Work Long Description -->
                <?php if ($work['long_description']): ?>
                    <div class="mb-4">
                        <h3>Detailed Description</h3>
                        <div class="content">
                            <?php echo $work['long_description']; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tags -->
                <?php if ($work['tags']): ?>
                    <div class="mb-4">
                        <h5>Technologies & Methods</h5>
                        <div class="tags">
                            <?php foreach ($work['tags'] as $tag): ?>
                                <span class="badge bg-info me-2 mb-2 p-2"><?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Project Link -->
                <?php if ($work['link'] && $work['link'] !== '#' && $work['link'] !== '#/works'): ?>
                    <div class="mb-4">
                        <a href="<?php echo htmlspecialchars($work['link']); ?>" class="btn btn-primary btn-lg" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>View Project
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <!-- Project Details Sidebar -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Project Details</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-5">Category:</dt>
                            <dd class="col-7"><?php echo htmlspecialchars($work['category']); ?></dd>
                            
                            <?php if ($work['place']): ?>
                                <dt class="col-5">Location:</dt>
                                <dd class="col-7"><?php echo htmlspecialchars($work['place']); ?></dd>
                            <?php endif; ?>
                            
                            <dt class="col-5">Created:</dt>
                            <dd class="col-7"><?php echo formatDate($work['created_at']); ?></dd>
                            
                            <dt class="col-5">Last Updated:</dt>
                            <dd class="col-7"><?php echo formatDate($work['updated_at']); ?></dd>
                        </dl>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Navigation</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="works.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Works
                            </a>
                            <a href="contact.php" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>Discuss This Project
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Works -->
                <?php
                $relatedWorks = $workModel->getAll($work['category'], 4);
                $relatedWorks = array_filter($relatedWorks, function($w) use ($workId) {
                    return $w['id'] != $workId;
                });
                $relatedWorks = array_slice($relatedWorks, 0, 3);
                ?>
                <?php if ($relatedWorks): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Related Works</h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($relatedWorks as $related): ?>
                                <div class="mb-3">
                                    <h6 class="mb-1">
                                        <a href="work-detail.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($related['title']); ?>
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-0"><?php echo truncateText($related['description'], 80); ?></p>
                                </div>
                            <?php endforeach; ?>
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