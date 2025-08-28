<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();

$blogModel = new BlogModel();
$categoryModel = new CategoryModel();

$success = '';
$error = '';
$editBlog = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
            case 'update':
                $data = [
                    'title' => sanitize($_POST['title']),
                    'summary' => sanitize($_POST['summary']),
                    'content' => $_POST['content'], // Allow HTML
                    'image_url' => sanitize($_POST['image_url']),
                    'category' => sanitize($_POST['category']),
                    'author' => sanitize($_POST['author']),
                    'publish_date' => sanitize($_POST['publish_date']),
                    'image_style' => sanitize($_POST['image_style']),
                    'place' => sanitize($_POST['place'])
                ];
                
                if ($_POST['action'] === 'create') {
                    if ($blogModel->create($data)) {
                        $success = 'Blog post created successfully!';
                    } else {
                        $error = 'Failed to create blog post.';
                    }
                } else {
                    $id = intval($_POST['blog_id']);
                    if ($blogModel->update($id, $data)) {
                        $success = 'Blog post updated successfully!';
                    } else {
                        $error = 'Failed to update blog post.';
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['blog_id']);
                if ($blogModel->delete($id)) {
                    $success = 'Blog post deleted successfully!';
                } else {
                    $error = 'Failed to delete blog post.';
                }
                break;
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editBlog = $blogModel->getById($editId);
}

$blogs = $blogModel->getAll();
$categories = $categoryModel->getAll('blog');
$pageTitle = 'Blog Management - Admin';
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
                        <h1 class="h2">Blog Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#blogModal">
                                <i class="fas fa-plus me-1"></i>Add New Post
                            </button>
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

                <!-- Blog Posts Table -->
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">All Blog Posts</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($blogs): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Author</th>
                                            <th>Published</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($blogs as $blog): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($blog['image_url']): ?>
                                                        <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" 
                                                             alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($blog['title']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo truncateText($blog['summary'], 50); ?></small>
                                                </td>
                                                <td><span class="badge bg-info"><?php echo htmlspecialchars($blog['category']); ?></span></td>
                                                <td><?php echo htmlspecialchars($blog['author']); ?></td>
                                                <td><?php echo formatDate($blog['publish_date']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="../blog-detail.php?id=<?php echo $blog['id']; ?>" 
                                                           class="btn btn-outline-info" target="_blank" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="?edit=<?php echo $blog['id']; ?>" 
                                                           class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteBlog(<?php echo $blog['id']; ?>)" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No blog posts found</h5>
                                <p class="text-muted">Write your first blog post to get started.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Blog Modal -->
    <div class="modal fade" id="blogModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $editBlog ? 'Edit Blog Post' : 'Add New Blog Post'; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editBlog ? 'update' : 'create'; ?>">
                        <?php if ($editBlog): ?>
                            <input type="hidden" name="blog_id" value="<?php echo $editBlog['id']; ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo $editBlog ? htmlspecialchars($editBlog['title']) : ''; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat['name']); ?>"
                                                <?php echo ($editBlog && $editBlog['category'] === $cat['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="summary" class="form-label">Summary *</label>
                            <textarea class="form-control" id="summary" name="summary" rows="2" required><?php echo $editBlog ? htmlspecialchars($editBlog['summary']) : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content *</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required><?php echo $editBlog ? htmlspecialchars($editBlog['content']) : ''; ?></textarea>
                            <small class="text-muted">HTML tags are allowed</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="author" class="form-label">Author *</label>
                                <input type="text" class="form-control" id="author" name="author" 
                                       value="<?php echo $editBlog ? htmlspecialchars($editBlog['author']) : htmlspecialchars($_SESSION['user_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="publish_date" class="form-label">Publish Date *</label>
                                <input type="date" class="form-control" id="publish_date" name="publish_date" 
                                       value="<?php echo $editBlog ? date('Y-m-d', strtotime($editBlog['publish_date'])) : date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="image_url" class="form-label">Featured Image URL</label>
                                <input type="url" class="form-control" id="image_url" name="image_url" 
                                       value="<?php echo $editBlog ? htmlspecialchars($editBlog['image_url']) : ''; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="image_style" class="form-label">Image Style</label>
                                <select class="form-select" id="image_style" name="image_style">
                                    <option value="cover" <?php echo ($editBlog && $editBlog['image_style'] === 'cover') ? 'selected' : ''; ?>>Cover</option>
                                    <option value="contain" <?php echo ($editBlog && $editBlog['image_style'] === 'contain') ? 'selected' : ''; ?>>Contain</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="place" class="form-label">Location</label>
                            <input type="text" class="form-control" id="place" name="place" 
                                   value="<?php echo $editBlog ? htmlspecialchars($editBlog['place']) : ''; ?>"
                                   placeholder="City, Country">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo $editBlog ? 'Update Post' : 'Publish Post'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="blog_id" id="deleteBlogId">
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteBlog(blogId) {
            if (confirm('Are you sure you want to delete this blog post? This action cannot be undone.')) {
                document.getElementById('deleteBlogId').value = blogId;
                document.getElementById('deleteForm').submit();
            }
        }

        <?php if ($editBlog): ?>
            // Show modal if editing
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('blogModal'));
                modal.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>