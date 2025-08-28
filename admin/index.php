<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();

$workModel = new WorkModel();
$blogModel = new BlogModel();
$messageModel = new MessageModel();
$userModel = new UserModel();

// Dashboard statistics
$totalWorks = count($workModel->getAll());
$totalBlogs = count($blogModel->getAll());
$totalMessages = count($messageModel->getAll());
$unreadMessages = $messageModel->getUnreadCount();
$totalUsers = count($userModel->getAll());

// Recent activities
$recentWorks = $workModel->getAll(null, 5);
$recentBlogs = $blogModel->getAll(null, 5);
$recentMessages = $messageModel->getAll(false);
$recentMessages = array_slice($recentMessages, 0, 5);

$pageTitle = 'Admin Dashboard - ' . getSiteSetting('site_title', 'GeoPortfolio');
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
    <!-- Admin Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-arrow-left me-2"></i><?php echo htmlspecialchars(getSiteSetting('site_title', 'GeoPortfolio')); ?>
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse admin-sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="works.php">
                                <i class="fas fa-briefcase"></i>Works
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="blog.php">
                                <i class="fas fa-blog"></i>Blog Posts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php">
                                <i class="fas fa-envelope"></i>Messages
                                <?php if ($unreadMessages > 0): ?>
                                    <span class="badge bg-danger ms-1"><?php echo $unreadMessages; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile-setup.php">
                                <i class="fas fa-user-edit"></i>Profile Setup
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="skills.php">
                                <i class="fas fa-cogs"></i>Skills
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="categories.php">
                                <i class="fas fa-tags"></i>Categories
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog"></i>Settings
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="admin-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">Dashboard</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <a href="../index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-eye me-1"></i>View Site
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Works</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalWorks; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Blog Posts</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalBlogs; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-blog fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Messages</div>
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-auto">
                                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $totalMessages; ?></div>
                                            </div>
                                            <?php if ($unreadMessages > 0): ?>
                                                <div class="col">
                                                    <small class="text-danger"><?php echo $unreadMessages; ?> unread</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsers; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <!-- Recent Works -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Works</h6>
                                <a href="works.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if ($recentWorks): ?>
                                    <?php foreach ($recentWorks as $work): ?>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-briefcase fa-lg text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($work['title']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($work['category']); ?> • <?php echo formatDate($work['created_at']); ?></small>
                                            </div>
                                            <a href="works.php?edit=<?php echo $work['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No works found. <a href="works.php">Add your first work</a>.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Messages</h6>
                                <a href="messages.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if ($recentMessages): ?>
                                    <?php foreach ($recentMessages as $msg): ?>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-envelope<?php echo $msg['is_read'] ? '-open' : ''; ?> fa-lg <?php echo $msg['is_read'] ? 'text-muted' : 'text-primary'; ?>"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($msg['name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($msg['email']); ?> • <?php echo formatDate($msg['created_at']); ?></small>
                                            </div>
                                            <a href="messages.php?view=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No messages yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Blog Posts -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Blog Posts</h6>
                                <a href="blog.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if ($recentBlogs): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Author</th>
                                                    <th>Published</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentBlogs as $blog): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($blog['title']); ?></td>
                                                        <td><span class="badge bg-info"><?php echo htmlspecialchars($blog['category']); ?></span></td>
                                                        <td><?php echo htmlspecialchars($blog['author']); ?></td>
                                                        <td><?php echo formatDate($blog['publish_date']); ?></td>
                                                        <td>
                                                            <a href="blog.php?edit=<?php echo $blog['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                            <a href="../blog-detail.php?id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No blog posts found. <a href="blog.php">Write your first post</a>.</p>
                                <?php endif; ?>
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